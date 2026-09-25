CREATE DATABASE IF NOT EXISTS db_alat_musik CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_alat_musik;

CREATE TABLE users (
  id_user INT UNSIGNED PRIMARY KEY,
  nama_lengkap VARCHAR(120) NOT NULL,
  username VARCHAR(60) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('Admin','Owner','Staff') NOT NULL DEFAULT 'Staff',
  created_at DATETIME NULL,
  updated_at DATETIME NULL
);

CREATE TABLE kategori_barang (
  id_kategori INT UNSIGNED PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
);

CREATE TABLE barang (
  id_barang INT UNSIGNED PRIMARY KEY,
  kode_barang VARCHAR(30) NOT NULL UNIQUE,
  nama_barang VARCHAR(150) NOT NULL,
  kategori_id INT UNSIGNED NOT NULL,
  merk VARCHAR(100) NULL,
  satuan VARCHAR(30) NOT NULL,
  stok INT UNSIGNED NOT NULL DEFAULT 0,
  harga_beli DECIMAL(14,2) NOT NULL DEFAULT 0,
  harga_jual DECIMAL(14,2) NOT NULL DEFAULT 0,
  status_barang ENUM('Tersedia','Tidak Tersedia') NOT NULL DEFAULT 'Tidak Tersedia',
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  CONSTRAINT fk_barang_kategori FOREIGN KEY (kategori_id) REFERENCES kategori_barang(id_kategori) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE barang_masuk (
  id_masuk INT UNSIGNED PRIMARY KEY,
  tanggal_masuk DATE NOT NULL,
  barang_id INT UNSIGNED NOT NULL,
  jumlah INT NOT NULL,
  keterangan TEXT NULL,
  user_id INT UNSIGNED NOT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  CONSTRAINT fk_masuk_barang FOREIGN KEY (barang_id) REFERENCES barang(id_barang) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_masuk_user FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE barang_keluar (
  id_keluar INT UNSIGNED PRIMARY KEY,
  tanggal_keluar DATE NOT NULL,
  barang_id INT UNSIGNED NOT NULL,
  jumlah INT NOT NULL,
  keterangan TEXT NULL,
  user_id INT UNSIGNED NOT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  CONSTRAINT fk_keluar_barang FOREIGN KEY (barang_id) REFERENCES barang(id_barang) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_keluar_user FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE RESTRICT ON UPDATE CASCADE
);
