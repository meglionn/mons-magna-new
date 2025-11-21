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
        
        return view('financial', compact('transactions', 'stats'));
    }

    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'OrderID' => 'nullable|exists:orders,OrderID',
            'Jumlah' => 'required|numeric|min:0',
            'Tanggal' => 'required|date',
            'Keterangan' => 'nullable|string',
        ]);

        $validated['JenisTransaksi'] = 'Pemasukan';

        Transaction::create($validated);

        return redirect()->route('financial.index')
            ->with('success', 'Pendapatan berhasil ditambahkan');
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'OrderID' => 'nullable|exists:orders,OrderID',
            'Jumlah' => 'required|numeric|min:0',
            'Tanggal' => 'required|date',
            'Keterangan' => 'nullable|string',
        ]);

        $validated['JenisTransaksi'] = 'Pengeluaran';

        Transaction::create($validated);

        return redirect()->route('financial.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('financial.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }
}