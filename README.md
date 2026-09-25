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

## Struktur Penting

```text
app/Config/Routes.php              Route dan pembatasan akses halaman
app/Config/Filters.php             Alias filter auth dan role
app/Filters/AuthFilter.php         Cek session login
app/Filters/RoleFilter.php         Cek role Admin, Owner, Staff
app/Controllers/Auth.php           Login dan logout
app/Controllers/BarangController.php
app/Controllers/BarangMasukController.php
app/Controllers/BarangKeluarController.php
app/Controllers/LaporanController.php
app/Models/                        Model database
app/Database/Migrations/           Struktur tabel
app/Database/Seeds/DatabaseSeeder.php
app/Views/                         Tampilan halaman
public/index.php                   Front controller aplikasi
writable/                          Log, cache, session, dan file sementara
database.sql                       Script SQL alternatif
```

## Hak Akses

- Admin: dashboard, stok, master data, user, transaksi, laporan, PDF, Excel.
- Staff: dashboard, stok, transaksi barang masuk, transaksi barang keluar.
- Owner: dashboard, stok, laporan, PDF, Excel.

## Akun Demo

| Role | Username | Password |
| --- | --- | --- |
| Admin | `admin` | `admin123` |
| Owner | `owner` | `owner123` |
| Staff | `staff` | `staff123` |

Password akun demo dibuat di [DatabaseSeeder.php](app/Database/Seeds/DatabaseSeeder.php).

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

## Cara Membuat Web Ini Dari Nol Sampai Jadi

1. Siapkan folder project.

Letakkan project di:

```text
C:\xampp\htdocs\web-alat-musik
```

Jika membuat dari awal dengan Composer:

```bash
composer create-project codeigniter4/appstarter web-alat-musik
cd web-alat-musik
```

2. Install library laporan.

```bash
composer require dompdf/dompdf phpoffice/phpspreadsheet
```

3. Atur environment.

Buat atau edit file `.env`, lalu isi base URL dan database seperti contoh pada bagian instalasi.

4. Buat database.

```sql
CREATE DATABASE db_alat_musik CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

5. Buat migration.
php spark make:migration CreateUsers

Urutan migration dibuat seperti ini:

- `users`: akun login dan role.
- `kategori_barang`: master kategori.
- `barang`: master barang, harga, stok, dan status.
- `barang_masuk`: riwayat penambahan stok.
- `barang_keluar`: riwayat pengurangan stok.

Jalankan migration:

```bash
php spark migrate
```

6. Buat seeder.

Seeder mengisi akun demo dan data awal kategori/barang.

```bash
php spark db:seed DatabaseSeeder
```

7. Buat model.

Model utama:

- `UserModel`: login dan manajemen pengguna.
- `KategoriModel`: CRUD kategori.
- `BarangModel`: CRUD barang, kode barang, sinkron status stok.
- `BarangMasukModel`: transaksi masuk dan relasi laporan.
- `BarangKeluarModel`: transaksi keluar dan relasi laporan.

Catatan penting: semua tabel memakai ID manual, jadi setiap model memiliki helper `nextId()`.

8. Buat filter login dan role.

File yang dibuat:

- `app/Filters/AuthFilter.php`
- `app/Filters/RoleFilter.php`

Daftarkan alias di `app/Config/Filters.php`:

```php
'auth' => AuthFilter::class,
'role' => RoleFilter::class,
```

9. Buat route.

Route utama ada di `app/Config/Routes.php`.

Pola akses:

- Route login tidak memakai filter.
- Semua halaman internal memakai filter `auth`.
- Master data memakai `role:Admin`.
- Transaksi memakai `role:Admin,Staff`.
- Laporan memakai `role:Admin,Owner`.

10. Buat controller.

Urutan yang mudah:

- `Auth.php` untuk login/logout.
- `Dashboard.php` untuk ringkasan.
- `StokController.php` untuk stok read-only.
- `KategoriController.php` untuk CRUD kategori.
- `BarangController.php` untuk CRUD barang.
- `UserController.php` untuk CRUD user.
- `BarangMasukController.php` untuk transaksi masuk.
- `BarangKeluarController.php` untuk transaksi keluar.
- `LaporanController.php` untuk PDF, Excel, dan laporan persediaan.

11. Buat view.

View dibuat di `app/Views`.

Bagian penting:

- `layouts/main.php`: layout utama, sidebar role, DataTables, SweetAlert.
- `auth/login.php`: halaman login.
- `dashboard/index.php`: ringkasan dan grafik.
- `barang`, `kategori`, `users`: form dan tabel master data.
- `transaksi/masuk`, `transaksi/keluar`: form dan tabel transaksi.
- `laporan/persediaan.php`: laporan gabungan masuk/keluar.
- `laporan/pdf_persediaan.php`: template cetak PDF.

12. Buat logika stok.

Barang masuk:

- Simpan transaksi ke `barang_masuk`.
- Tambahkan stok di tabel `barang`.
- Jalankan `syncStatus()`.

Barang keluar:

- Cek stok cukup.
- Simpan transaksi ke `barang_keluar`.
- Kurangi stok di tabel `barang`.
- Jalankan `syncStatus()`.

Saat edit/delete transaksi, stok lama harus dikembalikan dulu sebelum stok baru diterapkan.

13. Buat laporan persediaan.

Laporan persediaan menggabungkan `barang_masuk` dan `barang_keluar`.

Kolom utama:

- Tanggal
- Nama Barang
- Merk
- Jenis
- Jumlah
- Stok Awal
- Sisa Stok
- Penginput
- Keterangan

Karena tabel transaksi tidak menyimpan snapshot stok, `sisa_stok` dihitung mundur dari stok aktual saat ini. `stok_awal` dihitung dari `sisa_stok` dan `jumlah`.

14. Buat export.

- PDF: gunakan Dompdf, render view HTML laporan.
- Excel: gunakan PhpSpreadsheet, tulis header dan rows ke file `.xlsx`.

15. Jalankan dan uji.

```bash
php spark serve
```

Cek alur berikut:

- Login Admin.
- Tambah kategori.
- Tambah barang.
- Tambah barang masuk.
- Tambah barang keluar.
- Cek stok berubah.
- Cek laporan persediaan.
- Cetak PDF.
- Export Excel.
- Login Owner dan pastikan hanya laporan yang terbuka.
- Login Staff dan pastikan hanya transaksi yang terbuka.

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
