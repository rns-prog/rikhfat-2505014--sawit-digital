# Sawitku: Digitalisasi Pencatatan Panen Kelapa Sawit untuk Petani dan Pengelola Kebun Skala Kecil hingga Besar

Sawitku adalah aplikasi web untuk mencatat kebun, hasil panen, dan biaya operasional, lalu melihat ringkasan untung-rugi serta pengingat kegiatan kebun. Aplikasi ini dibuat sebagai tugas proyek mata kuliah **Pemrograman Berbasis AI**.

## Latar masalah

Petani dan pengelola kebun kelapa sawit sering mencatat hasil panen dan biaya secara manual di buku. Catatan yang tersebar menyulitkan mereka mengetahui pendapatan, pengeluaran, keuntungan bersih, dan produktivitas setiap blok kebun.

Sawitku membantu mengumpulkan catatan tersebut dalam satu aplikasi. Pengguna dapat mengelola kebun/blok, mencatat panen dan biaya, melihat laporan untung-rugi berdasarkan periode atau kebun, serta membuat pengingat panen dan pemupukan.

## Fitur

| Jenis | Fitur | Keterangan |
|---|---|---|
| **Inti** | Catat panen | Catatan tanggal, kebun, jumlah tandan, berat, harga per kg, dan pembeli; tersedia filter dan ringkasan. |
| **Inti** | Catat biaya | Catat biaya menurut kategori. Upah panen dapat dicatat manual atau dihitung otomatis dari berat panen dan tarif per kg. |
| **Inti** | Laporan untung-rugi | Rekap panen, pendapatan, biaya, dan laba; dilengkapi grafik serta unduhan PDF. |
| Pendukung | Login dan pendaftaran | Akun digunakan untuk membatasi data setiap pengguna. |
| Pendukung | Manajemen kebun/blok | Tambah, ubah, dan hapus kebun yang dimiliki pengguna. |
| Pendukung | Dashboard | Ringkasan data panen dan biaya, grafik, kebun, dan kalender jadwal. |
| Pendukung | Jadwal pengingat | Jadwal panen dan pemupukan sekali atau berulang mingguan/bulanan; pengingat tampil di dalam aplikasi. |

## Teknologi

| Teknologi | Penggunaan dan alasan |
|---|---|
| Laravel 12 | Menangani route, autentikasi, validasi, akses database, dan struktur aplikasi MVC. |
| Blade | Membuat halaman HTML di sisi server dan menggunakan kembali layout serta komponen tampilan. |
| SQLite | Database bawaan yang ringan dan mudah dipasang tanpa menjalankan server database terpisah. Untuk lingkungan lain, koneksi dapat diganti melalui `.env` dan skema dijalankan melalui migration; konfigurasi koneksi dan perilaku database tetap perlu disesuaikan. |
| Chart.js | Menampilkan grafik dashboard dan laporan di browser. |
| barryvdh/laravel-dompdf | Menghasilkan file PDF laporan dari tampilan Blade. |
| Vite | Mengompilasi dan membundel CSS serta JavaScript untuk penggunaan lokal maupun build produksi. |

## Alur sistem

```text
Daftar / Login
      |
      v
Tambah kebun atau blok
      |
      +--> Catat panen --------+
      |                        |
      +--> Catat biaya --------+--> Dashboard dan laporan untung-rugi
      |                              |
      +--> Atur jadwal              +--> Unduh laporan PDF
             panen/pemupukan
```

### Contoh alur teknis: pencatatan panen

1. Route `panen.*` di `routes/web.php` berada di dalam middleware `auth`.
2. `PanenController` menerima permintaan dan memanggil `PanenRequest` untuk validasi serta pemeriksaan kepemilikan.
3. Controller membaca dan menyimpan data lewat relasi pengguna yang sedang login, bukan mengambil catatan secara global.
4. Model `Panen` mendefinisikan relasi ke pengguna dan kebun, serta accessor pendapatan (`berat_kg × harga_per_kg`).
5. Controller mengirim data ke tampilan Blade di `resources/views/panen/`.

