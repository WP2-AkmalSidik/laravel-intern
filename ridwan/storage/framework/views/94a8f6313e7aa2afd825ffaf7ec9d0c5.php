<?php $__env->startSection('title', 'Koleksi Perpustakaan'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Koleksi Buku</h1>

        <!-- Filter & Search Form -->
        <form method="GET" action="<?php echo e(route('books.index')); ?>" class="flex flex-wrap items-center gap-2">
            <select name="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua Status --</option>
                <option value="tersedia" <?php echo e(request('status') == 'tersedia' ? 'selected' : ''); ?>>Tersedia</option>
                <option value="dipinjam" <?php echo e(request('status') == 'dipinjam' ? 'selected' : ''); ?>>Dipinjam</option>
            </select>

            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari judul, penulis, ISBN..." class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-blue-700">Cari</button>
            <?php if(request('search') || request('status')): ?>
                <a href="<?php echo e(route('books.index')); ?>" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-md text-sm hover:bg-gray-300">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600 text-sm">
                    <th class="py-3 px-4">Judul Buku</th>
                    <th class="py-3 px-4">Penulis</th>
                    <th class="py-3 px-4">ISBN</th>
                    <th class="py-3 px-4">Kategori</th>
                    <th class="py-3 px-4">Tahun</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y text-sm">
                <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-900"><?php echo e($book->judul); ?></td>
                        <td class="py-3 px-4 text-gray-600"><?php echo e($book->penulis); ?></td>
                        <td class="py-3 px-4 font-mono text-xs text-gray-500"><?php echo e($book->isbn); ?></td>
                        <td class="py-3 px-4 text-gray-600"><?php echo e($book->kategori); ?></td>
                        <td class="py-3 px-4"><?php echo e($book->tahun_terbit); ?></td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold border <?php echo e($book->status_badge); ?>">
                                <?php echo e(ucfirst($book->status)); ?>

                            </span>
                        </td>
                        <td class="py-3 px-4 text-center space-x-2">
                            <a href="<?php echo e(route('books.show', $book->id)); ?>" class="text-blue-600 hover:underline">Detail</a>
                            <a href="<?php echo e(route('books.edit', $book->id)); ?>" class="text-amber-600 hover:underline">Edit</a>
                            <form action="<?php echo e(route('books.destroy', $book->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-500">Data buku tidak ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <?php echo e($books->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp3\htdocs\laravel-intern-main\ridwan\resources\views/books/index.blade.php ENDPATH**/ ?>