<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Daftar route aplikasi.
 *
 * Debugging penting:
 * - Auto route dimatikan, jadi endpoint baru wajib didaftarkan di file ini.
 * - Group auth memastikan semua halaman internal wajib login.
 * - Group role membatasi fitur Admin, Staff, dan Owner.
 *
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Dashboard');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Semua route di dalam group ini wajib login terlebih dahulu.
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {
    // Dashboard dan stok dapat dibaca oleh semua role yang sudah login.
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('stok', 'StokController::index');

    // Master data hanya untuk Admin karena berisi operasi CRUD dan reset password.
    $routes->group('', ['filter' => 'role:Admin'], static function (RouteCollection $routes) {
        $routes->resource('kategori', ['controller' => 'KategoriController']);
        $routes->resource('barang', ['controller' => 'BarangController']);
        $routes->resource('users', ['controller' => 'UserController']);
        $routes->post('users/reset-password/(:num)', 'UserController::resetPassword/$1');
    });

    // Transaksi stok boleh dilakukan Admin dan Staff, tetapi tidak boleh diakses Owner.
    $routes->group('', ['filter' => 'role:Admin,Staff'], static function (RouteCollection $routes) {
        $routes->resource('barang-masuk', ['controller' => 'BarangMasukController']);
        $routes->resource('barang-keluar', ['controller' => 'BarangKeluarController']);
    });

    // Laporan bersifat read-only untuk Owner dan tetap bisa dikelola/diunduh oleh Admin.
    $routes->group('laporan', ['filter' => 'role:Admin,Owner'], static function (RouteCollection $routes) {
        $routes->get('', 'LaporanController::persediaan');
        $routes->get('barang-masuk', 'LaporanController::barangMasuk');
        $routes->get('barang-keluar', 'LaporanController::barangKeluar');
        $routes->get('persediaan', 'LaporanController::persediaan');
        $routes->get('barang-masuk/pdf', 'LaporanController::barangMasukPdf');
        $routes->get('barang-keluar/pdf', 'LaporanController::barangKeluarPdf');
        $routes->get('persediaan/pdf', 'LaporanController::persediaanPdf');
        $routes->get('barang-masuk/excel', 'LaporanController::barangMasukExcel');
        $routes->get('barang-keluar/excel', 'LaporanController::barangKeluarExcel');
        $routes->get('persediaan/excel', 'LaporanController::persediaanExcel');
    });
});
