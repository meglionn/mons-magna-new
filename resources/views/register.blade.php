@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full space-y-8">
    <div class="text-center">
      <div class="flex justify-center mb-4">
        <div class="bg-white p-4 rounded-xl shadow">
          <img src="{{ asset('images/logo.png') }}" alt="Mons Magna" class="h-12 w-12">
        </div>
      </div>
      <h2 class="mt-2 text-center text-2xl font-extrabold text-gray-900">Buat akun baru</h2>
      <p class="mt-2 text-center text-sm text-gray-600">Sudah punya akun? <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:underline">Masuk</a></p>
    </div>

    <form class="mt-8 space-y-6 bg-white p-8 rounded-2xl shadow" action="{{ route('register.post') }}" method="POST">
      @csrf
      <div class="rounded-md shadow-sm -space-y-px">
        <div class="mb-4">
          <label for="name" class="sr-only">Nama</label>
          <input id="name" name="name" type="text" required value="{{ old('name') }}" class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Nama lengkap">
        </div>
        <div class="mb-4">
          <label for="email" class="sr-only">Email</label>
          <input id="email" name="email" type="email" required value="{{ old('email') }}" class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Email">
          @error('email')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div class="mb-4">
          <label for="password" class="sr-only">Password</label>
          <input id="password" name="password" type="password" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Password">
        </div>
        <div class="mb-4">
          <label for="password_confirmation" class="sr-only">Konfirmasi Password</label>
          <input id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Konfirmasi Password">
        </div>

        <div class="mb-4">
          <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Pilih Role</label>
          <select id="role" name="role" required class="block w-full rounded-md border-gray-300 px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <option value="">-- Pilih Role --</option>
            <option value="Owner">Owner</option>
            <option value="Admin">Admin</option>
            <option value="Produksi">Produksi</option>
            <option value="Keuangan">Keuangan</option>
          </select>
          @error('role')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div>
        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-semibold rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
          Daftar
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-gray-100 rounded-3xl p-8 shadow-lg">

        {{-- Tombol Kembali --}}
        <button onclick="window.location.href='{{ route('welcome') }}'" 
        class="border rounded-lg px-4 py-2 flex items-center gap-2 hover:bg-gray-100 transition">
        Kembali
        </button>


        {{-- Logo dan Judul --}}
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="bg-white p-4 rounded-2xl shadow-lg">
                    <img src="{{ asset('images/logo.png') }}" alt="Mons Magna" class="h-16 w-16">
                </div>
            </div>
            <h1 class="text-gray-900 mb-2 font-semibold text-xl">Mons Magna</h1>
            <p class="text-gray-600">Sistem Manajemen Inventori</p>
        </div>

        {{-- Kartu Register --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-semibold mb-1">Buat Akun Baru</h2>
            <p class="text-sm text-gray-500 mb-4">Isi formulir di bawah untuk mendaftar</p>

            {{-- Alert Error --}}
            <div class="bg-red-100 border border-red-300 text-red-700 text-sm rounded-lg p-3 mb-4">
                Kata sandi tidak cocok
            </div>

            {{-- Alert Success --}}
            <div class="bg-green-100 border border-green-300 text-green-700 text-sm rounded-lg p-3 mb-4">
                Akun berhasil dibuat! Mengarahkan ke halaman masuk...
            </div>

            <form class="space-y-4">
                {{-- Nama Lengkap --}}
                <div class="space-y-2">
                    <label for="fullName" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.364 4.56 9 9 0 015.12 17.804z" />
                        </svg>
                        <input id="fullName" type="text" placeholder="Masukkan nama lengkap"
                               class="pl-10 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Nama Perusahaan --}}
                <div class="space-y-2">
                    <label for="company" class="block text-sm font-medium text-gray-700">Nama Perusahaan (Opsional)</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        <input id="company" type="text" placeholder="Masukkan nama perusahaan"
                               class="pl-10 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Email --}}
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8-6H8m8 12H8m0 0a9 9 0 110-18 9 9 0 010 18z" />
                        </svg>
                        <input id="email" type="email" placeholder="Masukkan email"
                               class="pl-10 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Nama Pengguna --}}
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium text-gray-700">Nama Pengguna</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.364 4.56 9 9 0 015.12 17.804z" />
                        </svg>
                        <input id="username" type="text" placeholder="Pilih nama pengguna"
                               class="pl-10 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Kata Sandi --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.105.895-2 2-2h.01a2 2 0 00-2.01 2zM4 9V7a4 4 0 014-4h8a4 4 0 014 4v2M4 9h16M4 9v10a4 4 0 004 4h8a4 4 0 004-4V9" />
                        </svg>
                        <input id="password" type="password" placeholder="Buat kata sandi"
                               class="pl-10 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Konfirmasi Kata Sandi --}}
                <div class="space-y-2">
                    <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.105.895-2 2-2h.01a2 2 0 00-2.01 2zM4 9V7a4 4 0 014-4h8a4 4 0 014 4v2M4 9h16M4 9v10a4 4 0 004 4h8a4 4 0 004-4V9" />
                        </svg>
                        <input id="confirmPassword" type="password" placeholder="Ulangi kata sandi"
                               class="pl-10 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Tombol Daftar --}}
                <button type="button" 
                        class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">
                    Daftar
                </button>
            </form>
        </div>

        <p class="text-center text-gray-600 text-xs mt-6">
            © 2025 Mons Magna. Hak cipta dilindungi.
        </p>
    </div>
</div>
@endsection
