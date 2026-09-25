<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card"><div class="card-body">
    <?php // Form user dipakai untuk tambah dan edit; password edit boleh kosong agar tidak berubah. ?>
    <form method="post" action="<?= $data ? site_url('users/' . $data['id_user']) : site_url('users') ?>">
        <?= csrf_field() ?>
        <?php if ($data): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input class="form-control" name="nama_lengkap" value="<?= esc(old('nama_lengkap', $data['nama_lengkap'] ?? '')) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Username</label><input class="form-control" name="username" value="<?= esc(old('username', $data['username'] ?? '')) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Password <?= $data ? '(kosongkan jika tidak diubah)' : '' ?></label><input type="password" class="form-control" name="password" <?= $data ? '' : 'required' ?>></div>
            <div class="col-md-6"><label class="form-label">Role</label><select class="form-select" name="role" required><?php foreach (['Admin', 'Owner', 'Staff'] as $role): ?><option value="<?= $role ?>" <?= old('role', $data['role'] ?? 'Staff') === $role ? 'selected' : '' ?>><?= $role ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="mt-3"><button class="btn btn-primary">Simpan</button><a class="btn btn-light" href="<?= site_url('users') ?>">Kembali</a></div>
    </form>
</div></div>
<?= $this->endSection() ?>
