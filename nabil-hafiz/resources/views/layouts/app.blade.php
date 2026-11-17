
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Sistem Arsip Surat') - Laravel Intern Hub</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <div class="flex items-center gap-3">
                    <a href="{{ route('letters.index') }}" class="flex items-center gap-2 group">

                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-red-600 to-rose-500 flex items-center justify-center text-white shadow-md shadow-red-500/20 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        </div>

                        <div>
                            <span class="font-bold text-lg text-slate-900 group-hover:text-red-600 transition-colors">
                                Laravel<span class="text-red-600">Intern</span>
                            </span>

                            <span class="block text-[11px] font-medium text-slate-400 -mt-1">
                                Modul Pembelajaran Magang
                            </span>
                        </div>

                    </a>
                </div>

                <nav class="flex items-center gap-2 sm:gap-4 text-sm font-medium">

                    <a href="{{ route('letters.index') }}"
                       class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('letters.index') || request()->routeIs('letters.show') || request()->routeIs('letters.edit') ? 'bg-red-50 text-red-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        📋 Arsip Surat
                    </a>

                    <a href="{{ route('letters.create') }}"
                       class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('letters.create') ? 'bg-red-50 text-red-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        ➕ Tambah Surat
                    </a>

                </nav>

            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-6">

        @if (session('success'))
            <div class="p-4 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-sm">

                <div class="text-emerald-500 mt-0.5">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>

                <div class="flex-1">
                    <p class="font-semibold text-sm">Aksi Berhasil!</p>
                    <p class="text-sm text-emerald-700 mt-0.5">
                        {{ session('success') }}
                    </p>
                </div>

                <button type="button"
                        onclick="this.parentElement.remove()"
                        class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

            </div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 shadow-sm">

                <div class="text-rose-500 mt-0.5">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>

                <div class="flex-1">
                    <p class="font-semibold text-sm">Terjadi Kesalahan!</p>
                    <p class="text-sm text-rose-700 mt-0.5">
                        {{ session('error') }}
                    </p>
                </div>

            </div>
        @endif

    </div>

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-4 pb-12">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <div>
                <strong>Proyek Latihan Magang:</strong> Nabil (Folder: <code class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded">C:\laragon\www\laravel-intern\nabil-hafiz</code>)
            </div>
            <div>
                Mencakup: <span class="font-medium text-slate-700">Architecture, MVC, Routing & Controller, Eloquent ORM, Validation, Request & Response</span>
            </div>
        </div>
    </footer>

</body>
</html>
