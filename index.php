<?php require 'config.php'; $wa = 'https://wa.me/' . WA_NUMBER; ?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e(NAMA_KLINIK) ?> — Universitas Muhammadiyah Sukabumi</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css"></head><body>
<header class="top"><a class="brand" href="#beranda"><img src="assets/img/logo.png" alt="" width="52" height="52"><?= e(NAMA_KLINIK) ?></a>
<nav><a href="#beranda">Beranda</a><a href="#manfaat">Manfaat</a><a href="#layanan">Layanan</a><a class="navbtn" href="admin/login.php">Login Admin</a></nav></header>

<section id="beranda" class="hero wrap">
  <div><p class="eyebrow">Universitas Muhammadiyah Sukabumi</p>
  <h1>Kesehatan Anda, prioritas kami.</h1>
  <p class="lead">Klinik Pratama UMMI hadir untuk membantu kebutuhan layanan kesehatan Anda. Kenali layanan kami dan hubungi klinik untuk informasi jadwal praktik.</p>
  <p class="actions"><a class="btn" href="<?= e($wa) ?>" target="_blank" rel="noopener">Hubungi via WhatsApp</a><a class="btn ghost" href="#layanan">Lihat Layanan</a></p></div>
  <figure class="photo"><img src="assets/img/klinik.jpg" alt="Gedung Klinik Pratama UMMI"></figure>
</section>

<section id="manfaat" class="band"><div class="wrap two">
  <div><p class="eyebrow">Mengenal klinik kami</p><h2>Informasi layanan lebih dekat dengan Anda.</h2></div>
  <p class="lead">Sebagai klinik milik Universitas Muhammadiyah Sukabumi, Klinik Pratama UMMI menyediakan informasi layanan dan kanal komunikasi yang mudah diakses. Untuk memastikan jam praktik dan ketersediaan layanan, silakan hubungi hotline klinik.</p>
</div></section>

<section id="layanan" class="wrap feat">
  <p class="eyebrow">Layanan klinik</p><h2>Layanan di Klinik Pratama UMMI.</h2>
  <p class="muted">Berikut beberapa layanan yang tercantum pada poster Klinik Pratama UMMI.</p>
  <div class="grid">
    <div class="card"><i>✚</i><h3>Poli Umum</h3><p>Layanan poli umum untuk kebutuhan pemeriksaan kesehatan.</p></div>
    <div class="card"><i>◇</i><h3>Tindakan Sederhana</h3><p>Pelayanan tindakan sederhana tersedia di klinik.</p></div>
    <div class="card"><i>✧</i><h3>Perawatan Luka</h3><p>Layanan perawatan luka sesuai kebutuhan pemeriksaan.</p></div>
    <div class="card"><i>✳</i><h3>Khitan</h3><p>Hubungi klinik untuk informasi layanan khitan dan jadwalnya.</p></div>
  </div>
  <p class="small">Jam praktik dan ketersediaan layanan: konfirmasi melalui hotline 0813-6781-1756.</p>
</section>

<footer><div><b><?= e(NAMA_KLINIK) ?></b><br><span>Universitas Muhammadiyah Sukabumi</span></div>
<div class="flinks"><span>0813-6781-1756</span><span>@klinikummi</span><span>klinikummi@ummi.ac.id</span></div></footer>
</body></html>
