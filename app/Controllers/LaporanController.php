<?php

namespace App\Controllers;

use App\Models\BarangKeluarModel;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Laporan transaksi dan persediaan untuk Admin/Owner.
 *
 * Debugging penting:
 * - Halaman persediaan menggabungkan barang masuk dan barang keluar.
 * - `sisa_stok` dihitung mundur dari stok terkini karena tabel transaksi tidak menyimpan snapshot stok.
 * - `stok_awal` dihitung dari jenis transaksi: masuk = sisa - jumlah, keluar = sisa + jumlah.
 * - Export PDF memakai Dompdf, export Excel memakai PhpSpreadsheet.
 */
class LaporanController extends BaseController
{
    public function barangMasuk()
    {
        // Laporan hanya dapat dibaca oleh Admin dan Owner.
        if ($denied = $this->requireRole(['Admin', 'Owner'])) {
            return $denied;
        }

        return redirect()->to(site_url('laporan/persediaan?' . http_build_query($this->filterWithJenis('masuk'))));
    }

    public function barangKeluar()
    {
        if ($denied = $this->requireRole(['Admin', 'Owner'])) {
            return $denied;
        }

        return redirect()->to(site_url('laporan/persediaan?' . http_build_query($this->filterWithJenis('keluar'))));
    }

    public function persediaan()
    {
        if ($denied = $this->requireRole(['Admin', 'Owner'])) {
            return $denied;
        }

        return view('laporan/persediaan', $this->laporanPersediaan());
    }

    public function barangMasukPdf()
    {
        if ($denied = $this->requireRole(['Admin', 'Owner'])) {
            return $denied;
        }

        return $this->pdf('Laporan Barang Masuk', 'laporan/pdf_transaksi', $this->laporanTransaksi('masuk'));
    }

    public function barangKeluarPdf()
    {
        if ($denied = $this->requireRole(['Admin', 'Owner'])) {
            return $denied;
        }

        return $this->pdf('Laporan Barang Keluar', 'laporan/pdf_transaksi', $this->laporanTransaksi('keluar'));
    }

    public function persediaanPdf()
    {
        if ($denied = $this->requireRole(['Admin', 'Owner'])) {
            return $denied;
        }

        return $this->pdf('Laporan Persediaan', 'laporan/pdf_persediaan', $this->laporanPersediaan());
    }

    public function barangMasukExcel()
    {
        if ($denied = $this->requireRole(['Admin', 'Owner'])) {
            return $denied;
        }

        return $this->excelTransaksi('masuk');
    }

    public function barangKeluarExcel()
    {
        if ($denied = $this->requireRole(['Admin', 'Owner'])) {
            return $denied;
        }

        return $this->excelTransaksi('keluar');
    }

    public function persediaanExcel()
    {
        if ($denied = $this->requireRole(['Admin', 'Owner'])) {
            return $denied;
        }

        $data = $this->laporanPersediaan();
        return $this->downloadExcel('laporan-persediaan.xlsx', ['Tanggal', 'Kode', 'Nama Barang', 'Merk', 'Jenis', 'Jumlah', 'Stok Awal', 'Sisa Stok', 'Penginput', 'Keterangan'], array_map(static fn ($row) => [
            $row['tanggal'], $row['kode_barang'], $row['nama_barang'], $row['merk'], strtoupper($row['jenis']), $row['jumlah'], $row['stok_awal'], $row['sisa_stok'], $row['nama_lengkap'], $row['keterangan'],
        ], $data['items']));
    }

    private function laporanTransaksi(string $type): array
    {
        // Satu helper dipakai untuk laporan barang masuk dan keluar agar filter periodenya konsisten.
        $awal = $this->request->getGet('awal');
        $akhir = $this->request->getGet('akhir');
        $model = $type === 'masuk' ? new BarangMasukModel() : new BarangKeluarModel();
        $dateField = $type === 'masuk' ? 'tanggal_masuk' : 'tanggal_keluar';
        $query = $model->withRelations()->orderBy($dateField, 'DESC');
        if ($awal && $akhir) {
            // Jika periode diisi lengkap, data dibatasi dari tanggal awal sampai akhir.
            $query->where($dateField . ' >=', $awal)->where($dateField . ' <=', $akhir);
        }

        return [
            'title' => $type === 'masuk' ? 'Laporan Barang Masuk' : 'Laporan Barang Keluar',
            'type' => $type,
            'dateField' => $dateField,
            'items' => $query->findAll(),
            'awal' => $awal,
            'akhir' => $akhir,
        ];
    }

