@extends('layouts.app')

@section('content')
<div x-data="inventoryData" class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-2xl font-semibold" data-cy="inventory-title">Inventori Bahan</h2>
      <p class="text-gray-600 text-sm">Kelola bahan baku untuk produksi sepatu</p>
    </div>

    <button @click="editMode = false; showMaterialDialog = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
      + Tambah Bahan
    </button>
    
    {{-- Modal Tambah Bahan --}}
    @include('inventorydialog')
  </div>

  {{-- Statistik --}}
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white border rounded-lg p-4 shadow-sm">
      <div class="flex justify-between items-center mb-2">
        <p class="text-sm text-gray-600">Total Bahan</p>
        <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2h-2.586a1 1 0 01-.707-.293l-1.414-1.414A1 1 0 0012.586 3H10a2 2 0 00-2 2v16h10a2 2 0 002-2v-6z" />
        </svg>
      </div>
      <p class="text-2xl font-semibold">{{ $stats['totalMaterials'] }}</p>
      <p class="text-xs text-gray-500 mt-1">Jenis bahan</p>
    </div>

    <div class="bg-white border rounded-lg p-4 shadow-sm">
      <div class="flex justify-between items-center mb-2">
        <p class="text-sm text-gray-600">Total Inventori</p>
        <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M9 3v18m6-18v18M3 9h18M3 15h18" />
        </svg>
      </div>
      <p class="text-2xl font-semibold">{{ number_format($stats['totalInventory']) }}</p>
      <p class="text-xs text-gray-500 mt-1">Total unit</p>
    </div>

    <div class="bg-white border rounded-lg p-4 shadow-sm">
      <div class="flex justify-between items-center mb-2">
        <p class="text-sm text-gray-600">Nilai Inventori</p>
        <svg class="w-5 h-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.333-1-4-1-4 2s2.667 3 4 4 4 1 4-2m0 0v1m0-1V7m-4 5v1m0-1V7" />
        </svg>
      </div>
      <p class="text-xl font-semibold">IDR {{ number_format($stats['totalValue']) }}</p>
      <p class="text-xs text-gray-500 mt-1">Total nilai</p>
    </div>

    <div class="bg-white border rounded-lg p-4 shadow-sm">
      <div class="flex justify-between items-center mb-2">
        <p class="text-sm text-gray-600">Peringatan Stok Rendah</p>
        <svg class="w-5 h-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4m0 4h.01" />
        </svg>
      </div>
      <p class="text-2xl font-semibold">{{ $stats['lowStock'] }}</p>
      <p class="text-xs text-gray-500 mt-1">Perlu di-restock</p>
    </div>
  </div>

  {{-- Alert Low Stock --}}
  @if($stats['lowStock'] > 0)
  <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.662 1.732-3L13.732 4c-.77-1.338-2.694-1.338-3.464 0L3.34 16c-.77 1.338.192 3 1.732 3z"/>
    </svg>
    <p><strong>Peringatan:</strong> Ada {{ $stats['lowStock'] }} bahan dengan stok rendah yang perlu di-restock.</p>
  </div>
  @endif

  {{-- Filter & Pencarian --}}
  <div class="flex flex-col md:flex-row gap-4">
    <div class="relative flex-1">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
      </svg>
      <input type="text" class="w-full border rounded-lg pl-10 p-2 focus:ring-2 focus:ring-blue-500" placeholder="Cari bahan...">
    </div>
  </div>

  {{-- Tabel Bahan --}}
  <div class="bg-white border rounded-lg shadow-sm overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Nama Bahan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Kategori</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Jenis Bahan</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Stok</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Min. Stok</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Harga Satuan</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
          <th class="px-4 py-2 text-center font-medium text-gray-600">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($materials as $material)
        <tr class="border-t hover:bg-gray-50">
          <td class="px-4 py-2 font-medium">{{ $material->NamaBahan }}</td>
          <td class="px-4 py-2">{{ $material->Kategori ?? '-' }}</td>
          <td class="px-4 py-2">{{ $material->JenisBahan ?? '-' }}</td>
          <td class="px-4 py-2 text-right">{{ number_format($material->StokBahan) }}</td>
          <td class="px-4 py-2 text-right">{{ number_format($material->MinimumStok) }}</td>
          <td class="px-4 py-2 text-right">IDR {{ number_format($material->HargaSatuan ?? 0) }}</td>
          <td class="px-4 py-2">
            @if($material->StokBahan <= $material->MinimumStok)
              <span class="text-red-700 bg-red-100 px-2 py-1 rounded text-xs">Stok Rendah</span>
            @else
              <span class="text-green-700 bg-green-100 px-2 py-1 rounded text-xs">Tersedia</span>
            @endif
          </td>
          <td class="px-4 py-2 text-right">
            <div class="flex justify-end gap-2">
              <button 
                @click="openEditModal({{ $material->MaterialID }}, {{ json_encode($material) }})" 
                class="text-blue-600 hover:text-blue-800 px-2">✏️</button>
              <form action="{{ route('inventorymaterial.destroy', $material->MaterialID) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 px-2">🗑️</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="px-4 py-8 text-center text-gray-500">
            Belum ada data material. Klik tombol "Tambah Bahan" untuk menambahkan.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('inventoryData', () => ({
    showMaterialDialog: false,
    editMode: false,
    editingMaterialId: null,
    formData: {
      NamaBahan: '',
      Kategori: '',
      JenisBahan: '',
      StokBahan: 0,
      HargaSatuan: 0,
      TotalNilaiInventori: 0,
      MinimumStok: 10,
      jumlahBeli: 0,
      hargaBeliTerbaru: 0,
    },
    calculatedStock: 0,
    calculatedPrice: 0,
    calculatedTotalValue: 0,

    formatCurrency(value) {
      return new Intl.NumberFormat('id-ID').format(Math.round(value || 0));
    },

    updateCalculations() {
      const oldStok = Number(this.formData.StokBahan) || 0;
      // ensure we treat TotalNilaiInventori as a number (in case it's a formatted string)
      const oldTotalValue = Number(String(this.formData.TotalNilaiInventori).replace(/[^0-9.-]+/g, '')) || 0;
      const jumlahBeli = Number(this.formData.jumlahBeli) || 0;
      const hargaBaru = Number(this.formData.hargaBeliTerbaru) || 0;

      if (jumlahBeli === 0 || hargaBaru === 0) {
        this.calculatedStock = oldStok;
        this.calculatedPrice = this.formData.HargaSatuan;
        this.calculatedTotalValue = oldTotalValue;
        return;
      }

      const nilaiBeliTerbaru = jumlahBeli * hargaBaru;
      this.calculatedStock = oldStok + jumlahBeli;
      // new total value is old total value plus newly purchased value
      this.calculatedTotalValue = oldTotalValue + nilaiBeliTerbaru;
      // average price MUST be new total value divided by new total stock
      this.calculatedPrice = this.calculatedStock > 0 ? (this.calculatedTotalValue / this.calculatedStock) : 0;
    },

    openEditModal(materialId, materialData) {
      this.editingMaterialId = materialId;
      this.editMode = true;
      this.formData = {
        NamaBahan: materialData.NamaBahan,
        Kategori: materialData.Kategori,
        JenisBahan: materialData.JenisBahan,
        StokBahan: materialData.StokBahan,
        HargaSatuan: materialData.HargaSatuan,
        TotalNilaiInventori: materialData.TotalNilaiInventori || (materialData.StokBahan * materialData.HargaSatuan),
        MinimumStok: materialData.MinimumStok,
        jumlahBeli: 0,
        hargaBeliTerbaru: 0,
      };
      this.calculatedStock = Number(materialData.StokBahan) || 0;
      this.calculatedPrice = Number(materialData.HargaSatuan) || 0;
      // ensure TotalNilaiInventori is numeric; fallback to stock * unit price
      const rawTotal = materialData.TotalNilaiInventori;
      this.calculatedTotalValue = (typeof rawTotal === 'number' && !isNaN(rawTotal)) ? rawTotal : (this.calculatedStock * this.calculatedPrice);
      this.showMaterialDialog = true;
    },

    handleFormSubmit(e) {
      const form = e.target;
      if (this.editMode) {
        form.action = '{{ route('inventorymaterial.update', ':material') }}'.replace(':material', this.editingMaterialId);
        form.method = 'POST';
        
        // Add hidden method for PUT
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
      }
      form.submit();
    }
  }));
});
</script>
@endsection
