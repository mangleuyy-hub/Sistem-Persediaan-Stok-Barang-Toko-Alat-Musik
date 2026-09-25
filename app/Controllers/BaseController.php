<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Controller dasar untuk seluruh controller aplikasi.
 *
 * Debugging penting:
 * - Helper `url` dan `form` dimuat global dari sini.
 * - `requireRole()` dipakai sebagai guard tambahan selain filter route.
 * - Jika akses halaman tiba-tiba 403, cek nilai `role` di session login.
 */
abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['url', 'form'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    protected function currentRole(): ?string
    {
        // Role disimpan di session saat login dan dipakai ulang oleh controller.
        $role = session()->get('role');

        return is_string($role) ? $role : null;
    }

    protected function hasRole(array|string $roles): bool
    {
        $roles = (array) $roles;

        return in_array($this->currentRole(), $roles, true);
    }

    protected function requireRole(array|string $roles): ?ResponseInterface
    {
        // Guard tambahan di controller agar akses tetap aman walaupun route berubah.
        if ($this->hasRole($roles)) {
            return null;
        }

        return $this->response
            ->setStatusCode(403)
            ->setBody('<h1>403 Forbidden</h1><p>Anda tidak memiliki akses ke halaman tersebut.</p>');
    }
}
