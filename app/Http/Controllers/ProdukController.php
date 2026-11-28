<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdukRequest;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\ProdukGambarRequest;
use Illuminate\Support\Facades\Storage;

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

        $ambang = (int) config('toko.stok_menipis', 5);

        // Filter cepat status stok: semua (default), menipis (1..ambang), habis (0).
        $status = (string) $request->query('status', '');
        if (! in_array($status, ['menipis', 'habis'], true)) {
            $status = '';
        }

        $produk = Produk::query()
            ->select($kolom)
            ->when($q !== '', fn ($query) => $query->where('nama_produk', 'like', '%'.addcslashes($q, '%_\\').'%'))
            ->when($status === 'menipis', fn ($query) => $query->whereBetween('stok', [1, $ambang]))
            ->when($status === 'habis', fn ($query) => $query->where('stok', 0))
            ->orderBy('nama_produk')
            ->paginate(20)
            ->withQueryString();

        return view('produk.index', [
            'produk' => $produk,
            'q' => $q,
            'status' => $status,
            'pemilik' => $pemilik,
            'ambang' => $ambang,
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

    public function editGambar(Produk $produk): View
    {
        return view('produk.gambar', [
            'produk' => $produk,
        ]);
    }

    /**
     * Menyimpan atau mengganti gambar produk.
     */
    public function updateGambar(
        ProdukGambarRequest $request,
        Produk $produk
    ): RedirectResponse {

        $gambarBaru = $request->file('gambar')
            ->store('produk', 'public');

        // Menghapus gambar lama jika tersedia.
        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
        }

        // Memperbarui lokasi gambar di database.
        $produk->update([
            'gambar' => $gambarBaru,
        ]);

        return redirect()
            ->route('produk.index')
            ->with('status', 'Gambar produk berhasil diperbarui.');
    }
}
