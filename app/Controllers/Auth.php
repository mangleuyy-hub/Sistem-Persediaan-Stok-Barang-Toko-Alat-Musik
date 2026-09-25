<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Mengatur login, pembuatan session, dan logout.
 *
 * Debugging penting:
 * - Login gagal biasanya karena username tidak ditemukan atau hash password tidak cocok.
 * - Session `isLoggedIn`, `id_user`, dan `role` menjadi sumber filter auth/role.
 * - Role valid aplikasi hanya Admin, Owner, dan Staff.
 */
class Auth extends BaseController
{
    public function index()
    {
        // Pengguna yang sudah login langsung diarahkan ke dashboard.
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function login()
    {
        // Ambil user berdasarkan username, lalu cocokkan password hash.
        $user = (new UserModel())->where('username', $this->request->getPost('username'))->first();

        if (!$user || !password_verify((string) $this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->with('error', 'Username atau Password Salah');
        }

        // Hanya role yang dikenal aplikasi yang boleh dibuatkan session.
        if (!in_array($user['role'], ['Admin', 'Owner', 'Staff'], true)) {
            return redirect()->back()->with('error', 'Role pengguna tidak valid.');
        }

        // Data session ini dipakai oleh filter role dan tampilan sidebar.
        session()->set([
            'isLoggedIn' => true,
            'id_user' => $user['id_user'],
            'nama_lengkap' => $user['nama_lengkap'],
            'username' => $user['username'],
            'role' => $user['role'],
        ]);

        return redirect()->to('/dashboard')->with('success', 'Login berhasil.');
    }

    public function logout()
    {
        // Hapus seluruh session agar akses halaman internal wajib login ulang.
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda berhasil logout.');
    }
}