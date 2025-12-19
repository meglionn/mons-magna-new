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
          <th class="px-4 py-2 text-left font-medium text-gray-600">Spesifikasi</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Total Harga</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tahap</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Tenggat</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Prioritas</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
          <th class="px-4 py-2 text-center font-medium text-gray-600">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($orders as $order)
          @if($order->orderDetails->count() > 0)
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
              <td class="px-4 py-2">
                @php
                  $detail = $order->orderDetails->first();
                  $spesifikasi = $detail ? array_filter([
                    $detail->Ukuran,
                    $detail->Warna,
                    $detail->JenisBahan,
                  ]) : [];
                @endphp
                {{ count($spesifikasi) > 0 ? implode(' / ', $spesifikasi) : '-' }}
              </td>
              @php
                $calculatedTotal = $order->produksi?->first()?->TotalHarga ?? $order->TotalHarga ?? $order->orderDetails->sum('Subtotal') ?? 0;
              @endphp
              <td class="px-4 py-2">IDR {{ number_format($calculatedTotal, 0, ',', '.') }}</td>
              <td class="px-4 py-2">{{ $order->produksi?->first()?->StatusProduksi ?? '-' }}</td>
              <td class="px-4 py-2">{{ $order->produksi?->first()?->TanggalSelesai?->format('d/m/Y') ?? '-' }}</td>
              <td class="px-4 py-2">
                <span class="px-2 py-1 text-xs @if($order->Prioritas === 'Mendesak' || $order->Prioritas === 'Tinggi') bg-red-100 text-red-700 @elseif($order->Prioritas === 'Sedang') bg-orange-100 text-orange-700 @else bg-green-100 text-green-700 @endif rounded-lg">
                  {{ $order->Prioritas ?? 'Normal' }}
                </span>
              </td>
              <td class="px-4 py-2">
                <span class="px-2 py-1 text-xs @if($order->StatusOrder === 'Tertunda' || $order->StatusOrder === 'Pending') bg-yellow-100 text-yellow-700 @elseif($order->StatusOrder === 'Selesai') bg-green-100 text-green-700 @else bg-blue-100 text-blue-700 @endif rounded-lg">
                  {{ $order->StatusOrder }}
                </span>
              </td>
              <td class="px-4 py-2 text-right">
                <div class="flex justify-end gap-2">
                  <button 
                    onclick="openEditProductionModal({{ $order->OrderID }})"
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
            <td colspan="11" class="px-4 py-8 text-center text-gray-500">
              Belum ada data pesanan produksi
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Edit Production Order Modal --}}
  <div id="editProductionModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 24px; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto;">
      <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Edit Pesanan Produksi</h3>
      <form id="editProductionForm" method="POST">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 16px;">
          <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Nama Pelanggan *</label>
          <input type="text" id="editProdCustomerName" name="CustomerName" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 16px;">
          <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Nama Produk *</label>
          <select id="editProdProductID" name="ProductID" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
            <option value="">Pilih produk</option>
            @foreach($products as $product)
              <option value="{{ $product->ProductID }}">{{ $product->NamaProduk }} - IDR {{ number_format($product->Harga) }}</option>
            @endforeach
            <option value="__other">Lainnya...</option>
          </select>

          <input type="text" id="editProdProductName" name="ProductName" style="display:none; width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px; margin-top:8px;" placeholder="Nama produk baru atau custom">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Tanggal Pesanan *</label>
            <input type="date" id="editProdTanggal" name="Tanggal" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          </div>
          <div>
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Jumlah *</label>
            <input type="number" id="editProdJumlah" name="Jumlah" required min="1" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Total Harga (IDR) *</label>
            <input type="number" id="editProdTotalHarga" name="TotalHarga" required min="0" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          </div>
          <div>
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Ukuran *</label>
            <select id="editProdUkuran" name="Ukuran" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
              <option value="36">36</option>
              <option value="37">37</option>
              <option value="38">38</option>
              <option value="39">39</option>
              <option value="40">40</option>
              <option value="41">41</option>
              <option value="42">42</option>
              <option value="43">43</option>
              <option value="44">44</option>
              <option value="45">45</option>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Tanggal Mulai Produksi *</label>
            <input type="date" id="editProdTanggalMulai" name="TanggalMulai" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          </div>
          <div>
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Target Selesai</label>
            <input type="date" id="editProdTenggalSelesai" name="TenggalSelesai" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Status Order *</label>
            <select id="editProdStatusOrder" name="StatusOrder" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
              <option value="Pending">Pending</option>
              <option value="Proses">Dalam Proses</option>
              <option value="Produksi">Produksi</option>
              <option value="Selesai">Selesai</option>
              <option value="Ditunda">Ditunda</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Status Produksi *</label>
            <select id="editProdStatusProduksi" name="StatusProduksi" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
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

        <div style="margin-bottom: 16px;">
          <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Prioritas *</label>
          <select id="editProdPrioritas" name="Prioritas" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
            <option value="Rendah">Rendah</option>
            <option value="Sedang">Sedang</option>
            <option value="Tinggi">Tinggi</option>
            <option value="Mendesak">Mendesak</option>
          </select>
        </div>

        <div style="margin-bottom: 16px;">
          <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Catatan</label>
          <textarea id="editProdKeterangan" name="Keterangan" rows="3" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;"></textarea>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
          <button type="button" onclick="closeEditProductionModal()" style="padding: 8px 16px; background: #d1d5db; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
          <button type="submit" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan</button>
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

          const form = document.getElementById('editProductionForm');
          form.action = '/pesanan/' + orderId;

          document.getElementById('editProdCustomerName').value = order.customer?.Nama || '';

          // Populate product select + fallback name input
          const prodSelect = document.getElementById('editProdProductID');
          const prodNameInput = document.getElementById('editProdProductName');

          if (product?.ProductID) {
            prodSelect.value = product.ProductID;
            prodNameInput.style.display = 'none';
            prodNameInput.removeAttribute('required');
            prodNameInput.value = '';
          } else if (product?.NamaProduk) {
            // product exists in payload only by name
            prodSelect.value = '__other';
            prodNameInput.style.display = 'block';
            prodNameInput.value = product.NamaProduk;
            prodNameInput.setAttribute('required', 'required');
          } else {
            prodSelect.value = '';
            prodNameInput.style.display = 'none';
            prodNameInput.value = '';
            prodNameInput.removeAttribute('required');
          }

          document.getElementById('editProdTanggal').value = order.Tanggal ? order.Tanggal.split('T')[0] : '';
          document.getElementById('editProdJumlah').value = orderDetail?.Jumlah || 1;
          document.getElementById('editProdTotalHarga').value = order.TotalHarga || orderDetail?.Subtotal || 0;
          document.getElementById('editProdUkuran').value = product?.Ukuran || orderDetail?.Ukuran || '37';
          document.getElementById('editProdTanggalMulai').value = produksi?.TanggalMulai ? produksi.TanggalMulai.split('T')[0] : '';
          document.getElementById('editProdTenggalSelesai').value = produksi?.TanggalSelesai ? produksi.TanggalSelesai.split('T')[0] : '';
          document.getElementById('editProdStatusOrder').value = order.StatusOrder || 'Pending';
          document.getElementById('editProdStatusProduksi').value = produksi?.StatusProduksi || 'Pending';
          document.getElementById('editProdPrioritas').value = order.Prioritas || 'Sedang';
          document.getElementById('editProdKeterangan').value = produksi?.Keterangan || '';

          document.getElementById('editProductionModal').style.display = 'flex';
        }
      });
    }

    function closeEditProductionModal() {
      document.getElementById('editProductionModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('editProductionModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeEditProductionModal();
      }
    });

    // Toggle showing product name input when user selects 'Lainnya...'
    function toggleEditProductName(selectEl) {
      const nameInput = document.getElementById('editProdProductName');
      if (selectEl.value === '__other') {
        nameInput.style.display = 'block';
        nameInput.setAttribute('required', 'required');
      } else {
        nameInput.style.display = 'none';
        nameInput.value = '';
        nameInput.removeAttribute('required');
      }
    }

    // Wire change event for the product select (in case of manual changes)
    document.addEventListener('DOMContentLoaded', function() {
      const prodSelect = document.getElementById('editProdProductID');
      if (prodSelect) prodSelect.addEventListener('change', function() { toggleEditProductName(this); });
    });
  </script>
</div>