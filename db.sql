-- Opsional: tabel juga dibuat otomatis oleh config.php
CREATE DATABASE IF NOT EXISTS klinik CHARACTER SET utf8mb4;
USE klinik;
CREATE TABLE IF NOT EXISTS settings (k VARCHAR(50) PRIMARY KEY, v TEXT NOT NULL);
-- Semua isi data (pasien, rekam medis, jadwal) disimpan SEBAGAI CIPHERTEXT di kolom `data`.
CREATE TABLE IF NOT EXISTS records (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kind ENUM('pasien','rekam','jadwal') NOT NULL,
  data MEDIUMTEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (kind)
);
