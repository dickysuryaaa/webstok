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
