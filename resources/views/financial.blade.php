@extends('layouts.app')

@section('content')
<div 
  x-data="{ tab: 'transactions', showIncomeDialog: false, showExpenseDialog: false }"
  class="max-w-screen-2xl mx-auto px-8"
>

  {{-- PAGE TITLE --}}
  <div>
    <h2 class="text-2xl font-semibold">Manajemen Keuangan</h2>
    <p class="text-gray-600">Lacak pendapatan, pengeluaran, dan profitabilitas</p>
  </div>

  {{-- TOP ACTION BUTTONS --}}
  <div class="flex justify-between items-center">
    <div></div>

    <div class="flex gap-3">
      <button class="border rounded-lg px-4 py-2 flex items-center hover:bg-gray-100">
        Export Laporan
      </button>

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
      <p class="text-green-600 text-2xl font-semibold mt-2">IDR 0.0M</p>
      <p class="text-gray-500 text-sm mt-1">Pendapatan bulan ini</p>
    </div>

    {{-- Total Pengeluaran --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="font-medium text-gray-700">Total Pengeluaran</h3>
      <p class="text-red-600 text-2xl font-semibold mt-2">IDR 0.0M</p>
      <p class="text-gray-500 text-sm mt-1">Pengeluaran bulan ini</p>
    </div>

    {{-- Laba Bersih --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="font-medium text-gray-700">Laba Bersih</h3>
      <p class="text-green-600 text-2xl font-semibold mt-2">IDR 0.0M</p>
      <p class="text-gray-500 text-sm mt-1">Pendapatan - Pengeluaran</p>
    </div>

    {{-- Margin Laba --}}
    <div class="bg-white border rounded-xl p-6">
      <h3 class="font-medium text-gray-700">Margin Laba</h3>
      <p class="text-blue-600 text-2xl font-semibold mt-2">0.0%</p>
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
      @click="tab = 'expenses'"
      :class="tab === 'expenses' ? 'font-semibold border-b-2 border-black' : 'text-gray-500'"
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
          <tr>
            <td class="p-3">—</td>
            <td class="p-3">—</td>
            <td class="p-3">—</td>
            <td class="p-3">—</td>
            <td class="p-3">—</td>
            <td class="p-3">—</td>
            <td class="p-3">—</td>
            <td class="p-3">—</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

  {{-- TAB: ANALISIS PENDAPATAN --}}
  <div x-show="tab === 'income'" class="bg-white border rounded-xl p-6">
    <h3 class="text-lg font-semibold mb-4">Analisis Pendapatan</h3>
    <p class="text-gray-500">Grafik & perincian pendapatan akan ditampilkan di sini.</p>
  </div>

  {{-- TAB: ANALISIS PENGELUARAN --}}
  <div x-show="tab === 'expenses'" class="bg-white border rounded-xl p-6">
    <h3 class="text-lg font-semibold mb-4">Analisis Pengeluaran</h3>
    <p class="text-gray-500">Grafik & perincian pengeluaran akan ditampilkan di sini.</p>
  </div>

  {{-- TAB: LAPORAN --}}
  <div x-show="tab === 'reports'" class="bg-white border rounded-xl p-6">
    <h3 class="text-lg font-semibold mb-4">Laporan</h3>
    <p class="text-gray-500">Ringkasan laporan keuangan ditampilkan di sini.</p>
  </div>

</div>
@endsection