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
        ]);

        $order->update($validated);

        return redirect()->route('order')->with('success', 'Pesanan berhasil diupdate');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('order')->with('success', 'Pesanan berhasil dihapus');
    }
}
