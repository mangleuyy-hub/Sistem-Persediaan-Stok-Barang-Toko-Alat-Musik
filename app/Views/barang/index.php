<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
    <form class="d-flex gap-2" method="get">
        <input class="form-control" name="q" value="<?= esc($q ?? '') ?>" placeholder="Cari barang">
        <button class="btn btn-outline-primary">Cari</button>
    </form>
    <a class="btn btn-primary" href="<?= site_url('barang/new') ?>">Tambah Barang</a>
</div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-striped align-middle datatable">
        <thead><tr><th>Kode</th><th>Nama</th><th>Kategori</th><th>Merk</th><th>Stok</th><th>Harga Jual</th><th>Status</th><th width="230">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($barang as $row): ?>
            <tr>
                <td><?= esc($row['kode_barang']) ?></td><td><?= esc($row['nama_barang']) ?></td><td><?= esc($row['nama_kategori']) ?></td><td><?= esc($row['merk']) ?></td>
                <td><?= esc($row['stok']) ?></td><td>Rp <?= number_format((float) $row['harga_jual'], 0, ',', '.') ?></td>
                <td><span class="badge bg-<?= $row['status_barang'] === 'Tersedia' ? 'success' : 'danger' ?>"><?= esc($row['status_barang']) ?></span></td>
                <td class="d-flex gap-2">
                    <a class="btn btn-sm btn-info text-white" href="<?= site_url('barang/' . $row['id_barang']) ?>">Detail</a>
                    <a class="btn btn-sm btn-warning" href="<?= site_url('barang/' . $row['id_barang'] . '/edit') ?>">Edit</a>
                    <form class="form-delete" action="<?= site_url('barang/' . $row['id_barang']) ?>" method="post"><?= csrf_field() ?><input type="hidden" name="_method" value="DELETE"><button class="btn btn-sm btn-danger">Hapus</button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $pager->links() ?>
</div></div>
<?= $this->endSection() ?>
