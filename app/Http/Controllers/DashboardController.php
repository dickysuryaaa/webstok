<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
        ], [
            'tanggal.date_format' => 'Format tanggal tidak valid.',
        ]);

        $pemilik = $request->user()->isPemilik();
        $hariIni = now()->startOfDay();

        $tanggal = $request->filled('tanggal')
            ? Carbon::createFromFormat('Y-m-d', $request->query('tanggal'), config('app.timezone'))->startOfDay()
            : $hariIni->copy();

        // Keuntungan hanya dihitung di DB dan dikirim ke view untuk pemilik.
        $kolom = 'COUNT(*) as transaksi, COALESCE(SUM(total_harga), 0) as total';
        if ($pemilik) {
            $kolom .= ', COALESCE(SUM(keuntungan), 0) as untung';
        }

        $rekap = Penjualan::query()
            ->selectRaw($kolom)
            ->whereBetween('tanggal_penjualan', [$tanggal->copy()->startOfDay(), $tanggal->copy()->endOfDay()])
            ->first();

        $ambang = (int) config('toko.stok_menipis', 5);

        $menipis = Produk::query()
            ->select(['id', 'nama_produk', 'stok'])
            ->where('stok', '<=', $ambang)
            ->orderBy('stok')
            ->orderBy('nama_produk')
            ->get();

        return view('dashboard', [
            'tanggal' => $tanggal,
            'hariIni' => $hariIni,
            'kemarin' => $hariIni->copy()->subDay(),
            'ambang' => $ambang,
            'adalahHariIni' => $tanggal->isSameDay($hariIni),
            'transaksi' => (int) $rekap->transaksi,
            'total' => (int) $rekap->total,
            'untung' => $pemilik ? (int) $rekap->untung : null,
            'pemilik' => $pemilik,
            'menipis' => $menipis,
        ]);
    }
}
