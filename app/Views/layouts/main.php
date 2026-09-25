<?php
// Layout utama semua halaman setelah login.
// Debugging penting: sidebar mengikuti session role, flash message ditampilkan SweetAlert,
// dan DataTables global dibuat tanpa paging/search karena halaman memakai filter sendiri.
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Sistem Stok Alat Musik') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; }
        .sidebar { min-height: 100vh; background: #172033; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: .7rem 1rem; border-radius: 6px; }
        .sidebar a:hover, .sidebar a.active { background: #2563eb; color: #fff; }
        .content { min-height: 100vh; }
        .card-stat { border: 0; border-radius: 8px; box-shadow: 0 8px 24px rgba(15, 23, 42, .08); }
        @media (max-width: 767px) { .sidebar { min-height: auto; } }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <?php $role = session('role'); ?>
            <h5 class="text-white mb-4">Toko Alat Musik</h5>
            <a href="<?= site_url('dashboard') ?>">Dashboard</a>
            <a href="<?= site_url('stok') ?>">Stok Saat Ini</a>
            <?php // Menu sidebar ditampilkan sesuai role agar user hanya melihat fitur yang diizinkan. ?>
            <?php if ($role === 'Admin'): ?>
                <div class="text-uppercase small text-secondary mt-3 mb-1 px-2">Master Data</div>
                <a href="<?= site_url('kategori') ?>">Kategori Barang</a>
                <a href="<?= site_url('barang') ?>">Daftar Barang</a>
                <a href="<?= site_url('users') ?>">Manajemen Pengguna</a>
            <?php endif; ?>
            <?php if (in_array($role, ['Admin', 'Staff'], true)): ?>
                <div class="text-uppercase small text-secondary mt-3 mb-1 px-2">Transaksi</div>
                <a href="<?= site_url('barang-masuk') ?>">Barang Masuk</a>
                <a href="<?= site_url('barang-keluar') ?>">Barang Keluar</a>
            <?php endif; ?>
            <?php if (in_array($role, ['Admin', 'Owner'], true)): ?>
                <div class="text-uppercase small text-secondary mt-3 mb-1 px-2">Laporan</div>
                <a href="<?= site_url('laporan/persediaan') ?>">Laporan Persediaan</a>
            <?php endif; ?>
            <a class="mt-3" href="<?= site_url('logout') ?>">Logout</a>
        </aside>
        <main class="col-md-9 col-lg-10 content p-0">
            <nav class="navbar navbar-expand bg-white border-bottom px-4">
                <span class="navbar-brand mb-0 h6"><?= esc($title ?? 'Dashboard') ?></span>
                <span class="ms-auto text-muted small"><?= esc(session('nama_lengkap')) ?> (<?= esc(session('role')) ?>)</span>
            </nav>
            <div class="p-4">
                <?php // Konten tiap halaman dirender dari section "content" milik view anak. ?>
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // DataTables dipakai hanya untuk styling tabel; paging/searching ditangani server-side oleh controller.
    $('.datatable').DataTable({ paging: false, searching: false, info: false });
    // Semua form hapus memakai konfirmasi SweetAlert sebelum submit.
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', event => {
            event.preventDefault();
            Swal.fire({
                title: 'Hapus data?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then(result => { if (result.isConfirmed) form.submit(); });
        });
    });
    // Flash message dari controller ditampilkan sebagai notifikasi yang konsisten di semua halaman.
    <?php if (session()->getFlashdata('success')): ?>
    Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= esc(session()->getFlashdata('success')) ?>', timer: 2200, showConfirmButton: false });
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    Swal.fire({ icon: 'error', title: 'Gagal', text: '<?= esc(session()->getFlashdata('error')) ?>' });
    <?php endif; ?>
</script>
<?php // Section script memberi ruang untuk JavaScript khusus halaman, seperti grafik dashboard. ?>
<?= $this->renderSection('script') ?>
</body>
</html>
