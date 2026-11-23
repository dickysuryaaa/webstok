<?php

namespace App\Services;

use App\Models\LogChatbot;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Chatbot cek stok berbasis aturan. Tidak bergantung pada HTTP maupun library AI,
 * sehingga dapat dipasang ke kanal lain (WhatsApp/Telegram) di kemudian hari.
 *
 * Hanya kolom id, nama_produk, stok, harga_jual yang dibaca dari tabel produk:
 * harga_beli dan keuntungan tidak pernah dimuat maupun ditampilkan.
 */
class ChatbotStokService
{
    private const SAPAAN = ['halo', 'hai', 'pagi', 'siang', 'sore', 'malam'];

    /** Pelengkap sapaan ("selamat pagi"); hanya dipakai pada pengecekan sapaan. */
    private const PELENGKAP_SAPAAN = ['selamat'];

    private const KATA_BANTU = [
        'stok', 'stock', 'ada', 'berapa', 'sisa', 'masih', 'cek', 'harga', 'barang', 'nya',
        'apa', 'gak', 'nggak', 'tidak', 'ya', 'kak', 'min', 'dong', 'tolong', 'mau', 'tanya',
    ];

    private const BATAS_HASIL = 5;

    public function jawab(string $pesan, ?User $user): string
    {
        $kata = $this->normalisasi($pesan);
        $produkId = null;

        if ($this->hanyaSapaan($kata)) {
            $jawaban = 'Halo! Saya bisa membantu cek stok barang. '.$this->teksBantuan();
        } else {
            $kunci = array_values(array_filter($kata, fn (string $k) => ! in_array($k, self::KATA_BANTU, true)));

            if ($kunci === []) {
                $jawaban = $this->teksBantuan();
            } else {
                $hasil = $this->cari($kunci);
                $jawaban = $this->susunJawaban($hasil, implode(' ', $kunci));
                $produkId = $hasil->count() === 1 ? $hasil->first()->id : null;
            }
        }

        LogChatbot::create([
            'user_id' => $user?->id,
            'produk_id' => $produkId,
            'pertanyaan' => mb_substr($pesan, 0, 255),
            'jawaban' => $jawaban,
        ]);

        return $jawaban;
    }

    /** Huruf kecil, hapus tanda baca, rapikan spasi; dikembalikan sebagai daftar kata. */
    private function normalisasi(string $pesan): array
    {
        $teks = mb_strtolower($pesan);
        $teks = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $teks) ?? '';
        $teks = trim(preg_replace('/\s+/u', ' ', $teks) ?? '');

        return $teks === '' ? [] : explode(' ', $teks);
    }

    private function hanyaSapaan(array $kata): bool
    {
        if ($kata === []) {
            return false;
        }

        $boleh = [...self::SAPAAN, ...self::PELENGKAP_SAPAAN];

        return array_diff($kata, $boleh) === [] && array_intersect($kata, self::SAPAAN) !== [];
    }

    /** @return Collection<int, Produk> */
    private function cari(array $kunci): Collection
    {
        $query = Produk::query()->select(['id', 'nama_produk', 'stok', 'harga_jual']);

        foreach ($kunci as $k) {
            $query->where('nama_produk', 'like', '%'.addcslashes($k, '\%_').'%');
        }

        return $query
            ->orderByRaw('LOWER(nama_produk) = ? DESC', [implode(' ', $kunci)])
            ->orderBy('nama_produk')
            ->limit(self::BATAS_HASIL)
            ->get();
    }

    private function susunJawaban(Collection $hasil, string $kataKunci): string
    {
        if ($hasil->isEmpty()) {
            return 'Maaf, produk "'.$kataKunci.'" tidak ditemukan. Coba cek ejaan atau gunakan nama yang lebih singkat.';
        }

        if ($hasil->count() > 1) {
            $baris = $hasil->map(fn (Produk $p) => '- '.$p->nama_produk.': stok '.$p->stok.', '.$this->rupiah($p->harga_jual));

            return "Ada beberapa produk yang cocok:\n".$baris->implode("\n");
        }

        /** @var Produk $p */
        $p = $hasil->first();
        $ambang = (int) config('toko.stok_menipis', 5);

        if ($p->stok <= 0) {
            return $p->nama_produk.' sedang habis.';
        }

        if ($p->stok <= $ambang) {
            return $p->nama_produk.' tersisa '.$p->stok.' unit (menipis). Harga jual '.$this->rupiah($p->harga_jual).'.';
        }

        return $p->nama_produk.' tersedia, stok '.$p->stok.' unit. Harga jual '.$this->rupiah($p->harga_jual).'.';
    }

    private function teksBantuan(): string
    {
        $contoh = Produk::query()->orderBy('id')->value('nama_produk') ?? 'nama barang';

        return 'Ketik nama barang untuk cek stok, contoh: "stok '.$contoh.'".';
    }

    private function rupiah(int $nilai): string
    {
        return 'Rp '.number_format($nilai, 0, ',', '.');
    }
}
