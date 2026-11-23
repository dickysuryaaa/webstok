<?php

namespace Tests\Feature;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdukAksesTest extends TestCase
{
    use RefreshDatabase;

    private function dataProduk(): array
    {
        return [
            'nama_produk' => 'Produk Baru',
            'stok' => 10,
            'harga_beli' => 1000,
            'harga_jual' => 1500,
        ];
    }

    public function test_pekerja_tidak_bisa_membuka_rute_tambah_ubah_hapus_produk(): void
    {
        $pekerja = User::factory()->create();
        $produk = Produk::factory()->create();

        $this->actingAs($pekerja)->get(route('produk.create'))->assertForbidden();
        $this->actingAs($pekerja)->post(route('produk.store'), $this->dataProduk())->assertForbidden();
        $this->actingAs($pekerja)->get(route('produk.edit', $produk))->assertForbidden();
        $this->actingAs($pekerja)->put(route('produk.update', $produk), $this->dataProduk())->assertForbidden();
        $this->actingAs($pekerja)->delete(route('produk.destroy', $produk))->assertForbidden();

        $this->assertDatabaseHas('produk', ['id' => $produk->id, 'nama_produk' => $produk->nama_produk]);
        $this->assertDatabaseMissing('produk', ['nama_produk' => 'Produk Baru']);
    }

    public function test_pemilik_bisa_tambah_ubah_hapus_produk(): void
    {
        $pemilik = User::factory()->pemilik()->create();

        $this->actingAs($pemilik)->post(route('produk.store'), $this->dataProduk())
            ->assertRedirect(route('produk.index'));
        $produk = Produk::where('nama_produk', 'Produk Baru')->firstOrFail();

        $this->actingAs($pemilik)->put(route('produk.update', $produk), [...$this->dataProduk(), 'stok' => 25])
            ->assertRedirect(route('produk.index'));
        $this->assertSame(25, $produk->fresh()->stok);

        $this->actingAs($pemilik)->delete(route('produk.destroy', $produk))
            ->assertRedirect(route('produk.index'));
        $this->assertDatabaseMissing('produk', ['id' => $produk->id]);
    }

    public function test_pekerja_tidak_melihat_harga_beli_dan_keuntungan_di_daftar_produk(): void
    {
        Produk::factory()->create(['nama_produk' => 'Barang Uji', 'stok' => 20, 'harga_beli' => 12345, 'harga_jual' => 23456]);

        $respons = $this->actingAs(User::factory()->create())->get(route('produk.index'));

        $respons->assertOk()->assertSee('Barang Uji')->assertSee('23.456');
        $respons->assertDontSee('12.345');
        $respons->assertDontSee('11.111');
        $respons->assertDontSee('Harga beli');
        $respons->assertDontSee('Keuntungan');
    }

    public function test_pemilik_melihat_harga_beli_dan_keuntungan_di_daftar_produk(): void
    {
        Produk::factory()->create(['nama_produk' => 'Barang Uji', 'stok' => 20, 'harga_beli' => 12345, 'harga_jual' => 23456]);

        $this->actingAs(User::factory()->pemilik()->create())->get(route('produk.index'))
            ->assertOk()->assertSee('12.345')->assertSee('11.111')->assertSee('Harga beli');
    }

    public function test_pekerja_tidak_melihat_keuntungan_di_dasbor_tetapi_melihat_total(): void
    {
        $pekerja = User::factory()->create();
        $produk = Produk::factory()->create(['stok' => 10, 'harga_beli' => 1000, 'harga_jual' => 3333]);
        Penjualan::factory()->create([
            'produk_id' => $produk->id, 'user_id' => $pekerja->id, 'jumlah' => 3,
            'total_harga' => 9999, 'keuntungan' => 7777, 'tanggal_penjualan' => now(),
        ]);

        $respons = $this->actingAs($pekerja)->get(route('dashboard'));

        $respons->assertOk()->assertSee('Jumlah transaksi')->assertSee('Total penjualan')->assertSee('9.999');
        $respons->assertDontSee('7.777');
        $respons->assertDontSee('Keuntungan');
    }

    public function test_pemilik_melihat_keuntungan_di_dasbor(): void
    {
        $pemilik = User::factory()->pemilik()->create();
        $produk = Produk::factory()->create();
        Penjualan::factory()->create([
            'produk_id' => $produk->id, 'user_id' => $pemilik->id, 'jumlah' => 3,
            'total_harga' => 9999, 'keuntungan' => 7777, 'tanggal_penjualan' => now(),
        ]);

        $this->actingAs($pemilik)->get(route('dashboard'))
            ->assertOk()->assertSee('Total keuntungan')->assertSee('7.777');
    }

    public function test_pekerja_tidak_melihat_keuntungan_di_halaman_penjualan(): void
    {
        $pekerja = User::factory()->create();
        $produk = Produk::factory()->create(['harga_beli' => 12345, 'harga_jual' => 23456]);
        Penjualan::factory()->create([
            'produk_id' => $produk->id, 'user_id' => $pekerja->id, 'jumlah' => 1,
            'total_harga' => 23456, 'keuntungan' => 11111, 'tanggal_penjualan' => now(),
        ]);

        $this->actingAs($pekerja)->get(route('penjualan.index'))
            ->assertOk()->assertDontSee('12.345')->assertDontSee('11.111');
    }
}
