<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Sistem Perpustakaan'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800 font-sans">
    <nav class="bg-blue-700 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16 items-center">
            <a href="<?php echo e(route('books.index')); ?>" class="font-bold text-xl tracking-wide">📚 Perpustakaan Digital</a>
            <div class="space-x-4">
                <a href="<?php echo e(route('books.create')); ?>" class="bg-blue-800 px-3 py-2 rounded-md hover:bg-blue-900 text-sm font-semibold">+ Tambah Buku</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <?php if(session('success')): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow" role="alert">
                <p class="font-bold">Berhasil!</p>
                <p><?php echo e(session('success')); ?></p>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>
</body>
</html>
<?php /**PATH C:\xampp3\htdocs\laravel-intern-main\ridwan\resources\views/layouts/app.blade.php ENDPATH**/ ?>