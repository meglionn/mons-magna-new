@extends('layouts.app')

@section('content')
<div 
  x-data="{
    activeTab: 'transactions',
    showTransactionDialog: false,
    transactionType: 'income',
    editMode: false
  }"
  class="space-y-6"
>
  <div x-data="{ showIncomeDialog: false, showExpenseDialog: false }" class="space-y-6">
  
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-2xl font-semibold">Manajemen Keuangan</h2>
      <p class="text-gray-600">Lacak pendapatan, pengeluaran, dan laporan finansial</p>
    </div>
    <div class="flex gap-2">
      <button 
        class="border rounded-lg px-4 py-2 flex items-center hover:bg-gray-100"
        @click="showExpenseDialog = true"
      >
        + Tambah Pengeluaran
      </button>

      <button 
        class="bg-blue-600 text-white rounded-lg px-4 py-2 flex items-center hover:bg-blue-700"
        @click="showIncomeDialog = true"
      >
        + Tambah Pendapatan
      </button>
    </div>
  </div>

  {{-- Include Dialogs --}}
  @include('partials.financialdialog-income')
  @include('partials.financialdialog-expense')

</div>

  {{-- newtab --}}
  <div class="space-y-6 p-6" x-data="{ tab: 'transactions' }">
  <div class="flex border-b">
    <button @click="tab = 'transactions'"
      :class="tab === 'transactions' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
      class="px-4 py-2 font-medium transition">
      Transaksi
    </button>

    <button @click="tab = 'income'"
      :class="tab === 'income' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
      class="px-4 py-2 font-medium transition">
      Analisis Pendapatan
    </button>

    <button @click="tab = 'expenses'"
      :class="tab === 'expenses' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
      class="px-4 py-2 font-medium transition">
      Analisis Pengeluaran
    </button>

    <button @click="tab = 'reports'"
      :class="tab === 'reports' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
      class="px-4 py-2 font-medium transition">
      Laporan
    </button>

  {{-- TAB: Transaksi --}}
  <div x-show="tab === 'transactions'" class="space-y-4">
    <div class="bg-white border rounded-lg p-6">
      <h3 class="text-lg font-semibold mb-4">Daftar Transaksi</h3>
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-100 text-left text-sm">
            <th class="p-2 border-b">Tanggal</th>
            <th class="p-2 border-b">Tipe</th>
            <th class="p-2 border-b">Kategori</th>
            <th class="p-2 border-b">Deskripsi</th>
            <th class="p-2 border-b text-right">Jumlah</th>
            <th class="p-2 border-b">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr class="hover:bg-gray-50">
            <td class="p-2 border-b">2025-11-09</td>
            <td class="p-2 border-b text-green-600">Pendapatan</td>
            <td class="p-2 border-b">Penjualan Produk</td>
            <td class="p-2 border-b">Penjualan sepatu kulit</td>
            <td class="p-2 border-b text-right text-green-600">+1,200,000</td>
            <td class="p-2 border-b">
              <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Selesai</span>
            </td>
          </tr>
          <tr class="hover:bg-gray-50">
            <td class="p-2 border-b">2025-11-08</td>
            <td class="p-2 border-b text-red-600">Pengeluaran</td>
            <td class="p-2 border-b">Bahan Baku</td>
            <td class="p-2 border-b">Pembelian kulit sapi premium</td>
            <td class="p-2 border-b text-right text-red-600">-850,000</td>
            <td class="p-2 border-b">
              <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">Tertunda</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  {{-- TAB: Analisis Pendapatan --}}
  <div x-show="tab === 'income'" class="space-y-4">
    <div class="bg-white border rounded-lg p-6">
      <h3 class="text-lg font-semibold mb-2">Analisis Pendapatan</h3>
      <p class="text-gray-500 text-sm mb-4">Rincian sumber pendapatan bulan ini</p>

      <div class="space-y-4">
        <div>
          <div class="flex justify-between text-sm font-medium">
            <span>Penjualan Produk</span><span>IDR 3.2M</span>
          </div>
          <div class="w-full h-2 bg-gray-200 rounded-full mt-1">
            <div class="h-2 bg-green-500 rounded-full" style="width: 70%"></div>
          </div>
        </div>
        <div>
          <div class="flex justify-between text-sm font-medium">
            <span>Pesanan Custom</span><span>IDR 1.0M</span>
          </div>
          <div class="w-full h-2 bg-gray-200 rounded-full mt-1">
            <div class="h-2 bg-blue-500 rounded-full" style="width: 25%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- TAB: Analisis Pengeluaran --}}
  <div x-show="tab === 'expenses'" class="space-y-4">
    <div class="bg-white border rounded-lg p-6">
      <h3 class="text-lg font-semibold mb-2">Analisis Pengeluaran</h3>
      <p class="text-gray-500 text-sm mb-4">Rincian biaya berdasarkan kategori</p>

      <div class="space-y-4">
        <div>
          <div class="flex justify-between text-sm font-medium">
            <span>Bahan Baku</span><span>IDR 2.4M</span>
          </div>
          <div class="w-full h-2 bg-gray-200 rounded-full mt-1">
            <div class="h-2 bg-red-500 rounded-full" style="width: 60%"></div>
          </div>
        </div>
        <div>
          <div class="flex justify-between text-sm font-medium">
            <span>Tenaga Kerja</span><span>IDR 1.2M</span>
          </div>
          <div class="w-full h-2 bg-gray-200 rounded-full mt-1">
            <div class="h-2 bg-orange-500 rounded-full" style="width: 30%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- TAB: Laporan --}}
  <div x-show="tab === 'reports'" class="space-y-4">
    <div class="bg-white border rounded-lg p-6">
      <h3 class="text-lg font-semibold mb-2">Ringkasan Keuangan</h3>
      <p class="text-gray-500 text-sm mb-6">Gambaran umum performa bulan ini</p>

      <div class="grid grid-cols-2 gap-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
          <p class="text-sm text-gray-600">Total Pendapatan</p>
          <p class="text-2xl text-green-600 font-semibold">IDR 4.2M</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
          <p class="text-sm text-gray-600">Total Pengeluaran</p>
          <p class="text-2xl text-red-600 font-semibold">IDR 3.6M</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
          <p class="text-sm text-gray-600">Laba Bersih</p>
          <p class="text-2xl text-blue-600 font-semibold">IDR 0.6M</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
          <p class="text-sm text-gray-600">Margin Laba</p>
          <p class="text-2xl text-yellow-600 font-semibold">14.3%</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection