<?php

namespace App\Controllers;

use App\Models\BarangModel;

/**
 * Halaman stok read-only untuk semua user yang sudah login.
 *
 * Debugging penting:
 * - Data stok berasal dari BarangModel::withKategori().
 * - Pencarian memakai parameter GET `q`.
 * - Pagination memakai pager dari query builder model.
 */
class StokController extends BaseController
{
    public function index()
    {
        $q = $this->request->getGet('q');
        // Halaman stok hanya membaca data barang beserta nama kategorinya.
        $query = (new BarangModel())->withKategori()->orderBy('nama_barang', 'ASC');
        if ($q) {
            // Filter stok bisa mencari nama barang, kode barang, atau merk.
            $query->groupStart()->like('nama_barang', $q)->orLike('kode_barang', $q)->orLike('merk', $q)->groupEnd();
        }

        return view('stok/index', [
            'title' => 'Stok Saat Ini',
            'items' => $query->paginate(10),
            'pager' => $query->pager,
            'q' => $q,
        ]);
    }
}
