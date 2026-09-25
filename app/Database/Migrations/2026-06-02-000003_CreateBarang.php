<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration ketiga: membuat master barang dan stok aktual.
 *
 * Debugging penting:
 * - Kolom `stok` adalah nilai terkini yang diubah oleh transaksi.
 * - `kode_barang` unik agar laporan dan transaksi mudah ditelusuri.
 */
class CreateBarang extends Migration
{
    public function up()
    {
        // Tabel master barang menyimpan identitas, harga, stok, dan status ketersediaan.
        $this->forge->addField([
            'id_barang' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kode_barang' => ['type' => 'VARCHAR', 'constraint' => 30],
            'nama_barang' => ['type' => 'VARCHAR', 'constraint' => 150],
            'kategori_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'merk' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'satuan' => ['type' => 'VARCHAR', 'constraint' => 30],
            'stok' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'harga_beli' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'harga_jual' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'status_barang' => ['type' => 'ENUM', 'constraint' => ['Tersedia', 'Tidak Tersedia'], 'default' => 'Tidak Tersedia'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_barang', true);
        // Kode barang unik agar tiap barang mudah dilacak di transaksi dan laporan.
        $this->forge->addUniqueKey('kode_barang');
        // Kategori tidak boleh dihapus jika masih dipakai barang.
        $this->forge->addForeignKey('kategori_id', 'kategori_barang', 'id_kategori', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('barang');
    }

    public function down()
    {
        $this->forge->dropTable('barang');
    }
}
