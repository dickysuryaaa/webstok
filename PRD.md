# PRD: Aplikasi Web Stok, Penjualan, dan Chatbot Cek Stok

> Dokumen ini ditulis agar dapat langsung diberikan ke agent AI pembuat kode.
> Baca seluruh dokumen sebelum menulis kode. Kerjakan sesuai urutan di bagian 11.
> Jangan menambah fitur di luar bagian 4 (In Scope). Jika ada hal yang ambigu, pilih opsi paling sederhana, tulis asumsinya di `DECISIONS.md`, lalu lanjut.

## 1. Ringkasan

Aplikasi web untuk sebuah toko mitra yang saat ini mencatat semuanya secara manual. Aplikasi memusatkan data produk (stok, harga beli, harga jual, keuntungan), mencatat penjualan dengan cepat, dan menyediakan chatbot yang dapat mengecek stok secara otomatis. Aplikasi harus nyaman dipakai dari HP.

Konteks proyek: tugas kuliah dengan batas **3 tabel utama dan 1 alur kerja inti**. Kesederhanaan lebih penting daripada kelengkapan.

## 2. Masalah yang Diselesaikan (dari wawancara pemilik)

| ID | Masalah | Dasar |
|---|---|---|
| M1 | Mencatat penjualan satu per satu secara manual memakan waktu | Wawancara no. 1, 2 |
| M2 | Harga jual barang stok lama sering terlupa | Wawancara no. 3 |
| M3 | Harga acuan tersebar di grup Telegram pemasok, tidak tersimpan di sistem toko | Wawancara no. 4 |
| M4 | Pemilik ingin aplikasi lengkap (stok, harga beli, harga jual, keuntungan) yang bisa dicek lewat HP | Wawancara no. 5, 6 |

Fitur chatbot berasal dari rencana toko, bukan dari wawancara pertama.

## 3. Pengguna dan Peran

| Peran | Kegiatan | Perangkat |
|---|---|---|
| `pemilik` | Melihat semua data termasuk harga beli dan keuntungan, mengelola produk, melihat rekap | HP (utama), laptop |
| `pekerja` | Mencatat penjualan, melihat stok dan harga jual, memakai chatbot | HP atau laptop |
| `pelanggan` (opsional) | Menanyakan stok lewat chatbot. **Belum dikonfirmasi mitra**, dikontrol lewat flag `CHATBOT_PUBLIC` (default `false`) | HP |

## 4. Ruang Lingkup

### In Scope
1. Login dan dua peran (`pemilik`, `pekerja`).
2. Manajemen produk (CRUD + pencarian).
3. Pencatatan penjualan dengan stok berkurang otomatis.
4. Chatbot cek stok (berbasis aturan, bukan AI generatif).
5. Rekap harian dan daftar stok menipis.
6. Tampilan responsif untuk HP.

### Out of Scope (jangan dibuat)
- Pengiriman, pelacakan pesanan, GPS.
- Chatbot berbasis LLM atau percakapan bebas.
- Integrasi WhatsApp/Telegram, notifikasi, sinkronisasi harga pemasok.
- Pembayaran daring, keranjang multi-item, laporan bulanan, ekspor Excel/PDF.
- Tabel baru di luar bagian 6.

## 5. Tech Stack

| Komponen | Pilihan |
|---|---|
| Framework | Laravel 11 atau lebih baru, PHP 8.2+ |
| Database | **MySQL 8 atau MariaDB 10.6+** (utama, untuk pengembangan dan produksi). SQLite in-memory hanya untuk tes otomatis |
| Auth | Laravel Breeze (Blade) |
| Frontend | Blade + Tailwind CSS via Vite, mobile-first |
| Tes | PHPUnit / Pest feature tests |
| Chatbot | Service class PHP di sisi server, tanpa library AI |

Asumsi: stack di atas dipilih karena proyek berupa tugas Laravel. Ubah bila dosen atau mitra menentukan lain.

Alasan MySQL/MariaDB: pemilik dan pekerja dapat menulis bersamaan, dan `lockForUpdate()` pada aturan bisnis nomor 2 hanya benar-benar mengunci baris di MySQL/MariaDB, tidak di SQLite. Aplikasi juga harus di-hosting agar bisa dibuka dari HP.

