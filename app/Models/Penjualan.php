<?php

namespace App\Models;

use Database\Factories\PenjualanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penjualan extends Model
{
    /** @use HasFactory<PenjualanFactory> */
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'produk_id', 'user_id', 'jumlah', 'total_harga', 'keuntungan', 'tanggal_penjualan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'total_harga' => 'integer',
            'keuntungan' => 'integer',
            'tanggal_penjualan' => 'datetime',
        ];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
