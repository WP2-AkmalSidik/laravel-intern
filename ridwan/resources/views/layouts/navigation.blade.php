<nav class="bg-blue-600 border-b border-blue-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <!-- Left: Brand Logo & Title -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('books.index') }}" class="flex items-center space-x-2 text-white font-bold text-xl tracking-tight">
                    <span class="text-2xl">📚</span>
                    <span>Perpustakaan Digital</span>
                </a>
            </div>

            <!-- Right: Navigation & Actions -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('books.index') }}" class="text-white hover:text-blue-100 px-3 py-2 text-sm font-medium">
                    Koleksi Buku
                </a>

                @auth
                    <a href="{{ route('books.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-medium rounded-lg transition shadow-sm border border-blue-500">
                        + Tambah Buku
                    </a>

                    <!-- User Dropdown Menu -->
                    <div class="ms-3 relative" x-data="{ open: false }">
                        <button @click="open = ! open" class="flex items-center text-sm font-medium text-white hover:text-blue-100 focus:outline-none">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ms-1 fill-current h-4 w-4" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 z-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:text-blue-100 px-3 py-2 text-sm font-medium">
                        Log in
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
