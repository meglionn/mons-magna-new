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
        // Allow ProductID to be nullable (we'll validate existence later)
        $validated = $request->validate([
            'CustomerID' => 'nullable|exists:customers,CustomerID',
            'ProductID' => 'nullable',
            'ProductName' => 'nullable|string|max:255',
            'Tanggal' => 'required|date',
            'TanggalMulai' => 'required|date',
            'TenggalSelesai' => 'nullable|date|after_or_equal:TanggalMulai',
            'Jumlah' => 'required|integer|min:1',
            'StatusOrder' => 'required|string',
            'StatusProduksi' => 'required|string',
            'Prioritas' => 'required|string',
            'Keterangan' => 'nullable|string',
        ]);

        // If ProductID is provided and is not the special '__new' token, ensure it exists
        $productId = $validated['ProductID'] ?? null;
        if ($productId && $productId !== '__new') {
            $product = Product::find($productId);
            if (!$product) {
                return back()->withErrors(['Product' => 'Produk yang dipilih tidak ditemukan'])->withInput();
            }
        }

        // Require either existing ProductID (or '__new') or new ProductName
        if ((empty($productId) || $productId === '__new') && empty($validated['ProductName'])) {
            return back()->withErrors(['Product' => 'Pilih produk atau tambahkan nama produk baru'])->withInput();
        }

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'CustomerID' => $validated['CustomerID'] ?? null,
                'Tanggal' => $validated['Tanggal'],
                'StatusOrder' => $validated['StatusOrder'],
                'Prioritas' => $validated['Prioritas'] ?? 'Sedang',
                'TotalHarga' => 0,
            ]);

            // If ProductID is '__new' or absent, create product from ProductName
            if (($productId === '__new' || !$productId) && !empty($validated['ProductName'])) {
                $product = Product::create([
                    'NamaProduk' => $validated['ProductName'],
                    'Harga' => 0,
                ]);
                $productId = $product->ProductID;
            }

            // Create order detail (basic)
            OrderDetail::create([
                'OrderID' => $order->OrderID,
                'ProductID' => $productId,
                'Jumlah' => $validated['Jumlah'],
                'HargaSatuan' => 0,
                'Subtotal' => 0,
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
