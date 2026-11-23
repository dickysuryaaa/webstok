<?php

namespace App\Services;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenjualanService
{
    /**
     * Catat satu penjualan dan kurangi stok dalam satu transaksi DB.
     * Baris produk dikunci (lockForUpdate) agar stok tidak balapan.
     *
     * @throws ValidationException bila produk tidak ada, stok habis, atau jumlah melebihi stok.
     */
    public function catat(User $user, int $produkId, int $jumlah): Penjualan
    {
        return DB::transaction(function () use ($user, $produkId, $jumlah) {
            $produk = Produk::query()->whereKey($produkId)->lockForUpdate()->first();

            if (! $produk) {
                throw ValidationException::withMessages(['produk_id' => 'Produk tidak ditemukan.']);
            }

            if ($produk->stok === 0) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Stok "'.$produk->nama_produk.'" sedang habis. Penjualan tidak dapat dicatat.',
                ]);
            }

            if ($jumlah > $produk->stok) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah melebihi stok. Stok "'.$produk->nama_produk.'" tersisa '.$produk->stok.' unit.',
                ]);
            }

            $penjualan = Penjualan::create([
                'produk_id' => $produk->id,
                'user_id' => $user->id,
                'jumlah' => $jumlah,
                'total_harga' => $produk->harga_jual * $jumlah,
                'keuntungan' => ($produk->harga_jual - $produk->harga_beli) * $jumlah,
                'tanggal_penjualan' => now(),
            ]);

            $produk->decrement('stok', $jumlah);

            return $penjualan;
        });
    }
}
