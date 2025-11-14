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
    <form class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
          <label class="text-sm font-medium">Tanggal</label>
          <input type="date" class="w-full border rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="space-y-1">
          <label class="text-sm font-medium">Kategori</label>
          <select class="w-full border rounded-lg p-2">
            <option>Raw Materials</option>
            <option>Labor</option>
            <option>Utilities</option>
            <option>Rent</option>
            <option>Marketing</option>
            <option>Maintenance</option>
            <option>Equipment</option>
            <option>Transportation</option>
            <option>Other Expenses</option>
          </select>
        </div>
      </div>

      <div class="space-y-1">
        <label class="text-sm font-medium">Deskripsi</label>
        <textarea rows="2" class="w-full border rounded-lg p-2" placeholder="Masukkan detail pengeluaran..."></textarea>
      </div>

      <div class="space-y-1">
        <label class="text-sm font-medium">Jumlah (IDR)</label>
        <input type="number" class="w-full border rounded-lg p-2" placeholder="0">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
          <label class="text-sm font-medium">Metode Pembayaran</label>
          <select class="w-full border rounded-lg p-2">
            <option>Cash</option>
            <option>Bank Transfer</option>
            <option>Credit Card</option>
            <option>Debit Card</option>
            <option>E-Wallet</option>
          </select>
        </div>

        <div class="space-y-1">
          <label class="text-sm font-medium">No. Referensi</label>
          <input type="text" class="w-full border rounded-lg p-2" placeholder="e.g., EXP-2025-001">
        </div>
      </div>

      <div class="space-y-1">
        <label class="text-sm font-medium">Status</label>
        <select class="w-full border rounded-lg p-2">
          <option>Completed</option>
          <option>Pending</option>
          <option>Cancelled</option>
        </select>
      </div>

      {{-- Footer --}}
      <div class="mt-6 flex justify-end gap-2 border-t pt-4">
        <button type="button" class="border rounded-lg px-4 py-2 hover:bg-gray-100" @click="showExpenseDialog = false">
          Cancel
        </button>
        <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700">
          Tambah Pengeluaran
        </button>
      </div>
    </form>
  </div>
</div>
