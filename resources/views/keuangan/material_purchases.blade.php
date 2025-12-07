@extends('layouts.app')

@section('content')
<div class="p-6">
  <h2 class="text-2xl font-semibold mb-4">Riwayat Pembelian Material</h2>

  <div class="mb-4">
    <form action="{{ route('materialpurchase.store') }}" method="POST" class="grid grid-cols-2 gap-4 md:grid-cols-4">
      @csrf
      <input type="hidden" name="Tanggal" value="{{ now() }}">
      <div>
        <label class="text-sm">Material</label>
        <select name="MaterialID" required class="w-full border rounded p-2">
          <option value="">Pilih material</option>
          @foreach(\App\Models\Material::all() as $m)
          <option value="{{ $m->MaterialID }}">{{ $m->NamaBahan }} (ID: {{ $m->MaterialID }})</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-sm">Jumlah</label>
        <input type="number" name="Jumlah" min="1" required class="w-full border rounded p-2">
      </div>
      <div>
        <label class="text-sm">Harga Satuan (IDR)</label>
        <input type="number" name="HargaSatuan" min="0" step="0.01" required class="w-full border rounded p-2">
      </div>
      <div>
        <label class="text-sm">Supplier</label>
        <input type="text" name="Supplier" class="w-full border rounded p-2">
      </div>
      <div class="md:col-span-4 text-right">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Catat Pembelian & Update Stok</button>
      </div>
    </form>
  </div>

  <div class="bg-white border rounded shadow">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left">Tanggal</th>
          <th class="px-4 py-2 text-left">Material</th>
          <th class="px-4 py-2 text-right">Jumlah</th>
          <th class="px-4 py-2 text-right">Harga Satuan</th>
          <th class="px-4 py-2 text-right">Total</th>
          <th class="px-4 py-2 text-left">Supplier</th>
        </tr>
      </thead>
      <tbody>
        @foreach($purchases as $p)
        <tr class="border-t">
          <td class="px-4 py-2">{{ $p->Tanggal }}</td>
          <td class="px-4 py-2">{{ $p->material->NamaBahan ?? '—' }}</td>
          <td class="px-4 py-2 text-right">{{ number_format($p->Jumlah) }}</td>
          <td class="px-4 py-2 text-right">IDR {{ number_format($p->HargaSatuan) }}</td>
          <td class="px-4 py-2 text-right">IDR {{ number_format($p->Total) }}</td>
          <td class="px-4 py-2">{{ $p->Supplier }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
