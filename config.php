<?php
// Sesuaikan dengan server Anda (Laragon: user root, password kosong)
const DB_HOST = '127.0.0.1';
const DB_NAME = 'klinik';
const DB_USER = 'root';
const DB_PASS = '';

const NAMA_KLINIK = 'Klinik Pratama UMMI';
const WA_NUMBER   = '6281367811756';

ini_set('session.use_strict_mode', '1');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict', 'secure' => !empty($_SERVER['HTTPS'])]);
session_start();

function db(): PDO {
    static $pdo;
    if ($pdo) return $pdo;
    $o = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
    $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
    try {
        $pdo = new PDO("$dsn;dbname=" . DB_NAME, DB_USER, DB_PASS, $o);
    } catch (PDOException $ex) {
        if ((int)$ex->getCode() !== 1049) throw $ex;      // database belum ada -> buat otomatis
        (new PDO($dsn, DB_USER, DB_PASS, $o))->exec('CREATE DATABASE `' . DB_NAME . '` CHARACTER SET utf8mb4');
        $pdo = new PDO("$dsn;dbname=" . DB_NAME, DB_USER, DB_PASS, $o);
    }
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (k VARCHAR(50) PRIMARY KEY, v TEXT NOT NULL)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kind ENUM('pasien','rekam','jadwal') NOT NULL,
        data MEDIUMTEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (kind))");
    return $pdo;
}
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function check_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Token tidak valid. Muat ulang halaman.'); }
}
