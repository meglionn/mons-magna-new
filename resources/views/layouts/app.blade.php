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
          <div class="ml-4 flex items-center gap-2">
            <span class="text-sm text-gray-700">{{ $user->NamaLengkap ?? $user->Username }} ({{ $user->Role }})</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
              @csrf
              <button type="submit" class="text-sm text-red-600 hover:underline">Logout</button>
            </form>
            <button type="button" onclick="openDeleteAccountModal()" class="text-sm text-red-600 hover:underline">Delete Account</button>
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

  {{-- Delete Account Modal --}}
  <div id="deleteAccountModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; flex-items: center; justify-content: center; align-items: center;">
    <div style="background: white; padding: 32px; border-radius: 12px; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
      <h2 style="font-size: 20px; font-weight: bold; margin-bottom: 16px; color: #dc2626;">Hapus Akun</h2>
      <p style="color: #666; margin-bottom: 16px;">Ini akan menghapus akun Anda secara permanen beserta semua data pesanan dan pelanggan. Tindakan ini tidak dapat dibatalkan.</p>
      
      <form id="deleteAccountForm" method="POST" action="{{ route('account.delete') }}">
        @csrf
        @method('DELETE')
        
        <div style="margin-bottom: 16px;">
          <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Masukkan Password Anda untuk Konfirmasi:</label>
          <input type="password" id="deletePassword" name="password" placeholder="Password" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
          <button type="button" onclick="closeDeleteAccountModal()" style="padding: 8px 16px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
          <button type="submit" style="padding: 8px 16px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Hapus Akun</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openDeleteAccountModal() {
      document.getElementById('deleteAccountModal').style.display = 'flex';
    }

    function closeDeleteAccountModal() {
      document.getElementById('deleteAccountModal').style.display = 'none';
      document.getElementById('deletePassword').value = '';
    }

    // Close modal when clicking outside
    document.getElementById('deleteAccountModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeDeleteAccountModal();
      }
    });
  </script>
</body>
</html>