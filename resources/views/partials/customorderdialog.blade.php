<div 
x-show="showDialog" 
  x-cloak 
  x-transition.opacity.duration.200ms
  @keydown.escape.window="showDialog = false"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">

  {{-- Box isi modal --}}
  <div 
    @click.outside="$parent.showDialog = false" 
    class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6">

    {{-- Header --}}
    <div class="flex justify-between items-center border-b pb-4 mb-4">
      <div>
        <h2 class="text-2xl font-semibold">Buat Pesanan Custom</h2>
        <p class="text-gray-500 text-sm">Isi detail pelanggan, produk, ukuran, dan pembayaran</p>
      </div>
      <button 
        @click="$parent.showDialog = false" 
        class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
    </div>

    {{-- FORM --}}
    <form method="POST" action="{{ route('order.custom.store') }}">
      @csrf
      
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Nama Pelanggan *</label>
          <input type="text" name="CustomerName" required placeholder="Nama pelanggan" class="w-full border rounded-lg p-2" />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Tanggal *</label>
            <input type="date" name="Tanggal" required value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Due Date *</label>
            <input type="date" name="TenggalSelesai" required class="w-full border rounded-lg p-2">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Product Type *</label>
          <input type="text" name="ProductType" required class="w-full border rounded-lg p-2" placeholder="Custom Leather Shoes">
        </div>

        <div class="grid grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Size *</label>
            <input type="text" name="Size" required class="w-full border rounded-lg p-2" placeholder="42">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Color *</label>
            <input type="text" name="Color" required class="w-full border rounded-lg p-2" placeholder="Black">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Material *</label>
            <input type="text" name="Material" required class="w-full border rounded-lg p-2" placeholder="Leather">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Total Harga (IDR) *</label>
          <input type="number" name="TotalHarga" required min="0" class="w-full border rounded-lg p-2" placeholder="1200000">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Deposit Paid (IDR)</label>
          <input type="number" name="DepositPaid" min="0" class="w-full border rounded-lg p-2" placeholder="600000">
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Status *</label>
            <select name="StatusOrder" required class="w-full border rounded-lg p-2">
              <option value="Tertunda">Tertunda</option>
              <option value="Dikonfirmasi">Dikonfirmasi</option>
              <option value="Dalam Produksi">Dalam Produksi</option>
              <option value="Siap">Siap</option>
              <option value="Terkirim">Terkirim</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Prioritas *</label>
            <select name="Prioritas" required class="w-full border rounded-lg p-2">
              <option value="Normal">Normal</option>
              <option value="Mendesak">Mendesak</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Style</label>
          <input type="text" name="Style" class="w-full border rounded-lg p-2" placeholder="Oxford, Derby, etc.">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Custom Features</label>
          <textarea name="CustomFeatures" class="w-full border rounded-lg p-2" rows="2"></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Special Requirements</label>
          <textarea name="SpecialRequirements" class="w-full border rounded-lg p-2" rows="2"></textarea>
        </div>

        {{-- Hidden fields for optional measurements --}}
        <input type="hidden" name="FootLength" value="">
        <input type="hidden" name="FootWidth" value="">
        <input type="hidden" name="InstepHeight" value="">
        <input type="hidden" name="AdditionalNotes" value="">
      </div>

      <div class="mt-6 flex justify-end gap-2 border-t pt-4">
        <button 
          type="button"
          @click="showDialog = false" 
          class="border rounded-lg px-4 py-2 hover:bg-gray-100">
          Cancel
        </button>
        <button 
          type="submit"
          class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700">
          Save Order
        </button>
      </div>
    </form>

  </div>
</div>
</div>