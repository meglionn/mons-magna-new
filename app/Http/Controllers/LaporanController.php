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
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

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

        // SALES DATA - From Orders Table with date filter
        $ordersQuery = Order::query();
        if ($startDate) {
            $ordersQuery->whereBetween('Tanggal', [$startDate, $endDate]);
        }
        
        $totalOrders = $ordersQuery->count();
        $totalSales = $ordersQuery->sum('TotalHarga');
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // Customer Statistics
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

        // INVENTORY DATA - From Materials Table
        $materials = Material::all();
        $totalMaterials = $materials->count();
        $totalValue = $materials->sum(function($material) {
            return $material->Stok * $material->HargaPerUnit;
        });
        
        // Low stock: items with stock less than 10
        // Out of stock: items with stock = 0
        $lowStockItems = $materials->where('Stok', '>', 0)->where('Stok', '<', 10)->count();
        $outOfStock = $materials->where('Stok', '=', 0)->count();
        
        $inventoryData = [
            'totalMaterials' => $totalMaterials,
            'totalValue' => $totalValue,
            'lowStockItems' => $lowStockItems,
            'outOfStock' => $outOfStock,
        ];

        // FINANCIAL DATA - From Transactions Table with date filter
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

        // Get materials list with low stock warnings
        $lowStockMaterials = $materials->where('Stok', '>', 0)->where('Stok', '<', 10);

        // TOP PRODUCTS - From OrderDetails joined with Products and Orders (with date filter)
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

        // EXPENSES BY CATEGORY - From Transactions with date filter
        $expensesByCategoryQuery = Transaction::where('JenisTransaksi', 'Pengeluaran');
        if ($startDate) {
            $expensesByCategoryQuery->whereBetween('Tanggal', [$startDate, $endDate]);
        }
        $expensesByCategory = $expensesByCategoryQuery
            ->selectRaw('Kategori, SUM(Jumlah) as total')
            ->groupBy('Kategori')
            ->orderByDesc('total')
            ->get();

        // INCOME BY CATEGORY - From Transactions with date filter
        $incomeByCategoryQuery = Transaction::where('JenisTransaksi', 'Pemasukan');
        if ($startDate) {
            $incomeByCategoryQuery->whereBetween('Tanggal', [$startDate, $endDate]);
        }
        $incomeByCategory = $incomeByCategoryQuery
            ->selectRaw('Kategori, SUM(Jumlah) as total')
            ->groupBy('Kategori')
            ->orderByDesc('total')
            ->get();

        // MONTHLY COMPARISON - Last 6 months
            $driver = DB::connection()->getDriverName();

            if ($driver === 'sqlite') {
                $dateExpr = "strftime('%Y-%m', Tanggal)";
            } else {
                // For MySQL and others use DATE_FORMAT
                $dateExpr = "DATE_FORMAT(Tanggal, '%Y-%m')";
            }

            $monthlyData = Transaction::selectRaw(
                    "$dateExpr as month,\n                SUM(CASE WHEN JenisTransaksi = 'Pemasukan' THEN Jumlah ELSE 0 END) as income,\n                SUM(CASE WHEN JenisTransaksi = 'Pengeluaran' THEN Jumlah ELSE 0 END) as expense"
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

    /**
     * Laporan Keuangan - Only financial data for Keuangan role
     */
    public function laporanKeuangan($filter = 'all', $startDate = null, $endDate = null)
    {
        if (!$endDate) $endDate = now();
        
        // FINANCIAL DATA - From Transactions Table with date filter
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
                "$dateExpr as month,\n                SUM(CASE WHEN JenisTransaksi = 'Pemasukan' THEN Jumlah ELSE 0 END) as income,\n                SUM(CASE WHEN JenisTransaksi = 'Pengeluaran' THEN Jumlah ELSE 0 END) as expense"
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        // Return view with only financial data
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
        
        // Get data based on type
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
        
        // Get data based on type
        $data = $this->getReportData($type, $filter);
        
        return Excel::download(new class($data, $type) implements FromCollection, WithHeadings {
            protected $data;
            protected $type;
            
            public function __construct($data, $type) {
                $this->data = $data;
                $this->type = $type;
            }
            
            public function collection() {
                return collect($this->data);
            }
            
            public function headings(): array {
                if ($this->type == 'inventory') {
                    return ['Material', 'SKU', 'Stok', 'Satuan', 'Nilai (IDR)', 'Status'];
                } elseif ($this->type == 'sales') {
                    return ['Produk', 'SKU', 'Jumlah Terjual', 'Pendapatan (IDR)'];
                } else {
                    return ['Kategori', 'Total (IDR)', 'Persentase'];
                }
            }
        }, 'laporan-' . $type . '-' . now()->format('Y-m-d') . '.xlsx');
    }

    private function getReportData($type, $filter)
    {
        // Calculate date range
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
                $value = $material->Stok * $material->HargaPerUnit;
                $status = $material->Stok == 0 ? 'Habis' : ($material->Stok < 10 ? 'Stok Rendah' : 'Sehat');
                return [
                    $material->NamaMaterial,
                    $material->SKU,
                    $material->Stok,
                    $material->Satuan,
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
