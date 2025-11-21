<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-lg font-semibold">Pesanan Custom</h2>
      <p class="text-gray-600">Kelola pesanan sepatu custom dengan spesifikasi khusus</p>
    </div>

    {{-- Tombol dialog - FIXED: menggunakan parent scope --}}
    <button 
      @click="$parent.showCustomDialog = true"
      class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
      + Pesanan Custom Baru
    </button>
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
        <button class="px-3 py-1 border rounded-lg text-sm hover:bg-gray-100 whitespace-nowrap">{{ $filter }}</button>
      @endforeach
    </div>
  </div>

  {{-- Tabel Pesanan Custom --}}
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
          <td class="px-4 py-2">#CST-001</td>
          <td class="px-4 py-2">
            <p class="font-medium">Agus Wijaya</p>
          </td>
          <td class="px-4 py-2">Sepatu Kulit</td>
          <td class="px-4 py-2 text-sm">42 / Hitam / Leather</td>
          <td class="px-4 py-2 text-right">IDR 1.200.000</td>
          <td class="px-4 py-2 text-right text-yellow-600">IDR 600.000</td>
          <td class="px-4 py-2">25/11/2025</td>
          <td class="px-4 py-2"><span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-lg">Mendesak</span></td>
          <td class="px-4 py-2"><span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-lg">Tertunda</span></td>
          <td class="px-4 py-2 text-right">
            <div class="flex justify-end gap-2">
              <button class="text-gray-600 hover:text-gray-800">✏️</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>