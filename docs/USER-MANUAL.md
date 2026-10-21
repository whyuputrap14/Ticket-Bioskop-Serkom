# Panduan Pengguna

## 1. Menyiapkan aplikasi

Atur koneksi database pada `.env`, kemudian jalankan:

```powershell
php artisan migrate
php artisan db:seed
php artisan serve
```

Buka alamat yang ditampilkan oleh `artisan serve` (umumnya `http://127.0.0.1:8000`).

## 2. Login dan registrasi

- Admin demonstrasi: `admin@gmail.com` / `password123`.
- Penonton membuat akun melalui tautan **Register** pada halaman awal/login. Setelah mendaftar, akun otomatis berperan sebagai user.
- Halaman admin dibatasi berdasarkan role. User biasa tidak dapat membuka URL admin secara langsung.

## 3. Alur admin

1. Login sebagai admin. Dashboard menyediakan menu **Jadwal Tayang**, **Pemesanan Tiket**, **Registrasi**, dan **Laporan**.
2. Buka **Jadwal Tayang**, lalu tambah film, tanggal/jam yang akan datang, dan studio. Jadwal yang sama untuk film dan studio yang sama tidak dapat didaftarkan dua kali.
3. Buka **Registrasi** untuk melihat, menambah, mengubah, atau menghapus akun penonton. Form admin membuat akun sebagai `user`, bukan admin.
4. Buka **Pemesanan Tiket** untuk memeriksa pesanan. Ubah status menjadi pending, completed (selesai/lunas), atau cancelled (dibatalkan). Admin juga dapat mengelola pesanan secara manual.
5. Buka **Laporan**, tentukan tanggal awal dan akhir, lalu pilih **Tampilkan Laporan**. Laporan merangkum jadwal film, tiket terjual, dan nilai pesanan pending/selesai. Pesanan yang dibatalkan tidak dihitung.

## 4. Alur penonton

1. Login menggunakan akun penonton.
2. Buka **Jadwal Tayang** untuk melihat film dan jadwal yang tersedia.
3. Pilih jadwal untuk film, masukkan nomor kursi serta jumlah tiket, kemudian konfirmasi pesanan.
4. Buka dashboard untuk memeriksa riwayat pesanan sendiri. User tidak dapat melihat riwayat milik akun lain.

## 5. Data demonstrasi

Seeder menambahkan film contoh dan 15 akun penonton bernama `Penonton Demo 01` sampai `Penonton Demo 15` dengan alamat `penonton01@example.test` sampai `penonton15@example.test`. Password akun demo dibuat acak dan tidak dipakai untuk login. Admin dapat menggunakan akun tersebut untuk memperlihatkan daftar penonton.

## 6. Pengujian

Jalankan `php artisan test` untuk menguji pembatasan role, CRUD film/jadwal/penonton/pesanan, checkout, dan laporan periode.
