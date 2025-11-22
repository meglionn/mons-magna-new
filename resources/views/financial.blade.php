@extends('layouts.app')

@section('content')
<div 
  x-data="{ tab: 'transactions', showIncomeDialog: false, showExpenseDialog: false, showSuccess: {{ session('success') ? 'true' : 'false' }} }"
  class="max-w-screen-2xl mx-auto px-8"
>

  {{-- SUCCESS MESSAGE --}}
  <div x-show="showSuccess" x-init="setTimeout(() => showSuccess = false, 3000)" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" style="display: none;">
    <span class="block sm:inline">@if(session('success')){{ session('success') }}@endif</span>
    <button @click="showSuccess = false" class="absolute top-0 right-0 px-4 py-3">
      <span class="text-2xl">&times;</span>
    </button>
  </div>

  {{-- PAGE TITLE --}}
  <div>
    <h2 class="text-2xl font-semibold">Manajemen Keuangan</h2>
    <p class="text-gray-600">Lacak pendapatan, pengeluaran, dan profitabilitas</p>
  </div>

  {{-- TOP ACTION BUTTONS --}}
  <div class="flex justify-between items-center">
    <div></div>

    <div class="flex gap-3">
      <a href="{{ route('financial.export') }}" class="border rounded-lg px-4 py-2 flex items-center hover:bg-gray-100">
         Export Laporan
      </a>

      <button 
        class="border rounded-lg px-4 py-2 flex items-center hover:bg-gray-100"
        @click="showExpenseDialog = true"
      >
        + Tambah Pengeluaran
      </button>

      <button 
        class="bg-blue-600 text-white rounded-lg px-4 py-2 flex items-center hover:bg-gray-800"
        @click="showIncomeDialog = true"
      >
        + Tambah Pendapatan
      </button>
    </div>
  </div>

  {{-- INCLUDE DIALOGS --}}
  @include('partials.financialdialog-income')
  @include('partials.financialdialog-expense')

  {{-- SUMMARY CARDS ROW --}}
  <div class="grid grid-cols-4 gap-6">

    {{-- Total Pendapatan --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="font-medium text-gray-700">Total Pendapatan</h3>
      <p class="text-green-600 text-2xl font-semibold mt-2">IDR {{ number_format($stats['totalIncome'], 0, ',', '.') }}</p>
      <p class="text-gray-500 text-sm mt-1">Total pendapatan</p>
    </div>

    {{-- Total Pengeluaran --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="font-medium text-gray-700">Total Pengeluaran</h3>
      <p class="text-red-600 text-2xl font-semibold mt-2">IDR {{ number_format($stats['totalExpenses'], 0, ',', '.') }}</p>
      <p class="text-gray-500 text-sm mt-1">Total pengeluaran</p>
    </div>

    {{-- Laba Bersih --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="font-medium text-gray-700">Laba Bersih</h3>
      <p class="{{ $stats['netProfit'] >= 0 ? 'text-green-600' : 'text-red-600' }} text-2xl font-semibold mt-2">IDR {{ number_format($stats['netProfit'], 0, ',', '.') }}</p>
      <p class="text-gray-500 text-sm mt-1">Pendapatan - Pengeluaran</p>
    </div>

    {{-- Margin Laba --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="font-medium text-gray-700">Margin Laba</h3>
      <p class="text-blue-600 text-2xl font-semibold mt-2">{{ number_format($stats['profitMargin'], 1) }}%</p>
      <p class="text-gray-500 text-sm mt-1">Rasio profitabilitas</p>
    </div>
  </div>

  {{-- TAB BUTTONS --}}
  <div class="flex border-b gap-6">
    <button 
      @click="tab = 'transactions'"
      :class="tab === 'transactions' ? 'font-semibold border-b-2 border-black' : 'text-gray-500'"
      class="pb-2"
    >Transaksi</button>

    <button 
      @click="tab = 'income'"
      :class="tab === 'income' ? 'font-semibold border-b-2 border-black' : 'text-gray-500'"
      class="pb-2"
    >Analisis Pendapatan</button>

    <button 
      @click="tab = 'expense'"
      :class="tab === 'expense' ? 'font-semibold border-b-2 border-black' : 'text-gray-500'"
      class="pb-2"
    >Analisis Pengeluaran</button>

    <button 
      @click="tab = 'reports'"
      :class="tab === 'reports' ? 'font-semibold border-b-2 border-black' : 'text-gray-500'"
      class="pb-2"
    >Laporan</button>
  </div>

  {{-- TAB: TRANSAKSI --}}
  <div x-show="tab === 'transactions'" class="space-y-6">

    {{-- FILTER ROW --}}
    <div class="flex items-center gap-4">
      <div class="border rounded-lg px-4 py-2 bg-white flex items-center gap-2">
        <span>🔍</span>
        <select class="bg-transparent outline-none">
          <option>Semua Tipe</option>
        </select>
      </div>

      <div class="border rounded-lg px-4 py-2 bg-white flex items-center gap-2">
        <span>📂</span>
        <select class="bg-transparent outline-none">
          <option>Semua Kategori</option>
        </select>
      </div>
    </div>

    {{-- TRANSACTION TABLE --}}
    <div class="bg-white border rounded-lg overflow-hidden">
      <table class="w-full text-left">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-3">Tanggal</th>
            <th class="p-3">Tipe</th>
            <th class="p-3">Kategori</th>
            <th class="p-3">Deskripsi</th>
            <th class="p-3">Jumlah (IDR)</th>
            <th class="p-3">Metode Pembayaran</th>
            <th class="p-3">Referensi</th>
            <th class="p-3">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $transaction)
          <tr class="border-t hover:bg-gray-50">
            <td class="p-3">{{ $transaction->Tanggal->format('d/m/Y') }}</td>
            <td class="p-3">
              <span class="px-2 py-1 rounded-full text-xs {{ $transaction->JenisTransaksi == 'Pemasukan' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $transaction->JenisTransaksi }}
              </span>
            </td>
            <td class="p-3">{{ $transaction->Kategori ?: '—' }}</td>
            <td class="p-3">{{ $transaction->Keterangan ?: '—' }}</td>
            <td class="p-3 font-semibold {{ $transaction->JenisTransaksi == 'Pemasukan' ? 'text-green-600' : 'text-red-600' }}">
              {{ $transaction->JenisTransaksi == 'Pemasukan' ? '+' : '-' }} {{ number_format($transaction->Jumlah, 0, ',', '.') }}
            </td>
            <td class="p-3">{{ $transaction->MetodePembayaran ?: '—' }}</td>
            <td class="p-3">{{ $transaction->OrderID ?: '—' }}</td>
            <td class="p-3">
              <form action="{{ route('financial.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                  Hapus
                </button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="p-3 text-center text-gray-500">Tidak ada transaksi</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

  {{-- TAB: ANALISIS PENDAPATAN --}}
  <div x-show="tab === 'income'" class="space-y-6">
    
    {{-- Pendapatan by Category --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="text-lg font-semibold mb-4">Pendapatan per Kategori</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-3">Kategori</th>
              <th class="p-3">Jumlah Transaksi</th>
              <th class="p-3">Total (IDR)</th>
              <th class="p-3">Persentase</th>
            </tr>
          </thead>
          <tbody>
            @forelse($incomeByCategory as $item)
            <tr class="border-t">
              <td class="p-3 font-medium">{{ $item->Kategori ?: 'Tidak ada kategori' }}</td>
              <td class="p-3">{{ $item->count }}</td>
              <td class="p-3 text-green-600 font-semibold">{{ number_format($item->total, 0, ',', '.') }}</td>
              <td class="p-3">{{ $stats['totalIncome'] > 0 ? number_format(($item->total / $stats['totalIncome']) * 100, 1) : 0 }}%</td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="p-3 text-center text-gray-500">Tidak ada data pendapatan</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Pendapatan by Payment Method --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="text-lg font-semibold mb-4">Pendapatan per Metode Pembayaran</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-3">Metode Pembayaran</th>
              <th class="p-3">Total (IDR)</th>
              <th class="p-3">Persentase</th>
            </tr>
          </thead>
          <tbody>
            @forelse($incomeByPayment as $item)
            <tr class="border-t">
              <td class="p-3 font-medium">{{ $item->MetodePembayaran ?: 'Tidak ditentukan' }}</td>
              <td class="p-3 text-green-600 font-semibold">{{ number_format($item->total, 0, ',', '.') }}</td>
              <td class="p-3">{{ $stats['totalIncome'] > 0 ? number_format(($item->total / $stats['totalIncome']) * 100, 1) : 0 }}%</td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="p-3 text-center text-gray-500">Tidak ada data</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- TAB: ANALISIS PENGELUARAN --}}
  <div x-show="tab === 'expense'" class="space-y-6">
    
    {{-- Pengeluaran by Category --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="text-lg font-semibold mb-4">Pengeluaran per Kategori</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-3">Kategori</th>
              <th class="p-3">Jumlah Transaksi</th>
              <th class="p-3">Total (IDR)</th>
              <th class="p-3">Persentase</th>
            </tr>
          </thead>
          <tbody>
            @forelse($expensesByCategory as $item)
            <tr class="border-t">
              <td class="p-3 font-medium">{{ $item->Kategori ?: 'Tidak ada kategori' }}</td>
              <td class="p-3">{{ $item->count }}</td>
              <td class="p-3 text-red-600 font-semibold">{{ number_format($item->total, 0, ',', '.') }}</td>
              <td class="p-3">{{ $stats['totalExpenses'] > 0 ? number_format(($item->total / $stats['totalExpenses']) * 100, 1) : 0 }}%</td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="p-3 text-center text-gray-500">Tidak ada data pengeluaran</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Pengeluaran by Payment Method --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="text-lg font-semibold mb-4">Pengeluaran per Metode Pembayaran</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-3">Metode Pembayaran</th>
              <th class="p-3">Total (IDR)</th>
              <th class="p-3">Persentase</th>
            </tr>
          </thead>
          <tbody>
            @forelse($expensesByPayment as $item)
            <tr class="border-t">
              <td class="p-3 font-medium">{{ $item->MetodePembayaran ?: 'Tidak ditentukan' }}</td>
              <td class="p-3 text-red-600 font-semibold">{{ number_format($item->total, 0, ',', '.') }}</td>
              <td class="p-3">{{ $stats['totalExpenses'] > 0 ? number_format(($item->total / $stats['totalExpenses']) * 100, 1) : 0 }}%</td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="p-3 text-center text-gray-500">Tidak ada data</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- TAB: LAPORAN --}}
  <div x-show="tab === 'reports'" class="space-y-6">
    
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-6">
        <h4 class="text-sm font-medium text-green-700 mb-2">Total Pendapatan</h4>
        <p class="text-3xl font-bold text-green-900">{{ number_format($stats['totalIncome'], 0, ',', '.') }}</p>
        <p class="text-xs text-green-600 mt-2">{{ $transactions->where('JenisTransaksi', 'Pemasukan')->count() }} transaksi</p>
      </div>
      
      <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-6">
        <h4 class="text-sm font-medium text-red-700 mb-2">Total Pengeluaran</h4>
        <p class="text-3xl font-bold text-red-900">{{ number_format($stats['totalExpenses'], 0, ',', '.') }}</p>
        <p class="text-xs text-red-600 mt-2">{{ $transactions->where('JenisTransaksi', 'Pengeluaran')->count() }} transaksi</p>
      </div>
      
      <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6">
        <h4 class="text-sm font-medium text-blue-700 mb-2">Keuntungan Bersih</h4>
        <p class="text-3xl font-bold text-blue-900">{{ number_format($stats['netProfit'], 0, ',', '.') }}</p>
        <p class="text-xs text-blue-600 mt-2">Margin: {{ $stats['profitMargin'] }}%</p>
      </div>
    </div>

    {{-- Top Categories --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white border rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4 text-green-700">Top Kategori Pendapatan</h3>
        <div class="space-y-3">
          @forelse($incomeByCategory->sortByDesc('total')->take(5) as $item)
          <div class="flex justify-between items-center">
            <span class="font-medium">{{ $item->Kategori ?: 'Tidak ada kategori' }}</span>
            <div class="text-right">
              <div class="text-green-600 font-semibold">{{ number_format($item->total, 0, ',', '.') }}</div>
              <div class="text-xs text-gray-500">{{ $item->count }} transaksi</div>
            </div>
          </div>
          @empty
          <p class="text-gray-500 text-center py-4">Tidak ada data</p>
          @endforelse
        </div>
      </div>

      <div class="bg-white border rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4 text-red-700">Top Kategori Pengeluaran</h3>
        <div class="space-y-3">
          @forelse($expensesByCategory->sortByDesc('total')->take(5) as $item)
          <div class="flex justify-between items-center">
            <span class="font-medium">{{ $item->Kategori ?: 'Tidak ada kategori' }}</span>
            <div class="text-right">
              <div class="text-red-600 font-semibold">{{ number_format($item->total, 0, ',', '.') }}</div>
              <div class="text-xs text-gray-500">{{ $item->count }} transaksi</div>
            </div>
          </div>
          @empty
          <p class="text-gray-500 text-center py-4">Tidak ada data</p>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Payment Methods Summary --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="text-lg font-semibold mb-4">Ringkasan Metode Pembayaran</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h4 class="font-medium text-green-700 mb-3">Pendapatan</h4>
          <div class="space-y-2">
            @forelse($incomeByPayment as $item)
            <div class="flex justify-between">
              <span>{{ $item->MetodePembayaran }}</span>
              <span class="font-semibold text-green-600">{{ number_format($item->total, 0, ',', '.') }}</span>
            </div>
            @empty
            <p class="text-gray-500 text-sm">Tidak ada data</p>
            @endforelse
          </div>
        </div>
        <div>
          <h4 class="font-medium text-red-700 mb-3">Pengeluaran</h4>
          <div class="space-y-2">
            @forelse($expensesByPayment as $item)
            <div class="flex justify-between">
              <span>{{ $item->MetodePembayaran }}</span>
              <span class="font-semibold text-red-600">{{ number_format($item->total, 0, ',', '.') }}</span>
            </div>
            @empty
            <p class="text-gray-500 text-sm">Tidak ada data</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection