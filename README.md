# Sistem Persediaan Stok Barang Toko Alat Musik

Aplikasi web persediaan stok toko alat musik berbasis CodeIgniter 4. Fitur utama: login session, role Admin/Owner/Staff, CRUD master data, transaksi barang masuk/keluar, dashboard statistik, laporan persediaan gabungan, cetak PDF, dan export Excel.

## Teknologi

- PHP 8.1 atau lebih baru
- CodeIgniter 4
- MySQL atau MariaDB
- Composer
- Bootstrap 5
- DataTables
- SweetAlert2
- Chart.js
- Dompdf untuk PDF
- PhpSpreadsheet untuk Excel

## Hak Akses

- Admin: dashboard, stok, master data, user, transaksi, laporan, PDF, Excel.
- Staff: dashboard, stok, transaksi barang masuk, transaksi barang keluar.
- Owner: dashboard, stok, laporan, PDF, Excel.

## Aturan Stok

- Stok barang tidak dapat bernilai negatif.
- Stok antara 1 sampai 9 ditandai dengan status **Menipis**.
- Transaksi barang keluar ditolak bila jumlahnya melebihi stok tersedia.
- Barang yang sudah memiliki riwayat transaksi tidak dapat dihapus agar laporan tetap konsisten.

## Cara Menjalankan Project Ini

1. Pastikan XAMPP sudah berjalan.

Aktifkan Apache dan MySQL dari XAMPP Control Panel.

2. Pastikan ekstensi PHP aktif.

Buka `php.ini` yang dipakai XAMPP, lalu aktifkan ekstensi berikut jika belum aktif:

```ini
extension=intl
extension=fileinfo
extension=gd
extension=zip
extension=mysqli
```

3. Install dependency.

Jalankan dari folder project:

```bash
composer install
```

4. Buat database.

Buka phpMyAdmin atau MySQL client, lalu jalankan:

```sql
CREATE DATABASE db_alat_musik CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

5. Cek konfigurasi `.env`.

Contoh konfigurasi lokal XAMPP:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/web-alat-musik/public/'

database.default.hostname = localhost
database.default.database = db_alat_musik
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

6. Jalankan migration dan seeder.

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

7. Buka aplikasi.

Jika memakai XAMPP:

```text
http://localhost/web-alat-musik/public/
```

Jika memakai server bawaan CodeIgniter:

```bash
php spark serve
```

Lalu buka:

```text
http://localhost:8080
```

## Import Database Alternatif

Jika tidak ingin memakai migration, import file `database.sql` lewat phpMyAdmin.

Rekomendasi utama tetap memakai:

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

Migration lebih aman karena struktur database mengikuti kode aplikasi.

## Debugging Cepat

Login selalu gagal:

- Cek username di tabel `users`.
- Cek password sudah hash.
- Cek session dan cookie browser.

Halaman 403:

- Cek role user di session.
- Cek filter route di `app/Config/Routes.php`.
- Cek alias filter di `app/Config/Filters.php`.

Stok tidak sesuai:

- Cek transaksi masuk/keluar terakhir.
- Cek logika edit/delete transaksi.
- Jalankan ulang `syncStatus()` untuk barang terkait jika status tidak sesuai stok.

PDF tidak keluar:

- Cek dependency Dompdf sudah terinstall.
- Cek view PDF tidak memiliki error PHP.
- Cek permission folder `writable`.

Excel tidak terunduh:

- Cek dependency PhpSpreadsheet.
- Pastikan ekstensi `zip` aktif di PHP.

Database error:

- Cek `.env`.
- Cek nama database.
- Cek MySQL XAMPP aktif.
- Cek migration sudah dijalankan.

## Perintah Berguna

```bash
php spark routes
php spark migrate
php spark migrate:rollback
php spark db:seed DatabaseSeeder
php spark cache:clear
php spark serve
```

## Catatan Pengembangan

- Auto route dimatikan, jadi route baru wajib ditulis manual.
- Jangan hapus user yang sedang login.
- Jangan hapus kategori yang masih dipakai barang.
- Setelah stok berubah, panggil `syncStatus()`.
- Jika menambah kolom database, update migration, model `allowedFields`, form, controller, dan view.
