<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ItemFotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.local',
            'password' => Hash::make('Password123!'),
        ]);

        $this->actingAs($admin);
    }

    // ==========================================
    // HELPER DATA TESTING
    // ==========================================

    private function buatItem(array $override = []): Item
    {
        $category = Category::where('nama', 'elektronik')
            ->firstOrFail();

        return Item::create(array_merge([
            'nama_barang' => 'Laptop Foto Testing',
            'category_id' => $category->id,
            'jumlah_stok' => 5,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ], $override));
    }

    private function tokenCsrf(): array
    {
        $token = 'test-csrf-token';

        $this->withSession([
            '_token' => $token,
        ]);

        return ['_token' => $token];
    }

    // ==========================================
    // A. PENGUJIAN UPLOAD FOTO
    // ==========================================

    public function test_foto_barang_bisa_diunggah_saat_create(): void
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::fake('public');

        $foto = UploadedFile::fake()->image('laptop.jpg');

        $response = $this->post(
            route('items.store'),
            array_merge(
                $this->tokenCsrf(),
                $this->buatItemData(),
                ['foto_barang' => $foto]
            )
        );

        $response->assertRedirect(route('items.index'));

        $item = Item::firstOrFail();

        $this->assertNotNull($item->foto_barang);

        $disk->assertExists($item->foto_barang);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'foto_barang' => $item->foto_barang,
        ]);
    }

    public function test_barang_bisa_dibuat_tanpa_foto(): void
    {
        Storage::fake('public');

        $response = $this->post(
            route('items.store'),
            array_merge(
                $this->tokenCsrf(),
                $this->buatItemData()
            )
        );

        $response->assertRedirect(route('items.index'));

        $item = Item::firstOrFail();

        $this->assertNull($item->foto_barang);
    }

    // ==========================================
    // B. PENGUJIAN PENGGANTIAN FOTO
    // ==========================================

    public function test_foto_lama_dihapus_ketika_diganti_foto_baru(): void
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::fake('public');

        $item = $this->buatItem();

        // Simpan foto lama
        $fotoLama = 'items/foto-lama.jpg';

        $disk->put($fotoLama, 'isi foto lama');

        $item->update([
            'foto_barang' => $fotoLama,
        ]);

        // Siapkan foto baru
        $fotoBaru = UploadedFile::fake()->image('foto-baru.jpg');

        $response = $this->put(
            route('items.update', $item),
            array_merge(
                $this->tokenCsrf(),
                $this->buatItemData(),
                ['foto_barang' => $fotoBaru]
            )
        );

        $response->assertRedirect(route('items.index'));

        $item->refresh();

        // Pastikan foto lama sudah dihapus
        $disk->assertMissing($fotoLama);

        // Pastikan foto baru tersimpan
        $this->assertNotNull($item->foto_barang);

        $disk->assertExists($item->foto_barang);
    }

    public function test_foto_lama_tetap_tersimpan_jika_tidak_upload_foto_baru(): void
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::fake('public');

        $item = $this->buatItem();

        $fotoLama = 'items/foto-lama.jpg';

        $disk->put($fotoLama, 'isi foto lama');

        $item->update([
            'foto_barang' => $fotoLama,
        ]);

        $response = $this->put(
            route('items.update', $item),
            array_merge(
                $this->tokenCsrf(),
                $this->buatItemData()
            )
        );

        $response->assertRedirect(route('items.index'));

        $item->refresh();

        $this->assertSame(
            $fotoLama,
            $item->foto_barang
        );

        $disk->assertExists($fotoLama);
    }

    // ==========================================
    // C. PENGUJIAN PENGHAPUSAN FOTO
    // ==========================================

    public function test_foto_barang_dihapus_ketika_barang_dihapus(): void
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::fake('public');

        $item = $this->buatItem();

        $fotoPath = 'items/foto-hapus.jpg';

        $disk->put($fotoPath, 'isi foto');

        $item->update([
            'foto_barang' => $fotoPath,
        ]);

        $response = $this->delete(
            route('items.destroy', $item),
            $this->tokenCsrf()
        );

        $response->assertRedirect(route('items.index'));

        // Pastikan data barang terhapus
        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
        ]);

        // Pastikan file foto juga terhapus
        $disk->assertMissing($fotoPath);
    }

    // ==========================================
    // D. PENGUJIAN VALIDASI FOTO
    // ==========================================

    public function test_file_bukan_gambar_ditolak(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create(
            'dokumen.pdf',
            100,
            'application/pdf'
        );

        $response = $this->post(
            route('items.store'),
            array_merge(
                $this->tokenCsrf(),
                $this->buatItemData(),
                ['foto_barang' => $file]
            )
        );

        $response->assertSessionHasErrors('foto_barang');

        $this->assertDatabaseCount('items', 0);
    }

    public function test_foto_melebihi_2_mb_ditolak(): void
    {
        Storage::fake('public');

        $foto = UploadedFile::fake()
            ->image('foto-besar.jpg')
            ->size(2049);

        $response = $this->post(
            route('items.store'),
            array_merge(
                $this->tokenCsrf(),
                $this->buatItemData(),
                ['foto_barang' => $foto]
            )
        );

        $response->assertSessionHasErrors('foto_barang');

        $this->assertDatabaseCount('items', 0);
    }

    // ==========================================
    // HELPER PAYLOAD REQUEST
    // ==========================================

    private function buatItemData(): array
    {
        $category = Category::where('nama', 'elektronik')
            ->firstOrFail();

        return [
            'nama_barang' => 'Laptop Foto Testing',
            'category_id' => $category->id,
            'jumlah_stok' => 5,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ];
    }
}
