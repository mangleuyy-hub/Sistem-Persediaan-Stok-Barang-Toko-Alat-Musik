<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\BarangMasukModel;
use App\Models\BarangKeluarModel;
use App\Models\KategoriModel;

/**
 * CRUD master barang untuk Admin.
 *
 * Debugging penting:
 * - Kode barang dibuat otomatis dari ID berikutnya jika form tidak mengirim kode.
 * - Status barang mengikuti stok: stok > 0 berarti Tersedia.
 * - Relasi kategori untuk tabel/detail berasal dari BarangModel::withKategori().
 */
class BarangController extends BaseController
{
    private BarangModel $barang;

    public function __construct()
    {
        // Model disimpan sebagai properti agar semua aksi CRUD memakai instance yang sama.
        $this->barang = new BarangModel();
    }

    public function index()
    {
        // Master barang hanya boleh diakses Admin.
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        $q = $this->request->getGet('q');
        $query = $this->barang->withKategori()->orderBy('id_barang', 'DESC');
        if ($q) {
            // Pencarian mencakup nama, kode, dan merk agar admin cepat menemukan barang.
            $query->groupStart()->like('nama_barang', $q)->orLike('kode_barang', $q)->orLike('merk', $q)->groupEnd();
        }

        return view('barang/index', [
            'title' => 'Daftar Barang',
            'barang' => $query->paginate(10),
            'pager' => $this->barang->pager,
            'q' => $q,
        ]);
    }

    public function show($id = null)
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        return view('barang/detail', ['title' => 'Detail Barang', 'data' => $this->barang->withKategori()->find($id)]);
    }

    public function new()
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        return view('barang/form', ['title' => 'Tambah Barang', 'data' => null, 'kategori' => (new KategoriModel())->findAll(), 'kode' => $this->barang->nextCode()]);
    }

    public function create()
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        $stok = (int) $this->request->getPost('stok');
        if ($stok < 0) {
            return redirect()->back()->withInput()->with('error', 'Stok tidak boleh bernilai negatif.');
        }

        $this->barang->insert(['id_barang' => $this->barang->nextId()] + $this->payload($stok));
        return redirect()->to('/barang')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        return view('barang/form', ['title' => 'Edit Barang', 'data' => $this->barang->find($id), 'kategori' => (new KategoriModel())->findAll(), 'kode' => null]);
    }

    public function update($id = null)
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        $stok = (int) $this->request->getPost('stok');
        if ($stok < 0) {
            return redirect()->back()->withInput()->with('error', 'Stok tidak boleh bernilai negatif.');
        }

        $this->barang->update($id, $this->payload($stok));
        return redirect()->to('/barang')->with('success', 'Barang berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        $adaTransaksiMasuk = (new BarangMasukModel())->where('barang_id', $id)->countAllResults() > 0;
        $adaTransaksiKeluar = (new BarangKeluarModel())->where('barang_id', $id)->countAllResults() > 0;

        // Barang yang sudah masuk riwayat transaksi tidak boleh dihapus agar laporan tetap konsisten.
        if ($adaTransaksiMasuk || $adaTransaksiKeluar) {
            return redirect()->to('/barang')->with('error', 'Barang tidak dapat dihapus karena sudah memiliki riwayat transaksi. Hapus transaksi terkait terlebih dahulu.');
        }

        $this->barang->delete($id);
        return redirect()->to('/barang')->with('success', 'Barang berhasil dihapus.');
    }

    private function payload(int $stok): array
    {
        // Payload memusatkan mapping input form dan penentuan status berdasarkan stok.
        return [
            'kode_barang' => $this->request->getPost('kode_barang') ?: $this->barang->nextCode(),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'kategori_id' => $this->request->getPost('kategori_id'),
            'merk' => $this->request->getPost('merk'),
            'satuan' => $this->request->getPost('satuan'),
            'stok' => $stok,
            'harga_beli' => $this->request->getPost('harga_beli'),
            'harga_jual' => $this->request->getPost('harga_jual'),
            'status_barang' => $stok > 0 ? 'Tersedia' : 'Tidak Tersedia',
        ];
    }
}
