<?php

namespace App\Controllers;

use App\Models\BarangKeluarModel;
use App\Models\BarangModel;

/**
 * Transaksi barang keluar untuk mengurangi stok.
 *
 * Debugging penting:
 * - `stokCukup()` mencegah jumlah keluar melebihi stok tersedia.
 * - Saat edit, jumlah lama dihitung kembali agar validasi stok tidak salah.
 * - Update stok dan simpan transaksi selalu dibungkus database transaction.
 */
class BarangKeluarController extends BaseController
{
    private BarangKeluarModel $keluar;
    private BarangModel $barang;

    public function __construct()
    {
        $this->keluar = new BarangKeluarModel();
        $this->barang = new BarangModel();
    }

    public function index()
    {
        // Transaksi barang keluar hanya untuk Admin dan Staff.
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        $query = $this->keluar->withRelations()->orderBy('tanggal_keluar', 'DESC');
        $awal = $this->request->getGet('awal');
        $akhir = $this->request->getGet('akhir');
        if ($awal && $akhir) {
            // Filter periode transaksi berdasarkan tanggal keluar.
            $query->where('tanggal_keluar >=', $awal)->where('tanggal_keluar <=', $akhir);
        }

        return view('transaksi/keluar/index', [
            'title' => 'Barang Keluar',
            'items' => $query->paginate(10),
            'pager' => $this->keluar->pager,
            'awal' => $awal,
            'akhir' => $akhir,
        ]);
    }

    public function show($id = null)
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        return view('transaksi/detail', ['title' => 'Detail Barang Keluar', 'type' => 'keluar', 'data' => $this->keluar->withRelations()->find($id)]);
    }

    public function new()
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        return view('transaksi/keluar/form', ['title' => 'Tambah Barang Keluar', 'data' => null, 'barang' => $this->barang->findAll()]);
    }

    public function create()
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        $barangId = (int) $this->request->getPost('barang_id');
        $jumlah = (int) $this->request->getPost('jumlah');
        // Barang keluar tidak boleh melebihi stok yang tersedia.
        if (! $this->stokCukup($barangId, $jumlah)) {
            return redirect()->back()->withInput()->with('error', 'Stok tidak cukup untuk transaksi barang keluar.');
        }

        // Simpan transaksi dan kurangi stok dalam satu transaksi database.
        $db = db_connect();
        $db->transStart();
        $this->keluar->insert(['id_keluar' => $this->keluar->nextId()] + $this->payload($barangId, $jumlah));
        $db->table('barang')->where('id_barang', $barangId)->set('stok', "stok - {$jumlah}", false)->update();
        $db->transComplete();
        $this->barang->syncStatus($barangId);

        return redirect()->to('/barang-keluar')->with('success', 'Transaksi barang keluar berhasil disimpan.');
    }

    public function edit($id = null)
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        return view('transaksi/keluar/form', ['title' => 'Edit Barang Keluar', 'data' => $this->keluar->find($id), 'barang' => $this->barang->findAll()]);
    }

    public function update($id = null)
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        $old = $this->keluar->find($id);
        $barangId = (int) $this->request->getPost('barang_id');
        $jumlah = (int) $this->request->getPost('jumlah');
        // Validasi update memperhitungkan jumlah lama agar edit data sendiri tetap adil.
        if (! $this->stokCukup($barangId, $jumlah, $old)) {
            return redirect()->back()->withInput()->with('error', 'Stok tidak cukup untuk memperbarui transaksi.');
        }

        // Saat edit, stok lama dikembalikan dulu lalu stok baru dikurangi.
        $db = db_connect();
        $db->transStart();
        $db->table('barang')->where('id_barang', $old['barang_id'])->set('stok', 'stok + ' . (int) $old['jumlah'], false)->update();
        $this->keluar->update($id, $this->payload($barangId, $jumlah));
        $db->table('barang')->where('id_barang', $barangId)->set('stok', "stok - {$jumlah}", false)->update();
        $db->transComplete();
        $this->barang->syncStatus((int) $old['barang_id']);
        $this->barang->syncStatus($barangId);

        return redirect()->to('/barang-keluar')->with('success', 'Transaksi barang keluar berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        if ($denied = $this->requireRole(['Admin', 'Staff'])) {
            return $denied;
        }

        $old = $this->keluar->find($id);
        // Menghapus barang keluar berarti stok yang pernah dikurangi harus ditambahkan kembali.
        $db = db_connect();
        $db->transStart();
        $db->table('barang')->where('id_barang', $old['barang_id'])->set('stok', 'stok + ' . (int) $old['jumlah'], false)->update();
        $this->keluar->delete($id);
        $db->transComplete();
        $this->barang->syncStatus((int) $old['barang_id']);

        return redirect()->to('/barang-keluar')->with('success', 'Transaksi barang keluar berhasil dihapus.');
    }

    private function stokCukup(int $barangId, int $jumlah, ?array $old = null): bool
    {
        // Untuk update data lama, jumlah transaksi lama dianggap kembali ke stok sementara.
        $barang = $this->barang->find($barangId);
        $stok = (int) ($barang['stok'] ?? 0);
        if ($old && (int) $old['barang_id'] === $barangId) {
            $stok += (int) $old['jumlah'];
        }

        return $jumlah > 0 && $jumlah <= $stok;
    }

    private function payload(int $barangId, int $jumlah): array
    {
        // Payload menjaga mapping field form ke kolom tabel barang_keluar.
        return [
            'tanggal_keluar' => $this->request->getPost('tanggal_keluar'),
            'barang_id' => $barangId,
            'jumlah' => $jumlah,
            'keterangan' => $this->request->getPost('keterangan'),
            'user_id' => session()->get('id_user'),
        ];
    }
}
