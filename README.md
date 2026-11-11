# Sistem Informasi Klinik Pratama UMMI (PHP)
1. Pastikan ekstensi PHP `openssl` & `pdo_mysql` aktif (PHP 8+). Database `klinik` dan tabelnya dibuat otomatis (atau impor `db.sql`). Sesuaikan `config.php`.
2. Buka `/setup.php`, klik "Buat kunci admin", simpan private key (.pem). Lalu HAPUS setup.php.
3. Buka `/admin/login.php`, tempel atau unggah private key.
Gunakan HTTPS di produksi.
