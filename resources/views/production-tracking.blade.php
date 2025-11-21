<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-lg font-semibold">Pelacakan Pesanan & Produksi</h2>
      <p class="text-gray-600">Pantau dan kelola pesanan produksi sepatu</p>
    </div>
    
    {{-- Tombol buka modal - FIXED: menggunakan parent scope --}}
    <button 
      @click="$parent.showProductionDialog = true"
      class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
      + Pesanan Produksi Baru
    </button>
  </div>

  {{-- Filter --}}
  <div class="flex flex-col sm:flex-row gap-4">
    <div class="relative flex-1">
      <input 
        type="text" 
        placeholder="Cari pesanan produksi..." 
        class="pl-10 w-full border border-gray-300 rounded-lg py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
      <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M9 17a8 8 0 100-16 8 8 0 000 16z" />
      </svg>
    </div>
    <div class="flex gap-2 overflow-x-auto">
      @foreach (['Semua','Pending','Dalam Proses','Cek Kualitas','Selesai'] as $filter)
        <button class="px-3 py-1 border rounded-lg text-sm hover:bg-gray-100 whitespace-nowrap">{{ $filter }}</button>
      @endforeach
    </div>
  </div>

  {{-- Tabel Pesanan Produksi --}}
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left font-medium text-gray-600">No. Pesanan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Pelanggan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Produk</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Jumlah</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tahap</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Ditugaskan Ke</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tenggat</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Prioritas</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <tr>
          <td class="px-4 py-2">#PRD-001</td>
          <td class="px-4 py-2">Budi Santoso</td>
          <td class="px-4 py-2">Sepatu Kasual</td>
          <td class="px-4 py-2">20</td>
          <td class="px-4 py-2">Penjahitan</td>
          <td class="px-4 py-2">Tim A</td>
          <td class="px-4 py-2">20/11/2025</td>
          <td class="px-4 py-2"><span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-lg">Tinggi</span></td>
          <td class="px-4 py-2"><span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg">Dalam Proses</span></td>
          <td class="px-4 py-2 text-right">
            <button class="text-blue-600 hover:text-blue-800">✏️</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>