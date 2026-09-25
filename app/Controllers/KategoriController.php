<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;

/**
 * CRUD kategori barang untuk Admin.
 *
 * Debugging penting:
 * - Kategori tidak boleh dihapus jika masih dipakai barang.
 * - Foreign key database juga memakai RESTRICT, jadi validasi delete ada dua lapis.
 * - ID kategori dibuat manual melalui KategoriModel::nextId().
 */
class KategoriController extends BaseController
{
    private KategoriModel $kategori;

    public function __construct()
    {
        $this->kategori = new KategoriModel();
    }

    public function index()
    {
        // Master kategori hanya boleh dibuka Admin.
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        // Pencarian sederhana berdasarkan nama kategori.
        $keyword = $this->request->getGet('q');
        $query = $this->kategori->orderBy('id_kategori', 'DESC');
        if ($keyword) {
            $query->like('nama_kategori', $keyword);
        }

        return view('kategori/index', [
            'title' => 'Kategori Barang',
            'kategori' => $query->paginate(10),
            'pager' => $this->kategori->pager,
            'q' => $keyword,
        ]);
    }

    public function new()
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        return view('kategori/form', ['title' => 'Tambah Kategori', 'data' => null]);
    }

    public function create()
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        $this->kategori->insert([
            'id_kategori' => $this->kategori->nextId(),
            'nama_kategori' => $this->request->getPost('nama_kategori'),
        ]);
        return redirect()->to('/kategori')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        return view('kategori/form', ['title' => 'Edit Kategori', 'data' => $this->kategori->find($id)]);
    }

    public function update($id = null)
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        $this->kategori->update($id, ['nama_kategori' => $this->request->getPost('nama_kategori')]);
        return redirect()->to('/kategori')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        if ($denied = $this->requireRole('Admin')) {
            return $denied;
        }

        // Kategori yang masih dipakai barang tidak boleh dihapus karena foreign key RESTRICT.
        $barang = new BarangModel();
        $produkCount = $barang->where('kategori_id', $id)->countAllResults();

        if ($produkCount > 0) {
            // Pesan dibedakan agar admin tahu apakah masih ada stok aktif di kategori ini.
            $stokTersedia = (int) (new BarangModel())
                ->selectSum('stok')
                ->where('kategori_id', $id)
                ->first()['stok'];

            $message = $stokTersedia > 0
                ? 'Kategori tidak dapat dihapus karena produk dan stok masih tersedia.'
                : 'Kategori tidak dapat dihapus karena masih memiliki produk. Hapus atau pindahkan produk terlebih dahulu.';

            return redirect()->to('/kategori')->with('error', $message);
        }

        $this->kategori->delete($id);
        return redirect()->to('/kategori')->with('success', 'Kategori berhasil dihapus.');
    }
}