    private function laporanPersediaan(): array
    {
        // Parameter GET ini juga diteruskan ke PDF/Excel agar hasil cetak sama dengan tabel layar.
        $awal = $this->request->getGet('awal');
        $akhir = $this->request->getGet('akhir');
        $jenis = $this->request->getGet('jenis') ?: 'semua';
        $keyword = trim((string) $this->request->getGet('q'));

        $allItems = $this->mutasiPersediaan();
        $items = array_values(array_filter($allItems, static function (array $row) use ($awal, $akhir, $jenis, $keyword): bool {
            if ($awal && $row['tanggal'] < $awal) {
                return false;
            }
            if ($akhir && $row['tanggal'] > $akhir) {
                return false;
            }
            if (in_array($jenis, ['masuk', 'keluar'], true) && $row['jenis'] !== $jenis) {
                return false;
            }
            if ($keyword !== '') {
                $haystack = strtolower($row['nama_barang'] . ' ' . $row['kode_barang'] . ' ' . $row['merk'] . ' ' . $row['nama_lengkap']);
                return strpos($haystack, strtolower($keyword)) !== false;
            }

            return true;
        }));

        return [
            'title' => 'Laporan Persediaan',
            'items' => $items,
            'awal' => $awal,
            'akhir' => $akhir,
            'jenis' => $jenis,
            'q' => $keyword,
            'totalMasuk' => array_sum(array_map(static fn ($row) => $row['jenis'] === 'masuk' ? (int) $row['jumlah'] : 0, $items)),
            'totalKeluar' => array_sum(array_map(static fn ($row) => $row['jenis'] === 'keluar' ? (int) $row['jumlah'] : 0, $items)),
        ];
    }

    private function filterWithJenis(string $jenis): array
    {
        return [
            'awal' => $this->request->getGet('awal'),
            'akhir' => $this->request->getGet('akhir'),
            'jenis' => $jenis,
            'q' => $this->request->getGet('q'),
        ];
    }

    private function mutasiPersediaan(): array
    {
        // Dua tabel transaksi dibuat menjadi satu bentuk data agar mudah difilter dan dicetak.
        $masuk = array_map(static fn ($row) => [
            'id' => (int) $row['id_masuk'],
            'tanggal' => $row['tanggal_masuk'],
            'barang_id' => (int) $row['barang_id'],
            'kode_barang' => $row['kode_barang'],
            'nama_barang' => $row['nama_barang'],
            'merk' => $row['merk'] ?? '-',
            'jenis' => 'masuk',
            'jumlah' => (int) $row['jumlah'],
            'nama_lengkap' => $row['nama_lengkap'],
            'keterangan' => $row['keterangan'] ?? '-',
        ], (new BarangMasukModel())->withRelations()->findAll());

        $keluar = array_map(static fn ($row) => [
            'id' => (int) $row['id_keluar'],
            'tanggal' => $row['tanggal_keluar'],
            'barang_id' => (int) $row['barang_id'],
            'kode_barang' => $row['kode_barang'],
            'nama_barang' => $row['nama_barang'],
            'merk' => $row['merk'] ?? '-',
            'jenis' => 'keluar',
            'jumlah' => (int) $row['jumlah'],
            'nama_lengkap' => $row['nama_lengkap'],
            'keterangan' => $row['keterangan'] ?? '-',
        ], (new BarangKeluarModel())->withRelations()->findAll());

        $items = array_merge($masuk, $keluar);
        // Urut lama ke baru dulu agar rekonstruksi stok bisa dihitung dari stok terbaru ke belakang.
        usort($items, static fn ($a, $b) => [$a['tanggal'], $a['id'], $a['jenis']] <=> [$b['tanggal'], $b['id'], $b['jenis']]);

        $stocks = array_column((new BarangModel())->findAll(), 'stok', 'id_barang');
        $current = [];
        foreach ($stocks as $barangId => $stok) {
            $current[(int) $barangId] = (int) $stok;
        }

        for ($i = count($items) - 1; $i >= 0; $i--) {
            $barangId = $items[$i]['barang_id'];
            $items[$i]['sisa_stok'] = $current[$barangId] ?? 0;
            $items[$i]['stok_awal'] = $items[$i]['jenis'] === 'masuk'
                ? $items[$i]['sisa_stok'] - $items[$i]['jumlah']
                : $items[$i]['sisa_stok'] + $items[$i]['jumlah'];
            $current[$barangId] += $items[$i]['jenis'] === 'masuk' ? -$items[$i]['jumlah'] : $items[$i]['jumlah'];
        }

        // Setelah stok selesai dihitung, data dikembalikan ke urutan terbaru untuk tampilan laporan.
        usort($items, static fn ($a, $b) => [$b['tanggal'], $b['id'], $b['jenis']] <=> [$a['tanggal'], $a['id'], $a['jenis']]);

        return $items;
    }

    private function pdf(string $title, string $view, array $data)
    {
        // Dompdf mengubah view HTML laporan menjadi file PDF landscape.
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view($view, ['title' => $title] + $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return $this->response->setHeader('Content-Type', 'application/pdf')->setBody($dompdf->output());
    }

    private function excelTransaksi(string $type)
    {
        // Data transaksi dipetakan ke format baris spreadsheet sebelum diunduh.
        $data = $this->laporanTransaksi($type);
        $rows = array_map(static fn ($row) => [
            $row['kode_barang'], $row['nama_barang'], $row[$data['dateField']], $row['jumlah'], $row['nama_lengkap'], $row['keterangan'],
        ], $data['items']);

        return $this->downloadExcel('laporan-barang-' . $type . '.xlsx', ['Kode', 'Nama Barang', 'Tanggal', 'Jumlah', 'User', 'Keterangan'], $rows);
    }

    private function downloadExcel(string $filename, array $headers, array $rows)
    {
        // PhpSpreadsheet menulis output ke buffer, lalu buffer dikirim sebagai response download.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray($rows, null, 'A2');
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }
}
