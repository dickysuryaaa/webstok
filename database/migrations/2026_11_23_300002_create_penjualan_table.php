<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedInteger('jumlah');
            $table->unsignedInteger('total_harga');
            // Signed: penjualan rugi menghasilkan keuntungan negatif.
            $table->integer('keuntungan');
            $table->dateTime('tanggal_penjualan')->useCurrent();
            $table->timestamps();

            $table->index('tanggal_penjualan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
