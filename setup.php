<?php
// Jalankan SEKALI untuk membuat pasangan kunci. Hapus file ini setelah selesai.
require 'config.php'; require 'lib/crypto.php';
$ada = (bool) db()->query("SELECT COUNT(*) FROM settings WHERE k='public_key'")->fetchColumn();
$private = null;
if (!$ada && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    try { [$private, $public] = generate_keypair(); }
    catch (Throwable $ex) { exit('Gagal membuat kunci. Pastikan ekstensi openssl aktif di php.ini. Detail: ' . e($ex->getMessage())); }
    db()->prepare("INSERT INTO settings (k,v) VALUES ('public_key',?)")->execute([$public]);
    $ada = true;
}
?><!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Setup kunci admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
<main class="auth"><div class="box wide">
<h1>Kunci admin</h1>
<?php if ($private): ?>
  <p class="warnbox"><b>Simpan sekarang.</b> Private key tidak disimpan di server dan tidak bisa dipulihkan. Tanpa key ini, data tidak dapat dibuka.</p>
  <textarea readonly rows="14" onclick="this.select()"><?= e($private) ?></textarea>
  <p><a class="btn" download="private-key-klinik.pem" href="data:application/x-pem-file;base64,<?= base64_encode($private) ?>">Unduh private key</a>
     <a class="btn ghost" href="admin/login.php">Lanjut ke login admin</a></p>
  <p class="muted">Setelah menyimpan key, hapus <code>setup.php</code> dari server.</p>
<?php elseif ($ada): ?>
  <p>Setup sudah dilakukan. Hapus <code>setup.php</code> dari server.</p>
  <p><a class="btn" href="admin/login.php">Ke login admin</a></p>
<?php else: ?>
  <p class="muted">Buat pasangan kunci RSA 3072-bit. Public key disimpan di database untuk mengenkripsi data. Private key menjadi satu-satunya akses masuk admin.</p>
  <form method="post"><input type="hidden" name="csrf" value="<?= csrf() ?>"><button class="btn" type="submit">Buat kunci admin</button></form>
<?php endif ?>
</div></main></html>
