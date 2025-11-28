<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\CustomDetail;
use App\Models\Produksi;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
{
    $orders = Order::with('customer')->orderBy('Tanggal', 'desc')->get();
    
    $stats = [
        'totalOrders' => Order::count(),
        'productionOrders' => Order::where('StatusOrder', 'Produksi')->count(),
        'customOrders' => Order::whereHas('customDetail')->count(),
        'activeOrders' => Order::whereIn('StatusOrder', ['Proses', 'Produksi'])->count(),
        'completed' => Order::where('StatusOrder', 'Selesai')->count(),
    ];
    
    $customers = Customer::all();
    $products = Product::all();
    
    return view('order', compact('orders', 'stats', 'customers', 'products'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'CustomerName' => 'required|string|max:255',
            'Tanggal' => 'required|date',
            'StatusOrder' => 'required|string|max:50',
            'TotalHarga' => 'nullable|numeric|min:0',
        ]);

        // If CustomerID not provided, create a new customer using provided name (and optional contact fields)
        $customer = Customer::create([
            'Nama' => $validated['CustomerName'],
            'Email' => $request->input('CustomerEmail'),
            'NoTelp' => $request->input('CustomerPhone'),
            'Alamat' => $request->input('CustomerAlamat'),
        ]);
        $order = Order::create([
            'CustomerID' => $customer->CustomerID,
            'Tanggal' => $validated['Tanggal'],
            'StatusOrder' => $validated['StatusOrder'],
            'TotalHarga' => $validated['TotalHarga'] ?? 0,
        ]);

        return redirect()->route('order')
            ->with('success', 'Pesanan berhasil ditambahkan');
    }

    public function storeProduction(Request $request)
    {
        $validated = $request->validate([
            'CustomerName' => 'required|string|max:255',
            'ProductName' => 'required|string|max:255',
            'Tanggal' => 'required|date',
            'TanggalMulai' => 'required|date',
            'TenggalSelesai' => 'nullable|date|after:TanggalMulai',
            'Jumlah' => 'required|integer|min:1',
            'StatusOrder' => 'required|string',
            'StatusProduksi' => 'required|string',
            'Prioritas' => 'required|string',
            'Keterangan' => 'nullable|string',
        ]);

        // Create Order
        // Ensure customer exists (create if needed)
        $customer = Customer::create([
            'Nama' => $validated['CustomerName'],
            'Email' => $request->input('CustomerEmail'),
            'NoTelp' => $request->input('CustomerPhone'),
            'Alamat' => $request->input('CustomerAlamat'),
        ]);

        // Cari produk, jika belum ada maka buat baru (harga default 0)
        $product = Product::firstOrCreate(
            ['NamaProduk' => $validated['ProductName']],
            ['Harga' => 0]
        );

        $order = Order::create([
            'CustomerID' => $customer->CustomerID,
            'Tanggal' => $validated['Tanggal'],
            'StatusOrder' => $validated['StatusOrder'],
            'TotalHarga' => 0, // Will be calculated from order details
        ]);

        // Create Order Detail
        $orderDetail = OrderDetail::create([
            'OrderID' => $order->OrderID,
            'ProductID' => $product->ProductID,
            'Jumlah' => $validated['Jumlah'],
            'HargaSatuan' => $product->Harga,
            'Subtotal' => $product->Harga * $validated['Jumlah'],
        ]);

        // Update order total
        $order->update(['TotalHarga' => $orderDetail->Subtotal]);

        // Create Production record
        Produksi::create([
            'OrderID' => $order->OrderID,
            'TanggalMulai' => $validated['TanggalMulai'],
            'TanggalSelesai' => $validated['TenggalSelesai'] ?? null,
            'StatusProduksi' => $validated['StatusProduksi'],
            'Keterangan' => $validated['Keterangan'] ?? null,
        ]);

        return redirect()->route('order')
            ->with('success', 'Pesanan produksi berhasil ditambahkan');
    }

    public function storeCustom(Request $request)
    {
        $validated = $request->validate([
            'CustomerName' => 'required|string|max:255',
        'Tanggal' => 'required|date',
        'TenggalSelesai' => 'required|date',
        'TotalHarga' => 'required|numeric|min:0',
        'DepositPaid' => 'nullable|numeric|min:0',
        'StatusOrder' => 'required|string',
        'Prioritas' => 'required|string',
        // Custom details
        'ProductType' => 'required|string',
        'Size' => 'required|string',
        'Color' => 'required|string',
        'Material' => 'required|string',
        'Style' => 'nullable|string',
        'CustomFeatures' => 'nullable|string',
        'FootLength' => 'nullable|string',
        'FootWidth' => 'nullable|string',
        'InstepHeight' => 'nullable|string',
        'SpecialRequirements' => 'nullable|string',
        'AdditionalNotes' => 'nullable|string',
    ]);

        // Ensure customer exists (create if needed)
        $customer = Customer::create([
            'Nama' => $validated['CustomerName'],
            'Email' => $request->input('CustomerEmail'),
            'NoTelp' => $request->input('CustomerPhone'),
            'Alamat' => $request->input('CustomerAlamat'),
        ]);
        // Create Order
        $order = Order::create([
            'CustomerID' => $customer->CustomerID,
            'Tanggal' => $validated['Tanggal'],
            'StatusOrder' => $validated['StatusOrder'],
            'TotalHarga' => $validated['TotalHarga'],
        ]);

    // Create Custom Detail
    $customNotes = [
        'ProductType' => $validated['ProductType'],
        'Size' => $validated['Size'],
        'Color' => $validated['Color'],
        'Material' => $validated['Material'],
        'Style' => $validated['Style'] ?? '',
        'CustomFeatures' => $validated['CustomFeatures'] ?? '',
        'FootLength' => $validated['FootLength'] ?? '',
        'FootWidth' => $validated['FootWidth'] ?? '',
        'InstepHeight' => $validated['InstepHeight'] ?? '',
        'SpecialRequirements' => $validated['SpecialRequirements'] ?? '',
        'AdditionalNotes' => $validated['AdditionalNotes'] ?? '',
    ];

    CustomDetail::create([
        'OrderID' => $order->OrderID,
        'JenisBahan' => $validated['Material'],
        'Warna' => $validated['Color'],
        'Ukuran' => $validated['Size'],
        'Model' => $validated['ProductType'],
        'CatatanTambahan' => json_encode($customNotes),
    ]);

        // Return to listing (route name `order` exists)
        return redirect()->route('order')
            ->with('success', 'Pesanan custom berhasil ditambahkan');
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'CustomerID' => 'nullable|exists:customers,CustomerID',
            'Tanggal' => 'required|date',
            'StatusOrder' => 'required|string|max:50',
            'TotalHarga' => 'nullable|numeric',
        ]);

        $order->update($validated);

        return redirect()->route('order')
            ->with('success', 'Pesanan berhasil diupdate');
    }

    public function destroy(Order $order)
    {
        // Delete related records first
        $order->orderDetails()->delete();
        $order->customDetail()->delete();
        Produksi::where('OrderID', $order->OrderID)->delete();
        
        $order->delete();

        return redirect()->route('order')
            ->with('success', 'Pesanan berhasil dihapus');
    }
}