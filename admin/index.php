<?php
require '../config.php'; require '../lib/crypto.php';
if (empty($_SESSION['pk']) || !($priv = openssl_pkey_get_private($_SESSION['pk']))) { header('Location: login.php'); exit; }

$forms = [
 'pasien' => ['Pasien', ['nama'=>'Nama','nik'=>'NIK','tgl_lahir'=>'Tanggal lahir','telp'=>'Telepon','alamat'=>'Alamat']],
 'rekam'  => ['Rekam medis', ['pasien_id'=>'Pasien','tanggal'=>'Tanggal','keluhan'=>'Keluhan','diagnosa'=>'Diagnosa','tindakan'=>'Tindakan','obat'=>'Obat']],
 'jadwal' => ['Jadwal dokter', ['dokter'=>'Dokter','spesialis'=>'Spesialis','hari'=>'Hari','jam'=>'Jam praktik']],
];
$p = $_GET['p'] ?? 'ringkasan';
if ($p !== 'ringkasan' && !isset($forms[$p])) $p = 'ringkasan';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($forms[$p])) {
    check_csrf();
    if (isset($_POST['hapus'])) {
        db()->prepare('DELETE FROM records WHERE id=? AND kind=?')->execute([(int)$_POST['hapus'], $p]);
    } else {
        $row = [];
        foreach ($forms[$p][1] as $f => $_) $row[$f] = trim($_POST[$f] ?? '');
        db()->prepare('INSERT INTO records (kind,data) VALUES (?,?)')->execute([$p, enc($row)]);  // dienkripsi sebelum masuk DB
    }
    header("Location: index.php?p=$p"); exit;
}

// Ambil + dekripsi semua data
function load(string $kind, $priv): array {
    $st = db()->prepare('SELECT id,data FROM records WHERE kind=? ORDER BY id DESC'); $st->execute([$kind]);
    $out = [];
    foreach ($st as $r) $out[] = ['id'=>$r['id']] + (dec($r['data'], $priv) ?? ['_gagal'=>true]);
    return $out;
}
$pasien = load('pasien', $priv);
$nama = array_column($pasien, 'nama', 'id');
$act = fn($k) => $p === $k ? ' class="on"' : '';
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin — <?= e(NAMA_KLINIK) ?></title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css"></head><body>
<header class="top"><a class="brand" href="../"><img src="../assets/img/logo.png" alt="" width="40" height="40">Admin <?= e(NAMA_KLINIK) ?></a>
<nav><a href="?p=ringkasan"<?= $act('ringkasan') ?>>Ringkasan</a><?php foreach ($forms as $k=>$f): ?><a href="?p=<?= $k ?>"<?= $act($k) ?>><?= e($f[0]) ?></a><?php endforeach ?><a href="logout.php">Keluar</a></nav></header>
<main class="admin">
<?php if ($p === 'ringkasan'): $rk = load('rekam',$priv); $jd = load('jadwal',$priv); $bln = date('Y-m'); ?>
  <h1>Laporan klinik</h1>
  <p class="muted">Seluruh data di halaman ini sudah didekripsi dengan private key Anda.</p>
  <div class="stats">
    <div class="card"><b><?= count($pasien) ?></b>Pasien</div>
    <div class="card"><b><?= count($rk) ?></b>Rekam medis</div>
    <div class="card"><b><?= count(array_filter($rk, fn($r)=>str_starts_with($r['tanggal']??'', $bln))) ?></b>Kunjungan bulan ini</div>
    <div class="card"><b><?= count($jd) ?></b>Jadwal dokter</div>
  </div>
<?php else: [$judul,$fields] = $forms[$p]; $rows = $p==='pasien' ? $pasien : load($p,$priv); ?>
  <h1><?= e($judul) ?></h1>
  <?php if (!public_key()): ?><p class="err">Kunci admin belum dibuat, data tidak bisa disimpan.</p><?php endif ?>
  <form method="post" class="card form">
    <input type="hidden" name="csrf" value="<?= csrf() ?>">
    <?php foreach ($fields as $f=>$label): ?>
      <label><?= e($label) ?>
      <?php if ($f==='pasien_id'): ?><select name="pasien_id" required><option value="">Pilih pasien</option>
        <?php foreach ($nama as $id=>$n): ?><option value="<?= (int)$id ?>"><?= e($n) ?></option><?php endforeach ?></select>
      <?php elseif ($f==='tgl_lahir'||$f==='tanggal'): ?><input type="date" name="<?= $f ?>" required>
      <?php elseif (in_array($f,['alamat','keluhan','diagnosa','tindakan','obat'])): ?><textarea name="<?= $f ?>" rows="2"></textarea>
      <?php else: ?><input name="<?= $f ?>" required><?php endif ?></label>
    <?php endforeach ?>
    <button class="btn" type="submit">Simpan data</button>
  </form>
  <div class="tablewrap"><table><thead><tr><?php foreach ($fields as $l): ?><th><?= e($l) ?></th><?php endforeach ?><th></th></tr></thead><tbody>
  <?php foreach ($rows as $r): ?><tr>
    <?php if (isset($r['_gagal'])): ?><td colspan="<?= count($fields) ?>" class="err">Data tidak dapat didekripsi dengan key ini.</td><?php else:
      foreach ($fields as $f=>$_): ?><td><?= e($f==='pasien_id' ? ($nama[$r[$f]] ?? '(dihapus)') : ($r[$f] ?? '')) ?></td><?php endforeach; endif ?>
    <td><form method="post" onsubmit="return confirm('Hapus data ini?')"><input type="hidden" name="csrf" value="<?= csrf() ?>"><button class="link" name="hapus" value="<?= (int)$r['id'] ?>">Hapus</button></form></td>
  </tr><?php endforeach ?>
  <?php if (!$rows): ?><tr><td colspan="<?= count($fields)+1 ?>" class="muted">Belum ada data. Isi formulir di atas untuk menambahkan.</td></tr><?php endif ?>
  </tbody></table></div>
<?php endif ?>
</main></body></html>
