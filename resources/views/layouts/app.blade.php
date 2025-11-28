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
    @php $user = auth()->user(); @endphp
    <div class="nav-tabs">
        @if($user && in_array($user->Role, ['Owner','Admin','Produksi']))
            <a href="{{ route('order') }}" class="{{ request()->is('pesanan*') ? 'active disabled' : '' }}">Pesanan</a>
        @endif

        @if($user && in_array($user->Role, ['Admin','Produksi']))
            <a href="{{ route('inventorymaterial') }}" class="{{ request()->is('inventory*') ? 'active disabled' : '' }}">Inventori Bahan</a>
        @endif

        @if($user && $user->Role === 'Keuangan')
            <a href="{{ route('financial') }}" class="{{ request()->is('keuangan*') ? 'active disabled' : '' }}">Keuangan</a>
        @endif

        @if($user && in_array($user->Role, ['Owner','Admin','Produksi','Keuangan']))
            <a href="{{ route('laporan') }}" class="{{ request()->is('laporan*') ? 'active disabled' : '' }}">Laporan</a>
        @endif

        {{-- Show user info and logout --}}
        @if($user)
          <div class="ml-4 flex items-center">
            <span class="text-sm text-gray-700 mr-3">{{ $user->NamaLengkap ?? $user->Username }} ({{ $user->Role }})</span>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="text-sm text-red-600 hover:underline">Logout</button>
            </form>
          </div>
        @endif
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