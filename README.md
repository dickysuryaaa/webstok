# Toko Stok

Aplikasi web untuk toko mitra: memusatkan data produk (stok, harga beli, harga jual, keuntungan), mencatat penjualan dengan cepat, dan menyediakan chatbot cek stok otomatis. Antarmuka berbahasa Indonesia dan dirancang mobile-first agar nyaman dipakai dari HP.

Proyek tugas kuliah: 3 tabel utama (`produk`, `penjualan`, `log_chatbot`) dan satu alur kerja inti (catat penjualan). Spesifikasi lengkap ada di `PRD.md`, asumsi pengembangan di `DECISIONS.md`.

## Fitur

- **Login dan dua peran**: `pemilik` dan `pekerja`. Registrasi publik dinonaktifkan; akun berasal dari seeder atau database.
- **Manajemen produk**: daftar dengan pencarian (`?q=`), paginasi 20, badge "Stok menipis" dan "Habis". Tambah, ubah, hapus hanya untuk pemilik. Produk yang sudah punya penjualan tidak dapat dihapus.
- **Pencatatan penjualan**: pilih produk (dropdown dengan pencarian ketik), isi jumlah, simpan. Stok berkurang otomatis dalam satu transaksi DB dengan `lockForUpdate`. `total_harga` dan `keuntungan` disimpan sebagai snapshot, sehingga perubahan harga tidak mengubah penjualan lama.
- **Chatbot cek stok** (`/chatbot`): berbasis aturan, tanpa AI generatif. Logika di `App\Services\ChatbotStokService`, tidak bergantung pada HTTP. Tidak pernah menampilkan harga beli atau keuntungan. Setiap pertanyaan dicatat; log hanya dapat dilihat pemilik.
- **Dasbor rekap harian**: jumlah transaksi, total penjualan, keuntungan (hanya pemilik), daftar stok menipis, dan pemilih tanggal.
- **Responsif HP**: layar 360 px tanpa scroll horizontal, tombol minimal 44 px, daftar produk berupa kartu di layar sempit dan tabel di layar lebar.

### Hak akses

| Aksi | pemilik | pekerja |
|---|---|---|
| Lihat produk, stok, harga jual | ya | ya |
| Lihat harga beli dan keuntungan | ya | tidak |
| Tambah/ubah/hapus produk | ya | tidak |
| Catat penjualan | ya | ya |
| Rekap harian | lengkap dengan keuntungan | total penjualan dan jumlah transaksi |
| Pakai chatbot | ya | ya |
| Lihat log chatbot | ya | tidak |

Pelanggan tanpa login dapat memakai chatbot hanya bila `CHATBOT_PUBLIC=true` (default `false`).

## Prasyarat

- PHP 8.2 atau lebih baru (dikembangkan dengan 8.3) dengan ekstensi `pdo_mysql`
- Composer
- MySQL 8 atau MariaDB 10.6+
- Node.js dan npm (untuk build Tailwind/Vite)
- Untuk menjalankan tes: ekstensi PHP `pdo_sqlite` dan `sqlite3` (tes memakai SQLite in-memory)

## Instalasi

1. Pasang dependensi:

   ```bash
   composer install
   npm install
   ```

2. Salin konfigurasi dan buat kunci aplikasi:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Buat database kosong di MySQL/MariaDB (collation `utf8mb4_unicode_ci`):

   ```sql
   CREATE DATABASE toko_stok CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. Sesuaikan koneksi di `.env` bila perlu (nilai bawaan `.env.example`):

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=toko_stok
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Jalankan migrasi dan seeder (membuat 2 akun contoh dan 10 produk contoh):

   ```bash
   php artisan migrate:fresh --seed
   ```

6. Build aset frontend:

   ```bash
   npm run build
   ```

### Konfigurasi toko (opsional, di `.env`)

| Variabel | Default | Fungsi |
|---|---|---|
| `TOKO_STOK_MENIPIS` | `5` | Ambang stok menipis (stok <= nilai ini) |
| `CHATBOT_PUBLIC` | `false` | `true` agar `/chatbot` dapat dipakai tanpa login |

## Akun contoh

Dibuat oleh `UserSeeder`. **Ganti password sebelum dipakai di produksi.**

| Peran | Email | Password |
|---|---|---|
| pemilik | `pemilik@toko.test` | `password` |
| pekerja | `pekerja@toko.test` | `password` |

## Menjalankan aplikasi

```bash
php artisan serve
```

Buka `http://localhost:8000`. Saat mengembangkan tampilan, jalankan `npm run dev` di terminal lain sebagai pengganti `npm run build`. Di Laragon, aplikasi juga dapat dibuka lewat virtual host otomatis (mis. `http://webstok.test`).

Untuk membuka dari HP di jaringan yang sama, jalankan `php artisan serve --host=0.0.0.0` lalu akses `http://<IP-komputer>:8000`.

## Menjalankan tes

```bash
php artisan test
```

Tes memakai SQLite in-memory (`phpunit.xml`), tidak menyentuh database MySQL. Bila muncul error `could not find driver`, aktifkan `extension=pdo_sqlite` dan `extension=sqlite3` di `php.ini`.

Cakupan tes minimum PRD bagian 12: pembatasan akses pekerja (403), penyembunyian harga beli dan keuntungan, snapshot penjualan, penolakan jumlah melebihi stok, perubahan harga tidak mengubah penjualan lama, larangan hapus produk yang punya penjualan, seluruh kondisi balasan chatbot, chatbot tidak membocorkan harga beli, dan akses `/chatbot` sesuai `CHATBOT_PUBLIC`.

Catatan: `lockForUpdate()` tidak berefek di SQLite, sehingga konkurensi penjualan tidak diuji otomatis dan cukup diverifikasi manual di MySQL.

## Struktur penting

| Lokasi | Isi |
|---|---|
| `app/Services/ChatbotStokService.php` | Logika chatbot (tanpa ketergantungan HTTP) |
| `app/Services/PenjualanService.php` | Transaksi penjualan dan pengurangan stok |
| `app/Http/Middleware/EnsureUserHasRole.php` | Middleware `role:pemilik` |
| `app/Http/Middleware/EnsureChatbotAccess.php` | Akses chatbot sesuai `CHATBOT_PUBLIC` |
| `config/toko.php` | Ambang stok menipis dan flag chatbot publik |
| `tests/Feature/` | Feature test |
