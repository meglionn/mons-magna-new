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
          @if($order->customDetail && $order->orderDetails->count() === 0)
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
              @php
                $orderTotal = $order->TotalHarga ?? $order->orderDetails->sum('Subtotal') ?? 0;
                $deposit = $order->DepositPaid ?? 0;
                $sisa = max(0, $orderTotal - $deposit);
              @endphp
              <td class="px-4 py-2 text-right text-yellow-600">IDR {{ number_format($sisa, 0, ',', '.') }}</td>
              @php
                $customTgl = data_get(json_decode($order->customDetail->CatatanTambahan, true) ?? [], 'TenggalSelesai');
              @endphp
              <td class="px-4 py-2">{{ $customTgl ? \Carbon\Carbon::parse($customTgl)->format('d/m/Y') : ($order->TenggalSelesai?->format('d/m/Y') ?? '-') }}</td>
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
                    onclick="openEditCustomModal({{ $order->OrderID }})"
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

  {{-- Edit Custom Order Modal (vanilla JS) --}}
  <div id="editCustomModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background: white; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); padding: 24px; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto; margin: 40px auto;">
      <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 12px;">Edit Pesanan Custom</h3>
      <form id="editCustomForm" action="" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 12px;">
          <label style="display:block; font-weight:600; margin-bottom:6px;">Nama Pelanggan *</label>
          <input type="text" id="editCustomerName" name="CustomerName" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
          <div>
            <label style="display:block; font-weight:600; margin-bottom:6px;">Tanggal *</label>
            <input type="date" id="editTanggal" name="Tanggal" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
          </div>
          <div>
            <label style="display:block; font-weight:600; margin-bottom:6px;">Due Date *</label>
            <input type="date" id="editTenggalSelesai" name="TenggalSelesai" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
          </div>
        </div>

        <div style="margin-bottom:12px;">
          <label style="display:block; font-weight:600; margin-bottom:6px;">Product Type *</label>
          <input type="text" id="editProductType" name="ProductType" required placeholder="Custom Leather Shoes" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:12px;">
          <div>
            <label style="display:block; font-weight:600; margin-bottom:6px;">Size *</label>
            <input type="text" id="editSize" name="Size" required placeholder="42" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
          </div>
          <div>
            <label style="display:block; font-weight:600; margin-bottom:6px;">Color *</label>
            <input type="text" id="editColor" name="Color" required placeholder="Black" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
          </div>
          <div>
            <label style="display:block; font-weight:600; margin-bottom:6px;">Material *</label>
            <input type="text" id="editMaterial" name="Material" required placeholder="Leather" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
          </div>
        </div>

        <div style="margin-bottom:12px;">
          <label style="display:block; font-weight:600; margin-bottom:6px;">Total Harga (IDR) *</label>
          <input type="number" id="editTotalHarga" name="TotalHarga" required min="0" placeholder="1200000" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
        </div>

        <div style="margin-bottom:12px;">
          <label style="display:block; font-weight:600; margin-bottom:6px;">Deposit Paid (IDR)</label>
          <input type="number" id="editDepositPaid" name="DepositPaid" min="0" placeholder="600000" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
          <div>
            <label style="display:block; font-weight:600; margin-bottom:6px;">Status *</label>
            <select id="editStatusOrder" name="StatusOrder" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
              <option value="Tertunda">Tertunda</option>
              <option value="Dikonfirmasi">Dikonfirmasi</option>
              <option value="Dalam Produksi">Dalam Produksi</option>
              <option value="Siap">Siap</option>
              <option value="Terkirim">Terkirim</option>
            </select>
          </div>
          <div>
            <label style="display:block; font-weight:600; margin-bottom:6px;">Prioritas *</label>
            <select id="editPrioritas" name="Prioritas" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
              <option value="Normal">Normal</option>
              <option value="Mendesak">Mendesak</option>
            </select>
          </div>
        </div>

        <div style="margin-bottom:12px;">
          <label style="display:block; font-weight:600; margin-bottom:6px;">Style</label>
          <input type="text" id="editStyle" name="Style" placeholder="Oxford, Derby, etc." style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
        </div>

        <div style="margin-bottom:12px;">
          <label style="display:block; font-weight:600; margin-bottom:6px;">Custom Features</label>
          <textarea id="editCustomFeatures" name="CustomFeatures" rows="2" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;"></textarea>
        </div>

        <div style="margin-bottom:12px;">
          <label style="display:block; font-weight:600; margin-bottom:6px;">Special Requirements</label>
          <textarea id="editSpecialRequirements" name="SpecialRequirements" rows="2" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;"></textarea>
        </div>

        <div style="display:flex; gap:12px; justify-content:flex-end;">
          <button type="button" onclick="closeEditCustomModal()" style="padding:8px 14px; border-radius:6px; background:#e5e7eb;">Batal</button>
          <button type="submit" style="padding:8px 14px; border-radius:6px; background:#2563eb; color:white;">Simpan</button>
        </div>
      </form>
    </div>
  </div>
  <script>
  function openEditCustomModal(orderId) {
    // SET FORM ACTION
    const form = document.getElementById('editCustomForm');
    form.action = `/pesanan/${orderId}`;

    // Fetch latest orders (same approach as other modals) and fill form
    fetch('/debug/orders').then(r => r.json()).then(orders => {
      const order = orders.find(o => o.OrderID === orderId);
      if (!order) {
        alert('Data order tidak ditemukan.');
        return;
      }

      console.log('Order data:', order); // Debug log

      const custom = order.customDetail || {};
      let note = {};
      
      // Parse CatatanTambahan safely
      try {
        if (custom.CatatanTambahan) {
          note = typeof custom.CatatanTambahan === 'string' 
            ? JSON.parse(custom.CatatanTambahan) 
            : custom.CatatanTambahan;
        }
      } catch (e) {
        console.warn('Failed to parse CatatanTambahan:', e);
        note = {};
      }

      console.log('Custom detail:', custom); // Debug log
      console.log('Parsed notes:', note); // Debug log

      // Isi form dengan data yang ada
      document.getElementById('editCustomerName').value = order.customer?.Nama || '';
      document.getElementById('editTanggal').value = order.Tanggal ? order.Tanggal.split('T')[0] : '';
      
      // Handle TenggalSelesai from multiple possible sources
      const tenggat = note.TenggalSelesai || order.TenggalSelesai || '';
      document.getElementById('editTenggalSelesai').value = tenggat ? (typeof tenggat === 'string' ? tenggat.split('T')[0] : tenggat) : '';

      document.getElementById('editProductType').value = note.ProductType || custom.Model || '';
      document.getElementById('editSize').value = note.Size || custom.Ukuran || '';
      document.getElementById('editColor').value = note.Color || custom.Warna || '';
      document.getElementById('editMaterial').value = note.Material || custom.JenisBahan || '';

      // Populate total price and deposit
      document.getElementById('editTotalHarga').value = order.TotalHarga || 0;
      document.getElementById('editDepositPaid').value = order.DepositPaid || 0;

      document.getElementById('editStatusOrder').value = order.StatusOrder || 'Tertunda';
      document.getElementById('editPrioritas').value = order.Prioritas || 'Normal';

      document.getElementById('editStyle').value = note.Style || '';
      document.getElementById('editCustomFeatures').value = note.CustomFeatures || '';
      document.getElementById('editSpecialRequirements').value = note.SpecialRequirements || '';

      console.log('Form filled successfully'); // Debug log

      // Tampilkan modal
      document.getElementById('editCustomModal').style.display = 'flex';
    }).catch(err => {
      console.error('Gagal mengambil data order:', err);
      alert('Gagal memuat data order. Refresh halaman dan coba lagi.');
    });
}


  function closeEditCustomModal() {
    document.getElementById('editCustomModal').style.display = 'none';
  }
</script>
</div>
