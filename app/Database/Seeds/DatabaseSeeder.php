<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder data awal aplikasi.
 *
 * Debugging penting:
 * - Jalankan setelah migration dengan `php spark db:seed DatabaseSeeder`.
 * - Akun demo dibuat di sini, jadi ubah password awal dari file ini jika diperlukan.
 * - Seeder ini aman untuk database kosong; hindari menjalankan berulang tanpa membersihkan data.
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Akun awal untuk mencoba tiga role utama aplikasi.
        $this->db->table('users')->insertBatch([
            ['id_user' => 1, 'nama_lengkap' => 'Administrator', 'username' => 'admin', 'password' => password_hash('admin123', PASSWORD_DEFAULT), 'role' => 'Admin', 'created_at' => $now],
            ['id_user' => 2, 'nama_lengkap' => 'Owner Toko', 'username' => 'owner', 'password' => password_hash('owner123', PASSWORD_DEFAULT), 'role' => 'Owner', 'created_at' => $now],
            ['id_user' => 3, 'nama_lengkap' => 'Staff Gudang', 'username' => 'staff', 'password' => password_hash('staff123', PASSWORD_DEFAULT), 'role' => 'Staff', 'created_at' => $now],
        ]);

        // Kategori contoh untuk mengelompokkan barang alat musik.
        $this->db->table('kategori_barang')->insertBatch([
            ['id_kategori' => 1, 'nama_kategori' => 'Gitar', 'created_at' => $now],
            ['id_kategori' => 2, 'nama_kategori' => 'Keyboard', 'created_at' => $now],
            ['id_kategori' => 3, 'nama_kategori' => 'Drum', 'created_at' => $now],
            ['id_kategori' => 4, 'nama_kategori' => 'Aksesoris', 'created_at' => $now],
        ]);

        // Barang contoh dengan kombinasi stok tersedia dan kosong.
        $this->db->table('barang')->insertBatch([
            ['id_barang' => 1, 'kode_barang' => 'BRG-0001', 'nama_barang' => 'Gitar Akustik Yamaha F310', 'kategori_id' => 1, 'merk' => 'Yamaha', 'satuan' => 'Unit', 'stok' => 12, 'harga_beli' => 1200000, 'harga_jual' => 1550000, 'status_barang' => 'Tersedia', 'created_at' => $now],
            ['id_barang' => 2, 'kode_barang' => 'BRG-0002', 'nama_barang' => 'Keyboard Casio CT-S200', 'kategori_id' => 2, 'merk' => 'Casio', 'satuan' => 'Unit', 'stok' => 7, 'harga_beli' => 1800000, 'harga_jual' => 2300000, 'status_barang' => 'Tersedia', 'created_at' => $now],
            ['id_barang' => 3, 'kode_barang' => 'BRG-0003', 'nama_barang' => 'Stick Drum 5A', 'kategori_id' => 4, 'merk' => 'Vic Firth', 'satuan' => 'Pasang', 'stok' => 0, 'harga_beli' => 80000, 'harga_jual' => 125000, 'status_barang' => 'Tidak Tersedia', 'created_at' => $now],
        ]);
    }
}
