<?php

namespace Database\Factories;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Produk> */
class ProdukFactory extends Factory
{
    public function definition(): array
    {
        $hargaBeli = fake()->numberBetween(10, 500) * 100;

        return [
            'nama_produk' => 'Produk '.fake()->unique()->bothify('??-####'),
            'stok' => fake()->numberBetween(0, 50),
            'harga_beli' => $hargaBeli,
            'harga_jual' => $hargaBeli + fake()->numberBetween(1, 50) * 100,
        ];
    }
}
