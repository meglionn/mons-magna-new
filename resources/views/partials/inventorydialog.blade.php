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
    <div class="grid gap-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Material Name</label>
          <input type="text" class="w-full border rounded-lg p-2" placeholder="Leather Upper">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Material Code</label>
          <input type="text" class="w-full border rounded-lg p-2" placeholder="MAT-001">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Category</label>
          <select class="w-full border rounded-lg p-2">
            <option>Select a category</option>
            <option>Leather</option>
            <option>Soles</option>
            <option>Thread & Laces</option>
            <option>Hardware</option>
            <option>Adhesives</option>
            <option>Dyes & Finishes</option>
            <option>Fabric</option>
            <option>Insoles</option>
            <option>Other</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Unit of Measurement</label>
          <select class="w-full border rounded-lg p-2">
            <option>Select a unit</option>
            <option>sq ft</option>
            <option>meters</option>
            <option>pairs</option>
            <option>pieces</option>
            <option>liters</option>
            <option>kg</option>
            <option>units</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Current Stock</label>
          <input type="number" class="w-full border rounded-lg p-2" placeholder="0">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Min. Stock</label>
          <input type="number" class="w-full border rounded-lg p-2" placeholder="0">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Max. Stock</label>
          <input type="number" class="w-full border rounded-lg p-2" placeholder="0">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Unit Price (IDR)</label>
        <input type="number" class="w-full border rounded-lg p-2" placeholder="500000">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Supplier</label>
        <input type="text" class="w-full border rounded-lg p-2" placeholder="e.g., Indo Leather Supply">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Storage Location</label>
        <input type="text" class="w-full border rounded-lg p-2" placeholder="e.g., Warehouse A - Section 1">
      </div>
    </div>

    {{-- Footer --}}
    <div class="mt-6 flex justify-end gap-2 border-t pt-4">
      <button 
        @click="showMaterialDialog = false"
        class="border rounded-lg px-4 py-2 hover:bg-gray-100">
        Cancel
      </button>
      <button class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700">
        <span x-text="editMode ? 'Update Material' : 'Add Material'"></span>
      </button>
    </div>

  </div>
</div>
