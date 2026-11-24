<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Laravel Intern Hub</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-red-600 to-rose-500 flex items-center justify-center text-white shadow-lg shadow-red-500/20 mb-4">
                <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>

            <h1 class="text-2xl font-extrabold text-slate-900">
                Laravel<span class="text-red-600">Intern</span>
            </h1>

            <p class="text-sm text-slate-500 mt-2">
                Masuk ke sistem pengelolaan arsip surat
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-7 sm:p-8">

            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-800">Selamat Datang!</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Silakan login menggunakan akun staf.
                </p>
            </div>

            @if (session('success'))
                <div class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="admin@gmail.com"
                        required
                        autofocus
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-red-600 py-3 font-semibold text-white transition hover:bg-red-700 focus:ring-4 focus:ring-red-100"
                >
                    Login
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('letters.index') }}" class="text-sm text-slate-500 hover:text-red-600">
                    ← Kembali ke Arsip Surat
                </a>
            </div>

        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            Laravel Intern Hub &copy; {{ date('Y') }}
        </p>

    </div>

</body>
</html>
