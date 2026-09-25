<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration kedua: membuat master kategori barang.
 *
 * Debugging penting:
 * - Tabel barang membutuhkan kategori_barang sebagai foreign key.
 * - Hapus kategori akan ditolak jika masih ada barang yang memakai kategori tersebut.
 */
class CreateKategoriBarang extends Migration
{
    public function up()
    {
        // Tabel master kategori dipakai sebagai referensi barang.
        $this->forge->addField([
            'id_kategori' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_kategori' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_kategori', true);
        $this->forge->createTable('kategori_barang');
    }

    public function down()
    {
        $this->forge->dropTable('kategori_barang');
    }
}
