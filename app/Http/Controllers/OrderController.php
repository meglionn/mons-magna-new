<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\CustomDetail;
use App\Models\Produksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
{
    $orders = Order::with(['customer', 'orderDetails.product', 'produksi', 'customDetail'])
        ->orderBy('Tanggal', 'desc')
        ->get();
    
    $stats = [
        'totalOrders' => Order::count(),
        'productionOrders' => Order::whereHas('orderDetails')->count(),
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
            'CustomerEmail' => 'nullable|email|max:100',
            'CustomerPhone' => 'nullable|string|max:20',
            'TotalHarga' => 'required|numeric|min:0',
            'Tanggal' => 'required|date',
            'TanggalMulai' => 'required|date',
            'TenggalSelesai' => 'nullable|date|after:TanggalMulai',
            'Jumlah' => 'required|integer|min:1',
            'StatusOrder' => 'required|string',
            'StatusProduksi' => 'required|string',
            'Prioritas' => 'required|string',
            'Keterangan' => 'nullable|string',
            'Size' => 'nullable|string',
            'Color' => 'nullable|string',
            'Material' => 'nullable|string',
        ]);

        // PERBAIKAN: Cek customer berdasarkan email (jika ada) atau nama
    if (!empty($validated['CustomerEmail'])) {
        // Prioritas cek berdasarkan email
        $customer = Customer::where('Email', $validated['CustomerEmail'])->first();
    } else {
        // Jika tidak ada email, cek berdasarkan nama (case-insensitive)
        $customer = Customer::whereRaw('LOWER(Nama) = ?', [strtolower($validated['CustomerName'])])->first();
    }

    // Jika customer tidak ditemukan, buat baru
    if (!$customer) {
        $customer = Customer::create([
            'Nama' => $validated['CustomerName'],
            'Email' => $request->input('CustomerEmail'),
            'NoTelp' => $request->input('CustomerPhone'),
            'Alamat' => $request->input('CustomerAlamat'),
        ]);
    }

        // Create or update product with specification
        $product = Product::firstOrCreate(
            ['NamaProduk' => $validated['ProductName']],
            [
                'Harga' => 0,
                'Model' => 'Standard',
                'Ukuran' => 0
            ]);
        
        // Create product with unique name including specifications if provided
        $productName = $validated['ProductName'];
        if ($validated['Color'] || $validated['Material']) {
            $productName .= ' (' . implode(' / ', array_filter([$validated['Color'], $validated['Material']])) . ')';
        }
        
        $order = Order::create([
            'CustomerID' => $customer->CustomerID,
            'Tanggal' => $validated['Tanggal'],
            'StatusOrder' => $validated['StatusOrder'],
            'TotalHarga' => 0,
            'Prioritas' => $validated['Prioritas'],
        ]);

        // Create Order Detail (include specs on orderdetails)
        $orderDetail = OrderDetail::create([
            'OrderID' => $order->OrderID,
            'ProductID' => $product->ProductID,
            'Jumlah' => $validated['Jumlah'],
            'HargaSatuan' => $product->Harga,
            'Subtotal' => $product->Harga * $validated['Jumlah'],
            'Ukuran' => $validated['Size'] ?? null,
            'Warna' => $validated['Color'] ?? null,
            'JenisBahan' => $validated['Material'] ?? null,
        ]);

        // Update order total
        $order->update(['TotalHarga' => $orderDetail->Subtotal]);

        // Create Production record
        $produksi = Produksi::create([
            'OrderID' => $order->OrderID,
            'TanggalMulai' => $validated['TanggalMulai'],
            'TanggalSelesai' => $validated['TenggalSelesai'] ?? null,
            'StatusProduksi' => $validated['StatusProduksi'],
            'Keterangan' => $validated['Keterangan'] ?? null,
        ]);

        // Do not create CustomDetail for production orders; specs are stored on orderdetails.

        // Note: Do NOT add specification columns to `orderdetails` here because
        // the `orderdetails` table does not have `Ukuran`, `Warna`, or `JenisBahan` columns.
        // Specifications are saved in `customdetails` (created above) when provided.

        // Log created records for debugging
        try {
            Log::info('OrderController@storeProduction created', [
                'order' => $order->toArray(),
                'orderDetail' => $orderDetail->toArray(),
                'produksi' => $produksi->toArray(),
            ]);
        } catch (\Exception $e) {
            // swallow logging errors
        }

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
    if (!empty($validated['CustomerEmail'])) {
        // Prioritas cek berdasarkan email
        $customer = Customer::where('Email', $validated['CustomerEmail'])->first();
    } else {
        // Jika tidak ada email, cek berdasarkan nama (case-insensitive)
        $customer = Customer::whereRaw('LOWER(Nama) = ?', [strtolower($validated['CustomerName'])])->first();
    }

    // Jika customer tidak ditemukan, buat baru
    if (!$customer) {
        $customer = Customer::create([
            'Nama' => $validated['CustomerName'],
            'Email' => $request->input('CustomerEmail'),
            'NoTelp' => $request->input('CustomerPhone'),
            'Alamat' => $request->input('CustomerAlamat'),
        ]);
    }
        
        // For custom orders create or find a product record using ProductType
        $product = Product::firstOrCreate(
            ['NamaProduk' => $validated['ProductType']],
            [
                'Harga' => $validated['TotalHarga'] ?? 0,
                'Model' => $validated['ProductType'] ?? 'Custom',
                'Ukuran' => $validated['Size'] ?? null,
            ]
        );
        
        // Create Order
        $order = Order::create([
            'CustomerID' => $customer->CustomerID,
            'Tanggal' => $validated['Tanggal'],
            'StatusOrder' => $validated['StatusOrder'],
            'TotalHarga' => $validated['TotalHarga'],
            'DepositPaid' => $validated['DepositPaid'] ?? 0,
        ]);

    // Create Custom Detail
    $customNotes = [
        'ProductType' => $validated['ProductType'],
        'Size' => $validated['Size'],
        'Color' => $validated['Color'],
        'Material' => $validated['Material'],
        'TenggalSelesai' => $validated['TenggalSelesai'] ?? null,
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
            'CustomerName' => 'nullable|string|max:255',
            'ProductName' => 'nullable|string|max:255',
            'Tanggal' => 'nullable|date',
            'TanggalMulai' => 'nullable|date',
            'TenggalSelesai' => 'nullable|date',
            'Jumlah' => 'nullable|integer|min:1',
            'StatusOrder' => 'nullable|string|max:50',
            'StatusProduksi' => 'nullable|string|max:50',
            'Prioritas' => 'nullable|string|max:50',
            'Keterangan' => 'nullable|string',
            'TotalHarga' => 'nullable|numeric',
            'DepositPaid' => 'nullable|numeric',
            'ProductType' => 'nullable|string',
            'Size' => 'nullable|string',
            'Color' => 'nullable|string',
            'Material' => 'nullable|string',
            'Style' => 'nullable|string',
            'CustomFeatures' => 'nullable|string',
            'SpecialRequirements' => 'nullable|string',
        ]);

        // Capture previous status to detect transition to 'Selesai'
        $previousStatus = $order->StatusOrder;

        // Update order basic fields
        $order->update([
            'Tanggal' => $validated['Tanggal'] ?? $order->Tanggal,
            'StatusOrder' => $validated['StatusOrder'] ?? $order->StatusOrder,
            'TotalHarga' => $validated['TotalHarga'] ?? $order->TotalHarga,
            'Prioritas' => $validated['Prioritas'] ?? $order->Prioritas,
            'DepositPaid' => $validated['DepositPaid'] ?? $order->DepositPaid,
        ]);

        // Update customer if CustomerName provided
        if (!empty($validated['CustomerName'])) {
            $order->customer->update(['Nama' => $validated['CustomerName']]);
        }

        // Update production order details
        if ($order->orderDetails->first() && !empty($validated['ProductName'])) {
            // Build product name with specifications
            $productName = $validated['ProductName'];
            $color = $validated['Color'] ?? null;
            $material = $validated['Material'] ?? null;
            if ($color || $material) {
                $productName .= ' (' . implode(' / ', array_filter([$color, $material])) . ')';
            }
            
            $product = Product::firstOrCreate(
                ['NamaProduk' => $productName],
                ['Harga' => 0, 'Ukuran' => $validated['Size'] ?? null]
            );
            
            // If product needs updating (for existing products), update the size
            if (!empty($validated['Size'])) {
                $product->update(['Ukuran' => $validated['Size']]);
            }
            
            $order->orderDetails->first()->update(['ProductID' => $product->ProductID]);
        } elseif ($order->orderDetails->first() && (($validated['Size'] ?? null) || ($validated['Color'] ?? null) || ($validated['Material'] ?? null))) {
            // Update existing product with new specifications
            $product = $order->orderDetails->first()->product;
            $updatedData = [];
            
            if (!empty($validated['Size'])) {
                $updatedData['Ukuran'] = $validated['Size'];
            }
            $color = $validated['Color'] ?? null;
            $material = $validated['Material'] ?? null;
            if ($color || $material) {
                $productName = $product->NamaProduk;
                // Remove old spec if it exists
                if (strpos($productName, '(') !== false) {
                    $productName = substr($productName, 0, strpos($productName, '('));
                }
                $productName = trim($productName);
                $productName .= ' (' . implode(' / ', array_filter([$color, $material])) . ')';
                $updatedData['NamaProduk'] = $productName;
            }
            
            if (!empty($updatedData)) {
                $product->update($updatedData);
            }
        }

        if ($order->orderDetails->first() && !empty($validated['Jumlah'])) {
            $orderDetail = $order->orderDetails->first();
            $orderDetail->update([
                'Jumlah' => $validated['Jumlah'],
                'Subtotal' => ($validated['Jumlah'] * $orderDetail->HargaSatuan),
            ]);
        }

        // Update production record if exists
        if ($order->produksi->first()) {
            $produksi = $order->produksi->first();
            $produksi->update([
                'TanggalMulai' => $validated['TanggalMulai'] ?? $produksi->TanggalMulai,
                'TanggalSelesai' => $validated['TenggalSelesai'] ?? $produksi->TanggalSelesai,
                'StatusProduksi' => $validated['StatusProduksi'] ?? $produksi->StatusProduksi,
                'Keterangan' => $validated['Keterangan'] ?? $produksi->Keterangan,
            ]);
        }

        // Update custom detail for custom orders
        if ($order->customDetail) {
            // Update existing custom detail
            $customNotes = json_decode($order->customDetail->CatatanTambahan, true) ?? [];
            $customNotes = array_merge($customNotes, [
                'ProductType' => $validated['ProductType'] ?? ($customNotes['ProductType'] ?? ''),
                'TenggalSelesai' => $validated['TenggalSelesai'] ?? ($customNotes['TenggalSelesai'] ?? null),
                'Size' => $validated['Size'] ?? ($customNotes['Size'] ?? ''),
                'Color' => $validated['Color'] ?? ($customNotes['Color'] ?? ''),
                'Material' => $validated['Material'] ?? ($customNotes['Material'] ?? ''),
                'Style' => $validated['Style'] ?? ($customNotes['Style'] ?? ''),
                'CustomFeatures' => $validated['CustomFeatures'] ?? ($customNotes['CustomFeatures'] ?? ''),
                'SpecialRequirements' => $validated['SpecialRequirements'] ?? ($customNotes['SpecialRequirements'] ?? ''),
            ]);

            $order->customDetail->update([
                'Warna' => $validated['Color'] ?? $order->customDetail->Warna,
                'Ukuran' => $validated['Size'] ?? $order->customDetail->Ukuran,
                'JenisBahan' => $validated['Material'] ?? $order->customDetail->JenisBahan,
                'Model' => $validated['ProductType'] ?? $order->customDetail->Model,
                'CatatanTambahan' => json_encode($customNotes),
            ]);
        }

        // Update specs on production order's first order detail if provided
        if ($order->orderDetails->count() > 0 && (!empty($validated['Size']) || !empty($validated['Color']) || !empty($validated['Material']))) {
            $orderDetail = $order->orderDetails->first();
            $orderDetail->update([
                'Ukuran' => $validated['Size'] ?? $orderDetail->Ukuran,
                'Warna' => $validated['Color'] ?? $orderDetail->Warna,
                'JenisBahan' => $validated['Material'] ?? $orderDetail->JenisBahan,
            ]);
        }

        // If order just moved to 'Selesai' from a different status, create a Pemasukan transaction
        $newStatus = $order->StatusOrder;
        if ($newStatus === 'Selesai' && $previousStatus !== 'Selesai') {
            // Determine amount: prefer TotalHarga, else sum subtotals
            $amount = $order->TotalHarga ?? $order->orderDetails->sum('Subtotal') ?? 0;

            // Determine payment status based on DepositPaid
            $deposit = $order->DepositPaid ?? 0;
            $paymentStatus = ($deposit >= $amount && $amount > 0) ? 'Lunas' : 'Belum Lunas';

            try {
                Transaction::create([
                    'OrderID' => $order->OrderID,
                    'JenisTransaksi' => 'Pemasukan',
                    'Kategori' => 'Penjualan',
                    'Jumlah' => $amount,
                    'Tanggal' => now(),
                    'MetodePembayaran' => null,
                    'Status' => $paymentStatus,
                    'Keterangan' => 'Order #' . $order->OrderID . ' selesai',
                ]);
            } catch (\Exception $e) {
                // Log and continue; do not block order update
                Log::error('Failed to create transaction for completed order: ' . $order->OrderID, ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('order')
            ->with('success', 'Pesanan berhasil diupdate');
    }

    public function destroy(Order $order)
    {
        // Simpan CustomerID sebelum order dihapus
        $customerId = $order->CustomerID;
        
        // Delete related records first
        $order->orderDetails()->delete();
        $order->customDetail()->delete();
        Produksi::where('OrderID', $order->OrderID)->delete();
        
        // Delete order
        $order->delete();
        
        // Cek apakah customer masih punya order lain
        if ($customerId) {
            $customer = Customer::find($customerId);
            if ($customer) {
                $remainingOrders = Order::where('CustomerID', $customerId)->count();
                
                // Jika tidak ada order lagi, hapus customer
                if ($remainingOrders == 0) {
                    $customer->delete();
                }
            }
        }

        return redirect()->route('order')
            ->with('success', 'Pesanan berhasil dihapus');
    }
}
