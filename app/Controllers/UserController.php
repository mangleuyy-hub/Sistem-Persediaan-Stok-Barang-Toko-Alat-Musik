<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Manajemen pengguna untuk Admin.
 *
 * Debugging penting:
 * - Password selalu disimpan dalam bentuk hash.
 * - Admin yang sedang login tidak boleh menghapus akunnya sendiri.
 * - Reset password mengubah password menjadi `password123`.
 */
class UserController extends BaseController
{
    private UserModel $users;

    public function __construct()
    {
        // Semua operasi manajemen pengguna memakai UserModel.
        $this->users = new UserModel();
    }

    public function index()
    {
        // Hanya Admin yang dapat melihat dan mengelola daftar user.
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        return view('users/index', ['title' => 'Manajemen Pengguna', 'users' => $this->users->orderBy('id_user', 'DESC')->paginate(10), 'pager' => $this->users->pager]);
    }

    public function new()
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        return view('users/form', ['title' => 'Tambah User', 'data' => null]);
    }

    public function create()
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        $this->users->insert(['id_user' => $this->users->nextId()] + $this->payload(true));
        return redirect()->to('/users')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        return view('users/form', ['title' => 'Edit User', 'data' => $this->users->find($id)]);
    }

    public function update($id = null)
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        $this->users->update($id, $this->payload(false));
        return redirect()->to('/users')->with('success', 'User berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        // Cegah admin menghapus akunnya sendiri agar tidak kehilangan akses sistem.
        if ((int) $id === (int) session()->get('id_user')) {
            return redirect()->to('/users')->with('error', 'User yang sedang login tidak dapat dihapus.');
        }
        $this->users->delete($id);
        return redirect()->to('/users')->with('success', 'User berhasil dihapus.');
    }

    public function resetPassword($id)
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        // Password reset memakai nilai default yang langsung di-hash sebelum disimpan.
        $this->users->update($id, ['password' => password_hash('password123', PASSWORD_DEFAULT)]);
        return redirect()->to('/users')->with('success', 'Password berhasil direset menjadi password123.');
    }

    private function payload(bool $withPassword): array
    {
        // Payload dipakai create/update; password hanya diubah saat dibuat atau saat field diisi.
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username' => $this->request->getPost('username'),
            'role' => $this->request->getPost('role'),
        ];

        if ($withPassword || $this->request->getPost('password')) {
            $data['password'] = password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        return $data;
    }
}
