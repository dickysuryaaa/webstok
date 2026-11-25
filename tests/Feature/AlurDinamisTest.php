<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlurDinamisTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_penjualan_memiliki_method_action_csrf_dan_field(): void
    {
        $html = $this->actingAs(User::factory()->create())
            ->get(route('penjualan.index'))->assertOk()->getContent();

        $this->assertStringContainsString('method="POST" action="'.route('penjualan.store').'"', $html);
        $this->assertStringContainsString('name="_token"', $html);
        $this->assertStringContainsString('name="produk_id"', $html);
        $this->assertStringContainsString('name="jumlah"', $html);
        $this->assertStringContainsString('Simpan Penjualan', $html);
    }

    public function test_pekerja_mencatat_penjualan_dan_tampil_di_daftar_hari_ini(): void
    {
        $pekerja = User::factory()->create();
        $produk = Produk::factory()->create(['nama_produk' => 'Barang Tampil', 'stok' => 10, 'harga_beli' => 1000, 'harga_jual' => 1500]);

        $this->actingAs($pekerja)->post(route('penjualan.store'), ['produk_id' => $produk->id, 'jumlah' => 3])
            ->assertRedirect(route('penjualan.index'))->assertSessionHas('status');

        $this->assertSame(7, $produk->fresh()->stok);
        $this->actingAs($pekerja)->get(route('penjualan.index'))->assertSee('Barang Tampil')->assertSee('Rp 4.500');
    }

    public function test_tombol_tambah_dan_ubah_terlihat_untuk_pemilik_saja(): void
    {
        Produk::factory()->create();

        $this->actingAs(User::factory()->pemilik()->create())->get(route('produk.index'))
            ->assertSee('Tambah Produk')->assertSee('Ubah');
        $this->actingAs(User::factory()->create())->get(route('produk.index'))
            ->assertDontSee('Tambah Produk')->assertDontSee('Ubah');
    }

    public function test_form_ubah_memuat_nilai_lama(): void
    {
        $produk = Produk::factory()->create(['nama_produk' => 'Nilai Lama', 'stok' => 42, 'harga_beli' => 1111, 'harga_jual' => 2222]);

        $this->actingAs(User::factory()->pemilik()->create())->get(route('produk.edit', $produk))
            ->assertOk()->assertSee('value="Nilai Lama"', false)->assertSee('value="42"', false)
            ->assertSee('1111')->assertSee('2222');
    }

    public function test_harga_jual_di_bawah_beli_perlu_konfirmasi(): void
    {
        $pemilik = User::factory()->pemilik()->create();
        $data = ['nama_produk' => 'Barang Rugi', 'stok' => 5, 'harga_beli' => 2000, 'harga_jual' => 1500];

        $this->actingAs($pemilik)->post(route('produk.store'), $data)->assertSessionHasErrors('harga_jual');
        $this->assertDatabaseMissing('produk', ['nama_produk' => 'Barang Rugi']);

        $this->actingAs($pemilik)->post(route('produk.store'), [...$data, 'konfirmasi_rugi' => '1'])
            ->assertRedirect(route('produk.index'));
        $this->assertDatabaseHas('produk', ['nama_produk' => 'Barang Rugi', 'harga_jual' => 1500]);
    }

    public function test_hapus_produk_bersejarah_menyarankan_stok_nol(): void
    {
        $pemilik = User::factory()->pemilik()->create();
        $produk = Produk::factory()->create(['stok' => 5]);
        $this->actingAs($pemilik)->post(route('penjualan.store'), ['produk_id' => $produk->id, 'jumlah' => 1]);

        $this->actingAs($pemilik)->delete(route('produk.destroy', $produk))
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'stok menjadi 0'));
        $this->assertDatabaseHas('produk', ['id' => $produk->id]);
    }
}
