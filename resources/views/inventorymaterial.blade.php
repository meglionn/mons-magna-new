@extends('layouts.app')

@section('content')
<div x-data="{ showMaterialDialog: false, editMode: false }">

  {{-- ALERT: stok rendah --}}
  <div class="hidden" id="lowStockAlert">
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.662 1.732-3L13.732 4c-.77-1.338-2.694-1.338-3.464 0L3.34 16c-.77 1.338.192 3 1.732 3z"/>
      </svg>
      <p><strong>Peringatan:</strong> Ada bahan dengan stok rendah yang perlu di-restock.</p>
    </div>
  </div>

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-2xl font-semibold">Inventori Bahan</h2>
      <p class="text-gray-600 text-sm">Kelola bahan baku untuk produksi sepatu</p>
    </div>

    <button @click="editMode = false; showMaterialDialog = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
      + Tambah Bahan
    </button>
    
  {{-- Modal Tambah Bahan --}}
  @include('partials.inventorydialog')
  </div>

  {{-- Statistik --}}
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white border rounded-lg p-4 shadow-sm">
      <div class="flex justify-between items-center mb-2">
        <p class="text-sm text-gray-600">Total Bahan</p>
        <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2h-2.586a1 1 0 01-.707-.293l-1.414-1.414A1 1 0 0012.586 3H10a2 2 0 00-2 2v16h10a2 2 0 002-2v-6z" />
        </svg>
      </div>
      <p class="text-2xl font-semibold">24</p>
      <p class="text-xs text-gray-500 mt-1">Jenis bahan</p>
    </div>

    <div class="bg-white border rounded-lg p-4 shadow-sm">
      <div class="flex justify-between items-center mb-2">
        <p class="text-sm text-gray-600">Total Inventori</p>
        <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M9 3v18m6-18v18M3 9h18M3 15h18" />
        </svg>
      </div>
      <p class="text-2xl font-semibold">4,890</p>
      <p class="text-xs text-gray-500 mt-1">Total unit</p>
    </div>

    <div class="bg-white border rounded-lg p-4 shadow-sm">
      <div class="flex justify-between items-center mb-2">
        <p class="text-sm text-gray-600">Nilai Inventori</p>
        <svg class="w-5 h-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.333-1-4-1-4 2s2.667 3 4 4 4 1 4-2m0 0v1m0-1V7m-4 5v1m0-1V7" />
        </svg>
      </div>
      <p class="text-xl font-semibold">IDR 3.2M</p>
      <p class="text-xs text-gray-500 mt-1">Total nilai</p>
    </div>

    <div class="bg-white border rounded-lg p-4 shadow-sm">
      <div class="flex justify-between items-center mb-2">
        <p class="text-sm text-gray-600">Peringatan Stok Rendah</p>
        <svg class="w-5 h-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4m0 4h.01" />
        </svg>
      </div>
      <p class="text-2xl font-semibold">2</p>
      <p class="text-xs text-gray-500 mt-1">Perlu di-restock</p>
    </div>
  </div>

  {{-- Filter & Pencarian --}}
  <div class="flex flex-col md:flex-row gap-4">
    <div class="relative flex-1">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
      </svg>
      <input type="text" class="w-full border rounded-lg pl-10 p-2" placeholder="Cari bahan...">
    </div>
    <div class="flex gap-2 overflow-x-auto">
      <button class="border rounded-lg px-3 py-1.5 text-sm bg-blue-600 text-white">Semua Kategori</button>
    </div>
  </div>

  {{-- Tabel Bahan --}}
  <div class="bg-white border rounded-lg shadow-sm overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Kode</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Nama Bahan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Kategori</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Stok</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Satuan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Harga Satuan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Supplier</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr class="border-t">
          <td class="px-4 py-2">MAT-001</td>
          <td class="px-4 py-2">Kulit Sapi Premium</td>
          <td class="px-4 py-2">Kulit</td>
          <td class="px-4 py-2">15</td>
          <td class="px-4 py-2">lembar</td>
          <td class="px-4 py-2">500.000</td>
          <td class="px-4 py-2">LeatherCraft ID</td>
          <td class="px-4 py-2"><span class="text-green-700 bg-green-100 px-2 py-1 rounded text-xs">Tersedia</span></td>
          <td class="px-4 py-2 text-right">
            <button class="text-gray-500 hover:text-blue-600 px-2">✏️</button>
            <button class="text-gray-500 hover:text-red-600 px-2">🗑️</button>
          </td>
        </tr>

        <tr class="border-t">
          <td class="px-4 py-2">MAT-002</td>
          <td class="px-4 py-2">Benang Nilon</td>
          <td class="px-4 py-2">Benang</td>
          <td class="px-4 py-2">4</td>
          <td class="px-4 py-2">gulung</td>
          <td class="px-4 py-2">30.000</td>
          <td class="px-4 py-2">SewPro</td>
          <td class="px-4 py-2"><span class="text-red-700 bg-red-100 px-2 py-1 rounded text-xs">Stok Rendah</span></td>
          <td class="px-4 py-2 text-right">
            <button class="text-gray-500 hover:text-blue-600 px-2">✏️</button>
            <button class="text-gray-500 hover:text-red-600 px-2">🗑️</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</div>
@endsection
