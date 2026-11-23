<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdukRequest;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProdukController extends Controller
{
    public function index(Request $request): View
    {
        $pemilik = $request->user()->isPemilik();
        $q = trim((string) $request->query('q', ''));

        // Pekerja tidak pernah memuat kolom harga_beli.
        $kolom = $pemilik
            ? ['id', 'nama_produk', 'stok', 'harga_beli', 'harga_jual']
            : ['id', 'nama_produk', 'stok', 'harga_jual'];

        $produk = Produk::query()
            ->select($kolom)
            ->when($q !== '', fn ($query) => $query->where('nama_produk', 'like', '%'.addcslashes($q, '%_\\').'%'))
            ->orderBy('nama_produk')
            ->paginate(20)
            ->withQueryString();

        return view('produk.index', [
            'produk' => $produk,
            'q' => $q,
            'pemilik' => $pemilik,
            'ambang' => (int) config('toko.stok_menipis', 5),
        ]);
    }

    public function create(): View
    {
        return view('produk.create', ['produk' => new Produk(['stok' => 0])]);
    }

    public function store(ProdukRequest $request): RedirectResponse
    {
        Produk::create($request->safe()->only(['nama_produk', 'stok', 'harga_beli', 'harga_jual']));

        return redirect()->route('produk.index')->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk): View
    {
        return view('produk.edit', ['produk' => $produk]);
    }

    public function update(ProdukRequest $request, Produk $produk): RedirectResponse
    {
        $produk->update($request->safe()->only(['nama_produk', 'stok', 'harga_beli', 'harga_jual']));

        return redirect()->route('produk.index')->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        if ($produk->penjualan()->exists()) {
            return redirect()->route('produk.index')->with(
                'error',
                'Produk "'.$produk->nama_produk.'" sudah memiliki riwayat penjualan dan tidak dapat dihapus. Atur stok menjadi 0 melalui menu ubah.'
            );
        }

        $produk->delete();

        return redirect()->route('produk.index')->with('status', 'Produk berhasil dihapus.');
    }
}
