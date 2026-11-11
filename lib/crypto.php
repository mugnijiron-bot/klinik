<?php
// Enkripsi hybrid: data -> AES-256-GCM (kunci acak per record); kunci AES -> RSA-OAEP (public key).
// Hanya pemegang PRIVATE KEY yang bisa membuka data.
function public_key() {
    $st = db()->prepare("SELECT v FROM settings WHERE k='public_key'");
    $st->execute();
    $pem = $st->fetchColumn();
    return $pem ? openssl_pkey_get_public($pem) : false;
}
function enc(array $data): string {
    $pub = public_key();
    if (!$pub) throw new RuntimeException('Kunci admin belum dibuat. Buka setup.php terlebih dahulu.');
    $k = random_bytes(32); $iv = random_bytes(12); $tag = '';
    $c = openssl_encrypt(json_encode($data, JSON_UNESCAPED_UNICODE), 'aes-256-gcm', $k, OPENSSL_RAW_DATA, $iv, $tag, '', 16);
    openssl_public_encrypt($k, $ek, $pub, OPENSSL_PKCS1_OAEP_PADDING);
    return implode('.', array_map('base64_encode', [$ek, $iv, $tag, $c]));
}
function dec(string $s, $priv): ?array {
    $p = explode('.', $s);
    if (count($p) !== 4) return null;
    [$ek, $iv, $tag, $c] = array_map('base64_decode', $p);
    if (!@openssl_private_decrypt($ek, $k, $priv, OPENSSL_PKCS1_OAEP_PADDING)) return null;
    $out = openssl_decrypt($c, 'aes-256-gcm', $k, OPENSSL_RAW_DATA, $iv, $tag);
    return $out === false ? null : json_decode($out, true);
}
// Pastikan private key cocok dengan public key di database
function verify_private_key(string $pem) {
    $priv = @openssl_pkey_get_private($pem);
    $pub = public_key();
    if (!$priv || !$pub) return false;
    $t = random_bytes(16);
    openssl_public_encrypt($t, $x, $pub, OPENSSL_PKCS1_OAEP_PADDING);
    return @openssl_private_decrypt($x, $y, $priv, OPENSSL_PKCS1_OAEP_PADDING) && hash_equals($t, $y) ? $priv : false;
}

// Buat pasangan kunci RSA 3072-bit. Memakai openssl.cnf bawaan proyek
// supaya tetap jalan di Windows/Laragon yang sering tidak menemukan file konfigurasinya.
function generate_keypair(): array {
    $opt = ['private_key_bits' => 3072, 'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'config' => __DIR__ . DIRECTORY_SEPARATOR . 'openssl.cnf'];
    $res = openssl_pkey_new($opt);
    if (!$res) throw new RuntimeException('Gagal membuat kunci: ' . openssl_error_string());
    if (!openssl_pkey_export($res, $private, null, $opt)) throw new RuntimeException('Gagal mengekspor kunci: ' . openssl_error_string());
    return [$private, openssl_pkey_get_details($res)['key']];
}
