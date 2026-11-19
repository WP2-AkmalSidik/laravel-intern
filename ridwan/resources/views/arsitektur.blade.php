<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard & Arsitektur Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8 font-sans">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">📚 System Dashboard & Architecture</h1>
        <p class="text-gray-600 text-sm mb-6">Ringkasan status koleksi buku dari database MySQL.</p>

        <!-- Stats Cards -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg text-center">
                <span class="block text-3xl font-extrabold text-blue-600">{{ $stats['total'] }}</span>
                <span class="text-xs font-medium text-gray-500 uppercase">Total Buku</span>
            </div>
            <div class="bg-green-50 border border-green-200 p-4 rounded-lg text-center">
                <span class="block text-3xl font-extrabold text-green-600">{{ $stats['buku_tersedia'] }}</span>
                <span class="text-xs font-medium text-gray-500 uppercase">Tersedia</span>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg text-center">
                <span class="block text-3xl font-extrabold text-yellow-600">{{ $stats['buku_dipinjam'] }}</span>
                <span class="text-xs font-medium text-gray-500 uppercase">Dipinjam</span>
            </div>
        </div>

        <!-- Direct Links -->
        <div class="flex gap-3">
            <a href="/books" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-blue-700 transition">
                📖 Kelola Buku (Blade View)
            </a>
            <a href="/api/books" target="_blank" class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-medium hover:bg-gray-900 transition">
                ⚡ Lihat API JSON (/api/books)
            </a>
        </div>
    </div>
</body>
</html>
