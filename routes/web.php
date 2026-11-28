<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.portal');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Akses chatbot ditentukan config('toko.chatbot_public') saat request.
Route::middleware('chatbot.akses')->group(function () {
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot/pesan', [ChatbotController::class, 'pesan'])->name('chatbot.pesan');
});

Route::middleware('auth')->group(function () {

    // Fitur yang hanya boleh diakses pemilik dan pekerja.
    Route::middleware('role:pemilik,pekerja')->group(function () {

        // Melihat produk.
        Route::get('/produk', [ProdukController::class, 'index'])
            ->name('produk.index');

        // Melihat dan mencatat penjualan.
        Route::get('/penjualan', [PenjualanController::class, 'index'])
            ->name('penjualan.index');

        Route::post('/penjualan', [PenjualanController::class, 'store'])
            ->name('penjualan.store');

        Route::get('/produk/{produk}/gambar', [ProdukController::class, 'editGambar'])
            ->name('produk.gambar.edit');

        Route::put('/produk/{produk}/gambar', [ProdukController::class, 'updateGambar'])
            ->name('produk.gambar.update');
    });

    // Fitur khusus pemilik.
    Route::middleware('role:pemilik')->group(function () {

        Route::get('/chatbot/log', [ChatbotController::class, 'log'])
            ->name('chatbot.log');

        Route::get('/produk/create', [ProdukController::class, 'create'])
            ->name('produk.create');

        Route::post('/produk', [ProdukController::class, 'store'])
            ->name('produk.store');

        Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])
            ->name('produk.edit');

        Route::put('/produk/{produk}', [ProdukController::class, 'update'])
            ->name('produk.update');

        Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])
            ->name('produk.destroy');
    });

    // Semua role yang sudah login boleh mengelola profilnya sendiri.
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
