<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
    <form class="d-flex flex-wrap gap-2" method="get">
        <input type="date" class="form-control" name="awal" value="<?= esc($awal ?? '') ?>">
        <input type="date" class="form-control" name="akhir" value="<?= esc($akhir ?? '') ?>">
        <button class="btn btn-outline-primary">Filter</button>
    </form>
    <a class="btn btn-primary" href="<?= site_url('barang-masuk/new') ?>">Tambah Transaksi</a>
</div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-striped align-middle datatable">
        <thead><tr><th>Tanggal</th><th>Kode</th><th>Nama Barang</th><th>Jumlah</th><th>User</th><th>Keterangan</th><th width="230">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?= esc($row['tanggal_masuk']) ?></td><td><?= esc($row['kode_barang']) ?></td><td><?= esc($row['nama_barang']) ?></td><td><?= esc($row['jumlah']) ?></td><td><?= esc($row['nama_lengkap']) ?></td><td><?= esc($row['keterangan']) ?></td>
                <td class="d-flex gap-2"><a class="btn btn-sm btn-info text-white" href="<?= site_url('barang-masuk/' . $row['id_masuk']) ?>">Detail</a><a class="btn btn-sm btn-warning" href="<?= site_url('barang-masuk/' . $row['id_masuk'] . '/edit') ?>">Edit</a><form class="form-delete" action="<?= site_url('barang-masuk/' . $row['id_masuk']) ?>" method="post"><?= csrf_field() ?><input type="hidden" name="_method" value="DELETE"><button class="btn btn-sm btn-danger">Hapus</button></form></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $pager->links() ?>
</div></div>
<?= $this->endSection() ?>
