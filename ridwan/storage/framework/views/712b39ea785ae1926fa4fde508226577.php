<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Flash Success Message -->
    <?php if(session('success')): ?>
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-md shadow-sm">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-sm font-medium text-emerald-800"><?php echo e(session('success')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">

        <!-- Header & Search Bar Row -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Daftar Koleksi Buku</h1>

            <!-- Search & Filter Form -->
            <form method="GET" action="<?php echo e(route('books.index')); ?>" class="flex flex-col sm:flex-row gap-2">
                <select name="status" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Semua Status --</option>
                    <option value="tersedia" <?php echo e(request('status') == 'tersedia' ? 'selected' : ''); ?>>Tersedia</option>
                    <option value="dipinjam" <?php echo e(request('status') == 'dipinjam' ? 'selected' : ''); ?>>Dipinjam</option>
                </select>

                <div class="flex gap-2">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari judul, penulis, ISBN..." class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 w-64">

                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        Cari
                    </button>

                    <?php if(request('search') || request('status')): ?>
                        <a href="<?php echo e(route('books.index')); ?>" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs font-bold text-gray-700 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3">Judul Buku</th>
                        <th class="px-4 py-3">Penulis</th>
                        <th class="px-4 py-3">ISBN</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Tahun</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/50 transition">
                            <!-- Judul Buku + Thumbnail 50x70 px -->
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-4">
                                    <div class="w-[50px] h-[70px] shrink-0 flex items-center justify-center">
                                        <?php if($book->cover_buku && Storage::disk('public')->exists($book->cover_buku)): ?>
                                            <img src="<?php echo e(asset('storage/' . $book->cover_buku)); ?>" alt="<?php echo e($book->judul); ?>" class="w-[50px] h-[70px] object-cover rounded border border-gray-200 shadow-sm">
                                        <?php else: ?>
                                            <div class="w-[50px] h-[70px] bg-gray-100 rounded border border-gray-200 flex flex-col items-center justify-center text-gray-400 p-1 text-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                                <span class="text-[8px] leading-tight font-medium mt-0.5">No Cover</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="font-bold text-gray-900 leading-snug"><?php echo e($book->judul); ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600"><?php echo e($book->penulis); ?></td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-500"><?php echo e($book->isbn); ?></td>
                            <td class="px-4 py-3 text-gray-600"><?php echo e($book->category->nama ?? $book->kategori ?? 'Tanpa Kategori'); ?></td>
                            <td class="px-4 py-3 text-gray-600"><?php echo e($book->tahun_terbit); ?></td>
                            <td class="px-4 py-3">
                                <?php if($book->status === 'tersedia'): ?>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Tersedia
                                    </span>
                                <?php else: ?>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                        Dipinjam
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="<?php echo e(route('books.show', $book)); ?>" class="text-blue-600 hover:text-blue-800 font-medium text-xs">
                                        Detail
                                    </a>

                                    <?php if(auth()->guard()->check()): ?>
                                        <a href="<?php echo e(route('books.edit', $book)); ?>" class="text-amber-600 hover:text-amber-800 font-medium text-xs">
                                            Edit
                                        </a>

                                        <form action="<?php echo e(route('books.destroy', $book)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium text-xs">
                                                Hapus
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data buku yang tersedia.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($books->hasPages()): ?>
            <div class="mt-6">
                <?php echo e($books->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp3\htdocs\laravel-intern\ridwan\resources\views/books/index.blade.php ENDPATH**/ ?>