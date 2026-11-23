<?php

namespace Tests\Feature;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjualanTest extends TestCase
{
    use RefreshDatabase;

    public function test_penjualan_valid_mengurangi_stok_dan_menyimpan_snapshot(): void
    {
        $user = User::factory()->create();
        $produk = Produk::factory()->create(['stok' => 10, 'harga_beli' => 1000, 'harga_jual' => 1500]);

        $this->actingAs($user)->post(route('penjualan.store'), ['produk_id' => $produk->id, 'jumlah' => 4])
            ->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame(6, $produk->fresh()->stok);
        $this->assertDatabaseHas('penjualan', [
            'produk_id' => $produk->id,
            'user_id' => $user->id,
            'jumlah' => 4,
            'total_harga' => 6000,
            'keuntungan' => 2000,
        ]);
    }

    public function test_penjualan_rugi_menyimpan_keuntungan_negatif(): void
    {
        $produk = Produk::factory()->create(['stok' => 5, 'harga_beli' => 2000, 'harga_jual' => 1500]);

        $this->actingAs(User::factory()->create())
            ->post(route('penjualan.store'), ['produk_id' => $produk->id, 'jumlah' => 2])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('penjualan', ['produk_id' => $produk->id, 'keuntungan' => -1000]);
    }

    public function test_penjualan_melebihi_stok_ditolak_dan_stok_tidak_berubah(): void
    {
        $produk = Produk::factory()->create(['stok' => 3]);

        $this->actingAs(User::factory()->create())
            ->post(route('penjualan.store'), ['produk_id' => $produk->id, 'jumlah' => 4])
            ->assertSessionHasErrors('jumlah');

        $this->assertSame(3, $produk->fresh()->stok);
        $this->assertSame(0, Penjualan::count());
    }

    public function test_penjualan_produk_stok_nol_ditolak(): void
    {
        $produk = Produk::factory()->create(['stok' => 0]);

        $this->actingAs(User::factory()->create())
            ->post(route('penjualan.store'), ['produk_id' => $produk->id, 'jumlah' => 1])
            ->assertSessionHasErrors('jumlah');

        $this->assertSame(0, Penjualan::count());
    }

    public function test_jumlah_nol_atau_produk_kosong_ditolak(): void
    {
        $produk = Produk::factory()->create(['stok' => 3]);
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('penjualan.store'), ['produk_id' => $produk->id, 'jumlah' => 0])
            ->assertSessionHasErrors('jumlah');
        $this->actingAs($user)->post(route('penjualan.store'), ['jumlah' => 1])
            ->assertSessionHasErrors('produk_id');

        $this->assertSame(3, $produk->fresh()->stok);
    }

    public function test_tamu_tidak_bisa_mencatat_penjualan(): void
    {
        $produk = Produk::factory()->create(['stok' => 3]);

        $this->post(route('penjualan.store'), ['produk_id' => $produk->id, 'jumlah' => 1])
            ->assertRedirect(route('login'));

        $this->assertSame(3, $produk->fresh()->stok);
    }

    public function test_mengubah_harga_produk_tidak_mengubah_penjualan_lama(): void
    {
        $pemilik = User::factory()->pemilik()->create();
        $produk = Produk::factory()->create(['nama_produk' => 'Produk Uji', 'stok' => 10, 'harga_beli' => 1000, 'harga_jual' => 1500]);

        $this->actingAs($pemilik)->post(route('penjualan.store'), ['produk_id' => $produk->id, 'jumlah' => 2]);

        $this->actingAs($pemilik)->put(route('produk.update', $produk), [
            'nama_produk' => 'Produk Uji',
            'stok' => 8,
            'harga_beli' => 5000,
            'harga_jual' => 9000,
        ])->assertSessionHasNoErrors();

        $penjualan = Penjualan::firstOrFail();
        $this->assertSame(3000, $penjualan->total_harga);
        $this->assertSame(1000, $penjualan->keuntungan);
        $this->assertSame(9000, $produk->fresh()->harga_jual);
    }

    public function test_produk_yang_punya_penjualan_tidak_bisa_dihapus(): void
    {
        $pemilik = User::factory()->pemilik()->create();
        $produk = Produk::factory()->create(['stok' => 5]);
        $this->actingAs($pemilik)->post(route('penjualan.store'), ['produk_id' => $produk->id, 'jumlah' => 1]);

        $this->actingAs($pemilik)->delete(route('produk.destroy', $produk))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('produk', ['id' => $produk->id]);
        $this->assertSame(1, Penjualan::count());
    }

    public function test_produk_tanpa_penjualan_bisa_dihapus(): void
    {
        $produk = Produk::factory()->create();

        $this->actingAs(User::factory()->pemilik()->create())->delete(route('produk.destroy', $produk))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('produk', ['id' => $produk->id]);
    }
}
