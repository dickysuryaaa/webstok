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
    if ($request->user()->isPelanggan()) {

        // Mengambil kata pencarian.
        $search = trim($request->query('search', ''));

        // Mengambil filter stok.
        $filter = $request->query('filter', 'all');

        if (!in_array($filter, ['all', 'tersedia', 'hampir_habis', 'habis'], true)) {
            $filter = 'all';
        }

        $ambang = (int) config('toko.stok_menipis', 5);

        // Mengatur jumlah produk per halaman.
        $perPage = (int) $request->query('per_page', 9);

        $allowedPerPage = [6, 9, 12, 15];

        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 9;
        }

        // Mengambil data produk.
        $produkQuery = Produk::query()
            ->select([
                'id',
                'nama_produk',
                'harga_jual',
                'stok',
                'gambar',
            ]);

        // Pencarian berdasarkan nama produk.
        if ($search !== '') {
            $produkQuery->where(
                'nama_produk',
                'like',
                '%' . $search . '%'
            );
        }

        // Filter berdasarkan status stok.
        if ($filter === 'tersedia') {
            $produkQuery->where('stok', '>', $ambang);
        } elseif ($filter === 'hampir_habis') {
            $produkQuery->where('stok', '>', 0)
                ->where('stok', '<=', $ambang);
        } elseif ($filter === 'habis') {
            $produkQuery->where('stok', 0);
        }

        // Pagination.
        $produk = $produkQuery
            ->orderBy('nama_produk')
            ->paginate($perPage)
            ->withQueryString();

        // Mengirim seluruh variabel ke dashboard pelanggan.
        return view('auth.pelanggan.dashboard', [
            'produk' => $produk,
            'search' => $search,
            'filter' => $filter,
            'perPage' => $perPage,
        ]);
    }

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
