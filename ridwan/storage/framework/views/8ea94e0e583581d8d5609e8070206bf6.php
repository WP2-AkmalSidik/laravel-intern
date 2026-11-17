<?php $__env->startSection('title', 'Detail Buku'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-800">Detail Informasi Buku</h1>
        <a href="<?php echo e(route('books.index')); ?>" class="text-sm text-blue-600 hover:underline">← Kembali ke Koleksi</a>
    </div>

    <div class="border-t border-b divide-y py-2 text-sm">
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Judul Buku</span>
            <span class="col-span-2 font-bold text-gray-900"><?php echo e($book->judul); ?></span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Penulis</span>
            <span class="col-span-2 font-medium"><?php echo e($book->penulis); ?></span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Nomor ISBN</span>
            <span class="col-span-2 font-mono text-gray-600"><?php echo e($book->isbn); ?></span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Penerbit</span>
            <span class="col-span-2"><?php echo e($book->penerbit); ?></span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Kategori</span>
            <span class="col-span-2"><?php echo e($book->kategori); ?></span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Tahun Terbit</span>
            <span class="col-span-2"><?php echo e($book->tahun_terbit); ?></span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Jumlah Halaman</span>
            <span class="col-span-2"><?php echo e($book->jumlah_halaman); ?> halaman</span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Status</span>
            <span class="col-span-2">
                <span class="px-2 py-1 rounded-full text-xs font-semibold border <?php echo e($book->status_badge); ?>">
                    <?php echo e(ucfirst($book->status)); ?>

                </span>
            </span>
        </div>
    </div>

    <div class="mt-6 flex justify-end space-x-3">
        <a href="<?php echo e(route('books.edit', $book->id)); ?>" class="bg-amber-500 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-amber-600">Edit Buku</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp3\htdocs\laravel-intern-main\ridwan\resources\views/books/show.blade.php ENDPATH**/ ?>