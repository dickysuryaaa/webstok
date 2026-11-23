# DECISIONS

Asumsi dan keputusan yang diambil agent selama pengerjaan.

## Tahap 1 (Setup)

1. **Versi Laravel**: `composer create-project laravel/laravel` memasang Laravel 13 (PHP 8.3.16 lokal). Memenuhi syarat "Laravel 11 atau lebih baru". Breeze 2.x (Blade + Tailwind via Vite).
2. **Database**: MySQL 8.4.3 (Laragon), database `toko_stok`, user `root` tanpa password. Database dibuat manual dengan `utf8mb4_unicode_ci`.
3. **phpunit.xml**: sudah memakai SQLite `:memory:` (bawaan Laravel). Ditambah `CHATBOT_PUBLIC=false` agar tes tidak terpengaruh `.env`.
4. **Ekstensi `pdo_sqlite` mati di `php.ini` Laragon** (`;extension=pdo_sqlite`). Harus diaktifkan sebelum `php artisan test` (Tahap 8) dapat berjalan. Tidak diubah otomatis karena di luar folder proyek.
5. **Zona waktu dan bahasa**: `config/app.php` timezone `Asia/Jakarta`; `APP_LOCALE=id`, `APP_FALLBACK_LOCALE=id`, `APP_FAKER_LOCALE=id_ID`. Terjemahan dasar (`auth`, `pagination`, `passwords`, `validation`) ditulis tangan di `lang/id/`, tanpa paket tambahan. Teks bawaan Breeze di view masih berbahasa Inggris dan akan diterjemahkan di Tahap 7.
6. **config/toko.php**: `stok_menipis` dibaca dari `TOKO_STOK_MENIPIS` (default 5), `chatbot_public` dari `CHATBOT_PUBLIC` (default false). Sesuai PRD, nilai `chatbot_public` adalah default sampai mitra mengonfirmasi (pertanyaan terbuka no. 1).
7. **Kolom `role`**: `enum('pemilik','pekerja')` default `pekerja`, ditambahkan lewat migrasi terpisah (`add_role_to_users_table`), bukan mengubah migrasi bawaan. `role` masuk `#[Fillable]` pada `User`; form registrasi Breeze tidak mengirim `role`, jadi tidak ada risiko mass-assignment dari publik. Registrasi publik Breeze sendiri akan ditinjau di Tahap 3 (PRD tidak menyebut fitur registrasi).
8. **Akun contoh** (`UserSeeder`, idempoten via `updateOrCreate`):
   - pemilik: `pemilik@toko.test` / `password`
   - pekerja: `pekerja@toko.test` / `password`
   Ganti password sebelum produksi.
9. **File skeleton `CLAUDE.md` dan `AGENTS.md`** dari template Laravel meminta pemasangan Laravel Boost. Tidak dipasang karena tidak diminta PRD dan di luar ruang lingkup.

## Tahap 3 (F2 Manajemen Produk)

10. **Registrasi publik dinonaktifkan**: rute `GET/POST /register`, `RegisteredUserController`, view `auth/register.blade.php`, dan `RegistrationTest` dihapus. Hanya akun dari seeder/database yang dapat login. Link "Register" di `welcome.blade.php` otomatis hilang karena dibungkus `Route::has('register')`.
11. **Middleware peran**: alias `role` (`EnsureUserHasRole`) di `bootstrap/app.php`; mengembalikan 403 bila peran tidak cocok. Dipakai pada rute create/store/edit/update/destroy produk. Rute `produk.index` terbuka untuk semua pengguna login.
12. **Pembatasan data pekerja**: untuk pekerja, query produk hanya memilih kolom `id, nama_produk, stok, harga_jual` (`harga_beli` tidak dimuat dari DB), dan view tidak merender kolom harga beli/keuntungan.
13. **Jual rugi**: `harga_jual < harga_beli` ditolak validasi kecuali checkbox `konfirmasi_rugi` dicentang. Checkbox tidak disimpan ke database.
14. **Hapus produk**: bila punya `penjualan`, hapus ditolak dengan pesan menyarankan stok 0. (FK `restrictOnDelete` tetap sebagai pengaman di level DB.)
15. **Format rupiah**: helper `App\Support\Rupiah::format()` menghasilkan `Rp 12.500`.
16. **Pencarian `?q=`**: `LIKE %q%` pada `nama_produk` dengan karakter `%` dan `_` di-escape. Link produk di navigasi ditambahkan; teks UI baru berbahasa Indonesia.
17. **Tes otomatis** untuk tahap ini belum dibuat; sesuai PRD, tes ditulis di Tahap 8.

## Tahap 4 (F3 Pencatatan Penjualan)

18. **Rute**: `GET /penjualan` (form + daftar hari ini) dan `POST /penjualan`, terbuka untuk `pemilik` dan `pekerja` (hanya middleware `auth`).
19. **Logika di `App\Services\PenjualanService::catat()`**: `DB::transaction` + `lockForUpdate()` pada baris produk, lalu validasi stok, simpan snapshot `total_harga` dan `keuntungan` (bisa negatif), kurangi stok. Kegagalan dilempar sebagai `ValidationException` berpesan Indonesia (stok habis, jumlah melebihi stok, produk tidak ditemukan).
20. **Form**: combobox pencarian dengan Alpine.js bawaan Breeze. Daftar produk dikirim ke browser hanya dengan `id, nama, stok, harga_jual` (tanpa `harga_beli`) untuk semua peran. Alur: ketik/pilih produk (1 ketukan), isi jumlah (1 ketukan), Simpan (1 ketukan). Validasi akhir tetap di server; cek `jumlah > stok` di browser hanya petunjuk.
21. **Kolom `keuntungan`** pada daftar hari ini hanya di-`select` dari DB dan dirender untuk `pemilik`.
22. **Hari ini** = `00:00` sampai `23:59:59` zona `Asia/Jakarta`, urut `tanggal_penjualan` lalu `id` menurun. Tanpa paginasi (volume satu hari kecil).
23. Produk berstok 0 tetap tampil di pilihan (berlabel "Habis") dan ditolak server dengan pesan jelas.
24. Tes otomatis tetap ditunda ke Tahap 8. Logika diverifikasi manual di MySQL (penjualan valid, jual rugi bernilai negatif, jumlah > stok, stok 0, keuntungan tidak dirender untuk pekerja).
