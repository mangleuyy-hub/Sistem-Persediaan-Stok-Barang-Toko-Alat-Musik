<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
    <?php // Filter periode dikirim dengan GET agar bisa ikut terbawa ke link PDF/Excel. ?>
    <form class="d-flex flex-wrap gap-2" method="get">
        <input type="date" class="form-control" name="awal" value="<?= esc($awal ?? '') ?>">
        <input type="date" class="form-control" name="akhir" value="<?= esc($akhir ?? '') ?>">
        <button class="btn btn-outline-primary">Filter</button>
    </form>
    <div class="d-flex gap-2">
        <?php // Link export mempertahankan filter awal/akhir yang sedang dipakai. ?>
        <a class="btn btn-danger" target="_blank" href="<?= site_url('laporan/barang-' . $type . '/pdf?' . http_build_query(['awal' => $awal, 'akhir' => $akhir])) ?>">Cetak PDF</a>
        <a class="btn btn-success" href="<?= site_url('laporan/barang-' . $type . '/excel?' . http_build_query(['awal' => $awal, 'akhir' => $akhir])) ?>">Export Excel</a>
    </div>
</div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-striped align-middle datatable">
        <thead><tr><th>Tanggal</th><th>Kode</th><th>Nama Barang</th><th>Jumlah</th><th>User</th><th>Keterangan</th></tr></thead>
        <tbody>
        <?php foreach ($items as $row): ?>
            <tr><td><?= esc($row[$dateField]) ?></td><td><?= esc($row['kode_barang']) ?></td><td><?= esc($row['nama_barang']) ?></td><td><?= esc($row['jumlah']) ?></td><td><?= esc($row['nama_lengkap']) ?></td><td><?= esc($row['keterangan']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div></div>
<?= $this->endSection() ?>
