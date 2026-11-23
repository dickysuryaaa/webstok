<?php

namespace Database\Factories;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Penjualan> */
class PenjualanFactory extends Factory
{
    public function definition(): array
    {
        $produk = Produk::factory()->create();
        $jumlah = fake()->numberBetween(1, 5);

        return [
            'produk_id' => $produk->id,
            'user_id' => User::factory(),
            'jumlah' => $jumlah,
            'total_harga' => $produk->harga_jual * $jumlah,
            'keuntungan' => ($produk->harga_jual - $produk->harga_beli) * $jumlah,
            'tanggal_penjualan' => now(),
        ];
    }
}
