<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration kelima: membuat riwayat transaksi barang keluar.
 *
 * Debugging penting:
 * - Tabel ini mencatat jumlah keluar dan user pencatat.
 * - Validasi stok keluar dilakukan di controller sebelum data disimpan.
 */
class CreateBarangKeluar extends Migration
{
    public function up()
    {
        // Tabel transaksi barang keluar mencatat pengurangan stok dan user pencatatnya.
        $this->forge->addField([
            'id_keluar' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal_keluar' => ['type' => 'DATE'],
            'barang_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jumlah' => ['type' => 'INT', 'constraint' => 11],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_keluar', true);
        // Barang dan user tidak boleh dihapus jika masih memiliki riwayat transaksi keluar.
        $this->forge->addForeignKey('barang_id', 'barang', 'id_barang', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('user_id', 'users', 'id_user', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('barang_keluar');
    }

    public function down()
    {
        $this->forge->dropTable('barang_keluar');
    }
}
