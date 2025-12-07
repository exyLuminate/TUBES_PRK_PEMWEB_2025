-- Active: 1762998158480@@127.0.0.1@3306@db_zerowaste
SQL Query
CREATE DATABASE IF NOT EXISTS db_zerowaste;
USE db_zerowaste;
-- ==========================================
-- 1. Tabel Users (Pengguna)
-- ==========================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'donatur', 'mahasiswa') NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    is_active TINYINT(1) DEFAULT 1 COMMENT '1: Aktif, 0: Banned',
    -- Timestamping & Soft Delete
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL
) ENGINE=InnoDB;

-- ==========================================
-- 2. Tabel Categories (Kategori Makanan)
-- ==========================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL,
    -- Timestamping & Soft Delete
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL
) ENGINE=InnoDB;

-- ==========================================
-- 3. Tabel Food Stocks (Katalog Makanan)
-- ==========================================
CREATE TABLE food_stocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donatur_id INT NOT NULL,
    category_id INT NOT NULL,
    judul VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    foto_path VARCHAR(255) NOT NULL,
    
    -- Manajemen Stok
    jumlah_awal INT UNSIGNED NOT NULL,
    stok_tersedia INT UNSIGNED NOT NULL,
    
    -- Detail & Filter
    lokasi_pickup TEXT NOT NULL,
    batas_waktu DATETIME NOT NULL COMMENT 'Deadline makanan bisa diambil',
    jenis_makanan ENUM('halal', 'non_halal') DEFAULT 'halal',
    status ENUM('tersedia', 'habis') DEFAULT 'tersedia',
    -- Timestamping & Soft Delete
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    
    FOREIGN KEY (donatur_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
-- ==========================================
-- 4. Tabel Claims (Transaksi Klaim)
-- ==========================================
CREATE TABLE claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    food_id INT NOT NULL,
    mahasiswa_id INT NOT NULL,
    kode_tiket VARCHAR(10) NOT NULL UNIQUE,
        -- Status Transaksi
    status ENUM('pending', 'diambil', 'batal', 'expired') DEFAULT 'pending',
    alasan_batal VARCHAR(255) DEFAULT NULL,
        -- Waktu Penting
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu Klik Ambil',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    verified_at DATETIME DEFAULT NULL COMMENT 'Waktu diambil di lokasi',
    deleted_at DATETIME DEFAULT NULL,
    
    FOREIGN KEY (food_id) REFERENCES food_stocks(id) ON DELETE RESTRICT,
    FOREIGN KEY (mahasiswa_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
-- ==========================================
-- 5. Tabel Activity Logs (Audit Trail)
-- ==========================================
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,	
    action VARCHAR(50) NOT NULL, -- Contoh: 'LOGIN', 'CLAIM', 'VERIFY'
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;





