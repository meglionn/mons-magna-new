<div 
  x-show="showExpenseDialog"
  x-cloak
  x-transition.opacity.duration.200ms
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
>
  <div 
    @click.outside="showExpenseDialog = false"
    class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 overflow-y-auto max-h-[90vh]"
  >
    {{-- Header --}}
    <div class="mb-4 border-b pb-3">
      <h2 class="text-xl font-semibold">Tambah Pengeluaran</h2>
      <p class="text-gray-500 text-sm">Isi detail pengeluaran baru</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('financial.expense.store') }}" method="POST" class="space-y-4">
      @csrf
      
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
          <label class="text-sm font-medium">Tanggal *</label>
          <input type="date" name="Tanggal" required value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
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
        <textarea name="Keterangan" rows="3" class="w-full border rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan detail pengeluaran..."></textarea>
      </div>

      {{-- Footer --}}
      <div class="mt-6 flex justify-end gap-2 border-t pt-4">
        <button type="button" class="border rounded-lg px-4 py-2 hover:bg-gray-100" @click="showExpenseDialog = false">
          Cancel
        </button>
        <button type="submit" class="bg-red-600 text-white rounded-lg px-4 py-2 hover:bg-red-700">
          Tambah Pengeluaran
        </button>
      </div>
    </form>
  </div>
</div>