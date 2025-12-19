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
            <h2 class="text-2xl font-semibold">Laporan & Analitik</h2>
            <p class="text-gray-600">
                Lihat dan unduh laporan detail bisnis Anda
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

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg p-4 shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-600">Total Penjualan</h3>
                <!-- Shopping Cart -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13M7 13H3"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold mt-2">{{ formatIDR($salesData['totalSales']) }}</p>
            <div class="flex items-center gap-1 text-xs text-gray-600 mt-1">
                <span>{{ $salesData['totalOrders'] }} pesanan</span>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-600">Nilai Material</h3>
                <!-- Package Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 12V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v4m18 0l-9 5-9-5m18 0v4a2 2 0 01-1 1.73l-7 4a2 2 0 01-2 0l-7-4A2 2 0 012 16v-4"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold mt-2">{{ formatIDR($inventoryData['totalValue']) }}</p>
            <p class="text-xs text-gray-600 mt-1">{{ $inventoryData['totalMaterials'] }} unit dalam stok</p>
        </div>

        <div class="bg-white rounded-lg p-4 shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-600">Stok Rendah</h3>
                <!-- Warning Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.29 3.86L1.82 18a1 1 0 00.86 1.5h18.64a1 1 0 00.86-1.5L13.71 3.86a1 1 0 00-1.72 0zM12 9v4m0 4h.01"/>
                </svg>
            </div>
            <p class="text-2xl text-yellow-600 font-semibold mt-2">{{ $inventoryData['lowStockItems'] }}</p>
            <p class="text-xs text-gray-600 mt-1">Item perlu di-reorder</p>
        </div>

        <div class="bg-white rounded-lg p-4 shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-600">Pelanggan Baru</h3>
                <!-- Users Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m0 0A4 4 0 0112 7a4 4 0 013 7.13M12 7a4 4 0 00-3 7.13M12 7V4m0 0a4 4 0 013 7.13M12 4a4 4 0 00-3 7.13"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold mt-2">{{ $salesData['newCustomers'] }}</p>
            <p class="text-xs text-gray-600 mt-1">{{ $salesData['repeatCustomers'] }} pesanan berulang</p>
        </div>
    </div>

    <!-- Tabs -->
