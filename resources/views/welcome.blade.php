@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">

        {{-- Logo dan Judul --}}
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <img src="{{ asset('images/logo.png') }}" alt="Mons Magna" class="h-20 w-20">
                </div>
            </div>
            <h1 class="text-gray-900 mb-3 text-2xl font-semibold">Mons Magna</h1>
            <p class="text-gray-600 text-lg mb-2">Sistem Manajemen Produk dan Inventori</p>
            <p class="text-gray-500 text-sm">
                Kelola produksi sepatu kulit, inventori bahan, pesanan, dan keuangan dalam satu platform terpadu.
            </p>
        </div>

        {{-- Gambar Produk --}}
        <div class="rounded-2xl overflow-hidden shadow-2xl mb-8">
            <img src="https://images.unsplash.com/photo-1760616172899-0681b97a2de3?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080"
                 alt="Mons Magna Leather Shoes"
                 class="w-full h-80 object-cover">
        </div>

        {{-- Bagian Aksi --}}
        <div class="space-y-4">
            <div class="text-center mb-6">
                <h2 class="text-gray-900 mb-2 text-xl font-semibold">Selamat Datang</h2>
                <p class="text-gray-600">Pilih opsi untuk melanjutkan</p>
            </div>

            {{-- Kartu Login --}}
            <a href="{{ route('login') }}" 
               class="block bg-white rounded-2xl hover:shadow-xl transition-shadow duration-300 cursor-pointer group">
                <div class="p-6 flex items-center space-x-4">
                    <div class="bg-blue-100 p-4 rounded-xl group-hover:bg-blue-200 transition-colors">
                        {{-- Icon Login --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 17l5-5-5-5m5 5H3"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-gray-900 mb-1 font-medium">Masuk ke Akun</h3>
                        <p class="text-gray-600 text-sm">
                            Sudah memiliki akun? Masuk untuk mengakses dashboard
                        </p>
                    </div>
                </div>
            </a>

            {{-- Kartu Register --}}
            <a href="{{ route('register') }}" 
               class="block bg-white rounded-2xl hover:shadow-xl transition-shadow duration-300 cursor-pointer group">
                <div class="p-6 flex items-center space-x-4">
                    <div class="bg-green-100 p-4 rounded-xl group-hover:bg-green-200 transition-colors">
                        {{-- Icon Register --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-gray-900 mb-1 font-medium">Buat Akun Baru</h3>
                        <p class="text-gray-600 text-sm">
                            Belum memiliki akun? Daftar sekarang untuk mulai
                        </p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Footer --}}
        <p class="text-center text-gray-500 text-xs mt-8">
            © 2025 Mons Magna. Hak cipta dilindungi.
        </p>
    </div>
</div>
@endsection
