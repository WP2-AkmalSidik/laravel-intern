<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CategoryObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_cache_dihapus_ketika_kategori_dibuat(): void
    {
        Cache::put('master_categories', 'data percobaan', 3600);

        Category::create([
            'nama' => 'Elektronik',
            'deskripsi' => 'Kategori elektronik',
        ]);

        $this->assertFalse(Cache::has('master_categories'));
    }

    public function test_cache_dihapus_ketika_kategori_diubah(): void
    {
        $category = Category::create([
            'nama' => 'Elektronik',
            'deskripsi' => 'Kategori elektronik',
        ]);

        Cache::put('master_categories', 'data percobaan', 3600);

        $category->update([
            'nama' => 'Peralatan Kantor',
        ]);

        $this->assertFalse(Cache::has('master_categories'));
    }

    public function test_cache_dihapus_ketika_kategori_dihapus(): void
    {
        $category = Category::create([
            'nama' => 'Elektronik',
            'deskripsi' => 'Kategori elektronik',
        ]);

        Cache::put('master_categories', 'data percobaan', 3600);

        $category->delete();

        $this->assertFalse(Cache::has('master_categories'));
    }
}
