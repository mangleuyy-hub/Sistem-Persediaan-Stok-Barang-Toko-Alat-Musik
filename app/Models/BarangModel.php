<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model master barang.
 *
 * Catatan debugging:
 * - `stok` adalah stok aktual yang berubah saat transaksi masuk/keluar.
 * - `syncStatus()` wajib dipanggil setelah stok berubah.
 * - `withKategori()` dipakai saat tampilan membutuhkan nama kategori.
 */
class BarangModel extends Model
{
    // Konfigurasi tabel master barang dan kolom yang boleh diisi dari form.
    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['id_barang', 'kode_barang', 'nama_barang', 'kategori_id', 'merk', 'satuan', 'stok', 'harga_beli', 'harga_jual', 'status_barang'];
    protected $useTimestamps = true;

    public function withKategori()
    {
        // Join kategori dipakai di halaman stok, barang, dashboard, dan laporan.
        return $this->select('barang.*, kategori_barang.nama_kategori')
            ->join('kategori_barang', 'kategori_barang.id_kategori = barang.kategori_id');
    }

    public function nextCode(): string
    {
        // Kode barang dibuat dari id terakhir agar format tetap berurutan: BRG-0001, BRG-0002, dst.
        $next = $this->nextId();
        return 'BRG-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function nextId(): int
    {
        // Karena tabel tidak memakai AUTO_INCREMENT, ID berikutnya dihitung dari ID terbesar saat ini.
        $last = $this->selectMax('id_barang')->first();

        return ((int) ($last['id_barang'] ?? 0)) + 1;
    }

    public function syncStatus(int $id): void
    {
        // Status barang selalu mengikuti stok aktual setelah transaksi masuk/keluar.
        $barang = $this->find($id);
        if ($barang) {
            $this->update($id, ['status_barang' => ((int) $barang['stok'] > 0) ? 'Tersedia' : 'Tidak Tersedia']);
        }
    }
}
