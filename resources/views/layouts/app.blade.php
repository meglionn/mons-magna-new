<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mons Magna - @yield('title', 'Dashboard')</title>
    
    {{-- Temporary: Use CDN instead of Vite --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Production: Uncomment this when deploying --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    
    <style>[x-cloak] { display: none !important; }</style>
    <style>
    .nav-tabs {
      display: flex;
      gap: 16px;
      background: #f4f4f6;
      padding: 8px 16px;
      border-radius: 12px;
      width: fit-content;
      margin: 20px auto;
    }
    .nav-tabs a {
      text-decoration: none;
      color: #222;
      font-weight: 600;
      padding: 6px 12px;
      border-radius: 10px;
      transition: 0.2s;
    }

    .nav-tabs a.disabled {
      pointer-events: none;
      opacity: 0.6;
      cursor: default;
    }
  
    .nav-tabs a:hover:not(.disabled) {
      background: #eaeaea;
    }
  </style>
</head>
<body class="antialiased bg-gray-50">
  @if (!request()->is('/') && !request()->is('login') && !request()->is('register'))
    <div class="nav-tabs">
        <a href="{{ route('order') }}" class="{{ request()->is('pesanan*') ? 'active disabled' : '' }}">Pesanan</a>
        <a href="{{ route('inventorymaterial') }}" class="{{ request()->is('inventory*') ? 'active disabled' : '' }}">Inventori Bahan</a>
        <a href="{{ route('financial') }}" class="{{ request()->is('keuangan*') ? 'active disabled' : '' }}">Keuangan</a>
        <a href="{{ route('laporan') }}" class="{{ request()->is('laporan*') ? 'active disabled' : '' }}">Laporan</a>
    </div>
  @endif

  <div class="container mx-auto px-4">
    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @yield('content')
  </div>
</body>
</html>