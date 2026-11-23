@extends('layouts.app')

@section('title', 'Login Admin')

@section('content')

    <div class="min-h-[70vh] flex items-center justify-center px-4">

        <div class="bg-white w-full max-w-md rounded-xl shadow-lg border border-gray-100 p-8">

            {{-- Header --}}
            <div class="text-center mb-8">

                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">

                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />

                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-800">
                    Login Admin
                </h2>

                <p class="text-gray-500 mt-2 text-sm">
                    Masuk untuk mengelola data inventaris.
                </p>

            </div>

            {{-- Pesan Error --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-5 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Form Login --}}
            <form action="{{ route('login') }}" method="POST">

                @csrf

                {{-- Email --}}
                <div class="mb-5">

                    <label for="email" class="block font-medium text-gray-700 mb-2">
                        Email
                    </label>

                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="Masukkan email admin" autocomplete="username" required autofocus
                        class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                </div>

                {{-- Password --}}
                <div class="mb-6">

                    <label for="password" class="block font-medium text-gray-700 mb-2">
                        Password
                    </label>

                    <input type="password" id="password" name="password" placeholder="Masukkan password"
                        autocomplete="current-password" required
                        class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                </div>

                {{-- Tombol --}}
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition">
                    Login
                </button>

            </form>

            {{-- Kembali ke Katalog --}}
            <div class="text-center mt-6">

                <a href="{{ route('items.index') }}" class="text-sm text-gray-500 hover:text-blue-600 transition">
                    &larr; Kembali ke Katalog Barang
                </a>

            </div>

        </div>

    </div>

@endsection
