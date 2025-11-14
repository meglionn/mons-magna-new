<div 
  x-show="showProductionDialog" 
  x-cloak 
  x-transition.opacity.duration.200ms
  @keydown.escape.window="showProductionDialog = false"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">

  <div 
    @click.outside="showProductionDialog = false"
    class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6">

    {{-- Header --}}
    <div class="flex justify-between items-center border-b pb-4 mb-4">
      <div>
        <h2 class="text-2xl font-semibold">Buat Pesanan Produksi</h2>
        <p class="text-gray-500 text-sm">Isi detail pesanan produksi dan tahap pengerjaan</p>
      </div>
      <button 
        @click="showProductionDialog = false"
        class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
    </div>

    {{-- Body --}}
    <div class="grid gap-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Nomor Pesanan</label>
          <input type="text" class="w-full border rounded-lg p-2" placeholder="PO-2025-001">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Nama Pelanggan</label>
          <input type="text" class="w-full border rounded-lg p-2" placeholder="Nama pelanggan">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Produk</label>
          <select class="w-full border rounded-lg p-2">
            <option>Pilih produk</option>
            <option>Kalla Baiq Classic (KB-001)</option>
            <option>Lana Lale Boots (LL-002)</option>
            <option>Chelsea Boot Black (CB-003)</option>
            <option>Textile Oxford Shoes (TO-004)</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">SKU</label>
          <input type="text" class="w-full border rounded-lg p-2" placeholder="KB-001">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Jumlah Total</label>
          <input type="number" class="w-full border rounded-lg p-2" placeholder="10">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Unit Selesai</label>
          <input type="number" class="w-full border rounded-lg p-2" placeholder="0">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
          <input type="date" class="w-full border rounded-lg p-2">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Tenggat Waktu</label>
          <input type="date" class="w-full border rounded-lg p-2">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Tahap Saat Ini</label>
          <select class="w-full border rounded-lg p-2">
            <option>Pending</option>
            <option>Pemotongan Pola</option>
            <option>Persiapan Kulit</option>
            <option>Penjahitan</option>
            <option>Pemasangan Sol</option>
            <option>Finishing</option>
            <option>Kontrol Kualitas</option>
            <option>Pengemasan</option>
            <option>Selesai</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Tim yang Ditugaskan</label>
          <select class="w-full border rounded-lg p-2">
            <option>Pilih tim</option>
            <option>Tim A</option>
            <option>Tim B</option>
            <option>Tim C</option>
            <option>Tim D</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Status</label>
          <select class="w-full border rounded-lg p-2">
            <option>Pending</option>
            <option>Dalam Proses</option>
            <option>Cek Kualitas</option>
            <option>Selesai</option>
            <option>Ditunda</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Prioritas</label>
          <select class="w-full border rounded-lg p-2">
            <option>Rendah</option>
            <option>Sedang</option>
            <option>Tinggi</option>
            <option>Mendesak</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Catatan</label>
        <textarea class="w-full border rounded-lg p-2" rows="3" placeholder="Tambahkan instruksi khusus atau catatan..."></textarea>
      </div>
    </div>

    {{-- Footer --}}
    <div class="mt-6 flex justify-end gap-2 border-t pt-4">
      <button 
        @click="showProductionDialog = false"
        class="border rounded-lg px-4 py-2 hover:bg-gray-100">
        Batal
      </button>
      <button class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700">
        Simpan Pesanan
      </button>
    </div>
  </div>
</div>
