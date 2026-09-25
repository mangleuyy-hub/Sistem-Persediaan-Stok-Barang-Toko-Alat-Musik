<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card"><div class="card-body">
    <?php // Jika $data berisi transaksi lama, form berubah menjadi mode edit dengan method PUT. ?>
    <form method="post" action="<?= $data ? site_url('barang-keluar/' . $data['id_keluar']) : site_url('barang-keluar') ?>">
        <?= csrf_field() ?><?php if ($data): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Tanggal Keluar</label><input type="date" class="form-control" name="tanggal_keluar" value="<?= esc(old('tanggal_keluar', $data['tanggal_keluar'] ?? date('Y-m-d'))) ?>" required></div>
            <div class="col-md-5"><label class="form-label">Barang</label><select class="form-select" name="barang_id" required><option value="">Pilih barang</option><?php foreach ($barang as $row): ?><option value="<?= $row['id_barang'] ?>" <?= (int) old('barang_id', $data['barang_id'] ?? 0) === (int) $row['id_barang'] ? 'selected' : '' ?>><?= esc($row['kode_barang'] . ' - ' . $row['nama_barang'] . ' (stok: ' . $row['stok'] . ')') ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label">Jumlah</label><input type="number" min="1" class="form-control" name="jumlah" value="<?= esc(old('jumlah', $data['jumlah'] ?? 1)) ?>" required></div>
            <div class="col-12"><label class="form-label">Keterangan</label><textarea class="form-control" name="keterangan" rows="3"><?= esc(old('keterangan', $data['keterangan'] ?? '')) ?></textarea></div>
        </div>
        <div class="mt-3"><button class="btn btn-primary">Simpan</button><a class="btn btn-light" href="<?= site_url('barang-keluar') ?>">Kembali</a></div>
    </form>
</div></div>
<?= $this->endSection() ?>
