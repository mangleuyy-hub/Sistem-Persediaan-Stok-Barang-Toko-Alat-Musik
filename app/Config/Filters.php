<?php

namespace Config;

use App\Filters\AuthFilter;
use App\Filters\RoleFilter;
use CodeIgniter\Config\BaseConfig;

/**
 * Registrasi filter aplikasi.
 *
 * Debugging penting:
 * - Alias `auth` dan `role` harus ada agar Config\Routes bisa memakai filter login/role.
 * - Toolbar hanya aktif di environment development.
 * - Jika filter tidak terpanggil, cek nama alias di file ini dan pemakaiannya di Routes.php.
 */
class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf' => \CodeIgniter\Filters\CSRF::class,
        'toolbar' => \CodeIgniter\Filters\DebugToolbar::class,
        'auth' => AuthFilter::class,
        'role' => RoleFilter::class,
    ];

    public array $globals = [
        'before' => [],
        'after' => [
            'toolbar' => ['except' => ['api/*']],
        ],
    ];

    public array $methods = [];
    public array $filters = [];
}
