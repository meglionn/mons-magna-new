<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Material;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;
use App\Exports\SalesExport;
use App\Exports\FinancialExport;

class LaporanController extends Controller
{
    public function laporan(Request $request)
    {
        $user = auth()->user();
        
        // Get date filter from request
        $filter = $request->get('filter', 'all');
        
        // Calculate date range based on filter
        $startDate = null;
        $endDate = now();
        
        switch($filter) {
            case 'week':
                $startDate = now()->subDays(7);
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                break;
            case 'quarter':
                $startDate = now()->subMonths(3);
                break;
            case 'year':
                $startDate = now()->startOfYear();
                break;
            case 'all':
            default:
                $startDate = null;
                break;
        }

        // If user is Keuangan, show only financial data
        if ($user && $user->Role === 'Keuangan') {
            return $this->laporanKeuangan($filter, $startDate, $endDate);
        }

        // SALES DATA
        $ordersQuery = Order::query();
        if ($startDate) {
            $ordersQuery->whereBetween('Tanggal', [$startDate, $endDate]);
        }
        
        $totalOrders = $ordersQuery->count();
        $totalSales = $ordersQuery->sum('TotalHarga');
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        $totalCustomers = Customer::count();
        $customersWithOrders = Order::distinct('CustomerID')->count('CustomerID');
        
        $salesData = [
            'totalSales' => $totalSales,
            'totalOrders' => $totalOrders,
            'averageOrderValue' => $averageOrderValue,
            'newCustomers' => $totalCustomers,
            'repeatCustomers' => $customersWithOrders,
            'conversionRate' => $totalCustomers > 0 ? ($customersWithOrders / $totalCustomers) * 100 : 0,
        ];

        // INVENTORY DATA
        $materials = Material::all();
        $totalMaterials = $materials->count();
        $totalValue = $materials->sum(function($material) {
            return $material->StokBahan * $material->HargaSatuan;
        });
        
        $lowStockItems = $materials->where('StokBahan', '>', 0)->where('StokBahan', '<', 10)->count();
        $outOfStock = $materials->where('StokBahan', '=', 0)->count();
        
        $inventoryData = [
            'totalMaterials' => $totalMaterials,
            'totalValue' => $totalValue,
            'lowStockItems' => $lowStockItems,
            'outOfStock' => $outOfStock,
        ];

        // FINANCIAL DATA
        $revenueQuery = Transaction::where('JenisTransaksi', 'Pemasukan');
        $expenseQuery = Transaction::where('JenisTransaksi', 'Pengeluaran');
        
        if ($startDate) {
            $revenueQuery->whereBetween('Tanggal', [$startDate, $endDate]);
            $expenseQuery->whereBetween('Tanggal', [$startDate, $endDate]);
        }
        
        $totalRevenue = $revenueQuery->sum('Jumlah');
        $totalExpenses = $expenseQuery->sum('Jumlah');
        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        
        $financialData = [
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'profitMargin' => round($profitMargin, 1),
        ];

        $lowStockMaterials = $materials->where('StokBahan', '>', 0)->where('StokBahan', '<', 10);

        // TOP PRODUCTS
        $topProductsQuery = DB::table('orderdetails')
            ->join('products', 'orderdetails.ProductID', '=', 'products.ProductID')
            ->join('orders', 'orderdetails.OrderID', '=', 'orders.OrderID')
            ->select(
                'products.ProductID',
                'products.NamaProduk',
                DB::raw('SUM(orderdetails.Jumlah) as total_quantity'),
                DB::raw('SUM(orderdetails.Subtotal) as total_revenue')
            );
        
        if ($startDate) {
            $topProductsQuery->whereBetween('orders.Tanggal', [$startDate, $endDate]);
        }
        
        $topProducts = $topProductsQuery
            ->groupBy('products.ProductID', 'products.NamaProduk')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // EXPENSES BY CATEGORY
        $expensesByCategoryQuery = Transaction::where('JenisTransaksi', 'Pengeluaran');
        if ($startDate) {
            $expensesByCategoryQuery->whereBetween('Tanggal', [$startDate, $endDate]);
        }
        $expensesByCategory = $expensesByCategoryQuery
            ->selectRaw('Kategori, SUM(Jumlah) as total')
            ->groupBy('Kategori')
            ->orderByDesc('total')
            ->get();

        // INCOME BY CATEGORY
        $incomeByCategoryQuery = Transaction::where('JenisTransaksi', 'Pemasukan');
        if ($startDate) {
            $incomeByCategoryQuery->whereBetween('Tanggal', [$startDate, $endDate]);
        }
        $incomeByCategory = $incomeByCategoryQuery
            ->selectRaw('Kategori, SUM(Jumlah) as total')
            ->groupBy('Kategori')
            ->orderByDesc('total')
            ->get();

        // MONTHLY COMPARISON
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $dateExpr = "strftime('%Y-%m', Tanggal)";
        } else {
            $dateExpr = "DATE_FORMAT(Tanggal, '%Y-%m')";
        }

        $monthlyData = Transaction::selectRaw(
                "$dateExpr as month,
                SUM(CASE WHEN JenisTransaksi = 'Pemasukan' THEN Jumlah ELSE 0 END) as income,
                SUM(CASE WHEN JenisTransaksi = 'Pengeluaran' THEN Jumlah ELSE 0 END) as expense"
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        return view('laporan', compact(
            'salesData',
            'inventoryData',
            'financialData',
            'materials',
            'lowStockMaterials',
            'topProducts',
            'expensesByCategory',
            'incomeByCategory',
            'monthlyData',
            'filter'
        ));
    }

