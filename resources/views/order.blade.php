@extends('layouts.app')

@section('content')
<div class="space-y-6 p-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-2xl font-semibold">Manajemen Pesanan</h2>
      <p class="text-gray-600">Kelola pesanan produksi dan pesanan sepatu custom</p>
    </div>
  </div>

  {{-- Stats Cards --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
    @php
      $stats = [
        ['label' => 'Total Pesanan', 'value' => 0, 'color' => 'text-gray-600', 'icon' => '📦'],
        ['label' => 'Pesanan Produksi', 'value' => 0, 'color' => 'text-blue-600', 'icon' => '⚙️'],
        ['label' => 'Pesanan Custom', 'value' => 0, 'color' => 'text-purple-600', 'icon' => '👟'],
        ['label' => 'Pesanan Aktif', 'value' => 0, 'color' => 'text-green-600', 'icon' => '🟢'],
        ['label' => 'Selesai', 'value' => 0, 'color' => 'text-gray-600', 'icon' => '✅'],
      ];
    @endphp

    @foreach ($stats as $stat)
      <div class="border rounded-xl p-4 shadow-sm bg-white">
        <div class="flex justify-between items-center mb-2">
          <p class="text-sm font-medium">{{ $stat['label'] }}</p>
          <span class="{{ $stat['color'] }}">{{ $stat['icon'] }}</span>
        </div>
        <div class="text-2xl font-semibold">{{ $stat['value'] }}</div>
        <p class="text-xs text-gray-500 mt-1">Data statis</p>
      </div>
    @endforeach
  </div>

  {{-- Tabs --}}
  <div x-data="{ tab: 'production' }" class="space-y-4">
    <div class="flex border-b">
      <button 
        @click="tab = 'production'" 
        :class="tab === 'production' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'" 
        class="px-4 py-2 font-medium transition">
        <span class="inline-flex items-center"><span class="mr-2">📦</span> Pesanan Produksi</span>
      </button>
      <button 
        @click="tab = 'custom'" 
        :class="tab === 'custom' ? 'border-b-2 border-purple-600 text-purple-600' : 'text-gray-600'" 
        class="px-4 py-2 font-medium transition">
        <span class="inline-flex items-center"><span class="mr-2">👟</span> Pesanan Custom</span>
      </button>
    </div>

    {{-- Production Tab --}}
    <div x-show="tab === 'production'" class="space-y-4">
      @include('partials.production-tracking')
    </div>

    {{-- Custom Tab --}}
    <div x-show="tab === 'custom'" class="space-y-4">
      @include('partials.custom-orders')
    </div>
  </div>
</div>
@endsection
