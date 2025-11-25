<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-lg font-semibold">Pesanan Custom</h2>
      <p class="text-gray-600">Kelola pesanan sepatu custom dengan spesifikasi khusus</p>
    </div>

    {{-- PERBAIKAN: Pindahkan x-data ke sini --}}
    <div x-data="{ showDialog: false }">
      <button 
        @click="showDialog = true"
        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
        + Pesanan Custom Baru
      </button>
      
      {{-- Include dialog di dalam scope yang sama --}}
      @include('partials.customorderdialog')
    </div>
  </div>

  {{-- Filter --}}
  <div class="flex flex-col sm:flex-row gap-4">
    <div class="relative flex-1">
      <input 
        type="text" 
        placeholder="Cari pesanan custom..." 
        class="pl-10 w-full border border-gray-300 rounded-lg py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
      />
      <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M9 17a8 8 0 100-16 8 8 0 000 16z" />
      </svg>
    </div>
    <div class="flex gap-2 overflow-x-auto">
      @foreach (['Semua','Tertunda','Dikonfirmasi','Dalam Produksi','Siap','Terkirim'] as $filter)
        <button class="px-3 py-1 border rounded-lg text-sm hover:bg-gray-100">{{ $filter }}</button>
      @endforeach
    </div>
  </div>

  {{-- Tabel --}}
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left font-medium text-gray-600">No. Pesanan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Pelanggan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tipe Produk</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Spesifikasi</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Harga</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Sisa Bayar</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tenggat</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Prioritas</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <tr>
          <td colspan="10" class="px-4 py-8 text-center text-gray-500">
            Belum ada data pesanan custom
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>