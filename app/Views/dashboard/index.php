<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="row g-3 mb-4">
    <?php
    // Kartu ringkasan dibuat dari array agar markup card tidak perlu ditulis berulang.
    $cards = [
        ['Total Barang', $totalBarang, 'primary'],
        ['Total Barang Masuk', $totalMasuk, 'success'],
        ['Total Barang Keluar', $totalKeluar, 'danger'],
        ['Stok Terendah (< 10)', $stokTerendah, 'warning'],
        ['Stok Tertinggi', $stokTertinggi ? $stokTertinggi['stok'] . ' - ' . $stokTertinggi['nama_barang'] : '0', 'info'],
    ];
    ?>
    <?php foreach ($cards as $card): ?>
        <div class="col-sm-6 col-xl">
            <div class="card card-stat">
                <div class="card-body">
                    <div class="text-muted small"><?= esc($card[0]) ?></div>
                    <div class="fs-4 fw-semibold text-<?= esc($card[2]) ?>"><?= esc((string) $card[1]) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6"><div class="card"><div class="card-body"><h6>Barang Masuk per Bulan</h6><canvas id="chartMasuk"></canvas></div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body"><h6>Barang Keluar per Bulan</h6><canvas id="chartKeluar"></canvas></div></div></div>
</div>

<div class="card">
    <div class="card-body">
        <h6 class="mb-3">Daftar Stok Kritis (&lt; 10)</h6>
        <div class="table-responsive">
            <table class="table table-striped align-middle datatable">
                <thead><tr><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Stok</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($stokKritis as $row): ?>
                    <tr>
                        <td><?= esc($row['kode_barang']) ?></td>
                        <td><?= esc($row['nama_barang']) ?></td>
                        <td><?= esc($row['nama_kategori']) ?></td>
                        <td><?= esc($row['stok']) ?></td>
                        <?php
                        $stok = (int) $row['stok'];
                        $statusClass = $stok > 0 && $stok < 10 ? 'warning text-dark' : ($row['status_barang'] === 'Tersedia' ? 'success' : 'danger');
                        $statusLabel = $stok > 0 && $stok < 10 ? 'Menipis' : $row['status_barang'];
                        ?>
                        <td><span class="badge bg-<?= $statusClass ?>"><?= esc($statusLabel) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script>
// Data grafik dikirim controller dalam bentuk array, lalu di-encode menjadi JSON untuk Chart.js.
const labels = <?= json_encode($chartLabels) ?>;
new Chart(document.getElementById('chartMasuk'), { type: 'bar', data: { labels, datasets: [{ label: 'Barang Masuk', data: <?= json_encode($chartMasuk) ?>, backgroundColor: '#16a34a' }] } });
new Chart(document.getElementById('chartKeluar'), { type: 'bar', data: { labels, datasets: [{ label: 'Barang Keluar', data: <?= json_encode($chartKeluar) ?>, backgroundColor: '#dc2626' }] } });
</script>
<?= $this->endSection() ?>
