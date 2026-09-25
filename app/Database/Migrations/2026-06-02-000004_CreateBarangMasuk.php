<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration keempat: membuat riwayat transaksi barang masuk.
 *
 * Debugging penting:
 * - Tabel ini mencatat jumlah masuk dan user pencatat.
 * - Tidak ada kolom stok akhir; laporan merekonstruksi stok dari stok aktual.
 */
class CreateBarangMasuk extends Migration
{
    public function up()
    {
        // Tabel transaksi barang masuk mencatat penambahan stok dan user pencatatnya.
        $this->forge->addField([
            'id_masuk' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal_masuk' => ['type' => 'DATE'],
            'barang_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jumlah' => ['type' => 'INT', 'constraint' => 11],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_masuk', true);
        // Barang dan user tidak boleh dihapus jika masih memiliki riwayat transaksi masuk.
        $this->forge->addForeignKey('barang_id', 'barang', 'id_barang', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('user_id', 'users', 'id_user', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('barang_masuk');
    }

    public function down()
    {
        $this->forge->dropTable('barang_masuk');
    }
}
