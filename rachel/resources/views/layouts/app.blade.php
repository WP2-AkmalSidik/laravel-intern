<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Inventaris')</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#F4F7F6] min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('items.index') }}" class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>

                <h1 class="text-xl font-bold text-gray-800">
                    Sistem Inventaris
                </h1>
            </a>

            <div class="flex items-center gap-4">

                {{-- Menu Publik --}}
                <a href="{{ route('items.index') }}"
                    class="px-4 py-2 rounded-lg transition
        {{ request()->routeIs('items.index', 'items.show')
            ? 'bg-blue-50 text-[#2563EB] font-bold'
            : 'text-gray-600 hover:bg-gray-100 hover:text-[#2563EB]' }}">
                    Data Barang
                </a>
                @auth
                    <a href="{{ route('loans.index') }}"
                        class="px-4 py-2 rounded-lg transition
        {{ request()->routeIs('loans.*')
            ? 'bg-blue-50 text-[#2563EB] font-bold'
            : 'text-gray-600 hover:bg-gray-100 hover:text-[#2563EB]' }}">
                        Peminjaman
                    </a>
                @endauth

                {{-- Admin dan Staff --}}
                @can('manage-inventory')
                    <a href="{{ route('items.rusak.index') }}"
                        class="px-4 py-2 rounded-lg transition
            {{ request()->routeIs('items.rusak.*')
                ? 'bg-blue-50 text-[#2563EB] font-bold'
                : 'text-gray-600 hover:bg-gray-100 hover:text-[#2563EB]' }}">
                        Riwayat Kerusakan
                    </a>
                @endcan

                {{-- Khusus Admin --}}
                @can('export-reports')
                    <a href="{{ route('laporan.index') }}"
                        class="px-4 py-2 rounded-lg transition
            {{ request()->routeIs('laporan.*')
                ? 'bg-blue-50 text-[#2563EB] font-bold'
                : 'text-gray-600 hover:bg-gray-100 hover:text-[#2563EB]' }}">
                        Laporan
                    </a>
                @endcan

                {{-- Informasi Pengguna dan Logout --}}
                @auth
                    <div class="flex items-center gap-3 border-l pl-4">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-700">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="text-xs text-gray-500 capitalize">
                                {{ Auth::user()->role }}
                            </p>
                        </div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf

                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Guest --}}
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                        Login
                    </a>
                @endauth

            </div>


        </div>
    </nav>

    {{-- Area Konten --}}
    <main class="max-w-7xl mx-auto px-6 py-8">

        {{-- Flash Success --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        {{-- Flash Error --}}
        @if (session('error'))
            <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')

    </main>

</body>

</html>
