<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="text-end mb-3"><a class="btn btn-primary" href="<?= site_url('users/new') ?>">Tambah User</a></div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-striped align-middle datatable">
        <thead><tr><th>Nama Lengkap</th><th>Username</th><th>Role</th><th>Dibuat</th><th width="260">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($users as $row): ?>
            <tr>
                <td><?= esc($row['nama_lengkap']) ?></td><td><?= esc($row['username']) ?></td><td><span class="badge bg-primary"><?= esc($row['role']) ?></span></td><td><?= esc($row['created_at']) ?></td>
                <td class="d-flex flex-wrap gap-2">
                    <a class="btn btn-sm btn-warning" href="<?= site_url('users/' . $row['id_user'] . '/edit') ?>">Edit</a>
                    <form action="<?= site_url('users/reset-password/' . $row['id_user']) ?>" method="post"><?= csrf_field() ?><button class="btn btn-sm btn-secondary">Reset Password</button></form>
                    <form class="form-delete" action="<?= site_url('users/' . $row['id_user']) ?>" method="post"><?= csrf_field() ?><input type="hidden" name="_method" value="DELETE"><button class="btn btn-sm btn-danger">Hapus</button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $pager->links() ?>
</div></div>
<?= $this->endSection() ?>
