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
        <h2 class="text-2xl font-semibold" x-text="editMode ? 'Edit Material' : 'Add New Material'"></h2>
        <p class="text-gray-500 text-sm">
          <span x-text="editMode ? 'Update the material details below.' : 'Fill in the details to add a new material to your inventory.'"></span>
        </p>
      </div>
      <button 
        @click="showMaterialDialog = false"
        class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('inventorymaterial.store') }}" class="grid gap-4">
      @csrf
      
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Material Name *</label>
          <input type="text" name="NamaBahan" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="Leather Upper">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Category</label>
          <select name="Kategori" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
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

      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Current Stock *</label>
          <input type="number" name="StokBahan" required min="0" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="0">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Min. Stock *</label>
          <input type="number" name="MinimumStok" required min="0" value="10" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="10">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Unit Price (IDR)</label>
          <input type="number" name="HargaSatuan" min="0" step="0.01" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="500000">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Material Type</label>
        <input type="text" name="JenisBahan" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="e.g., Full Grain Leather">
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
          <span x-text="editMode ? 'Update Material' : 'Add Material'"></span>
        </button>
      </div>
    </form>

  </div>
</div>