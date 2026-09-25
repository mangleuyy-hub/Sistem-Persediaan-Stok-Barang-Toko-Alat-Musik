<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $dateField = $type === 'masuk' ? 'tanggal_masuk' : 'tanggal_keluar'; ?>
<div class="card"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9"><?= esc($data[$dateField]) ?></dd>
        <dt class="col-sm-3">Kode Barang</dt><dd class="col-sm-9"><?= esc($data['kode_barang']) ?></dd>
        <dt class="col-sm-3">Nama Barang</dt><dd class="col-sm-9"><?= esc($data['nama_barang']) ?></dd>
        <dt class="col-sm-3">Jumlah</dt><dd class="col-sm-9"><?= esc($data['jumlah']) ?></dd>
        <dt class="col-sm-3">User</dt><dd class="col-sm-9"><?= esc($data['nama_lengkap']) ?></dd>
        <dt class="col-sm-3">Keterangan</dt><dd class="col-sm-9"><?= esc($data['keterangan']) ?></dd>
    </dl>
    <a class="btn btn-light mt-3" href="<?= site_url('barang-' . $type) ?>">Kembali</a>
</div></div>
<?= $this->endSection() ?>
