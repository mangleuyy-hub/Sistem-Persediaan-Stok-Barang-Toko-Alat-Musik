<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
    <form class="d-flex gap-2" method="get">
        <input class="form-control" name="q" value="<?= esc($q ?? '') ?>" placeholder="Cari kategori">
        <button class="btn btn-outline-primary">Cari</button>
    </form>
    <a class="btn btn-primary" href="<?= site_url('kategori/new') ?>">Tambah Kategori</a>
</div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-striped align-middle datatable">
        <thead><tr><th>No</th><th>Nama Kategori</th><th>Dibuat</th><th width="170">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($kategori as $i => $row): ?>
            <tr>
                <td><?= $i + 1 ?></td><td><?= esc($row['nama_kategori']) ?></td><td><?= esc($row['created_at']) ?></td>
                <td class="d-flex gap-2">
                    <a class="btn btn-sm btn-warning" href="<?= site_url('kategori/' . $row['id_kategori'] . '/edit') ?>">Edit</a>
                    <form class="form-delete" action="<?= site_url('kategori/' . $row['id_kategori']) ?>" method="post"><?= csrf_field() ?><input type="hidden" name="_method" value="DELETE"><button class="btn btn-sm btn-danger">Hapus</button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $pager->links() ?>
</div></div>
<?= $this->endSection() ?>
