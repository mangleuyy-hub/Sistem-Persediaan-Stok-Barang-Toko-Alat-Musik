<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model tabel kategori_barang.
 *
 * Catatan debugging:
 * - ID dibuat manual dengan nextId().
 * - Delete kategori bisa gagal jika masih direferensikan tabel barang.
 */
class KategoriModel extends Model
{
    // Master kategori untuk mengelompokkan barang.
    protected $table = 'kategori_barang';
    protected $primaryKey = 'id_kategori';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['id_kategori', 'nama_kategori'];
    protected $useTimestamps = true;

    public function nextId(): int
    {
        // Karena tabel tidak memakai AUTO_INCREMENT, ID berikutnya dihitung dari ID terbesar saat ini.
        $last = $this->selectMax('id_kategori')->first();

        return ((int) ($last['id_kategori'] ?? 0)) + 1;
    }
}
