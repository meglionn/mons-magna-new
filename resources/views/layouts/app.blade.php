<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mons Magna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
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
  
    .nav-tabs a:hover {
      background: #eaeaea;
    }
  </style>
</head>
<body class="antialiased">
  @if (!request()->is('/') && !request()->is('login') && !request()->is('register'))
    <div class="nav-tabs">
        <a href="{{ route('order') }}" class="{{ request()->is('pesanan*') ? 'active disabled' : '' }}">Pesanan</a>
        <a href="{{ route('inventorymaterial') }}" class="{{ request()->is('inventory*') ? 'active disabled' : '' }}">Inventori Bahan</a>
        <a href="{{ route('financial') }}" class="{{ request()->is('keuangan*') ? 'active disabled' : '' }}">Keuangan</a>
        <a href="{{ route('laporan') }}" class="{{ request()->is('laporan*') ? 'active disabled' : '' }}">Laporan</a>
    </div>
  @endif

  <div class="container">
    @yield('content')
</body>
</html>