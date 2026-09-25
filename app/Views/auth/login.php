<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sistem Stok Alat Musik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; display: grid; place-items: center; background: linear-gradient(135deg, #172033, #2563eb); }
        .login-card { width: min(420px, 92vw); border: 0; border-radius: 8px; box-shadow: 0 16px 50px rgba(0,0,0,.22); }
    </style>
</head>
<body>
<div class="card login-card">
    <div class="card-body p-4">
        <h4 class="mb-1">Login</h4>
        <p class="text-muted mb-4">Sistem Persediaan Stok Barang Toko Alat Musik</p>
        <form action="<?= site_url('login') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php // Flash message dari Auth controller ditampilkan setelah login/logout. ?>
<?php if (session()->getFlashdata('error')): ?>
<script>Swal.fire({ icon: 'error', title: 'Login gagal', text: '<?= esc(session()->getFlashdata('error')) ?>' });</script>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
<script>Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= esc(session()->getFlashdata('success')) ?>', timer: 1800, showConfirmButton: false });</script>
<?php endif; ?>
</body>
</html>
