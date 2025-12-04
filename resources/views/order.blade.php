@extends('layouts.app')

@section('content')
<div x-data="{ 
  tab: 'all', 
  showProductionDialog: false, 
  showCustomDialog: false 
}" class="space-y-6 p-6">

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
      $statsData = [
        ['label' => 'Total Semua Pesanan', 'value' => $stats['totalOrders'] ?? 0, 'color' => 'text-gray-600', 'icon' => '📦'],
        ['label' => 'Pesanan Produksi', 'value' => $stats['productionOrders'] ?? 0, 'color' => 'text-blue-600', 'icon' => '⚙️'],
        ['label' => 'Pesanan Custom', 'value' => $stats['customOrders'] ?? 0, 'color' => 'text-purple-600', 'icon' => '👟'],
        ['label' => 'Pesanan Aktif', 'value' => $stats['activeOrders'] ?? 0, 'color' => 'text-green-600', 'icon' => '🟢'],
        ['label' => 'Selesai', 'value' => $stats['completed'] ?? 0, 'color' => 'text-gray-600', 'icon' => '✅'],
      ];
    @endphp

    @foreach ($statsData as $stat)
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
  <div class="space-y-4">
    <div class="flex border-b">
      <button 
        @click="tab = 'all'" 
        :class="tab === 'all' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-600'" 
        class="px-4 py-2 font-medium transition">
        <span class="inline-flex items-center gap-2">📋 Semua Pesanan</span>
      </button>
      <button 
        @click="tab = 'production'" 
        :class="tab === 'production' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'" 
        class="px-4 py-2 font-medium transition">
        <span class="inline-flex items-center gap-2">📦 Pesanan Produksi</span>
      </button>
      <button 
        @click="tab = 'custom'" 
        :class="tab === 'custom' ? 'border-b-2 border-purple-600 text-purple-600' : 'text-gray-600'" 
        class="px-4 py-2 font-medium transition">
        <span class="inline-flex items-center gap-2">👟 Pesanan Custom</span>
      </button>
    </div>

    {{-- All Orders Tab --}}
    <div x-show="tab === 'all'" class="space-y-4">
      @include('partials.all-orders')
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

  {{-- Include Dialogs --}}
  @include('partials.productionorderdialog')
  @include('partials.customorderdialog')
</div>
@endsection