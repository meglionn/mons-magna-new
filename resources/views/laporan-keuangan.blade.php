@extends('layouts.app')

@section('content')
@php
// Smart number formatting function
function formatIDR($amount) {
    if ($amount >= 1000000000) {
        return 'IDR ' . number_format($amount / 1000000000, 1) . 'B';
    } elseif ($amount >= 1000000) {
        return 'IDR ' . number_format($amount / 1000000, 1) . 'M';
    } elseif ($amount >= 1000) {
        return 'IDR ' . number_format($amount / 1000, 1) . 'K';
    } else {
        return 'IDR ' . number_format($amount, 0);
    }
}
@endphp
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">Laporan Keuangan</h2>
            <p class="text-gray-600">
                Lihat dan kelola laporan keuangan bisnis Anda
            </p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <!-- Calendar Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10m-6 4h6m-2 4h2M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z"/>
                </svg>
                <select 
                    class="border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    onchange="window.location.href='{{ route('laporan') }}?filter=' + this.value"
                >
                    <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Semua Data</option>
                    <option value="week" {{ $filter == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="quarter" {{ $filter == 'quarter' ? 'selected' : '' }}>Kuartal Ini</option>
                    <option value="year" {{ $filter == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Financial Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-gray-600 text-sm mb-1">Total Pemasukan</p>
            <p class="text-2xl font-bold text-green-600">{{ formatIDR($financialData['totalRevenue']) }}</p>
            <p class="text-gray-500 text-xs mt-2">Semua pemasukan</p>
        </div>

        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-gray-600 text-sm mb-1">Total Pengeluaran</p>
            <p class="text-2xl font-bold text-red-600">{{ formatIDR($financialData['totalExpenses']) }}</p>
            <p class="text-gray-500 text-xs mt-2">Semua pengeluaran</p>
        </div>

        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-gray-600 text-sm mb-1">Keuntungan Bersih</p>
            <p class="text-2xl font-bold {{ $financialData['netProfit'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                {{ formatIDR($financialData['netProfit']) }}
            </p>
            <p class="text-gray-500 text-xs mt-2">Pendapatan - Pengeluaran</p>
        </div>

        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-gray-600 text-sm mb-1">Margin Keuntungan</p>
            <p class="text-2xl font-bold text-purple-600">{{ $financialData['profitMargin'] }}%</p>
            <p class="text-gray-500 text-xs mt-2">Persentase keuntungan</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Income by Category -->
        <div class="bg-white rounded-lg p-6 shadow">
            <h3 class="text-lg font-semibold mb-4">Pemasukan Berdasarkan Kategori</h3>
            <div class="space-y-3">
                @forelse($incomeByCategory as $item)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium">{{ $item->Kategori ?: 'Lainnya' }}</span>
                            <span class="text-sm text-green-600 font-bold">
                                IDR {{ number_format($item->total, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div 
                                class="bg-green-500 h-2 rounded-full" 
                                style="width: {{ ($item->total / $incomeByCategory->sum('total') * 100) ?? 0 }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Belum ada data pemasukan</p>
                @endforelse
            </div>
        </div>

        <!-- Expenses by Category -->
        <div class="bg-white rounded-lg p-6 shadow">
            <h3 class="text-lg font-semibold mb-4">Pengeluaran Berdasarkan Kategori</h3>
            <div class="space-y-3">
                @forelse($expensesByCategory as $item)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium">{{ $item->Kategori ?: 'Lainnya' }}</span>
                            <span class="text-sm text-red-600 font-bold">
                                IDR {{ number_format($item->total, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div 
                                class="bg-red-500 h-2 rounded-full" 
                                style="width: {{ ($item->total / $expensesByCategory->sum('total') * 100) ?? 0 }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Belum ada data pengeluaran</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Monthly Comparison Chart -->
    <div class="bg-white rounded-lg p-6 shadow">
        <h3 class="text-lg font-semibold mb-4">Perbandingan Bulanan (6 Bulan Terakhir)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="px-4 py-2 text-left">Bulan</th>
                        <th class="px-4 py-2 text-right">Pemasukan</th>
                        <th class="px-4 py-2 text-right">Pengeluaran</th>
                        <th class="px-4 py-2 text-right">Keuntungan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlyData as $month)
                        @php
                            $profit = $month->income - $month->expense;
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->format('M Y') }}</td>
                            <td class="px-4 py-2 text-right text-green-600 font-semibold">
                                IDR {{ number_format($month->income, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-right text-red-600 font-semibold">
                                IDR {{ number_format($month->expense, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-right {{ $profit >= 0 ? 'text-blue-600' : 'text-red-600' }} font-bold">
                                IDR {{ number_format($profit, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                Belum ada data bulanan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Section -->
    <div class="bg-white rounded-lg p-6 shadow">
        <h3 class="text-lg font-semibold mb-4">Ekspor Laporan</h3>
        <div class="flex gap-3">
            <a href="{{ route('laporan.export.pdf', 'financial') }}?filter={{ $filter }}" 
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 inline-flex items-center gap-2">
                📄 Ekspor PDF
            </a>
            <a href="{{ route('laporan.export.excel', 'financial') }}?filter={{ $filter }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 inline-flex items-center gap-2">
                📊 Ekspor Excel
            </a>
        </div>
    </div>

</div>
@endsection
