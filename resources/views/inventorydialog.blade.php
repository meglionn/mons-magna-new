<div 
  x-show="showMaterialDialog" 
  x-cloak 
  x-transition.opacity.duration.200ms
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">

  <div 
    @click.outside="showMaterialDialog = false"
    class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6">

    {{-- Header --}}
    <div class="flex justify-between items-center border-b pb-4 mb-4">
      <div>
        <h2 class="text-2xl font-semibold" x-text="editMode ? 'Edit Material & Tambah Stok' : 'Add New Material'"></h2>
        <p class="text-gray-500 text-sm">
          <span x-show="!editMode" x-text="'Fill in the details to add a new material to your inventory.'"></span>
          <span x-show="editMode" x-text="'Update material data dan tambah stok baru dengan perhitungan harga rata-rata otomatis.'"></span>
        </p>
      </div>
      <button 
        @click="showMaterialDialog = false"
        class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('inventorymaterial.store') }}" class="grid gap-4" @submit.prevent="handleFormSubmit">
      @csrf
      <input type="hidden" name="editMode" x-bind:value="editMode ? 'update_stock' : 'add'">
      <input type="hidden" name="_method" x-show="editMode" value="PUT">>
      
      {{-- Main Info Section --}}
      <div>
        <h3 class="text-lg font-semibold mb-3 text-gray-700">Informasi Dasar</h3>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Material Name *</label>
            <input type="text" name="NamaBahan" required x-model="formData.NamaBahan" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="Leather Upper">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select name="Kategori" x-model="formData.Kategori" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
              <option value="">Select a category</option>
              <option value="Kulit">Leather</option>
              <option value="Sol">Soles</option>
              <option value="Benang">Thread & Laces</option>
              <option value="Hardware">Hardware</option>
              <option value="Adhesives">Adhesives</option>
              <option value="Dyes">Dyes & Finishes</option>
              <option value="Fabric">Fabric</option>
              <option value="Insoles">Insoles</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>

        <div class="mt-3">
          <label class="block text-sm font-medium mb-1">Material Type</label>
          <input type="text" name="JenisBahan" x-model="formData.JenisBahan" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="e.g., Full Grain Leather">
        </div>
      </div>

      {{-- Current Stock Section --}}
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-3 text-gray-700">Data Stok Saat Ini</h3>
        <div class="grid grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Stok Saat Ini *</label>
            <input type="number" name="StokBahan" required min="0" x-model.number="formData.StokBahan" :readonly="editMode" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 bg-gray-50" placeholder="0">
            <p x-show="editMode" class="text-xs text-gray-500 mt-1">Hanya baca (otomatis terupdate)</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Harga Satuan Lama *</label>
            <input type="number" name="HargaSatuan" required min="0" step="0.01" x-model.number="formData.HargaSatuan" :readonly="editMode" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 bg-gray-50" placeholder="500000">
            <p x-show="editMode" class="text-xs text-gray-500 mt-1">Hanya baca (harga rata-rata lama)</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Total Nilai Inventori</label>
            <input type="number" name="TotalNilaiInventori" min="0" step="0.01" x-model.number="formData.TotalNilaiInventori" disabled class="w-full border rounded-lg p-2 bg-gray-50" placeholder="0">
            <p class="text-xs text-gray-500 mt-1">Stok × Harga Satuan</p>
          </div>
        </div>

        <div class="mt-3">
          <label class="block text-sm font-medium mb-1">Min. Stok *</label>
          <input type="number" name="MinimumStok" required min="0" x-model.number="formData.MinimumStok" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="10">
        </div>
      </div>

      {{-- Edit Mode: Stock Addition Section --}}
      <div x-show="editMode" class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-3 text-gray-700">📊 Tambah Stok & Hitung Harga Rata-rata</h3>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1">Jumlah Beli Terbaru *</label>
            <input type="number" name="jumlahBeli" min="0" x-model.number="formData.jumlahBeli" @input="updateCalculations" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-green-500" placeholder="Berapa unit?">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Harga Beli Terbaru (per unit) *</label>
            <input type="number" name="hargaBeliTerbaru" min="0" step="0.01" x-model.number="formData.hargaBeliTerbaru" @input="updateCalculations" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-green-500" placeholder="Harga baru">
          </div>
        </div>

        {{-- Calculation Preview --}}
        <div class="bg-white border rounded-lg p-3 space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-600">Stok Lama:</span>
            <span class="font-medium" x-text="formData.StokBahan + ' unit'"></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Total Nilai Lama:</span>
            <span class="font-medium" x-text="'IDR ' + formatCurrency(formData.TotalNilaiInventori)"></span>
          </div>
          <div class="border-t pt-2 flex justify-between">
            <span class="text-gray-600">+ Jumlah Beli:</span>
            <span class="font-medium text-green-600" x-text="formData.jumlahBeli + ' unit'"></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">+ Nilai Beli Terbaru:</span>
            <span class="font-medium text-green-600" x-text="'IDR ' + formatCurrency(formData.jumlahBeli * formData.hargaBeliTerbaru)"></span>
          </div>
          <div class="border-t-2 pt-2 bg-green-50 -mx-3 px-3 py-2 flex justify-between font-semibold">
            <span>= Stok Total Baru:</span>
            <span class="text-green-600" x-text="calculatedStock + ' unit'"></span>
          </div>
          <div class="bg-green-50 -mx-3 px-3 py-2 flex justify-between font-semibold">
            <span>= Harga Satuan Rata-rata:</span>
            <span class="text-green-600" x-text="'IDR ' + formatCurrency(calculatedPrice)"></span>
          </div>
          <div class="bg-green-50 -mx-3 px-3 py-2 flex justify-between font-semibold">
            <span>= Total Nilai Inventori Baru:</span>
            <span class="text-green-600" x-text="'IDR ' + formatCurrency(calculatedTotalValue)"></span>
          </div>
        </div>
      </div>

      {{-- Footer --}}
      <div class="mt-6 flex justify-end gap-2 border-t pt-4">
        <button 
          type="button"
          @click="showMaterialDialog = false"
          class="border rounded-lg px-4 py-2 hover:bg-gray-100">
          Cancel
        </button>
        <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700">
          <span x-text="editMode ? 'Update & Simpan' : 'Tambah Material'"></span>
        </button>
      </div>
    </form>

  </div>
</div>