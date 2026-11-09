# Catat Kebun

Aplikasi pencatatan kebun kelapa sawit untuk petani dan pengelola kebun. Tahap ini mencakup autentikasi pengguna dan pengelolaan data kebun/blok. Menu Panen, Biaya, dan Laporan ditampilkan sebagai bagian tahap berikutnya dan belum aktif.

## Menjalankan aplikasi

Prasyarat: PHP 8.2+, ekstensi `pdo_sqlite`, Composer, dan Node.js/npm.

1. Pasang dependensi PHP dan JavaScript:

   ```sh
   composer install
   npm install
   ```

2. Pastikan `.env` menggunakan `DB_CONNECTION=sqlite` dan berkas `database/database.sqlite` tersedia.
3. Siapkan database dan aset:

   ```sh
   php artisan migrate --seed
   npm run build
   ```

4. Jalankan aplikasi:

   ```sh
   php artisan serve
   ```

Pengguna dapat mendaftar melalui halaman **Daftar**, lalu masuk untuk mengelola kebun miliknya.

## Alur data kebun

- Route `kebun.*` di `routes/web.php` membatasi endpoint pengelolaan kebun untuk pengguna yang sudah masuk.
- `KebunController` mengambil data melalui relasi kebun milik pengguna yang sedang masuk. Pemeriksaan kepemilikan juga diterapkan sebelum ubah atau hapus.
- `KebunRequest` memvalidasi nama, lokasi, dan luas dalam hektare.
- Model `Kebun` menyimpan data melalui Eloquent; controller mengirim data dan status ke tampilan Blade di `resources/views/kebun`.
- Form hapus meminta konfirmasi sebelum mengirim permintaan penghapusan.

Autentikasi register, login, dan logout menggunakan scaffold Laravel Breeze (Blade). Pengujian dijalankan dengan `php artisan test`.
