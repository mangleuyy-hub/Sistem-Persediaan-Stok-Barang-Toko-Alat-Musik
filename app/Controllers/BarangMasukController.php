<?php

namespace App\Controllers;

use App\Models\BarangMasukModel;
use App\Models\BarangModel;

/**
 * Transaksi barang masuk untuk menambah stok.
 *
 * Debugging penting:
 * - Insert transaksi dan update stok dilakukan dalam database transaction.
 * - Saat edit/delete, stok lama harus dikembalikan agar stok akhir tetap benar.
 * - User pencatat transaksi diambil dari session `id_user`.
 */
class BarangMasukController extends BaseController
{
    private BarangMasukModel $masuk;
    private BarangModel $barang;

    public function __construct()
    {
        $this->masuk = new BarangMasukModel();
        $this->barang = new BarangModel();
    }

    public function index()
    {
        // Transaksi barang masuk hanya untuk Admin dan Staff.
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        $query = $this->masuk->withRelations()->orderBy('tanggal_masuk', 'DESC');
        $awal = $this->request->getGet('awal');
        $akhir = $this->request->getGet('akhir');
        if ($awal && $akhir) {
            // Filter periode laporan/transaksi berdasarkan tanggal masuk.
            $query->where('tanggal_masuk >=', $awal)->where('tanggal_masuk <=', $akhir);
        }

        return view('transaksi/masuk/index', [
            'title' => 'Barang Masuk',
            'items' => $query->paginate(10),
            'pager' => $this->masuk->pager,
            'awal' => $awal,
            'akhir' => $akhir,
        ]);
    }

    public function show($id = null)
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        return view('transaksi/detail', ['title' => 'Detail Barang Masuk', 'type' => 'masuk', 'data' => $this->masuk->withRelations()->find($id)]);
    }

    public function new()
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        return view('transaksi/masuk/form', ['title' => 'Tambah Barang Masuk', 'data' => null, 'barang' => $this->barang->findAll()]);
    }

    public function create()
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        // Simpan transaksi dan tambah stok dalam satu transaksi database.
        $db = db_connect();
        $db->transStart();
        $barangId = (int) $this->request->getPost('barang_id');
        $jumlah = (int) $this->request->getPost('jumlah');
        $this->masuk->insert(['id_masuk' => $this->masuk->nextId()] + $this->payload($barangId, $jumlah));
        $db->table('barang')->where('id_barang', $barangId)->set('stok', "stok + {$jumlah}", false)->update();
        $db->transComplete();
        // Status barang disesuaikan otomatis berdasarkan stok terbaru.
        $this->barang->syncStatus($barangId);

        return redirect()->to('/barang-masuk')->with('success', 'Transaksi barang masuk berhasil disimpan.');
    }

    public function edit($id = null)
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        return view('transaksi/masuk/form', ['title' => 'Edit Barang Masuk', 'data' => $this->masuk->find($id), 'barang' => $this->barang->findAll()]);
    }

    public function update($id = null)
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        $old = $this->masuk->find($id);
        $barangId = (int) $this->request->getPost('barang_id');
        $jumlah = (int) $this->request->getPost('jumlah');

        // Saat edit, stok lama dikembalikan dulu lalu stok baru diterapkan.
        $db = db_connect();
        $db->transStart();
        $db->table('barang')->where('id_barang', $old['barang_id'])->set('stok', 'stok - ' . (int) $old['jumlah'], false)->update();
        $this->masuk->update($id, $this->payload($barangId, $jumlah));
        $db->table('barang')->where('id_barang', $barangId)->set('stok', "stok + {$jumlah}", false)->update();
        $db->transComplete();
        $this->barang->syncStatus((int) $old['barang_id']);
        $this->barang->syncStatus($barangId);

        return redirect()->to('/barang-masuk')->with('success', 'Transaksi barang masuk berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        $old = $this->masuk->find($id);
        // Menghapus barang masuk berarti stok yang pernah ditambahkan harus dikurangi kembali.
        $db = db_connect();
        $db->transStart();
        $db->table('barang')->where('id_barang', $old['barang_id'])->set('stok', 'stok - ' . (int) $old['jumlah'], false)->update();
        $this->masuk->delete($id);
        $db->transComplete();
        $this->barang->syncStatus((int) $old['barang_id']);

        return redirect()->to('/barang-masuk')->with('success', 'Transaksi barang masuk berhasil dihapus.');
    }

    private function payload(int $barangId, int $jumlah): array
    {
        // Payload menjaga mapping field form ke kolom tabel barang_masuk.
        return [
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'barang_id' => $barangId,
            'jumlah' => $jumlah,
            'keterangan' => $this->request->getPost('keterangan'),
            'user_id' => session()->get('id_user'),
        ];
    }
}
