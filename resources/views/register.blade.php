@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full space-y-8">
    <div class="text-center">
      <div class="flex justify-center mb-4">
        <div class="bg-white p-1 rounded-xl shadow">
          <img src="{{ asset('images/logo.png') }}" alt="Mons Magna" class="h-21 w-20">
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
