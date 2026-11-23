<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        // Variasi stok: aman, menipis (<= 5), dan habis.
        $stok = [25, 40, 12, 8, 5, 3, 0, 30, 18, 1];

        foreach ($stok as $i => $jumlah) {
            $n = $i + 1;
            $hargaBeli = 5000 * $n;

            Produk::updateOrCreate(
                ['nama_produk' => "Produk Contoh {$n}"],
                [
                    'stok' => $jumlah,
                    'harga_beli' => $hargaBeli,
                    'harga_jual' => $hargaBeli + 1500 + 500 * $n,
                ]
            );
        }
    }
}
