@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full space-y-8">
    <div class="text-center">
      <div class="flex justify-center mb-4">
        <div class="bg-white p-4 rounded-xl shadow">
          <img src="{{ asset('images/logo.png') }}" alt="Mons Magna" class="h-12 w-12">
        </div>
      </div>
      <h2 class="mt-2 text-center text-2xl font-extrabold text-gray-900">Masuk ke akun Anda</h2>
      <p class="mt-2 text-center text-sm text-gray-600">Belum punya akun? <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:underline">Daftar</a></p>
    </div>

    <form class="mt-8 space-y-6 bg-white p-8 rounded-2xl shadow" action="{{ route('login.post') }}" method="POST">
      @csrf
      @if($errors->any())
      <div class="mb-4">
        <div class="text-sm text-red-600">{{ $errors->first() }}</div>
      </div>
      @endif
      <div class="rounded-md shadow-sm -space-y-px">
        <div class="mb-4">
          <label for="email" class="sr-only">Email</label>
          <input id="email" name="email" type="email" required value="{{ old('email') }}" class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Email">
        </div>
        <div>
          <label for="password" class="sr-only">Password</label>
          <input id="password" name="password" type="password" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Password">
        </div>
      </div>

      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
          <label for="remember" class="ml-2 block text-sm text-gray-900">Ingat saya</label>
        </div>
        <div class="text-sm">
          <a href="#" class="font-medium text-blue-600 hover:underline">Lupa password?</a>
        </div>
      </div>

      <div>
        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-semibold rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Masuk
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

        {{-- Kartu Login --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-semibold mb-1">Selamat Datang Kembali</h2>
            <p class="text-sm text-gray-500 mb-4">Masuk untuk mengakses dashboard Anda</p>

            {{-- Alert Error --}}
            <div class="bg-red-100 border border-red-300 text-red-700 text-sm rounded-lg p-3 mb-4">
                Nama pengguna atau kata sandi tidak valid
            </div>

            <form class="space-y-4">
                {{-- Username --}}
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium text-gray-700">Nama Pengguna</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.364 4.56 9 9 0 015.12 17.804z" />
                        </svg>
                        <input id="username" type="text" placeholder="Masukkan nama pengguna"
                               class="pl-10 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Password --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.105.895-2 2-2h.01a2 2 0 00-2.01 2zM4 9V7a4 4 0 014-4h8a4 4 0 014 4v2M4 9h16M4 9v10a4 4 0 004 4h8a4 4 0 004-4V9" />
                        </svg>
                        <input id="password" type="password" placeholder="Masukkan kata sandi"
                               class="pl-10 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Tombol Masuk --}}
                <button type="button"
                        class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-gray-600 text-xs mt-6">
            © 2025 Mons Magna. Hak cipta dilindungi.
        </p>
    </div>
</div>
@endsection
