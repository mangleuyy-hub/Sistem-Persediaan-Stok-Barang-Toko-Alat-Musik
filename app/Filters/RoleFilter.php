<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter pembatasan role berdasarkan argument route.
 *
 * Debugging penting:
 * - Contoh pemakaian route: ['filter' => 'role:Admin,Owner'].
 * - Nilai yang dibandingkan berasal dari session `role`.
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role');

        // Argument filter berisi role yang diizinkan, contoh: role:Admin,Owner.
        if ($arguments && ! in_array($role, $arguments, true)) {
            return service('response')
                ->setStatusCode(403)
                ->setBody('<h1>403 Forbidden</h1><p>Anda tidak memiliki akses ke halaman tersebut.</p>');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
