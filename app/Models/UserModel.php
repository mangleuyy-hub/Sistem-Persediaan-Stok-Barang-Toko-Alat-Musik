<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model tabel users.
 *
 * Catatan debugging:
 * - `useAutoIncrement = false`, jadi insert wajib menyertakan id_user.
 * - Password yang tersimpan harus berupa hash, bukan teks polos.
 */
class UserModel extends Model
{
    // Data user dipakai untuk login, pembatasan role, dan pencatat transaksi.
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['id_user', 'nama_lengkap', 'username', 'password', 'role'];
    protected $useTimestamps = true;

    public function nextId(): int
    {
        // Karena tabel tidak memakai AUTO_INCREMENT, ID berikutnya dihitung dari ID terbesar saat ini.
        $last = $this->selectMax('id_user')->first();

        return ((int) ($last['id_user'] ?? 0)) + 1;
    }
}
