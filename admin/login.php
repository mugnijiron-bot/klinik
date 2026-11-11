<?php
require '../config.php'; require '../lib/crypto.php';
if (!empty($_SESSION['pk'])) { header('Location: index.php'); exit; }
$belumSetup = !public_key();
$err = '';
if (!$belumSetup && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $_SESSION['try'] = ($_SESSION['try'] ?? 0) + 1;
    if ($_SESSION['try'] > 5) { $err = 'Terlalu banyak percobaan. Tutup browser lalu coba lagi.'; }
    else {
        $pem = trim($_POST['key'] ?? '');
        if (!$pem && !empty($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']))
            $pem = trim(file_get_contents($_FILES['file']['tmp_name'], false, null, 0, 20000));
        if ($pem && verify_private_key($pem)) {
            session_regenerate_id(true);
            $_SESSION['pk'] = $pem; $_SESSION['try'] = 0;
            header('Location: index.php'); exit;
        }
        usleep(700000);
        $err = 'Private key tidak valid atau tidak cocok dengan klinik ini.';
    }
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Masuk Admin — <?= e(NAMA_KLINIK) ?></title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css"></head><body class="loginpage">
<main class="login">
  <aside class="login-side">
    <a class="brand light" href="../"><img src="../assets/img/logo.png" alt="" width="48" height="48"><?= e(NAMA_KLINIK) ?></a>
    <div>
      <h2>Area admin klinik</h2>
      <p>Data pasien, rekam medis, dan jadwal dokter disimpan terenkripsi. Hanya private key Anda yang bisa membukanya.</p>
      <ul class="points">
        <li>Tanpa password. Masuk dengan private key.</li>
        <li>Key tidak pernah disimpan di server.</li>
        <li>Data didekripsi saat sesi Anda aktif.</li>
      </ul>
    </div>
    <a class="back" href="../">← Kembali ke beranda</a>
  </aside>

  <section class="login-main">
    <form class="loginform" method="post" enctype="multipart/form-data" id="f">
      <h1>Masuk dengan private key</h1>
      <p class="muted">Unggah file <code>.pem</code> atau tempel isi kuncinya.</p>
      <?php if ($belumSetup): ?>
        <p class="err">Kunci admin belum dibuat. Buka <a href="../setup.php"><u>setup.php</u></a> terlebih dahulu.</p>
      <?php endif ?>
      <?php if ($err): ?><p class="err" role="alert"><?= e($err) ?></p><?php endif ?>
      <input type="hidden" name="csrf" value="<?= csrf() ?>">

      <label class="drop" id="drop" tabindex="0">
        <input type="file" name="file" id="file" accept=".pem,.key,.txt" hidden>
        <b id="dropt">Pilih file private key</b>
        <span>atau seret file ke sini</span>
      </label>

      <label class="field">Atau tempel private key
        <textarea name="key" id="key" rows="7" spellcheck="false" autocomplete="off" placeholder="-----BEGIN PRIVATE KEY-----"></textarea>
      </label>
      <button class="btn block" type="submit" <?= $belumSetup ? 'disabled' : '' ?>>Buka data klinik</button>
    </form>
  </section>
</main>
<script>
const file = document.getElementById('file'), key = document.getElementById('key'),
      drop = document.getElementById('drop'), dropt = document.getElementById('dropt');
function load(f){ if(!f) return; const r = new FileReader();
  r.onload = () => { key.value = r.result; dropt.textContent = f.name; drop.classList.add('ok'); }; r.readAsText(f); }
file.onchange = () => load(file.files[0]);
['dragover','dragenter'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.add('over'); }));
['dragleave','drop'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.remove('over'); }));
drop.addEventListener('drop', e => load(e.dataTransfer.files[0]));
drop.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); file.click(); } });
</script>
</body></html>
