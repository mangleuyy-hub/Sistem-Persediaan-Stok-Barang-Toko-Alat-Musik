<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #555; padding: 6px; }
        th { background: #eee; }
    </style>
</head>
<body>
<h2><?= esc($title) ?></h2>
<table>
    <thead><tr><th>Tanggal</th><th>Kode</th><th>Nama Barang</th><th>Jumlah</th><th>User</th><th>Keterangan</th></tr></thead>
    <tbody>
    <?php foreach ($items as $row): ?>
        <tr><td><?= esc($row[$dateField]) ?></td><td><?= esc($row['kode_barang']) ?></td><td><?= esc($row['nama_barang']) ?></td><td><?= esc($row['jumlah']) ?></td><td><?= esc($row['nama_lengkap']) ?></td><td><?= esc($row['keterangan']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
