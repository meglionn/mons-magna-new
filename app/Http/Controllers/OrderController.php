<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

        // Create Order
        // Ensure customer exists (create if needed)
        $customer = Customer::create([
            'Nama' => $validated['CustomerName'],
            'Email' => $request->input('CustomerEmail'),
            'NoTelp' => $request->input('CustomerPhone'),
            'Alamat' => $request->input('CustomerAlamat'),
        ]);

        // Create or update product with specification
        $productData = [
            'Harga' => 0,
            'Ukuran' => $validated['Size'] ?? null,
        ];
        
        // Create product with unique name including specifications if provided
        $productName = $validated['ProductName'];
        if ($validated['Color'] || $validated['Material']) {
            $productName .= ' (' . implode(' / ', array_filter([$validated['Color'], $validated['Material']])) . ')';
        }
        
        $product = Product::firstOrCreate(
            ['NamaProduk' => $productName],
            $productData
        );

        $order = Order::create([
            'CustomerID' => $customer->CustomerID,
            'Tanggal' => $validated['Tanggal'],
            'StatusOrder' => $validated['StatusOrder'],
            'TotalHarga' => 0,
            'Prioritas' => $validated['Prioritas'],
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
        $produksi = Produksi::create([
            'OrderID' => $order->OrderID,
            'TanggalMulai' => $validated['TanggalMulai'],
            'TanggalSelesai' => $validated['TenggalSelesai'] ?? null,
            'StatusProduksi' => $validated['StatusProduksi'],
            'Keterangan' => $validated['Keterangan'] ?? null,
        ]);

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

        // Update order basic fields
        $order->update([
            'Tanggal' => $validated['Tanggal'] ?? $order->Tanggal,
            'StatusOrder' => $validated['StatusOrder'] ?? $order->StatusOrder,
            'TotalHarga' => $validated['TotalHarga'] ?? $order->TotalHarga,
            'Prioritas' => $validated['Prioritas'] ?? $order->Prioritas,
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

        // Update custom detail if exists
        if ($order->customDetail) {
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