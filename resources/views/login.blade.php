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
