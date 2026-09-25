<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter login untuk route internal.
 *
 * Debugging penting:
 * - Jika user selalu kembali ke login, cek apakah session tersimpan dan `isLoggedIn` bernilai true.
 * - Filter ini didaftarkan di Config\Filters dan dipakai di Config\Routes.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Session isLoggedIn dibuat saat login berhasil dan menjadi gerbang halaman internal.
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
