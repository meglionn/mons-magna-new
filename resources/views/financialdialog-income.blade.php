<div 
  x-show="showIncomeDialog"
  x-cloak
  x-transition.opacity.duration.200ms
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
>
  <div 
    @click.outside="showIncomeDialog = false"
    class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 overflow-y-auto max-h-[90vh]"
  >
    {{-- Header --}}
    <div class="mb-4 border-b pb-3">
      <h2 class="text-xl font-semibold">Tambah Pendapatan</h2>
      <p class="text-gray-500 text-sm">Isi detail pendapatan baru</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('financial.income.store') }}" method="POST" class="space-y-4">
      @csrf
      
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
          <label class="text-sm font-medium">Tanggal *</label>
          <input type="date" name="Tanggal" required value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        
      <div class="form-group">
        <label for="Status">Status</label>
        <select name="Status" id="Status" class="form-control">
          <option value="">-- Select status --</option>
          <option value="Completed">Completed</option>
          <option value="Pending">Pending</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>

        <div class="space-y-1">
          <label class="text-sm font-medium">Order ID (Opsional)</label>
          <input type="number" name="OrderID" class="w-full border rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="ID Pesanan terkait">
        </div>
      </div>

      <div class="space-y-1">
        <label class="text-sm font-medium">Jumlah (IDR) *</label>
        <input type="number" name="Jumlah" required min="0" step="0.01" class="w-full border rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0">
      </div>

      <div class="space-y-1">
        <label class="text-sm font-medium">Keterangan</label>
        <textarea name="Keterangan" rows="3" class="w-full border rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan detail pendapatan..."></textarea>
      </div>

      {{-- Footer --}}
      <div class="mt-6 flex justify-end gap-2 border-t pt-4">
        <button type="button" class="border rounded-lg px-4 py-2 hover:bg-gray-100" @click="showIncomeDialog = false">
          Cancel
        </button>
        <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700">
          Tambah Pendapatan
        </button>
      </div>
    </form>
  </div>
</div>