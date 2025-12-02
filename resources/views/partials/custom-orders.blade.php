<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-lg font-semibold">Pesanan Custom</h2>
      <p class="text-gray-600">Kelola pesanan sepatu custom dengan spesifikasi khusus</p>
    </div>
<div x-data="{ showDialog: false }" class="p-6">

  {{-- Tombol dialog --}}
  <button 
    @click="showDialog = true"
    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
    + Pesanan Custom Baru
  </button>
  @include('partials.Customorderdialog')
</div>

  </div>

  {{-- Filter --}}
  <div class="flex flex-col sm:flex-row gap-4">
    <div class="relative flex-1">
      <input 
        type="text" 
        placeholder="Cari pesanan custom..." 
        class="pl-10 w-full border border-gray-300 rounded-lg py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
      />
      <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M9 17a8 8 0 100-16 8 8 0 000 16z" />
      </svg>
    </div>
    <div class="flex gap-2 overflow-x-auto">
      @foreach (['Semua','Tertunda','Dikonfirmasi','Dalam Produksi','Siap','Terkirim'] as $filter)
        <button class="px-3 py-1 border rounded-lg text-sm hover:bg-gray-100">{{ $filter }}</button>
      @endforeach
    </div>
  </div>

  {{-- Tabel Pesanan Custom --}}
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left font-medium text-gray-600">No. Pesanan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Pelanggan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tipe Produk</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Spesifikasi</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Harga</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Sisa Bayar</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tenggat</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Prioritas</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($orders as $order)
          @if($order->customDetail)
            <tr>
              <td class="px-4 py-2">#{{ $order->OrderID }}</td>
              <td class="px-4 py-2">
                <p class="font-medium">{{ $order->customer?->Nama ?? 'N/A' }}</p>
              </td>
              <td class="px-4 py-2">
                @php
                  $customData = json_decode($order->customDetail->CatatanTambahan, true) ?? [];
                @endphp
                {{ $customData['ProductType'] ?? $order->customDetail->Model ?? '-' }}
              </td>
              <td class="px-4 py-2 text-sm">
                @php
                  $customData = json_decode($order->customDetail->CatatanTambahan, true) ?? [];
                @endphp
                {{ $customData['Size'] ?? $order->customDetail->Ukuran ?? '-' }} / 
                {{ $customData['Color'] ?? $order->customDetail->Warna ?? '-' }} / 
                {{ $customData['Material'] ?? $order->customDetail->JenisBahan ?? '-' }}
              </td>
              <td class="px-4 py-2 text-right">IDR {{ number_format($order->TotalHarga, 0, ',', '.') }}</td>
              <td class="px-4 py-2 text-right text-yellow-600">IDR {{ number_format(max(0, $order->TotalHarga - ($order->DepositPaid ?? 0)), 0, ',', '.') }}</td>
              <td class="px-4 py-2">{{ $order->TenggalSelesai?->format('d/m/Y') ?? '-' }}</td>
              <td class="px-4 py-2">
                <span class="px-2 py-1 text-xs @if($order->Prioritas === 'Mendesak') bg-red-100 text-red-700 @else bg-orange-100 text-orange-700 @endif rounded-lg">
                  {{ $order->Prioritas ?? 'Normal' }}
                </span>
              </td>
              <td class="px-4 py-2">
                <span class="px-2 py-1 text-xs @if($order->StatusOrder === 'Tertunda') bg-yellow-100 text-yellow-700 @elseif($order->StatusOrder === 'Selesai') bg-green-100 text-green-700 @else bg-blue-100 text-blue-700 @endif rounded-lg">
                  {{ $order->StatusOrder }}
                </span>
              </td>
              <td class="px-4 py-2 text-right">
                <div class="flex justify-end gap-2">
                  <button 
                    @click="openEditCustomModal({{ $order->OrderID }})"
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
              Belum ada data pesanan custom
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Edit Custom Order Modal --}}
  <div x-data="{ editModal: false, editData: {} }" @open-custom-edit-modal.window="editModal = true; editData = $event.detail" style="display: none;" x-show="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-3xl max-h-96 overflow-y-auto">
      <h3 class="text-lg font-semibold mb-4">Edit Pesanan Custom</h3>
      <form :action="'/pesanan/' + editData.id" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Nama Pelanggan</label>
          <input type="text" name="CustomerName" :value="editData.customerName" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1">Tanggal</label>
            <input type="date" name="Tanggal" :value="editData.tanggal" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Due Date</label>
            <input type="date" name="TenggalSelesai" :value="editData.tenggalSelesai" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Product Type</label>
          <input type="text" name="ProductType" :value="editData.productType" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1">Size</label>
            <input type="text" name="Size" :value="editData.size" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Color</label>
            <input type="text" name="Color" :value="editData.color" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Material</label>
            <input type="text" name="Material" :value="editData.material" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1">Total Harga (IDR)</label>
            <input type="number" name="TotalHarga" :value="editData.totalHarga" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Deposit Paid (IDR)</label>
            <input type="number" name="DepositPaid" :value="editData.depositPaid" class="w-full border border-gray-300 rounded px-3 py-2">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="StatusOrder" class="w-full border border-gray-300 rounded px-3 py-2">
              <option value="Tertunda">Tertunda</option>
              <option value="Dikonfirmasi">Dikonfirmasi</option>
              <option value="Dalam Produksi">Dalam Produksi</option>
              <option value="Siap">Siap</option>
              <option value="Terkirim">Terkirim</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Prioritas</label>
            <select name="Prioritas" class="w-full border border-gray-300 rounded px-3 py-2">
              <option value="Normal">Normal</option>
              <option value="Mendesak">Mendesak</option>
            </select>
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Style</label>
          <input type="text" name="Style" :value="editData.style" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Custom Features</label>
          <textarea name="CustomFeatures" :value="editData.customFeatures" class="w-full border border-gray-300 rounded px-3 py-2" rows="2"></textarea>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Special Requirements</label>
          <textarea name="SpecialRequirements" :value="editData.specialRequirements" class="w-full border border-gray-300 rounded px-3 py-2" rows="2"></textarea>
        </div>

        <div class="flex gap-2">
          <button type="button" @click="editModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openEditCustomModal(orderId) {
      fetch('/debug/orders').then(r => r.json()).then(orders => {
        const order = orders.find(o => o.OrderID === orderId);
        if (order && order.customDetail) {
          const customData = JSON.parse(order.customDetail?.CatatanTambahan || '{}');
          window.dispatchEvent(new CustomEvent('open-custom-edit-modal', { 
            detail: { 
              id: orderId, 
              customerName: order.customer?.Nama || '',
              tanggal: order.Tanggal,
              tenggalSelesai: order.TenggalSelesai || '',
              productType: order.customDetail?.Model || customData?.ProductType || '',
              size: order.customDetail?.Ukuran || customData?.Size || '',
              color: order.customDetail?.Warna || customData?.Color || '',
              material: order.customDetail?.JenisBahan || customData?.Material || '',
              style: customData?.Style || '',
              customFeatures: customData?.CustomFeatures || '',
              specialRequirements: customData?.SpecialRequirements || '',
              totalHarga: order.TotalHarga,
              depositPaid: order.DepositPaid || 0
            } 
          }));
        }
      });
    }
  </script>
</div>