### Hitung otomatis upah panen

Pengguna memilih kebun, tarif per kg, dan rentang tanggal. Pratinjau menghitung total berat dari catatan panen pengguna pada kebun dan rentang tersebut. Ketika formulir disimpan, server menghitung ulang total berat dan nominal (`total berat × tarif`); angka dari browser tidak dijadikan sumber kebenaran. Jika tidak ada panen dalam rentang itu, biaya otomatis tidak disimpan.

## Struktur database

Semua tabel utama memiliki kolom `id`, `created_at`, dan `updated_at`, kecuali tabel sistem/framework yang mengikuti skema Laravel.

| Tabel | Kolom utama | Relasi |
|---|---|---|
| `users` | `name`, `email` (unik), `email_verified_at`, `password`, `remember_token` | Satu pengguna memiliki banyak kebun, catatan panen, biaya, dan jadwal. |
| `kebuns` | `user_id`, `nama`, `lokasi`, `luas_ha` | Milik satu pengguna; memiliki banyak panen dan biaya. Jadwal dapat menunjuk ke kebun secara opsional. |
| `panens` | `user_id`, `kebun_id`, `tanggal`, `jumlah_tandan`, `berat_kg`, `harga_per_kg`, `pembeli` (opsional) | Milik satu pengguna dan satu kebun. |
| `biayas` | `user_id`, `kebun_id`, `tanggal`, `kategori`, `nominal`, `keterangan`, `mode_hitung`, `tarif_per_kg`, `tanggal_mulai`, `tanggal_selesai`, `total_berat_kg` | Milik satu pengguna dan satu kebun. Kolom perhitungan upah otomatis dapat bernilai `null` untuk biaya manual. |
| `jadwals` | `user_id`, `kebun_id` (opsional), `jenis`, `judul`, `tanggal_mulai`, `ulang_setiap`, `ulang_satuan`, `tanggal_berakhir`, `catatan` | Milik satu pengguna; kebun boleh kosong untuk jadwal semua kebun. Jika kebun dihapus, hubungan jadwal ke kebun dikosongkan. |

Relasi `user_id` pada tabel kebun, panen, biaya, dan jadwal mengacu ke `users`. Relasi panen dan biaya ke `kebuns` wajib. Penghapusan pengguna menghapus data terkait melalui foreign key cascade; penghapusan kebun menghapus catatan panen dan biaya terkait.

## Instalasi dan menjalankan di Windows/XAMPP

### Prasyarat

- PHP **8.2 atau lebih baru** dan Composer.
- Ekstensi PHP `pdo_sqlite` dan `sqlite3` aktif. Periksa `php.ini` yang digunakan XAMPP jika ekstensi belum tampil pada `php -m`.
- Node.js dan npm yang kompatibel dengan Vite 7.
- Git.

Pastikan executable PHP XAMPP dan Composer tersedia di `PATH`, lalu buka PowerShell.

### Langkah instalasi

```powershell
git clone https://github.com/rns-prog/sawit-panen-laravel.git
Set-Location sawit-panen-laravel

php -v
composer -V
node -v
npm -v

composer install

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
}

php artisan key:generate

if (-not (Test-Path database\database.sqlite)) {
    New-Item -ItemType File -Path database\database.sqlite | Out-Null
}
```

Pastikan `.env` memuat:

```dotenv
DB_CONNECTION=sqlite
```

Lanjutkan menyiapkan database dan aset:

```powershell
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Buka alamat yang ditampilkan Artisan, biasanya `http://127.0.0.1:8000`. Untuk pengembangan frontend dengan hot reload, jalankan `npm run dev` di terminal terpisah selama `php artisan serve` berjalan.

SQLite dipakai secara default. Jika ingin menggunakan MySQL, atur `DB_CONNECTION`, host, nama database, username, dan password di `.env`, siapkan databasenya, lalu jalankan migration. Pastikan juga tipe kolom dan konfigurasi migration sesuai dengan versi database yang digunakan.

