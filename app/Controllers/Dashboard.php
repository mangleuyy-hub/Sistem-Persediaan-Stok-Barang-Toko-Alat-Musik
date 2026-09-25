<?php

namespace App\Controllers;

use App\Models\BarangKeluarModel;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;

/**
 * Menyediakan ringkasan stok dan grafik transaksi untuk dashboard.
 *
 * Debugging penting:
 * - Data grafik dihitung dari tabel barang_masuk dan barang_keluar per bulan.
 * - Angka stok kritis memakai batas stok < 10.
 * - Jika grafik kosong, cek tanggal transaksi dan format DATE_FORMAT MySQL.
 */
class Dashboard extends BaseController
{
    public function index()
    {
        $barang = new BarangModel();
        $masuk = new BarangMasukModel();
        $keluar = new BarangKeluarModel();

        $labels = [];
        $chartMasuk = [];
        $chartKeluar = [];
        // Siapkan data grafik 12 bulan terakhir untuk Chart.js di view dashboard.
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} month"));
            $labels[] = date('M Y', strtotime($month . '-01'));
            $chartMasuk[] = (int) $masuk->selectSum('jumlah')->where("DATE_FORMAT(tanggal_masuk, '%Y-%m')", $month)->first()['jumlah'];
            $chartKeluar[] = (int) $keluar->selectSum('jumlah')->where("DATE_FORMAT(tanggal_keluar, '%Y-%m')", $month)->first()['jumlah'];
        }

        // Dashboard bersifat ringkasan read-only untuk semua role yang sudah login.
        return view('dashboard/index', [
            'title' => 'Dashboard',
            'totalBarang' => $barang->countAllResults(),
            'totalMasuk' => (int) $masuk->selectSum('jumlah')->first()['jumlah'],
            'totalKeluar' => (int) $keluar->selectSum('jumlah')->first()['jumlah'],
            'stokTerendah' => $barang->where('stok <', 10)->countAllResults(),
            'stokTertinggi' => $barang->orderBy('stok', 'DESC')->first(),
            'stokKritis' => $barang->withKategori()->where('stok <', 10)->orderBy('stok', 'ASC')->findAll(10),
            'chartLabels' => $labels,
            'chartMasuk' => $chartMasuk,
            'chartKeluar' => $chartKeluar,
        ]);
    }
}