<div class="space-y-6 p-6" x-data="{ tab: 'inventory' }">
  <div class="flex border-b">
    <button @click="tab = 'inventory'"
      :class="tab === 'inventory' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
      class="px-4 py-2 font-medium transition">
      📦 Laporan Inventori
    </button>
    <button @click="tab = 'sales'"
      :class="tab === 'sales' ? 'border-b-2 border-green-600 text-green-600' : 'text-gray-600'"
      class="px-4 py-2 font-medium transition">
      💰 Laporan Penjualan
    </button>
    <button @click="tab = 'financial'"
      :class="tab === 'financial' ? 'border-b-2 border-purple-600 text-purple-600' : 'text-gray-600'"
      class="px-4 py-2 font-medium transition">
      📊 Laporan Keuangan
    </button>
  </div>

  {{-- INVENTORY REPORT --}}
  <div x-show="tab === 'inventory'" class="space-y-6">
    <div class="border rounded-xl bg-white shadow-sm">
      <div class="flex justify-between items-start border-b p-4">
        <div>
          <h3 class="text-lg font-semibold">Laporan Inventori Material</h3>
          <p class="text-gray-500 text-sm">Level stok bahan baku, penggunaan, dan peringatan stok rendah</p>
        </div>
        <div class="flex gap-2">
          <a href="{{ route('laporan.export.pdf', ['type' => 'inventory', 'filter' => $filter]) }}" class="border rounded-lg px-3 py-2 text-sm hover:bg-gray-100">📄 Export PDF</a>
          <a href="{{ route('laporan.export.excel', ['type' => 'inventory', 'filter' => $filter]) }}" class="border rounded-lg px-3 py-2 text-sm hover:bg-gray-100">⬇️ Export Excel</a>
        </div>
      </div>

      <div class="p-6 space-y-6">
        {{-- Inventory Summary --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div><p class="text-sm text-gray-600">Total Material</p><p class="text-2xl font-semibold">{{ $inventoryData['totalMaterials'] }}</p></div>
          <div><p class="text-sm text-gray-600">Total Nilai</p><p class="text-2xl font-semibold">{{ formatIDR($inventoryData['totalValue']) }}</p></div>
          <div><p class="text-sm text-gray-600">Stok Rendah</p><p class="text-2xl text-yellow-600 font-semibold">{{ $inventoryData['lowStockItems'] }}</p></div>
          <div><p class="text-sm text-gray-600">Habis Stok</p><p class="text-2xl text-red-600 font-semibold">{{ $inventoryData['outOfStock'] }}</p></div>
        </div>

        {{-- Stock Table --}}
        <div>
          <h3 class="font-semibold mb-3">Stok Material</h3>
          <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left">Material</th>
                  <th class="px-4 py-2 text-left">Kategori</th>
                  <th class="px-4 py-2 text-right">Stok</th>
                  <th class="px-4 py-2 text-right">Nilai (IDR)</th>
                  <th class="px-4 py-2 text-left">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                @forelse($materials as $material)
                <tr>
                  <td class="px-4 py-2">{{ $material->NamaBahan }}</td>
                  <td class="px-4 py-2">{{ $material->Kategori }}</td>
                  <td class="px-4 py-2 text-right">{{ $material->StokBahan }} {{ $material->JenisBahan }}</td>
                  <td class="px-4 py-2 text-right">{{ formatIDR($material->StokBahan * $material->HargaSatuan) }}</td>
                  <td class="px-4 py-2">
                    @if($material->StokBahan == 0)
                      <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded">Habis</span>
                    @elseif($material->StokBahan < $material->MinimumStok)
                      <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded">Stok Rendah</span>
                    @else
                      <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">Sehat</span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada data material</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- Low Stock Alert --}}
        @if($lowStockMaterials->count() > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
          <h3 class="font-semibold text-yellow-800 mb-2">⚠️ Peringatan Stok Rendah</h3>
          <ul class="text-sm text-yellow-700 space-y-1">
            @foreach($lowStockMaterials as $material)
            <li>{{ $material->NamaBahan }} – Stok: {{ $material->StokBahan }} (Min: {{ $material->MinimumStok }})</li>
            @endforeach
          </ul>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- SALES REPORT --}}
  <div x-show="tab === 'sales'" class="space-y-6">
    <div class="border rounded-xl bg-white shadow-sm">
      <div class="flex justify-between items-start border-b p-4">
        <div>
          <h3 class="text-lg font-semibold">Laporan Penjualan</h3>
          <p class="text-gray-500 text-sm">Performa penjualan detail dan insight pelanggan</p>
        </div>
        <div class="flex gap-2">
          <a href="{{ route('laporan.export.pdf', ['type' => 'sales', 'filter' => $filter]) }}" class="border rounded-lg px-3 py-2 text-sm hover:bg-gray-100">📄 Export PDF</a>
          <a href="{{ route('laporan.export.excel', ['type' => 'sales', 'filter' => $filter]) }}" class="border rounded-lg px-3 py-2 text-sm hover:bg-gray-100">⬇️ Export Excel</a>
        </div>
      </div>

      <div class="p-6 space-y-6">
        {{-- Sales Summary --}}
        <div class="grid grid-cols-3 gap-4">
          <div><p class="text-sm text-gray-600">Total Pesanan</p><p class="text-2xl font-semibold">{{ $salesData['totalOrders'] }}</p></div>
          <div><p class="text-sm text-gray-600">Rata-rata Nilai Pesanan</p><p class="text-2xl font-semibold">{{ formatIDR($salesData['averageOrderValue']) }}</p></div>
          <div><p class="text-sm text-gray-600">Tingkat Konversi</p><p class="text-2xl font-semibold">{{ number_format($salesData['conversionRate'], 1) }}%</p></div>
        </div>

        {{-- Top Products --}}
        <div>
          <h3 class="font-semibold mb-3">Produk Terlaris</h3>
          <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left">Produk</th>
                  <th class="px-4 py-2 text-left">SKU</th>
                  <th class="px-4 py-2 text-right">Jumlah</th>
                  <th class="px-4 py-2 text-right">Pendapatan (IDR)</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                @forelse($topProducts as $product)
                <tr>
                  <td class="px-4 py-2">{{ trim($product->NamaProduk) }}</td>
                  <td class="px-4 py-2">PRD-{{ str_pad($product->ProductID, 3, '0', STR_PAD_LEFT) }}</td>
                  <td class="px-4 py-2 text-right">{{ $product->total_quantity }}</td>
                  <td class="px-4 py-2 text-right">{{ formatIDR($product->total_revenue) }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada data penjualan produk</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- Top 5 Best Selling Products --}}
        @if($topProducts->count() > 0)
        <div>
          <h3 class="font-semibold mb-4">Top 5 Produk Terlaris</h3>
          <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full text-sm">
              <thead class="bg-gradient-to-r from-blue-50 to-blue-100">
                <tr>
                  <th class="px-4 py-3 text-left font-semibold text-blue-900">Ranking</th>
                  <th class="px-4 py-3 text-left font-semibold text-blue-900">Nama Produk</th>
                  <th class="px-4 py-3 text-right font-semibold text-blue-900">Jumlah Terjual</th>
                  <th class="px-4 py-3 text-right font-semibold text-blue-900">Total Penjualan</th>
                  <th class="px-4 py-3 text-right font-semibold text-blue-900">Persentase</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                @php
                  $topFive = $topProducts->take(5);
                  $totalTopRevenue = $topFive->sum('total_revenue');
                @endphp
                @forelse($topFive as $index => $product)
                @php
                  $revenue = (float) $product->total_revenue ?? 0;
                  $percentage = $totalTopRevenue > 0 ? number_format(($revenue / $totalTopRevenue) * 100, 1) : 0;
                @endphp
                <tr class="hover:bg-gray-50 transition">
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-500 text-white text-xs font-bold">
                      {{ $index + 1 }}
                    </span>
                  </td>
                  <td class="px-4 py-3 font-medium text-gray-800">{{ trim($product->NamaProduk) }}</td>
                  <td class="px-4 py-3 text-right text-gray-600">{{ $product->total_quantity ?? 0 }} unit</td>
                  <td class="px-4 py-3 text-right font-semibold text-green-600">{{ formatIDR($revenue) }}</td>
                  <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                      <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-2 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full" style="width: {{ $percentage }}%"></div>
                      </div>
                      <span class="font-semibold text-blue-600 min-w-max">{{ $percentage }}%</span>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada data produk terjual</td>
                </tr>
                @endforelse
              </tbody>
              <tfoot class="bg-gray-50 font-semibold border-t-2 border-gray-300">
                <tr>
                  <td colspan="3" class="px-4 py-3 text-right">Total Top 5:</td>
                  <td class="px-4 py-3 text-right text-green-600">{{ formatIDR($totalTopRevenue) }}</td>
                  <td class="px-4 py-3 text-right text-blue-600">100%</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        @else
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
          <p class="text-blue-700 text-center">Tidak ada data penjualan produk untuk ditampilkan</p>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- FINANCIAL REPORT --}}
  <div x-show="tab === 'financial'" class="space-y-6">
    <div class="border rounded-xl bg-white shadow-sm">
      <div class="flex justify-between items-start border-b p-4">
        <div>
          <h3 class="text-lg font-semibold">Laporan Keuangan</h3>
          <p class="text-gray-500 text-sm">Analisis keuangan komprehensif untuk periode yang dipilih</p>
        </div>
        <div class="flex gap-2">
          <a href="{{ route('laporan.export.pdf', ['type' => 'financial', 'filter' => $filter]) }}" class="border rounded-lg px-3 py-2 text-sm hover:bg-gray-100">📄 Export PDF</a>
          <a href="{{ route('laporan.export.excel', ['type' => 'financial', 'filter' => $filter]) }}" class="border rounded-lg px-3 py-2 text-sm hover:bg-gray-100">⬇️ Export Excel</a>
        </div>
      </div>

      <div class="p-6 space-y-6">
        {{-- Financial Summary --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div><p class="text-sm text-gray-600">Total Pendapatan</p><p class="text-2xl text-green-600 font-semibold">{{ formatIDR($financialData['totalRevenue']) }}</p></div>
          <div><p class="text-sm text-gray-600">Total Pengeluaran</p><p class="text-2xl text-red-600 font-semibold">{{ formatIDR($financialData['totalExpenses']) }}</p></div>
          <div><p class="text-sm text-gray-600">Laba Bersih</p><p class="text-2xl text-blue-600 font-semibold">{{ formatIDR($financialData['netProfit']) }}</p></div>
          <div><p class="text-sm text-gray-600">Margin Laba</p><p class="text-2xl font-semibold">{{ $financialData['profitMargin'] }}%</p></div>
        </div>

        {{-- Expense Breakdown --}}
        @if($expensesByCategory->count() > 0)
        <div>
          <h3 class="font-semibold mb-3">Rincian Pengeluaran per Kategori</h3>
          <div class="space-y-3">
            @foreach($expensesByCategory as $expense)
            <div>
              <div class="flex justify-between text-sm">
                <span>{{ $expense->Kategori ?: 'Lainnya' }}</span>
                <span>{{ formatIDR($expense->total) }} ({{ $financialData['totalExpenses'] > 0 ? number_format(($expense->total / $financialData['totalExpenses']) * 100, 0) : 0 }}%)</span>
              </div>
              <div class="h-2 bg-gray-200 rounded-full mt-1">
                <div class="h-2 bg-red-500 rounded-full" style="width: {{ $financialData['totalExpenses'] > 0 ? ($expense->total / $financialData['totalExpenses']) * 100 : 0 }}%"></div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        @endif

        {{-- Monthly Comparison --}}
        <div>
          <h3 class="font-semibold mb-3">Perbandingan Bulanan</h3>
          <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left">Bulan</th>
                  <th class="px-4 py-2 text-right">Pendapatan</th>
                  <th class="px-4 py-2 text-right">Pengeluaran</th>
                  <th class="px-4 py-2 text-right">Laba</th>
                  <th class="px-4 py-2 text-right">Margin</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                @forelse($monthlyData as $data)
                @php
                  $profit = $data->income - $data->expense;
                  $margin = $data->income > 0 ? ($profit / $data->income) * 100 : 0;
                  $monthName = \Carbon\Carbon::parse($data->month . '-01')->locale('id')->format('F Y');
                @endphp
                <tr>
                  <td class="px-4 py-2">{{ $monthName }}</td>
                  <td class="px-4 py-2 text-right">{{ number_format($data->income, 0, ',', '.') }}</td>
                  <td class="px-4 py-2 text-right text-red-600">{{ number_format($data->expense, 0, ',', '.') }}</td>
                  <td class="px-4 py-2 text-right text-green-600">{{ number_format($profit, 0, ',', '.') }}</td>
                  <td class="px-4 py-2 text-right">{{ number_format($margin, 1) }}%</td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada data transaksi bulanan</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- Cash Flow --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
          <h3 class="font-semibold mb-3">Ringkasan Arus Kas</h3>
          <div class="grid grid-cols-3 gap-4">
            <div><p class="text-sm text-gray-600">Masuk</p><p class="text-xl text-green-600 font-semibold">{{ formatIDR($financialData['totalRevenue']) }}</p></div>
            <div><p class="text-sm text-gray-600">Keluar</p><p class="text-xl text-red-600 font-semibold">{{ formatIDR($financialData['totalExpenses']) }}</p></div>
            <div><p class="text-sm text-gray-600">Bersih</p><p class="text-xl text-blue-600 font-semibold">{{ formatIDR($financialData['netProfit']) }}</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection