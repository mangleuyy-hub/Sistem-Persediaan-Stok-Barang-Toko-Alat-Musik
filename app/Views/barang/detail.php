<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card"><div class="card-body">
    <dl class="row mb-0">
        <?php foreach (['kode_barang' => 'Kode', 'nama_barang' => 'Nama Barang', 'nama_kategori' => 'Kategori', 'merk' => 'Merk', 'satuan' => 'Satuan', 'stok' => 'Stok', 'status_barang' => 'Status'] as $key => $label): ?>
            <dt class="col-sm-3"><?= esc($label) ?></dt><dd class="col-sm-9"><?= esc($data[$key] ?? '-') ?></dd>
        <?php endforeach; ?>
        <dt class="col-sm-3">Harga Beli</dt><dd class="col-sm-9">Rp <?= number_format((float) $data['harga_beli'], 0, ',', '.') ?></dd>
        <dt class="col-sm-3">Harga Jual</dt><dd class="col-sm-9">Rp <?= number_format((float) $data['harga_jual'], 0, ',', '.') ?></dd>
    </dl>
    <a class="btn btn-light mt-3" href="<?= site_url('barang') ?>">Kembali</a>
</div></div>
<?= $this->endSection() ?>
