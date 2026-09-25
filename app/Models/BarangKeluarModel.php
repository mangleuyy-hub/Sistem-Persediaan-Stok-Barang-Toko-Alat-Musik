<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model transaksi barang keluar.
 *
 * Catatan debugging:
 * - Tabel ini hanya menyimpan jumlah keluar, bukan snapshot stok.
 * - Validasi stok dilakukan di BarangKeluarController, bukan di model ini.
 */
class BarangKeluarModel extends Model
{
    // Model transaksi pengurangan stok barang.
    protected $table = 'barang_keluar';
    protected $primaryKey = 'id_keluar';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['id_keluar', 'tanggal_keluar', 'barang_id', 'jumlah', 'keterangan', 'user_id'];
    protected $useTimestamps = true;

    public function withRelations()
    {
        // Relasi ini menyiapkan nama barang, kode barang, dan nama user untuk daftar/detail/laporan.
        return $this->select('barang_keluar.*, barang.nama_barang, barang.kode_barang, barang.merk, users.nama_lengkap')
            ->join('barang', 'barang.id_barang = barang_keluar.barang_id')
            ->join('users', 'users.id_user = barang_keluar.user_id');
    }

    public function nextId(): int
    {
        // Karena tabel tidak memakai AUTO_INCREMENT, ID berikutnya dihitung dari ID terbesar saat ini.
        $last = $this->selectMax('id_keluar')->first();

        return ((int) ($last['id_keluar'] ?? 0)) + 1;
    }
}
