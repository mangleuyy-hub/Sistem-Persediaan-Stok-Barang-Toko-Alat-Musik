<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$today = date('Y-m-d');
$query = ['awal' => $awal, 'akhir' => $akhir, 'jenis' => $jenis, 'q' => $q];
$pdfUrl = site_url('laporan/persediaan/pdf?' . http_build_query($query));
?>
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold mb-1">Laporan Persediaan</h2>
        <div class="text-muted">Riwayat mutasi keluar masuk persediaan barang</div>
    </div>
    <a class="btn btn-light border px-4 py-2" target="_blank" href="<?= esc($pdfUrl) ?>">Cetak Laporan (PDF)</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <span class="small text-muted me-2">Pilih Cepat:</span>
            <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('laporan/persediaan?' . http_build_query(['awal' => $today, 'akhir' => $today, 'jenis' => $jenis, 'q' => $q])) ?>">Hari Ini</a>
            <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('laporan/persediaan?' . http_build_query(['awal' => date('Y-m-d', strtotime('-6 days')), 'akhir' => $today, 'jenis' => $jenis, 'q' => $q])) ?>">7 Hari Terakhir</a>
            <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('laporan/persediaan?' . http_build_query(['awal' => date('Y-m-01'), 'akhir' => $today, 'jenis' => $jenis, 'q' => $q])) ?>">Bulan Ini</a>
        </div>
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-muted">Tanggal Mulai</label>
                <input type="date" class="form-control" name="awal" value="<?= esc($awal ?? '') ?>">
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-muted">Tanggal Selesai</label>
                <input type="date" class="form-control" name="akhir" value="<?= esc($akhir ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filter Tanggal</button>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Jenis Transaksi</label>
                <select class="form-select" name="jenis">
                    <option value="semua" <?= $jenis === 'semua' ? 'selected' : '' ?>>Semua Transaksi</option>
                    <option value="masuk" <?= $jenis === 'masuk' ? 'selected' : '' ?>>Barang Masuk</option>
                    <option value="keluar" <?= $jenis === 'keluar' ? 'selected' : '' ?>>Barang Keluar</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Cari Laporan (Nama Barang / Penginput)</label>
                <input type="search" class="form-control" name="q" value="<?= esc($q ?? '') ?>" placeholder="Ketik kata kunci pencarian...">
            </div>
            <div class="col-md-4">
                <a class="btn btn-outline-secondary w-100" href="<?= site_url('laporan/persediaan') ?>">Bersihkan Filter</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive p-0">
        <table class="table table-striped align-middle mb-0 datatable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Merk</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Stok Awal</th>
                    <th>Sisa Stok</th>
                    <th>Penginput</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $index => $row): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td class="fw-semibold"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                    <td class="fw-semibold"><?= esc($row['nama_barang']) ?></td>
                    <td><?= esc($row['merk'] ?: '-') ?></td>
                    <td><span class="badge bg-<?= $row['jenis'] === 'masuk' ? 'success' : 'danger' ?>"><?= strtoupper(esc($row['jenis'])) ?></span></td>
                    <td class="fw-semibold"><?= number_format((int) $row['jumlah'], 0, ',', '.') ?> unit</td>
                    <td class="fw-semibold"><?= number_format((int) $row['stok_awal'], 0, ',', '.') ?> unit</td>
                    <td><span class="fw-semibold"><?= number_format((int) $row['sisa_stok'], 0, ',', '.') ?> unit</span><br><small class="text-muted">setelah transaksi</small></td>
                    <td><?= esc($row['nama_lengkap']) ?></td>
                    <td><?= esc($row['keterangan'] ?: '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-end">Total Akumulasi Terfilter:</th>
                    <th colspan="4">
                        <span class="text-success">Masuk: +<?= number_format((int) $totalMasuk, 0, ',', '.') ?> unit</span><br>
                        <span class="text-danger">Keluar: -<?= number_format((int) $totalKeluar, 0, ',', '.') ?> unit</span>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
