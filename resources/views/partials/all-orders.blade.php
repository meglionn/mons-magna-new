<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-lg font-semibold">Semua Pesanan</h2>
      <p class="text-gray-600">Kelola semua pesanan produksi dan pesanan custom</p>
    </div>
  </div>

  {{-- Filter --}}
  <div class="flex flex-col sm:flex-row gap-4">
    <div class="relative flex-1">
      <input 
        type="text" 
        placeholder="Cari pesanan..." 
        class="pl-10 w-full border border-gray-300 rounded-lg py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
      <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M9 17a8 8 0 100-16 8 8 0 000 16z" />
      </svg>
    </div>
    <div class="flex gap-2 overflow-x-auto">
      @foreach (['Semua','Tertunda','Dikonfirmasi','Dalam Produksi','Siap','Terkirim','Selesai'] as $filter)
        <button class="px-3 py-1 border rounded-lg text-sm hover:bg-gray-100">{{ $filter }}</button>
      @endforeach
    </div>
  </div>

  {{-- Tabel Semua Pesanan --}}
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left font-medium text-gray-600">No. Pesanan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tipe</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Pelanggan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Produk/Spesifikasi</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tenggat</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Prioritas</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Keterangan</th>
          <th class="px-4 py-2 text-center font-medium text-gray-600">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($orders as $order)
          <tr>
            <td class="px-4 py-2">#{{ $order->OrderID }}</td>
            <td class="px-4 py-2">
              <span class="px-2 py-1 text-xs rounded-full @if($order->orderDetails->count() > 0) bg-blue-100 text-blue-700 @else bg-purple-100 text-purple-700 @endif">
                {{ $order->orderDetails->count() > 0 ? 'Produksi' : 'Custom' }}
              </span>
            </td>
            <td class="px-4 py-2">{{ $order->customer?->Nama ?? 'N/A' }}</td>
            <td class="px-4 py-2 text-sm">
              @if($order->orderDetails->count() > 0)
                @if($order->orderDetails->first())
                  {{ $order->orderDetails->first()->product?->NamaProduk ?? 'Produk' }}
                  @php
                    $detail = $order->orderDetails->first();
                    $spesifikasi = $detail ? array_filter([
                      $detail->Ukuran,
                      $detail->Warna,
                      $detail->JenisBahan,
                    ]) : [];
                  @endphp
                  @if(count($spesifikasi) > 0)
                    ({{ implode(' / ', $spesifikasi) }})
                  @endif
                @else
                  -
                @endif
              @else
                @if($order->customDetail)
                  @php
                    $customData = json_decode($order->customDetail->CatatanTambahan, true) ?? [];
                  @endphp
                  {{ $customData['Size'] ?? $order->customDetail->Ukuran ?? '-' }} / 
                  {{ $customData['Color'] ?? $order->customDetail->Warna ?? '-' }} / 
                  {{ $customData['Material'] ?? $order->customDetail->JenisBahan ?? '-' }}
                @else
                  -
                @endif
              @endif
            </td>
            <td class="px-4 py-2">
              @if($order->customDetail)
                @php $customTgl = data_get(json_decode($order->customDetail->CatatanTambahan, true) ?? [], 'TenggalSelesai'); @endphp
                {{ $customTgl ? \Carbon\Carbon::parse($customTgl)->format('d/m/Y') : '-' }}
              @else
                {{ $order->produksi?->first()?->TanggalSelesai?->format('d/m/Y') ?? '-' }}
              @endif
            </td>            
            <td class="px-4 py-2">
              <span class="px-2 py-1 text-xs @if($order->Prioritas === 'Mendesak' || $order->Prioritas === 'Tinggi') bg-red-100 text-red-700 @elseif($order->Prioritas === 'Sedang') bg-orange-100 text-orange-700 @else bg-green-100 text-green-700 @endif rounded-lg">
                {{ $order->Prioritas ?? 'Normal' }}
              </span>
            </td>
            <td class="px-4 py-2">
              <span class="px-2 py-1 text-xs @if($order->StatusOrder === 'Tertunda') bg-yellow-100 text-yellow-700 @elseif($order->StatusOrder === 'Selesai') bg-green-100 text-green-700 @else bg-blue-100 text-blue-700 @endif rounded-lg">
                {{ $order->StatusOrder }}
              </span>
            </td>
            <td class="px-4 py-2 text-left">
              @if($order->customDetail)
                @php $customData = json_decode($order->customDetail->CatatanTambahan, true) ?? []; @endphp
                {{ \Illuminate\Support\Str::limit($customData['CustomFeatures'] ?? $customData['SpecialRequirements'] ?? $order->customDetail->Model ?? '-', 80) }}
              @else
                {{ \Illuminate\Support\Str::limit($order->produksi?->first()?->Keterangan ?? '-', 80) }}
              @endif
            </td>
            <td class="px-4 py-2 text-center">
              <div class="flex justify-center gap-2">
                <button 
                  @click="openEditAllModal({{ $order->OrderID }})"
                  class="text-blue-600 hover:text-blue-800 text-lg"
                  title="Edit">
                  ✏️
                </button>
                <form action="{{ route('order.destroy', $order->OrderID) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-red-600 hover:text-red-800 text-lg" title="Hapus">🗑️</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
              Belum ada data pesanan
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Edit Order Modal --}}
  <div id="editAllOrderModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 50; flex-direction: column; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 24px; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto;">
      <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Edit Pesanan</h3>
      <form id="editAllOrderForm" method="POST">
        @csrf
        @method('PUT')
        
        {{-- Common Fields --}}
        <div style="margin-bottom: 16px;">
          <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Nama Pelanggan</label>
          <input type="text" id="editCustomerName" name="CustomerName" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Tanggal</label>
            <input type="date" id="editTanggal" name="Tanggal" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          </div>
          <div>
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Tenggat Selesai</label>
            <input type="date" id="editTenggalSelesai" name="TenggalSelesai" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          </div>
        </div>

        {{-- Production-specific Fields --}}
        <div id="productionFields" style="display: none;">
          <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Nama Produk</label>
            <input type="text" id="editProductName" name="ProductName" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
              <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Tanggal Mulai Produksi</label>
              <input type="date" id="editTanggalMulai" name="TanggalMulai" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
            </div>
            <div>
              <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Jumlah</label>
              <input type="number" id="editJumlah" name="Jumlah" min="1" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
              <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Status Order</label>
              <select id="editStatusOrder" name="StatusOrder" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          <option value="Pending">Pending</option>
          <option value="Proses">Dalam Proses</option>
          <option value="Produksi">Produksi</option>
          <option value="Selesai">Selesai</option>
          <option value="Ditunda">Ditunda</option>
              </select>
            </div>
            <div>
              <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Status Produksi</label>
              <select id="editStatusProduksi" name="StatusProduksi" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
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

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
              <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Size</label>
              <input type="text" id="editSize" name="Size" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
            </div>
            <div>
              <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Color</label>
              <input type="text" id="editColor" name="Color" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
            </div>
            <div>
              <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Material</label>
              <input type="text" id="editMaterial" name="Material" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
            </div>
          </div>

          <!-- Total Harga removed per request -->

          <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Keterangan</label>
            <textarea id="editKeterangan" name="Keterangan" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px; min-height: 80px;"></textarea>
          </div>
        </div>

        {{-- Custom-specific Fields --}}
        <div id="customFields" style="display: none;">
          <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Product Type</label>
            <input type="text" id="editProductType" name="ProductType" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
              <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Size</label>
              <input type="text" id="editCustomSize" name="Size" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
            </div>
            <div>
              <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Color</label>
              <input type="text" id="editCustomColor" name="Color" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
            </div>
            <div>
              <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Material</label>
              <input type="text" id="editCustomMaterial" name="Material" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
              <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Status</label>
              <select id="editCustomStatus" name="StatusOrder" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
                <option value="Tertunda">Tertunda</option>
                <option value="Dikonfirmasi">Dikonfirmasi</option>
                <option value="Dalam Produksi">Dalam Produksi</option>
                <option value="Siap">Siap</option>
                <option value="Terkirim">Terkirim</option>
              </select>
            </div>
            <div>
              <!-- Total Harga removed per request -->
            </div>
          </div>

          <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Deposit Paid (IDR)</label>
            <input type="number" id="editDepositPaid" name="DepositPaid" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          </div>

          <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Style</label>
            <input type="text" id="editStyle" name="Style" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          </div>

          <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Custom Features</label>
            <textarea id="editCustomFeatures" name="CustomFeatures" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px; min-height: 60px;"></textarea>
          </div>

          <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Special Requirements</label>
            <textarea id="editSpecialRequirements" name="SpecialRequirements" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px; min-height: 60px;"></textarea>
          </div>
        </div>

        {{-- Common Fields for both --}}
        <div style="margin-bottom: 16px;">
          <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Prioritas</label>
          <select id="editPrioritas" name="Prioritas" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
            <option value="Rendah">Rendah</option>
            <option value="Normal">Normal</option>
            <option value="Sedang">Sedang</option>
            <option value="Tinggi">Tinggi</option>
            <option value="Mendesak">Mendesak</option>
          </select>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
          <button type="button" onclick="closeEditAllOrderModal()" style="padding: 8px 16px; background: #d1d5db; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
          <button type="submit" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openEditAllModal(orderId) {
      fetch('/debug/orders').then(r => r.json()).then(orders => {
        const order = orders.find(o => o.OrderID === orderId);
        if (order) {
          const form = document.getElementById('editAllOrderForm');
          form.action = '/pesanan/' + orderId;

          // Fill common fields
          document.getElementById('editCustomerName').value = order.customer?.Nama || '';
          document.getElementById('editTanggal').value = order.Tanggal ? order.Tanggal.split('T')[0] : '';
          document.getElementById('editPrioritas').value = order.Prioritas || 'Normal';

          if (order.customDetail) {
            // Custom order
            document.getElementById('productionFields').style.display = 'none';
            document.getElementById('customFields').style.display = 'block';

            const customData = JSON.parse(order.customDetail?.CatatanTambahan || '{}');
            document.getElementById('editProductType').value = order.customDetail?.Model || customData?.ProductType || '';
            document.getElementById('editCustomSize').value = order.customDetail?.Ukuran || customData?.Size || '';
            document.getElementById('editCustomColor').value = order.customDetail?.Warna || customData?.Color || '';
            document.getElementById('editCustomMaterial').value = order.customDetail?.JenisBahan || customData?.Material || '';
            // TotalHarga editing removed; skip setting editCustomTotalHarga
            document.getElementById('editDepositPaid').value = order.DepositPaid || 0;
            document.getElementById('editStyle').value = customData?.Style || '';
            document.getElementById('editCustomFeatures').value = customData?.CustomFeatures || '';
            document.getElementById('editSpecialRequirements').value = customData?.SpecialRequirements || '';
            document.getElementById('editTenggalSelesai').value = customData?.TenggalSelesai ? customData.TenggalSelesai.split('T')[0] : '';
            document.getElementById('editCustomStatus').value = order.StatusOrder || 'Tertunda';
          } else { 
            // Production order
            document.getElementById('productionFields').style.display = 'block';
            document.getElementById('customFields').style.display = 'none';

            const produksi = order.produksi && order.produksi.length > 0 ? order.produksi[0] : {};
            const orderDetail = order.orderDetails && order.orderDetails.length > 0 ? order.orderDetails[0] : {};
            const product = orderDetail.product || {};

            document.getElementById('editProductName').value = product?.NamaProduk || '';
            document.getElementById('editTanggalMulai').value = produksi?.TanggalMulai ? produksi.TanggalMulai.split('T')[0] : '';
            document.getElementById('editTenggalSelesai').value = produksi?.TanggalSelesai ? produksi.TanggalSelesai.split('T')[0] : '';
            document.getElementById('editJumlah').value = orderDetail?.Jumlah || 1;
            document.getElementById('editKeterangan').value = produksi?.Keterangan || '';
            document.getElementById('editStatusOrder').value = order.StatusOrder || 'Pending';
            document.getElementById('editStatusProduksi').value = produksi?.StatusProduksi || 'Pending';
            document.getElementById('editSize').value = product?.Ukuran || '';
            document.getElementById('editColor').value = '';
            document.getElementById('editMaterial').value = '';
          }

          document.getElementById('editAllOrderModal').style.display = 'flex';
        }
      });
    }

    function closeEditAllOrderModal() {
      document.getElementById('editAllOrderModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('editAllOrderModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeEditAllOrderModal();
      }
    });
  </script>
</div>
