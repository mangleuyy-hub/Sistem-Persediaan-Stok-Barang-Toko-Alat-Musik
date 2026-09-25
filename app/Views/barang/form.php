<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card"><div class="card-body">
    <?php // Form ini dipakai untuk tambah dan edit barang; data null berarti mode tambah. ?>
    <form method="post" action="<?= $data ? site_url('barang/' . $data['id_barang']) : site_url('barang') ?>">
        <?= csrf_field() ?>
        <?php if ($data): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
        <div class="row g-3">
            <?php // Kode barang readonly karena dibuat otomatis oleh BarangModel::nextCode(). ?>
            <div class="col-md-4"><label class="form-label">Kode Barang</label><input class="form-control" name="kode_barang" value="<?= esc(old('kode_barang', $data['kode_barang'] ?? $kode)) ?>" readonly></div>
            <div class="col-md-8"><label class="form-label">Nama Barang</label><input class="form-control" name="nama_barang" value="<?= esc(old('nama_barang', $data['nama_barang'] ?? '')) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Kategori</label><select class="form-select" name="kategori_id" required><option value="">Pilih</option><?php foreach ($kategori as $kat): ?><option value="<?= $kat['id_kategori'] ?>" <?= (int) old('kategori_id', $data['kategori_id'] ?? 0) === (int) $kat['id_kategori'] ? 'selected' : '' ?>><?= esc($kat['nama_kategori']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Merk</label><input class="form-control" name="merk" value="<?= esc(old('merk', $data['merk'] ?? '')) ?>"></div>
            <div class="col-md-4"><label class="form-label">Satuan</label><input class="form-control" name="satuan" value="<?= esc(old('satuan', $data['satuan'] ?? 'Unit')) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Stok</label><input type="number" min="0" class="form-control" name="stok" value="<?= esc(old('stok', $data['stok'] ?? 0)) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Harga Beli</label><input type="number" min="0" class="form-control" name="harga_beli" value="<?= esc(old('harga_beli', $data['harga_beli'] ?? 0)) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Harga Jual</label><input type="number" min="0" class="form-control" name="harga_jual" value="<?= esc(old('harga_jual', $data['harga_jual'] ?? 0)) ?>" required></div>
        </div>
        <div class="mt-3"><button class="btn btn-primary">Simpan</button><a class="btn btn-light" href="<?= site_url('barang') ?>">Kembali</a></div>
    </form>
</div></div>
<?= $this->endSection() ?>
