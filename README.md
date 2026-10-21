# Sistem Informasi Manajemen Pemesanan Tiket Bioskop

Aplikasi Laravel untuk mengelola judul film, harga tiket, jadwal tayang, data penonton, pemesanan, pembayaran/status pesanan, dan laporan per periode.

## Persyaratan

- PHP 8.1 atau lebih baru
- Composer
- MySQL/MariaDB atau database yang didukung Laravel
- Node.js dan npm untuk membangun aset frontend

## Menjalankan aplikasi

1. Salin `.env.example` menjadi `.env`, lalu atur koneksi database.
2. Pasang dependency dan buat application key:

   ```powershell
   composer install
   php artisan key:generate
   ```

3. Jalankan migrasi dan data contoh:

   ```powershell
   php artisan migrate
   php artisan db:seed
   ```

   `db:seed` membuat akun admin, tiga film contoh, dan sedikitnya 15 akun penonton demo. Perintah ini tidak menghapus data; gunakan `migrate:fresh` hanya jika memang ingin menghapus seluruh tabel dan memulai ulang.

4. Pasang dan jalankan aset frontend:

   ```powershell
   npm install
   npm run dev
   ```

5. Jalankan aplikasi lokal:

   ```powershell
   php artisan serve
   ```

   Buka `http://127.0.0.1:8000`.

   Halaman awal langsung menampilkan katalog film. Pengunjung dapat melihat film dan jadwal tayang; login diperlukan untuk memesan tiket.

## Akun dan peran

- Admin awal: `admin@gmail.com` / `password123`. Segera ubah password untuk penggunaan selain demonstrasi.
- Penonton: buat akun melalui halaman **Register**. Semua pendaftaran publik mendapat peran `user`; pilihan peran admin tidak tersedia di form publik.
- Akun `Penonton Demo 01` sampai `Penonton Demo 15` adalah data contoh untuk memenuhi kebutuhan demonstrasi. Passwordnya dibuat acak, jadi akun tersebut ditujukan sebagai data daftar penonton, bukan akun login.

## Menu

| Peran | Menu | Fungsi |
|---|---|---|
| Pengunjung | Katalog Film | Lihat film, harga, dan jadwal tayang; login/register tersedia di bagian atas halaman |
| Admin | Jadwal Tayang | CRUD jadwal film, tanggal/jam, dan studio |
| Admin | Pemesanan Tiket | Lihat pesanan, ubah status pembayaran, tambah/edit/hapus pesanan |
| Admin | Registrasi | CRUD akun penonton |
| Admin | Laporan | Daftar jadwal/film, tiket terjual, dan nilai pesanan dalam rentang tanggal |
| Penonton | Jadwal Tayang | Lihat jadwal mendatang |
| Penonton | Pemesanan Tiket | Pesan tiket dan lihat riwayat pesanan sendiri |

Dokumentasi struktur database dan langkah pengguna tersedia di [docs/ERD.md](docs/ERD.md) dan [docs/USER-MANUAL.md](docs/USER-MANUAL.md).

## Pengujian

```powershell
php artisan test
```
