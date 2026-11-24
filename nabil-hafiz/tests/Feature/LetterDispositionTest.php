<?php

namespace Tests\Feature;

use Tests\TestCase;

class LetterDispositionTest extends TestCase
{
    /**
     * Halaman utama mengarahkan pengguna
     * ke halaman daftar surat.
     */
    public function test_home_redirects_to_letters_index(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('letters.index'));
    }
}
