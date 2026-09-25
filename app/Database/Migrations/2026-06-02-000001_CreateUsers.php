<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration pertama: membuat tabel akun pengguna.
 *
 * Debugging penting:
 * - Tabel ini harus dibuat sebelum transaksi karena barang_masuk/barang_keluar punya foreign key ke users.
 * - Role disimpan sebagai ENUM agar nilai tetap konsisten dengan filter aplikasi.
 */
class CreateUsers extends Migration
{
    public function up()
    {
        // Tabel users menyimpan akun login beserta role aksesnya.
        $this->forge->addField([
            'id_user' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_lengkap' => ['type' => 'VARCHAR', 'constraint' => 120],
            'username' => ['type' => 'VARCHAR', 'constraint' => 60],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => ['type' => 'ENUM', 'constraint' => ['Admin', 'Owner', 'Staff'], 'default' => 'Staff'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_user', true);
        // Username dibuat unik agar proses login tidak ambigu.
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
