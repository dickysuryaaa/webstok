<?php

namespace App\Models;

use Database\Factories\ProdukFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    /** @use HasFactory<ProdukFactory> */
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = ['nama_produk', 'stok', 'harga_beli', 'harga_jual', 'gambar'];

    protected function casts(): array
    {
        return [
            'stok' => 'integer',
            'harga_beli' => 'integer',
            'harga_jual' => 'integer',
            'gambar' => 'string',
        ];
    }

    /** Atribut turunan, bukan kolom. */
    protected function keuntunganPerUnit(): Attribute
    {
        return Attribute::get(fn () => $this->harga_jual - $this->harga_beli);
    }

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    public function logChatbot(): HasMany
    {
        return $this->hasMany(LogChatbot::class);
    }
}
