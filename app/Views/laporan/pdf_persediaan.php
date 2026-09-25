<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #555; padding: 5px; vertical-align: top; }
        th { background: #eee; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
<h2><?= esc($title) ?></h2>
<p>Riwayat mutasi keluar masuk persediaan barang</p>
<table>
    <thead><tr><th>No</th><th>Tanggal</th><th>Nama Barang</th><th>Merk</th><th>Jenis</th><th>Jumlah</th><th>Stok Awal</th><th>Sisa Stok</th><th>Penginput</th><th>Keterangan</th></tr></thead>
    <tbody>
    <?php foreach ($items as $index => $row): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
            <td><?= esc($row['nama_barang']) ?></td>
            <td><?= esc($row['merk'] ?: '-') ?></td>
            <td><?= strtoupper(esc($row['jenis'])) ?></td>
            <td><?= number_format((int) $row['jumlah'], 0, ',', '.') ?> unit</td>
            <td><?= number_format((int) $row['stok_awal'], 0, ',', '.') ?> unit</td>
            <td><?= number_format((int) $row['sisa_stok'], 0, ',', '.') ?> unit</td>
            <td><?= esc($row['nama_lengkap']) ?></td>
            <td><?= esc($row['keterangan'] ?: '-') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6" class="text-right">Total Akumulasi Terfilter</th>
            <th colspan="4">Masuk: +<?= number_format((int) $totalMasuk, 0, ',', '.') ?> unit<br>Keluar: -<?= number_format((int) $totalKeluar, 0, ',', '.') ?> unit</th>
        </tr>
    </tfoot>
</table>
</body>
</html>
