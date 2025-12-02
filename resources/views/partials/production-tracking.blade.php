<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-lg font-semibold">Pelacakan Pesanan & Produksi</h2>
      <p class="text-gray-600">Pantau dan kelola pesanan produksi sepatu</p>
    </div>
    <div x-data="{ showProductionDialog: false }" class="p-6">

  {{-- Tombol buka modal --}}
  <button 
    @click="showProductionDialog = true"
    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
    + Pesanan Produksi Baru
  </button>
  @include('partials.productionorderdialog')
</div>
  </div>

  {{-- Filter --}}
  <div class="flex flex-col sm:flex-row gap-4">
    <div class="relative flex-1">
      <input 
        type="text" 
        placeholder="Cari pesanan produksi..." 
        class="pl-10 w-full border border-gray-300 rounded-lg py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
      <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M9 17a8 8 0 100-16 8 8 0 000 16z" />
      </svg>
    </div>
    <div class="flex gap-2 overflow-x-auto">
      @foreach (['Semua','Pending','Dalam Proses','Cek Kualitas','Selesai'] as $filter)
        <button class="px-3 py-1 border rounded-lg text-sm hover:bg-gray-100">{{ $filter }}</button>
      @endforeach
    </div>
  </div>

  {{-- Tabel Pesanan Produksi --}}
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left font-medium text-gray-600">No. Pesanan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Pelanggan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Produk</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Jumlah</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tahap</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Ditugaskan Ke</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tenggat</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Prioritas</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
          <th class="px-4 py-2 text-center font-medium text-gray-600">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($orders as $order)
          @if($order->produksi || !$order->customDetail)
            <tr>
              <td class="px-4 py-2">#{{ $order->OrderID }}</td>
              <td class="px-4 py-2">{{ $order->customer?->Nama ?? 'N/A' }}</td>
              <td class="px-4 py-2">
                @if($order->orderDetails->first())
                  {{ $order->orderDetails->first()->product?->NamaProduk ?? 'Produk' }}
                @else
                  -
                @endif
              </td>
              <td class="px-4 py-2">{{ $order->orderDetails->sum('Jumlah') ?: '-' }}</td>
              <td class="px-4 py-2">{{ $order->StatusOrder ?? '-' }}</td>
              <td class="px-4 py-2">{{ $order->produksi?->first()?->StatusProduksi ?? '-' }}</td>
              <td class="px-4 py-2">{{ $order->produksi?->first()?->TanggalSelesai?->format('d/m/Y') ?? '-' }}</td>
              <td class="px-4 py-2">
                <span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-lg">Sedang</span>
              </td>
              <td class="px-4 py-2">
                <span class="px-2 py-1 text-xs @if($order->StatusOrder === 'Proses') bg-blue-100 text-blue-700 @elseif($order->StatusOrder === 'Selesai') bg-green-100 text-green-700 @else bg-gray-100 text-gray-700 @endif rounded-lg">
                  {{ $order->StatusOrder }}
                </span>
              </td>
              <td class="px-4 py-2 text-right">
                <div class="flex justify-end gap-2">
                  <button 
                    @click="openEditProductionModal({{ $order->OrderID }})"
                    class="text-blue-600 hover:text-blue-800 text-lg">
                    ✏️
                  </button>
                  <form action="{{ route('order.destroy', $order->OrderID) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 text-lg">🗑️</button>
                  </form>
                </div>
              </td>
            </tr>
          @endif
        @empty
          <tr>
            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
              Belum ada data pesanan produksi
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Edit Production Order Modal --}}
  <div x-data="{ editModal: false, editData: {} }" @open-edit-modal.window="editModal = true; editData = $event.detail" style="display: none;" x-show="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-3xl max-h-96 overflow-y-auto">
      <h3 class="text-lg font-semibold mb-4">Edit Pesanan Produksi</h3>
      <form :action="'/pesanan/' + editData.id" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1">Nama Pelanggan</label>
            <input type="text" name="CustomerName" :value="editData.customerName" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Nama Produk</label>
            <input type="text" name="ProductName" :value="editData.productName" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1">Tanggal Pesanan</label>
            <input type="date" name="Tanggal" :value="editData.tanggal" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Jumlah</label>
            <input type="number" name="Jumlah" :value="editData.jumlah" min="1" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1">Tanggal Mulai Produksi</label>
            <input type="date" name="TanggalMulai" :value="editData.tanggalMulai" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Target Selesai</label>
            <input type="date" name="TenggalSelesai" :value="editData.tenggalSelesai" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1">Status Order</label>
            <select name="StatusOrder" class="w-full border border-gray-300 rounded px-3 py-2">
              <option value="Pending">Pending</option>
              <option value="Proses">Dalam Proses</option>
              <option value="Produksi">Produksi</option>
              <option value="Selesai">Selesai</option>
              <option value="Ditunda">Ditunda</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Status Produksi</label>
            <select name="StatusProduksi" class="w-full border border-gray-300 rounded px-3 py-2">
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

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1">Prioritas</label>
            <select name="Prioritas" class="w-full border border-gray-300 rounded px-3 py-2">
              <option value="Rendah">Rendah</option>
              <option value="Sedang">Sedang</option>
              <option value="Tinggi">Tinggi</option>
              <option value="Mendesak">Mendesak</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Total Harga</label>
            <input type="number" name="TotalHarga" :value="editData.totalHarga" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Catatan</label>
          <textarea name="Keterangan" :value="editData.keterangan" class="w-full border border-gray-300 rounded px-3 py-2" rows="3"></textarea>
        </div>

        <div class="flex gap-2">
          <button type="button" @click="editModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openEditProductionModal(orderId) {
      fetch('/debug/orders').then(r => r.json()).then(orders => {
        const order = orders.find(o => o.OrderID === orderId);
        if (order) {
          const produksi = order.produksi && order.produksi.length > 0 ? order.produksi[0] : {};
          const orderDetail = order.orderDetails && order.orderDetails.length > 0 ? order.orderDetails[0] : {};
          const product = orderDetail.product || {};
          window.dispatchEvent(new CustomEvent('open-edit-modal', { 
            detail: { 
              id: orderId, 
              customerName: order.customer?.Nama || '',
              productName: product?.NamaProduk || '',
              tanggal: order.Tanggal,
              tanggalMulai: produksi?.TanggalMulai || '',
              tenggalSelesai: produksi?.TanggalSelesai || '',
              jumlah: orderDetail?.Jumlah || 1,
              keterangan: produksi?.Keterangan || ''
            } 
          }));
        }
      });
    }
  </script>
</div>
