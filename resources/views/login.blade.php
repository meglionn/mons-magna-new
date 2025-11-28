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
        <p class="text-center text-gray-600 text-xs mt-6">
            © 2025 Mons Magna. Hak cipta dilindungi.
        </p>
    </div>
@endsection
