<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Produksi;
use App\Models\Product;
use App\Models\CustomDetail;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'orderDetails.product', 'produksi', 'customDetail'])
            ->orderBy('OrderID', 'desc')
            ->get();

        $customers = Customer::orderBy('Nama')->get();
        $products = Product::orderBy('NamaProduk')->get();

        $stats = [
            'totalOrders' => Order::count(),
            'productionOrders' => Order::has('orderDetails')->count(),
            'customOrders' => Order::has('customDetail')->count(),
            'activeOrders' => Order::where('StatusOrder', '!=', 'Selesai')->count(),
            'completed' => Order::where('StatusOrder', 'Selesai')->count(),
        ];

        return view('order', compact('orders', 'customers', 'products', 'stats'));
    }

    public function store(Request $request)
    {
        // Generic store (not used by the production dialog directly)
        $validated = $request->validate([
            'CustomerID' => 'nullable|exists:customers,CustomerID',
            'Tanggal' => 'required|date',
            'StatusOrder' => 'required|string',
            'Prioritas' => 'nullable|string',
        ]);

        $order = Order::create([
            'CustomerID' => $validated['CustomerID'] ?? null,
            'Tanggal' => $validated['Tanggal'],
            'StatusOrder' => $validated['StatusOrder'],
            'Prioritas' => $validated['Prioritas'] ?? 'Sedang',
            'TotalHarga' => 0,
        ]);

        return redirect()->route('order')->with('success', 'Pesanan berhasil dibuat');
    }

    public function storeProduction(Request $request)
    {
        Log::debug('storeProduction input', $request->all());
        
        // Validation: allow selecting existing customer by ID or entering a new customer name
        $validated = $request->validate([
            'CustomerID' => 'nullable|exists:customers,CustomerID',
            'CustomerName' => 'nullable|string|max:255',
            'ProductID' => 'nullable',
            'NamaProduk' => 'nullable|string|max:255', // accept legacy field
            'ProductName' => 'nullable|string|max:255',
            'Tanggal' => 'required|date',
            'TanggalMulai' => 'required|date',
            'TenggalSelesai' => 'nullable|date|after_or_equal:TanggalMulai',
            'Jumlah' => 'required|integer|min:1',
            'StatusOrder' => 'required|string',
            'StatusProduksi' => 'required|string',
            'Prioritas' => 'required|string',
            'Keterangan' => 'nullable|string',
            'Ukuran' => 'nullable|string|max:50',
        ]);

        // Determine customer: prefer CustomerID, fallback to CustomerName
        if (!empty($validated['CustomerID'])) {
            $customer = Customer::find($validated['CustomerID']);
            if (!$customer) {
                return back()->withErrors(['CustomerID' => 'Pelanggan tidak ditemukan'])->withInput();
            }
        } elseif (!empty($validated['CustomerName'])) {
            $customer = Customer::firstOrCreate(
                ['Nama' => $validated['CustomerName']],
                ['Email' => null, 'NoTelp' => null, 'Alamat' => null]
            );
        } else {
            return back()->withErrors(['Customer' => 'Pilih pelanggan atau isi nama pelanggan'])->withInput();
        }

        // Handle product
        $productId = $validated['ProductID'] ?? null;

        // Legacy: client may send NamaProduk (name) instead of ProductID — try to resolve
        if (empty($productId) && !empty($validated['NamaProduk'])) {
            $found = Product::where('NamaProduk', $validated['NamaProduk'])->first();
            if ($found) {
                $productId = $found->ProductID;
                Log::debug('storeProduction: resolved NamaProduk to ProductID', ['NamaProduk' => $validated['NamaProduk'], 'ProductID' => $productId]);
            } else {
                // If NamaProduk present but not found, move value into ProductName to create
                $validated['ProductName'] = $validated['NamaProduk'];
            }
        }
        
        // If ProductID is '__new' or empty, create new product
        if ($productId === '__new' || empty($productId)) {
            if (empty($validated['ProductName'])) {
                Log::warning('storeProduction: missing ProductName', ['productId' => $productId, 'request' => $request->all()]);
                return back()->withErrors(['ProductName' => 'Nama produk baru harus diisi (productId=' . ($productId ?? 'null') . ')'])->withInput();
            }
            
            $product = Product::create([
                'NamaProduk' => $validated['ProductName'],
                'JenisProduk' => null,
                'Model' => 'Standard',
                'Ukuran' => $validated['Ukuran'] ?? 42,
                'Harga' => 0,
            ]);
            $productId = $product->ProductID;
        } else {
            // Verify product exists
            $product = Product::find($productId);
            if (!$product) {
                Log::warning('storeProduction: productId provided but not found', ['productId' => $productId, 'request' => $request->all()]);
                return back()->withErrors(['ProductID' => 'Produk tidak ditemukan (productId=' . ($productId ?? 'null') . ')'])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'CustomerID' => $customer->CustomerID,
                'Tanggal' => $validated['Tanggal'],
                'StatusOrder' => $validated['StatusOrder'],
                'Prioritas' => $validated['Prioritas'] ?? 'Sedang',
                'TotalHarga' => 0,
            ]);

            // Create order detail
            OrderDetail::create([
                'OrderID' => $order->OrderID,
                'ProductID' => $productId,
                'Jumlah' => $validated['Jumlah'],
                'HargaSatuan' => $product->Harga ?? 0,
                'Subtotal' => ($product->Harga ?? 0) * $validated['Jumlah'],
                'Ukuran' => $validated['Ukuran'] ?? null,
            ]);

            // Create produksi record
            Produksi::create([
                'OrderID' => $order->OrderID,
                'TanggalMulai' => $validated['TanggalMulai'],
                'TanggalSelesai' => $validated['TenggalSelesai'] ?? null,
                'StatusProduksi' => $validated['StatusProduksi'],
                'Keterangan' => $validated['Keterangan'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('order')->with('success', 'Pesanan produksi berhasil dibuat');
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Production order creation failed', ['error' => $ex->getMessage(), 'trace' => $ex->getTraceAsString()]);
            return back()->withErrors(['error' => 'Gagal menyimpan pesanan: ' . $ex->getMessage()])->withInput();
        }
    }

    public function storeCustom(Request $request)
    {
        $validated = $request->validate([
            'CustomerID' => 'nullable|exists:customers,CustomerID',
            'Tanggal' => 'required|date',
            'Keterangan' => 'nullable|string',
            'Size' => 'nullable|string',
            'Color' => 'nullable|string',
            'Material' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'CustomerID' => $validated['CustomerID'] ?? null,
                'Tanggal' => $validated['Tanggal'],
                'StatusOrder' => 'Pending',
                'Prioritas' => 'Sedang',
                'TotalHarga' => 0,
            ]);

            CustomDetail::create([
                'OrderID' => $order->OrderID,
                'Ukuran' => $validated['Size'] ?? null,
                'Warna' => $validated['Color'] ?? null,
                'JenisBahan' => $validated['Material'] ?? null,
                'CatatanTambahan' => json_encode(['Keterangan' => $validated['Keterangan'] ?? null]),
            ]);

            DB::commit();
            return redirect()->route('order')->with('success', 'Pesanan custom berhasil dibuat');
        } catch (\Exception $ex) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan pesanan custom: ' . $ex->getMessage()])->withInput();
        }
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'StatusOrder' => 'nullable|string',
            'Prioritas' => 'nullable|string',
            'Tanggal' => 'nullable|date',
            'TenggalSelesai' => 'nullable|date',
            'TotalHarga' => 'nullable|numeric|min:0',
            'DepositPaid' => 'nullable|numeric|min:0',
            'CustomerName' => 'nullable|string|max:255',
            
            // Production fields
            'ProductName' => 'nullable|string|max:255',
            'Jumlah' => 'nullable|integer|min:1',
            'TanggalMulai' => 'nullable|date',
            'StatusProduksi' => 'nullable|string',
            'Keterangan' => 'nullable|string',
            'Size' => 'nullable|string',
            'Color' => 'nullable|string',
            'Material' => 'nullable|string',
            
            // Custom fields
            'ProductType' => 'nullable|string|max:255',
            'Style' => 'nullable|string',
            'CustomFeatures' => 'nullable|string',
            'SpecialRequirements' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update customer if name provided
            if (!empty($validated['CustomerName']) && $order->customer) {
                $order->customer->update(['Nama' => $validated['CustomerName']]);
            }

            // Update order basic fields
            $orderData = array_filter([
                'StatusOrder' => $validated['StatusOrder'] ?? null,
                'Prioritas' => $validated['Prioritas'] ?? null,
                'Tanggal' => $validated['Tanggal'] ?? null,
                'TotalHarga' => $validated['TotalHarga'] ?? null,
                'DepositPaid' => $validated['DepositPaid'] ?? null,
            ], function($value) { return $value !== null; });
            
            if (!empty($orderData)) {
                $order->update($orderData);
            }

            // Update production order details
            if ($order->orderDetails->count() > 0) {
                $produksi = $order->produksi->first();
                if ($produksi) {
                    $produksiData = array_filter([
                        'TanggalMulai' => $validated['TanggalMulai'] ?? null,
                        'TanggalSelesai' => $validated['TenggalSelesai'] ?? null,
                        'StatusProduksi' => $validated['StatusProduksi'] ?? null,
                        'Keterangan' => $validated['Keterangan'] ?? null,
                    ], function($value) { return $value !== null; });
                    
                    if (!empty($produksiData)) {
                        $produksi->update($produksiData);
                    }
                }

                // Update order details (first item)
                $orderDetail = $order->orderDetails->first();
                if ($orderDetail) {
                    $detailData = array_filter([
                        'Jumlah' => $validated['Jumlah'] ?? null,
                        'Ukuran' => $validated['Size'] ?? null,
                        'Warna' => $validated['Color'] ?? null,
                        'JenisBahan' => $validated['Material'] ?? null,
                    ], function($value) { return $value !== null; });
                    
                    if (!empty($detailData)) {
                        $orderDetail->update($detailData);
                        
                        // Recalculate subtotal if quantity changed
                        if (isset($detailData['Jumlah'])) {
                            $orderDetail->Subtotal = $detailData['Jumlah'] * $orderDetail->HargaSatuan;
                            $orderDetail->save();
                        }
                    }
                }
            }
            
            // Update custom order details
            if ($order->customDetail) {
                $customDetail = $order->customDetail;
                
                // Get existing notes
                $existingNotes = [];
                try {
                    if ($customDetail->CatatanTambahan) {
                        $existingNotes = json_decode($customDetail->CatatanTambahan, true) ?: [];
                    }
                } catch (\Exception $e) {
                    $existingNotes = [];
                }
                
                // Merge with new data
                $newNotes = array_merge($existingNotes, array_filter([
                    'ProductType' => $validated['ProductType'] ?? null,
                    'Size' => $validated['Size'] ?? null,
                    'Color' => $validated['Color'] ?? null,
                    'Material' => $validated['Material'] ?? null,
                    'Style' => $validated['Style'] ?? null,
                    'CustomFeatures' => $validated['CustomFeatures'] ?? null,
                    'SpecialRequirements' => $validated['SpecialRequirements'] ?? null,
                    'TenggalSelesai' => $validated['TenggalSelesai'] ?? null,
                ], function($value) { return $value !== null; }));
                
                $customDetail->update([
                    'Ukuran' => $validated['Size'] ?? $customDetail->Ukuran,
                    'Warna' => $validated['Color'] ?? $customDetail->Warna,
                    'JenisBahan' => $validated['Material'] ?? $customDetail->JenisBahan,
                    'Model' => $validated['ProductType'] ?? $customDetail->Model,
                    'CatatanTambahan' => json_encode($newNotes),
                ]);
            }

            DB::commit();
            return redirect()->route('order')->with('success', 'Pesanan berhasil diupdate');
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Order update failed', ['error' => $ex->getMessage(), 'trace' => $ex->getTraceAsString()]);
            return back()->withErrors(['error' => 'Gagal mengupdate pesanan: ' . $ex->getMessage()])->withInput();
        }
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('order')->with('success', 'Pesanan berhasil dihapus');
    }
}