Contoh `.env` (agent wajib menyertakan `.env.example` dengan nilai ini):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toko_stok
DB_USERNAME=root
DB_PASSWORD=
```

Konfigurasi tes (`phpunit.xml`): `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`. Karena `lockForUpdate()` tidak berefek di SQLite, tes konkurensi penjualan tidak perlu dibuat; cukup diverifikasi manual di MySQL.

## 6. Model Data (3 Tabel Utama)

Tabel `users` bawaan Laravel dipakai untuk login dan tidak dihitung sebagai tabel utama. Tambahkan kolom `role` (`pemilik` | `pekerja`, default `pekerja`).

### `produk`
| Kolom | Tipe | Aturan |
|---|---|---|
| id | bigint PK | |
| nama_produk | string(150) | wajib, unik |
| stok | unsigned integer | wajib, default 0, tidak boleh negatif |
| harga_beli | unsigned integer (rupiah) | wajib |
| harga_jual | unsigned integer (rupiah) | wajib, harus >= harga_beli (validasi, boleh dilanggar hanya dengan konfirmasi di form) |
| timestamps | | |

Atribut turunan (accessor, bukan kolom): `keuntungan_per_unit = harga_jual - harga_beli`.

### `penjualan`
Satu baris = satu produk terjual dalam satu transaksi (tanpa keranjang multi-item).

| Kolom | Tipe | Aturan |
|---|---|---|
| id | bigint PK | |
| produk_id | FK -> produk | wajib, `restrictOnDelete` |
| user_id | FK -> users | pencatat penjualan |
| jumlah | unsigned integer | wajib, >= 1, <= stok saat itu |
| total_harga | unsigned integer | snapshot: `harga_jual * jumlah` saat transaksi |
| keuntungan | **signed integer** (bukan `unsigned`) | snapshot: `(harga_jual - harga_beli) * jumlah` saat transaksi; bisa negatif bila dijual rugi. MySQL menolak nilai negatif di kolom `unsigned` |
| tanggal_penjualan | datetime | default sekarang |
| timestamps | | |

### `log_chatbot`
| Kolom | Tipe | Aturan |
|---|---|---|
| id | bigint PK | |
| user_id | FK nullable -> users | null bila pelanggan publik |
| produk_id | FK nullable -> produk | null bila produk tidak ditemukan |
| pertanyaan | string(255) | teks asli |
| jawaban | text | teks balasan |
| created_at | datetime | |

Relasi: Produk hasMany Penjualan; Penjualan belongsTo Produk dan User; Produk hasMany LogChatbot.

## 7. Aturan Bisnis

1. Stok tidak boleh negatif. Penjualan dengan `jumlah > stok` ditolak dengan pesan jelas.
2. Pembuatan penjualan dan pengurangan stok harus dalam satu **DB transaction** dengan `lockForUpdate` pada baris produk.
3. `total_harga` dan `keuntungan` disimpan sebagai snapshot. Mengubah harga produk di kemudian hari **tidak** mengubah penjualan lama.
4. Produk yang sudah punya penjualan tidak boleh dihapus (tampilkan pesan, sarankan set stok 0).
5. Ambang stok menipis = 5 unit, konfigurabel lewat `config('toko.stok_menipis', 5)`.
6. Semua nominal dalam rupiah bulat, ditampilkan sebagai `Rp 12.500`.
7. Zona waktu `Asia/Jakarta`, bahasa antarmuka Indonesia.

## 8. Hak Akses

| Aksi | pemilik | pekerja | pelanggan (jika `CHATBOT_PUBLIC=true`) |
|---|---|---|---|
| Lihat daftar produk, stok, harga jual | ya | ya | tidak |
| Lihat harga beli dan keuntungan | ya | **tidak** | tidak |
| Tambah/ubah/hapus produk | ya | tidak | tidak |
| Catat penjualan | ya | ya | tidak |
| Lihat rekap harian (termasuk keuntungan) | ya | hanya total penjualan dan jumlah transaksi | tidak |
| Pakai chatbot | ya | ya | ya |
| Lihat log chatbot | ya | tidak | tidak |

Asumsi: pekerja tidak boleh melihat harga beli dan keuntungan. Ubah bila mitra berbeda.

## 9. Fitur dan Kriteria Penerimaan

### F1. Autentikasi dan peran (M4)
- [ ] Login dan logout bekerja; halaman selain login dan chatbot publik memerlukan auth.
- [ ] Middleware peran: `role:pemilik` untuk rute pengelolaan produk dan log chatbot.
- [ ] Seeder membuat 1 akun `pemilik` dan 1 akun `pekerja` (email dan password contoh, tertulis di README).

### F2. Manajemen produk (M2, M3, M4)
- [ ] Daftar produk dengan pencarian nama (query `?q=`), urut nama, paginasi 20.
- [ ] Kolom daftar: nama, stok, harga jual; untuk `pemilik` ditambah harga beli dan keuntungan per unit.
- [ ] Badge "Stok menipis" bila `stok <= ambang` dan "Habis" bila `stok = 0`.
- [ ] Form tambah dan ubah produk dengan validasi (bagian 6). Hanya `pemilik`.
- [ ] Stok bertambah lewat ubah produk (tidak ada fitur barang masuk terpisah).
- [ ] Tampilan kartu di layar sempit, tabel di layar lebar.

### F3. Pencatatan penjualan (M1)
- [ ] Form: pilih produk (dropdown dengan pencarian ketik), isi jumlah, simpan. Maksimal 3 ketukan di HP setelah memilih produk.
- [ ] Menampilkan stok tersisa dan harga jual saat produk dipilih.
- [ ] Setelah simpan: stok berkurang, `total_harga` dan `keuntungan` tersimpan sebagai snapshot, tampil pesan sukses.
- [ ] Penolakan bila jumlah melebihi stok.
- [ ] Daftar penjualan hari ini di halaman yang sama, terbaru di atas.

### F4. Chatbot cek stok otomatis (rencana toko, selaras M4)
- [ ] Halaman `/chatbot` berupa tampilan chat sederhana (daftar pesan + kolom input), tanpa library eksternal.
- [ ] Pengiriman pesan lewat `POST /chatbot/pesan` (JSON), balasan ditampilkan tanpa reload.
- [ ] Logika berada di `App\Services\ChatbotStokService` yang **tidak bergantung pada HTTP**, agar dapat dipasang ke WhatsApp/Telegram di kemudian hari.
- [ ] Setiap pertanyaan dan jawaban dicatat di `log_chatbot`.
- [ ] Chatbot **tidak pernah** menampilkan `harga_beli` atau `keuntungan` kepada siapa pun, termasuk pemilik.
- Spesifikasi perilaku ada di bagian 10.

### F5. Rekap harian dan stok menipis (M4)
- [ ] Halaman dasbor: jumlah transaksi hari ini, total penjualan hari ini, keuntungan hari ini (hanya `pemilik`), daftar produk stok menipis.
- [ ] Pemilih tanggal untuk melihat rekap hari lain.

### F6. Responsif HP
- [ ] Semua halaman nyaman di lebar 360 px: tombol minimal 44 px, tanpa scroll horizontal.
- [ ] Meta viewport benar, navigasi berupa menu ringkas.

## 10. Spesifikasi Chatbot (Rule-Based)

### Alur `ChatbotStokService::jawab(string $pesan, ?User $user): string`
1. **Normalisasi**: huruf kecil, hapus tanda baca, rapikan spasi.
2. **Cek sapaan**: bila pesan hanya sapaan (`halo`, `hai`, `pagi`, `siang`, `sore`, `malam`) -> balas sapaan + contoh pertanyaan.
3. **Ekstrak kata kunci produk**: buang kata bantu (`stok`, `stock`, `ada`, `berapa`, `sisa`, `masih`, `cek`, `harga`, `barang`, `nya`, `apa`, `gak`, `nggak`, `tidak`, `ya`, `kak`, `min`, `dong`, `tolong`, `mau`, `tanya`). Sisa kata = kata kunci.
4. Bila kata kunci kosong -> balas bantuan format pertanyaan.
5. **Pencarian**: `produk.nama_produk LIKE %kata%` untuk setiap kata kunci (semua kata harus cocok). Urutkan: kecocokan persis dulu, lalu abjad. Batasi 5 hasil.
6. **Hasil**:
   - 1 produk cocok -> jawaban stok (lihat templat).
   - 2 sampai 5 produk cocok -> daftar singkat nama, stok, harga jual, tanpa menebak.
   - 0 produk -> balas tidak ditemukan + saran cek ejaan.
7. Catat ke `log_chatbot` (`produk_id` terisi bila tepat 1 hasil).

### Templat balasan
| Kondisi | Balasan |
|---|---|
| Stok > ambang | `{nama} tersedia, stok {stok} unit. Harga jual Rp {harga}.` |
| 0 < stok <= ambang | `{nama} tersisa {stok} unit (menipis). Harga jual Rp {harga}.` |
| Stok = 0 | `{nama} sedang habis.` |
| Banyak hasil | `Ada beberapa produk yang cocok:` lalu satu baris per produk: `- {nama}: stok {stok}, Rp {harga}` |
| Tidak ditemukan | `Maaf, produk "{kata}" tidak ditemukan. Coba cek ejaan atau gunakan nama yang lebih singkat.` |
| Bantuan | `Ketik nama barang untuk cek stok, contoh: "stok {contoh}".` (contoh diambil dari produk pertama di database, atau teks umum bila kosong) |

### Contoh uji
| Input | Perilaku yang diharapkan |
|---|---|
| `stok [produk A] ada?` | Jawaban stok produk A |
| `HARGA [produk A] berapa` | Jawaban yang sama (harga ikut ditampilkan) |
| `halo` | Sapaan + contoh |
| `xyzabc` | Tidak ditemukan |
| `stok` (tanpa nama) | Bantuan format |
| Kata kunci cocok 3 produk | Daftar 3 produk |

## 11. Urutan Pengerjaan (kerjakan berurutan, commit per tahap)

1. **Setup**: proyek Laravel, Breeze, koneksi MySQL/MariaDB (`.env` dan `.env.example`), zona waktu dan bahasa, kolom `role`, seeder akun.
2. **Migrasi dan model**: `produk`, `penjualan`, `log_chatbot`, relasi, accessor `keuntungan_per_unit`, factory dan seeder 10 produk contoh bernama generik (mis. "Produk Contoh 1").
3. **F2 Produk**: controller, request validation, view, middleware peran.
4. **F3 Penjualan**: service/transaction, form, daftar hari ini.
5. **F4 Chatbot**: `ChatbotStokService`, controller, halaman chat, log.
6. **F5 Rekap**: dasbor dan stok menipis.
7. **F6 Responsif** dan perapian UI.
8. **Tes** (bagian 12) dan **README**.

## 12. Tes Minimum (feature test)

- Pekerja tidak bisa membuka rute tambah/ubah/hapus produk (403).
- Pekerja tidak melihat harga beli atau keuntungan di respons halaman produk dan rekap.
- Penjualan valid mengurangi stok dan menyimpan snapshot `total_harga` dan `keuntungan`.
- Penjualan dengan `jumlah > stok` ditolak dan stok tidak berubah.
- Mengubah harga produk tidak mengubah `total_harga` penjualan lama.
- Produk yang punya penjualan tidak bisa dihapus.
- Chatbot: satu hasil, banyak hasil, tidak ditemukan, stok habis, stok menipis, sapaan, pesan kosong.
- Respons chatbot tidak pernah memuat nilai `harga_beli`.
- `/chatbot` publik hanya bisa diakses tanpa login bila `CHATBOT_PUBLIC=true`.

## 13. Definition of Done

- [ ] Semua kotak F1 sampai F6 tercentang.
- [ ] `php artisan migrate:fresh --seed` berjalan bersih di MySQL/MariaDB (bukan hanya SQLite).
- [ ] `php artisan test` hijau.
- [ ] README memuat: cara instalasi, akun contoh, cara menjalankan, daftar fitur.
- [ ] `DECISIONS.md` memuat semua asumsi yang diambil agent.
- [ ] Tidak ada fitur dari bagian Out of Scope.

## 14. Pertanyaan Terbuka (harus dikonfirmasi ke mitra, jangan diputuskan sendiri oleh agent)

1. Siapa yang memakai chatbot: pemilik, pekerja, atau juga pelanggan? (menentukan `CHATBOT_PUBLIC`)
2. Chatbot diakses lewat apa: web, WhatsApp, atau Telegram?
3. Apakah pekerja boleh melihat harga beli dan keuntungan?
4. Bagaimana stok masuk dicatat saat barang baru datang dari pemasok?

Sampai dijawab, gunakan nilai default di dokumen ini dan catat di `DECISIONS.md`.
