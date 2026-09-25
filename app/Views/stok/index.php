<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<form class="d-flex gap-2 mb-3" method="get">
    <input class="form-control" name="q" value="<?= esc($q ?? '') ?>" placeholder="Cari stok barang">
    <button class="btn btn-outline-primary">Cari</button>
</form>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-striped align-middle datatable">
        <thead><tr><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Merk</th><th>Stok</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($items as $row): ?>
            <tr><td><?= esc($row['kode_barang']) ?></td><td><?= esc($row['nama_barang']) ?></td><td><?= esc($row['nama_kategori']) ?></td><td><?= esc($row['merk']) ?></td><td><?= esc($row['stok']) ?></td><td><span class="badge bg-<?= $row['status_barang'] === 'Tersedia' ? 'success' : 'danger' ?>"><?= esc($row['status_barang']) ?></span></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $pager->links() ?>
</div></div>
<?= $this->endSection() ?>