### Akun contoh seeder

Seeder membuat akun lokal berikut:

| Email | Kata sandi |
|---|---|
| `petani.contoh@example.com` | `password` |

Akun ini hanya untuk pengembangan/demo lokal. Ganti kata sandi dan konfigurasi keamanan sebelum aplikasi dipasang di lingkungan yang dapat diakses pihak lain.

## Pengujian

Jalankan seluruh pengujian dengan:

```powershell
php artisan test
```

Pengujian mencakup fitur autentikasi, kebun, panen, biaya, laporan, dan perhitungan kemunculan jadwal.

## Struktur folder penting

```text
app/
  Http/Controllers/   Controller halaman dan aksi aplikasi
  Http/Requests/      Validasi dan otorisasi formulir
  Models/             Model Eloquent dan relasi
  Services/           Agregasi laporan dan penyusunan event jadwal
database/
  migrations/         Definisi skema database
  seeders/            Data contoh untuk pengembangan
resources/
  css/                CSS aplikasi
  js/                 Dashboard, grafik, kalender, dan interaksi form
  views/              Tampilan Blade
routes/
  web.php             Route halaman aplikasi
  auth.php            Route autentikasi Breeze
tests/
  Feature/            Pengujian alur fitur dan akses pengguna
  Unit/               Pengujian perhitungan kemunculan jadwal
docs/                 Dokumentasi dan bukti uji pengguna yang diisi mahasiswa
```

## Penggunaan AI

GitHub Copilot dan Claude digunakan untuk membantu penulisan kode dan desain aplikasi sesuai ketentuan mata kuliah. Pemilihan masalah, wawancara, penentuan fitur, pengujian, dan pemahaman alur sistem dikerjakan sendiri oleh mahasiswa. Mahasiswa tetap perlu memahami, memeriksa, dan bertanggung jawab atas kode serta dokumentasi proyek.

## Pengerjaan

- **Jenis pengerjaan:** Individu
- **Nama:** Rikhfat Nur Sandi
- **Program studi:** Ilmu Komputer

## Status wawancara dan uji coba pengguna

Bagian ini **belum merupakan laporan hasil uji pengguna**. Mahasiswa mengisinya sendiri setelah wawancara dan uji coba benar-benar dilakukan. Jangan isi dengan data perkiraan; minta izin sebelum mencantumkan nama atau menyimpan foto/tangkapan layar.

### Template pengisian manual

- **Narasumber (nama samaran bila diperlukan):** `[isi setelah wawancara]`
- **Peran narasumber:** `[isi setelah wawancara]`
- **Tanggal wawancara:** `[tanggal]`
- **Ringkasan masalah yang disampaikan:** `[isi ringkasan faktual]`
- **Tanggal uji coba:** `[tanggal]`
- **Skenario uji coba:** `[langkah yang dicoba]`
- **Umpan balik uji coba:** `[catat sesuai masukan narasumber]`
- **Tindak lanjut:** `[perbaikan atau alasan belum diperbaiki]`
- **Tautan foto/tangkapan layar di folder `docs/`:** `[isi path file asli dengan izin yang sesuai, atau tulis "belum ada"]`

## Batasan dan rencana pengembangan

Versi ini berfokus pada pencatatan kebun, panen, biaya, laporan, dan pengingat dalam aplikasi. Belum tersedia:

- Notifikasi pengingat melalui email atau WhatsApp.
- Role terpisah untuk pekerja; data dikelola melalui akun pengguna.
- Aplikasi mobile native atau sinkronisasi offline.
- Fitur operasional lain seperti stok pupuk, inventaris, atau penggajian pekerja.

Pengembangan berikutnya dapat diprioritaskan setelah mendapatkan umpan balik pengguna yang nyata, misalnya meningkatkan kemudahan pencatatan di ponsel atau menambahkan kanal notifikasi sesuai kebutuhan dan persetujuan pengguna.
