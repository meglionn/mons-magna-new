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
    <form action="{{ route('financial.income.store') }}" method="POST" class="space-y-4" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').textContent='Menyimpan...'">
      @csrf
      
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
          <label class="text-sm font-medium">Tanggal *</label>
          <input type="date" name="Tanggal" required value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="space-y-1">
          <label class="text-sm font-medium">Kategori</label>
          <select name="Kategori" class="w-full border rounded-lg p-2">
            <option value="Product Sales" selected>Product Sales</option>
            <option value="Wholesale Orders">Wholesale Orders</option>
            <option value="Custom Orders">Custom Orders</option>
            <option value="Other Income">Other Income</option>
          </select>
        </div>
      </div>

      <div class="space-y-1">
        <label class="text-sm font-medium">Deskripsi</label>
        <textarea name="Keterangan" rows="2" class="w-full border rounded-lg p-2" placeholder="Masukkan detail pendapatan..."></textarea>
      </div>

      <div class="space-y-1">
        <label class="text-sm font-medium">Jumlah (IDR) *</label>
        <input type="number" name="Jumlah" required min="0" step="0.01" class="w-full border rounded-lg p-2" placeholder="0">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
          <label class="text-sm font-medium">Metode Pembayaran</label>
          <select name="MetodePembayaran" class="w-full border rounded-lg p-2">
            <option value="Cash" selected>Cash</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="E-Wallet">E-Wallet</option>
          </select>
        </div>

        <div class="space-y-1">
          <label class="text-sm font-medium">No. Referensi / Order ID</label>
          <input type="text" name="OrderID" class="w-full border rounded-lg p-2" placeholder="e.g., INV-2025-001 atau Order ID">
        </div>
      </div>

      <div class="space-y-1">
        <label class="text-sm font-medium">Status</label>
        <select name="Status" class="w-full border rounded-lg p-2">
          <option value="Completed" selected>Completed</option>
          <option value="Pending">Pending</option>
          <option value="Cancelled">Cancelled</option>
        </select>
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
