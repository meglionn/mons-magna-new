<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('order')->orderBy('Tanggal', 'desc')->get();
        
        $income = Transaction::where('JenisTransaksi', 'Pemasukan')->sum('Jumlah');
        $expenses = Transaction::where('JenisTransaksi', 'Pengeluaran')->sum('Jumlah');
        $netProfit = $income - $expenses;
        
        $stats = [
            'totalIncome' => $income,
            'totalExpenses' => $expenses,
            'netProfit' => $netProfit,
            'profitMargin' => $income > 0 ? ($netProfit / $income) * 100 : 0,
        ];
        
        // Income Analysis
        $incomeByCategory = Transaction::where('JenisTransaksi', 'Pemasukan')
            ->selectRaw('Kategori, SUM(Jumlah) as total, COUNT(*) as count')
            ->groupBy('Kategori')
            ->get();
            
        $incomeByPayment = Transaction::where('JenisTransaksi', 'Pemasukan')
            ->selectRaw('MetodePembayaran, SUM(Jumlah) as total')
            ->groupBy('MetodePembayaran')
            ->get();
        
        // Expense Analysis
        $expensesByCategory = Transaction::where('JenisTransaksi', 'Pengeluaran')
            ->selectRaw('Kategori, SUM(Jumlah) as total, COUNT(*) as count')
            ->groupBy('Kategori')
            ->get();
            
        $expensesByPayment = Transaction::where('JenisTransaksi', 'Pengeluaran')
            ->selectRaw('MetodePembayaran, SUM(Jumlah) as total')
            ->groupBy('MetodePembayaran')
            ->get();
        
        return view('financial', compact(
            'transactions', 
            'stats', 
            'incomeByCategory', 
            'incomeByPayment',
            'expensesByCategory',
            'expensesByPayment'
        ));
    }

    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'OrderID' => 'nullable|string|max:255',
            'Kategori' => 'nullable|string|max:255',
            'Jumlah' => 'required|numeric|min:0',
            'Tanggal' => 'required|date',
            'MetodePembayaran' => 'nullable|string|max:255',
            'Status' => 'nullable|string|max:255',
            'Keterangan' => 'nullable|string',
        ]);

        $validated['JenisTransaksi'] = 'Pemasukan';

        Transaction::create($validated);

        return redirect()->route('financial')
            ->with('success', 'Pendapatan berhasil ditambahkan');
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'OrderID' => 'nullable|string|max:255',
            'Kategori' => 'nullable|string|max:255',
            'Jumlah' => 'required|numeric|min:0',
            'Tanggal' => 'required|date',
            'MetodePembayaran' => 'nullable|string|max:255',
            'Status' => 'nullable|string|max:255',
            'Keterangan' => 'nullable|string',
        ]);

        $validated['JenisTransaksi'] = 'Pengeluaran';

        Transaction::create($validated);

        return redirect()->route('financial')
            ->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('financial')
            ->with('success', 'Transaksi berhasil dihapus');
    }

    public function export()
    {
        $transactions = Transaction::orderBy('Tanggal', 'desc')->get();
        
        $filename = 'laporan_keuangan_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['Tanggal', 'Tipe', 'Kategori', 'Deskripsi', 'Jumlah (IDR)', 'Metode Pembayaran', 'Referensi', 'Status']);
            
            // Data rows
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->Tanggal->format('d/m/Y'),
                    $transaction->JenisTransaksi,
                    $transaction->Kategori ?: '-',
                    $transaction->Keterangan ?: '-',
                    $transaction->Jumlah,
                    $transaction->MetodePembayaran ?: '-',
                    $transaction->OrderID ?: '-',
                    $transaction->Status ?: '-',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}