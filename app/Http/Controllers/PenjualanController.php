<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenjualanRequest;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Services\PenjualanService;
use App\Support\Rupiah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    public function index(Request $request): View
    {
        $pemilik = $request->user()->isPemilik();

        // Pilihan produk untuk form: tanpa harga_beli, untuk semua peran.
        $pilihan = Produk::query()
            ->select(['id', 'nama_produk', 'stok', 'harga_jual'])
            ->orderBy('nama_produk')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'nama' => $p->nama_produk,
                'stok' => $p->stok,
                'harga' => $p->harga_jual,
                'harga_teks' => Rupiah::format($p->harga_jual),
            ])
            ->values();

        // Kolom keuntungan hanya dimuat dari DB untuk pemilik.
        $kolom = ['id', 'produk_id', 'jumlah', 'total_harga', 'tanggal_penjualan'];
        if ($pemilik) {
            $kolom[] = 'keuntungan';
        }

        $hariIni = Penjualan::query()
            ->select($kolom)
            ->with('produk:id,nama_produk')
            ->whereBetween('tanggal_penjualan', [now()->startOfDay(), now()->endOfDay()])
            ->orderByDesc('tanggal_penjualan')
            ->orderByDesc('id')
            ->get();

        return view('penjualan.index', [
            'pilihan' => $pilihan,
            'hariIni' => $hariIni,
            'pemilik' => $pemilik,
        ]);
    }

    public function store(PenjualanRequest $request, PenjualanService $service): RedirectResponse
    {
        $penjualan = $service->catat(
            $request->user(),
            (int) $request->validated('produk_id'),
            (int) $request->validated('jumlah'),
        );

        $produk = Produk::query()->select(['id', 'nama_produk', 'stok'])->find($penjualan->produk_id);

        return redirect()->route('penjualan.index')->with(
            'status',
            'Penjualan tercatat: '.$produk->nama_produk.' x '.$penjualan->jumlah
                .' = '.Rupiah::format($penjualan->total_harga).'. Sisa stok: '.$produk->stok.' unit.'
        );
    }
}
