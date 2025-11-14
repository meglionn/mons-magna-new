<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function laporan()
    { //diubah, disambung ke database
        $salesData = [
            'totalSales' => 125000000,
            'totalOrders' => 200,
            'averageOrderValue' => 625000,
            'newCustomers' => 25,
            'repeatCustomers' => 15,
            'conversionRate' => 12.5,
        ];

        $inventoryData = [
            'totalMaterials' => 48,
            'totalValue' => 86000000,
            'lowStockItems' => 5,
            'outOfStock' => 2,
        ];

        $financialData = [
            'totalRevenue' => 210000000,
            'totalExpenses' => 160000000,
            'netProfit' => 50000000,
            'profitMargin' => 23.8,
        ];

        return view('laporan', compact(
            'salesData',
            'inventoryData',
            'financialData'
        ));
    }
}
