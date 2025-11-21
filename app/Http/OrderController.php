<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer')->get();
        $stats = [
            'totalOrders' => Order::count(),
            'productionOrders' => Order::where('StatusOrder', 'Produksi')->count(),
            'customOrders' => Order::whereHas('customDetail')->count(),
            'activeOrders' => Order::whereIn('StatusOrder', ['Proses', 'Produksi'])->count(),
            'completed' => Order::where('StatusOrder', 'Selesai')->count(),
        ];
        
        return view('order', compact('orders', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'CustomerID' => 'nullable|exists:customers,CustomerID',
            'Tanggal' => 'required|date',
            'StatusOrder' => 'required|string|max:50',
            'TotalHarga' => 'nullable|numeric',
        ]);

        Order::create($validated);

        return redirect()->route('order.index')
            ->with('success', 'Pesanan berhasil ditambahkan');
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

        return redirect()->route('order.index')
            ->with('success', 'Pesanan berhasil diupdate');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('order.index')
            ->with('success', 'Pesanan berhasil dihapus');
    }
}