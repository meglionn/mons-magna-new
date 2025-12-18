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

    {{-- Form --}}
    <form method="POST" action="{{ route('order.production.store') }}" class="grid gap-4">
      @csrf
      
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Nama Pelanggan *</label>
          <select name="CustomerID" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
            <option value="">Pilih pelanggan</option>
            @foreach($customers as $customer)
              <option value="{{ $customer->CustomerID }}">{{ $customer->Nama }}</option>
            @endforeach
          </select>
        </div>
        <div x-data="{ productId: '', newProduct: false }">
          <label class="block text-sm font-medium mb-1">Produk *</label>
          <select name="ProductID" x-model="productId" @change="newProduct = productId === '__new'" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
            <option value="">Pilih produk</option>
            @foreach($products as $product)
              <option value="{{ $product->ProductID }}">{{ $product->NamaProduk }} - IDR {{ number_format($product->Harga) }}</option>
            @endforeach
            <option value="__new">+ Tambah produk baru...</option>
          </select>

          <input x-show="newProduct" x-cloak x-bind:required="newProduct" type="text" name="ProductName" placeholder="Nama produk baru" class="mt-2 w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500" />
        </div>
      </div>
      


      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Tanggal Pesanan *</label>
          <input type="date" name="Tanggal" required value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Jumlah *</label>
          <input type="number" name="Jumlah" required min="1" value="1" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="10">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Tanggal Mulai Produksi *</label>
          <input type="date" name="TanggalMulai" required value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Target Selesai</label>
          <input type="date" name="TenggalSelesai" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Status Order *</label>
          <select name="StatusOrder" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
            <option value="Pending">Pending</option>
            <option value="Proses" selected>Dalam Proses</option>
            <option value="Produksi">Produksi</option>
            <option value="Selesai">Selesai</option>
            <option value="Ditunda">Ditunda</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Status Produksi *</label>
          <select name="StatusProduksi" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
            <option value="Pending">Pending</option>
            <option value="Pemotongan Pola">Pemotongan Pola</option>
            <option value="Persiapan Kulit">Persiapan Kulit</option>
            <option value="Penjahitan">Penjahitan</option>
            <option value="Pemasangan Sol">Pemasangan Sol</option>
            <option value="Finishing">Finishing</option>
            <option value="Kontrol Kualitas">Kontrol Kualitas</option>
            <option value="Pengemasan">Pengemasan</option>
            <option value="Selesai">Selesai</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Prioritas *</label>
        <select name="Prioritas" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
          <option value="Rendah">Rendah</option>
          <option value="Sedang" selected>Sedang</option>
          <option value="Tinggi">Tinggi</option>
          <option value="Mendesak">Mendesak</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Catatan</label>
        <textarea name="Keterangan" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Tambahkan instruksi khusus atau catatan..."></textarea>
      </div>

      {{-- Footer --}}
      <div class="mt-6 flex justify-end gap-2 border-t pt-4">
        <button 
          type="button"
          @click="showProductionDialog = false"
          class="border rounded-lg px-4 py-2 hover:bg-gray-100">
          Batal
        </button>
        <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700">
          Simpan Pesanan
        </button>
      </div>
    </form>
  </div>
</div>