<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model transaksi barang masuk.
 *
 * Catatan debugging:
 * - Tabel ini hanya menyimpan jumlah masuk, bukan snapshot stok.
 * - Laporan mengambil nama barang, merk, dan user lewat withRelations().
 */
class BarangMasukModel extends Model
{
    // Model transaksi penambahan stok barang.
    protected $table = 'barang_masuk';
    protected $primaryKey = 'id_masuk';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['id_masuk', 'tanggal_masuk', 'barang_id', 'jumlah', 'keterangan', 'user_id'];
    protected $useTimestamps = true;

    public function withRelations()
    {
        // Relasi ini menyiapkan nama barang, kode barang, dan nama user untuk daftar/detail/laporan.
        return $this->select('barang_masuk.*, barang.nama_barang, barang.kode_barang, barang.merk, users.nama_lengkap')
            ->join('barang', 'barang.id_barang = barang_masuk.barang_id')
            ->join('users', 'users.id_user = barang_masuk.user_id');
    }

    public function nextId(): int
    {
        // Karena tabel tidak memakai AUTO_INCREMENT, ID berikutnya dihitung dari ID terbesar saat ini.
        $last = $this->selectMax('id_masuk')->first();

        return ((int) ($last['id_masuk'] ?? 0)) + 1;
    }
}
