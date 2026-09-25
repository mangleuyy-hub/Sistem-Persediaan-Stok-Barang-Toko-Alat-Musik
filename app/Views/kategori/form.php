<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card"><div class="card-body">
    <form method="post" action="<?= $data ? site_url('kategori/' . $data['id_kategori']) : site_url('kategori') ?>">
        <?= csrf_field() ?>
        <?php if ($data): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
        <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input class="form-control" name="nama_kategori" value="<?= esc(old('nama_kategori', $data['nama_kategori'] ?? '')) ?>" required>
        </div>
        <button class="btn btn-primary">Simpan</button>
        <a class="btn btn-light" href="<?= site_url('kategori') ?>">Kembali</a>
    </form>
</div></div>
<?= $this->endSection() ?>
