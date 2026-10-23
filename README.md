# Mais Batik

## Deskripsi Singkat

Mais Batik adalah **[isi deskripsi singkat dan tujuan proyek di sini]**.

## Status Fitur

| Fitur | Status |
| --- | --- |
| Halaman awal | Tersedia; bagian cerita dan inspirasi motif |
| Filter kategori, pencarian, dan detail produk | Tersedia |
| Navigasi responsif | Tersedia |
| Login Admin dan CRUD produk/pesanan | Tersedia; konfigurasi database dan akun admin wajib disiapkan |
| Katalog publik berbasis database | Tersedia setelah database disiapkan |
| POS | Placeholder; belum terhubung ke sistem |

Perbarui status fitur seiring perkembangan proyek.

## Struktur Proyek

```text
Mais Batik/
├── config/                 # Konfigurasi aplikasi dan database
│   ├── connection.php
│   └── database.php.example
├── database/
│   └── schema.sql          # Skema tabel produk dan pesanan
├── controllers/            # Ruang untuk controller aplikasi berikutnya
├── models/                 # Repository produk dan pesanan
│   ├── ProductRepository.php
│   └── OrderRepository.php
├── views/                  # Template antarmuka
├── routes/                 # Definisi rute aplikasi
├── public/                 # Document root dan aset publik
│   ├── assets/
│   │   ├── css/
│   │   │   ├── style.css
│   │   │   └── admin.css
│   │   ├── js/
│   │   │   ├── main.js
│   │   │   └── admin.js
│   │   └── images/
│   │       ├── baju-koko.svg
│   │       ├── sarung-instan-anak.svg
│   │       └── blouse-batik.svg
│   ├── admin/
│   │   └── index.php       # Login dan panel CRUD admin
│   ├── katalog.php         # Katalog publik dari database
│   └── index.html          # Halaman awal publik
├── storage/
│   └── uploads/             # File unggahan; jangan simpan file publik di sini
└── tests/                   # Pengujian aplikasi
```

`public/` adalah document root yang disarankan untuk web server. Simpan kode aplikasi dan konfigurasi di luar document root.

## Panduan Instalasi Lokal

### Prasyarat

- PHP 8.1 atau lebih baru dengan ekstensi PDO MySQL dan mbstring.
- Composer, jika proyek memiliki `composer.json`.
- Node.js dan npm, jika proyek memiliki `package.json`.
- Git.

### Langkah instalasi

1. Clone repositori, lalu masuk ke folder proyek:

   ```bash
   git clone <URL_REPOSITORI>
   cd <NAMA_FOLDER_PROYEK>
   ```

2. Salin templat konfigurasi database:

   ```bash
   cp config/database.php.example config/database.php
   ```

   Di Windows PowerShell, gunakan `Copy-Item config/database.php.example config/database.php`.
   Atur variabel environment `DB_HOST`, `DB_NAME`, `DB_USERNAME`, dan `DB_PASSWORD`
   pada lingkungan tempat aplikasi dijalankan. `DB_PORT` bersifat opsional dan
   menggunakan `3306` secara default. Jangan masukkan kredensial asli ke repositori.

3. Buat database dan tabel:

   ```bash
   mysql -u nama_user -p < database/schema.sql
   ```

   Di Windows PowerShell, gunakan:

   ```powershell
   Get-Content database\schema.sql | mysql -u nama_user -p
   ```

   `DB_NAME` harus bernilai `mais_batik`, sesuai nama database pada skema.

4. Konfigurasikan akun admin di environment server. Gunakan nama pengguna
   `ADMIN_USERNAME` dan hash password `ADMIN_PASSWORD_HASH`; jangan simpan
   kata sandi teks biasa di repositori. Buat hash dengan PHP:

   ```bash
   php -r "echo password_hash('GantiDenganPasswordKuat', PASSWORD_DEFAULT), PHP_EOL;"
   ```

   Simpan hasil hash tersebut di environment server sebagai `ADMIN_PASSWORD_HASH`.
   Pastikan gunakan HTTPS saat panel admin diakses melalui jaringan.

5. Instal dependensi sesuai teknologi yang digunakan:

   ```bash
   composer install   # Jika tersedia composer.json
   npm install        # Jika tersedia package.json
   ```

6. Atur web server agar document root mengarah ke folder `public/`, lalu jalankan
   aplikasi menggunakan perintah yang sesuai dengan framework atau konfigurasi proyek.
   Panel tersedia di `/admin/`, sedangkan katalog berbasis database ada di `/katalog.php`.
   Fungsi CRUD dan katalog dinamis membutuhkan PHP dan database MySQL yang aktif.

## Link Live Deployment

> Belum tersedia — tambahkan URL deployment di sini.

## Screenshot

> Belum tersedia — tambahkan screenshot aplikasi di sini.
