<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-4">
        <a href="<?php echo e(route('books.index')); ?>" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
            &larr; Kembali ke Katalog
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Cover Beresolusi Penuh -->
            <div class="w-full md:w-1/3 flex justify-center">
                <?php if($book->cover_buku && Storage::disk('public')->exists($book->cover_buku)): ?>
                    <img src="<?php echo e(asset('storage/' . $book->cover_buku)); ?>" alt="<?php echo e($book->judul); ?>" class="w-full max-w-[250px] h-auto object-cover rounded-lg shadow-md border border-gray-200">
                <?php else: ?>
                    <div class="w-full max-w-[250px] h-80 bg-gray-100 rounded-lg border border-gray-200 flex flex-col items-center justify-center text-gray-400 p-4 text-center">
                        <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span class="text-xs font-semibold">Tidak Ada Sampul Buku</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Rincian Buku -->
            <div class="w-full md:w-2/3 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                            <?php echo e($book->category->nama ?? $book->kategori ?? 'Tanpa Kategori'); ?>

                        </span>
                        <?php if($book->status === 'tersedia'): ?>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Tersedia
                            </span>
                        <?php else: ?>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                Dipinjam
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo e($book->judul); ?></h1>
                    <p class="text-base text-gray-600 mb-6">Penulis: <span class="font-semibold text-gray-800"><?php echo e($book->penulis); ?></span></p>

                    <div class="grid grid-cols-2 gap-4 text-sm border-t border-b border-gray-100 py-4 mb-6">
                        <div>
                            <span class="text-gray-400 block text-xs">Penerbit</span>
                            <span class="font-medium text-gray-700"><?php echo e($book->penerbit); ?></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-xs">Nomor ISBN</span>
                            <span class="font-mono font-medium text-gray-700"><?php echo e($book->isbn); ?></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-xs">Tahun Terbit</span>
                            <span class="font-medium text-gray-700"><?php echo e($book->tahun_terbit); ?></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-xs">Jumlah Halaman</span>
                            <span class="font-medium text-gray-700"><?php echo e($book->jumlah_halaman); ?> Halaman</span>
                        </div>
                    </div>
                </div>

                <?php if(auth()->guard()->check()): ?>
                    <div class="flex items-center space-x-3 pt-4">
                        <a href="<?php echo e(route('books.edit', $book)); ?>" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition shadow-sm">
                            Edit Buku
                        </a>

                        <form action="<?php echo e(route('books.destroy', $book)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                Hapus Buku
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp3\htdocs\laravel-intern\ridwan\resources\views/books/show.blade.php ENDPATH**/ ?>