    public function laporanKeuangan($filter = 'all', $startDate = null, $endDate = null)
    {
        if (!$endDate) $endDate = now();
        
        $revenueQuery = Transaction::where('JenisTransaksi', 'Pemasukan');
        $expenseQuery = Transaction::where('JenisTransaksi', 'Pengeluaran');
        
        if ($startDate) {
            $revenueQuery->whereBetween('Tanggal', [$startDate, $endDate]);
            $expenseQuery->whereBetween('Tanggal', [$startDate, $endDate]);
        }
        
        $totalRevenue = $revenueQuery->sum('Jumlah');
        $totalExpenses = $expenseQuery->sum('Jumlah');
        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        
        $financialData = [
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'profitMargin' => round($profitMargin, 1),
        ];

        $expensesByCategoryQuery = Transaction::where('JenisTransaksi', 'Pengeluaran');
        if ($startDate) {
            $expensesByCategoryQuery->whereBetween('Tanggal', [$startDate, $endDate]);
        }
        $expensesByCategory = $expensesByCategoryQuery
            ->selectRaw('Kategori, SUM(Jumlah) as total')
            ->groupBy('Kategori')
            ->orderByDesc('total')
            ->get();

        $incomeByCategoryQuery = Transaction::where('JenisTransaksi', 'Pemasukan');
        if ($startDate) {
            $incomeByCategoryQuery->whereBetween('Tanggal', [$startDate, $endDate]);
        }
        $incomeByCategory = $incomeByCategoryQuery
            ->selectRaw('Kategori, SUM(Jumlah) as total')
            ->groupBy('Kategori')
            ->orderByDesc('total')
            ->get();

        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $dateExpr = "strftime('%Y-%m', Tanggal)";
        } else {
            $dateExpr = "DATE_FORMAT(Tanggal, '%Y-%m')";
        }

        $monthlyData = Transaction::selectRaw(
                "$dateExpr as month,
                SUM(CASE WHEN JenisTransaksi = 'Pemasukan' THEN Jumlah ELSE 0 END) as income,
                SUM(CASE WHEN JenisTransaksi = 'Pengeluaran' THEN Jumlah ELSE 0 END) as expense"
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        return view('laporan-keuangan', compact(
            'financialData',
            'expensesByCategory',
            'incomeByCategory',
            'monthlyData',
            'filter'
        ));
    }

    public function exportPDF($type, Request $request)
    {
        $filter = $request->get('filter', 'all');
        $data = $this->getReportData($type, $filter);
        
        $pdf = Pdf::loadView('exports.laporan-pdf', [
            'type' => $type,
            'data' => $data,
            'date' => now()->format('d/m/Y')
        ]);
        
        return $pdf->download('laporan-' . $type . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel($type, Request $request)
    {
        $filter = $request->get('filter', 'all');
        $data = $this->getReportData($type, $filter);
        
        $filename = 'laporan-' . $type . '-' . now()->format('Y-m-d') . '.xlsx';
        
        // Gunakan class export yang proper
        if ($type == 'inventory') {
            return Excel::download(new InventoryExport($data), $filename);
        } elseif ($type == 'sales') {
            return Excel::download(new SalesExport($data), $filename);
        } else { // financial
            return Excel::download(new FinancialExport($data), $filename);
        }
    }

    private function getReportData($type, $filter)
    {
        $startDate = null;
        $endDate = now();
        
        switch($filter) {
            case 'week':
                $startDate = now()->subDays(7);
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                break;
            case 'quarter':
                $startDate = now()->subMonths(3);
                break;
            case 'year':
                $startDate = now()->startOfYear();
                break;
        }
        
        if ($type == 'inventory') {
            return Material::all()->map(function($material) {
                $value = $material->StokBahan * $material->HargaSatuan;
                $status = $material->StokBahan == 0 ? 'Habis' : ($material->StokBahan < 10 ? 'Stok Rendah' : 'Sehat');
                return [
                    $material->NamaBahan,
                    'MAT-' . str_pad($material->MaterialID, 3, '0', STR_PAD_LEFT),
                    $material->StokBahan,
                    'unit',
                    $value,
                    $status
                ];
            });
        } elseif ($type == 'sales') {
            $query = DB::table('orderdetails')
                ->join('products', 'orderdetails.ProductID', '=', 'products.ProductID')
                ->join('orders', 'orderdetails.OrderID', '=', 'orders.OrderID')
                ->select(
                    'products.NamaProduk',
                    'products.ProductID',
                    DB::raw('SUM(orderdetails.Jumlah) as total_quantity'),
                    DB::raw('SUM(orderdetails.Subtotal) as total_revenue')
                );
            
            if ($startDate) {
                $query->whereBetween('orders.Tanggal', [$startDate, $endDate]);
            }
            
            return $query->groupBy('products.ProductID', 'products.NamaProduk')
                ->orderByDesc('total_revenue')
                ->get()
                ->map(function($product) {
                    return [
                        $product->NamaProduk,
                        'PRD-' . str_pad($product->ProductID, 3, '0', STR_PAD_LEFT),
                        $product->total_quantity,
                        $product->total_revenue
                    ];
                });
        } else { // financial
            $query = Transaction::where('JenisTransaksi', 'Pengeluaran');
            if ($startDate) {
                $query->whereBetween('Tanggal', [$startDate, $endDate]);
            }
            
            $totalExpenses = $query->sum('Jumlah');
            
            return $query->selectRaw('Kategori, SUM(Jumlah) as total')
                ->groupBy('Kategori')
                ->orderByDesc('total')
                ->get()
                ->map(function($expense) use ($totalExpenses) {
                    $percentage = $totalExpenses > 0 ? ($expense->total / $totalExpenses) * 100 : 0;
                    return [
                        $expense->Kategori ?: 'Lainnya',
                        $expense->total,
                        number_format($percentage, 1) . '%'
                    ];
                });
        }
    }
}