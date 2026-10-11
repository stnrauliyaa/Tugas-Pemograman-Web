<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/src/GuestBook.php';

// ---------- Konfigurasi koneksi (sesuaikan dengan lingkungan Anda) ----------
const DB_HOST = 'localhost';
const DB_NAME = 'perpustakaan';
const DB_USER = 'root';
const DB_PASS = '';

// ---------- Helper sanitasi keluaran (pencegahan XSS) ----------
function e(string $teks): string
{
    return htmlspecialchars($teks, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ---------- Koneksi PDO ----------
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // gunakan prepared statement asli
        ]
    );
} catch (PDOException $ex) {
    error_log($ex->getMessage());
    http_response_code(500);
    exit('Tidak dapat terhubung ke basis data.');
}

$guestBook = new GuestBook($pdo);

// ---------- Token CSRF ----------
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$galat  = [];
$lama   = ['nama' => '', 'email' => '', 'pesan' => ''];
$sukses = $_SESSION['flash_sukses'] ?? null;
unset($_SESSION['flash_sukses']);


// ---------- Proses form ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Verifikasi CSRF (hash_equals menghindari timing attack)
    $tokenKiriman = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], (string) $tokenKiriman)) {
        http_response_code(403);
        exit('Permintaan ditolak: token CSRF tidak valid.');
    }

    // 2) Ambil & rapikan masukan
    $lama = [
        'nama'  => trim((string) ($_POST['nama']  ?? '')),
        'email' => trim((string) ($_POST['email'] ?? '')),
        'pesan' => trim((string) ($_POST['pesan'] ?? '')),
    ];

    // 3) Validasi
    $galat = $guestBook->validasi($lama['nama'], $lama['email'], $lama['pesan']);

    // 4) Simpan jika valid, lalu redirect (Post/Redirect/Get)
    if (empty($galat)) {
        try {
            $guestBook->simpan($lama['nama'], $lama['email'], $lama['pesan']);
            $_SESSION['csrf_token']   = bin2hex(random_bytes(32)); // token baru
            $_SESSION['flash_sukses'] = 'Terima kasih, pesan Anda berhasil dikirim.';
            header('Location: guestbook.php');
            exit;
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            $galat['umum'] = 'Terjadi kesalahan saat menyimpan pesan.';
        }
    }
}


// ---------- Ambil data untuk ditampilkan ----------
try {
    $daftarPesan = $guestBook->ambilSemua();
} catch (PDOException $ex) {
    error_log($ex->getMessage());
    $daftarPesan   = [];
    $galat['umum'] = 'Gagal memuat daftar pesan.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buku Tamu Perpustakaan</title>
        <style>
        body { font-family: system-ui, sans-serif; max-width: 800px; margin: 2rem auto; padding: 0 1rem; color: #222; }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input, textarea { width: 100%; padding: .5rem; box-sizing: border-box; }
        button { margin-top: 1rem; padding: .6rem 1.2rem; cursor: pointer; }
        .galat { color: #b00020; font-size: .9rem; }
        .sukses { background: #e6f4ea; padding: .7rem; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ccc; padding: .5rem; text-align: left; vertical-align: top; }
        th { background: #f3f3f3; }
    </style>
</head>
<body>
    <h1>Buku Tamu Perpustakaan</h1>

        <?php if (isset($galat['umum'])): ?>
        <p class="galat"><?= e($galat['umum']) ?></p>
    <?php endif; ?>

    <?php if ($sukses): ?>
        <p class="sukses"><?= e($sukses) ?></p>
    <?php endif; ?>

    <form method="post" action="guestbook.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" value="<?= e($lama['nama']) ?>">
        <?php if (isset($galat['nama'])): ?><span class="galat"><?= e($galat['nama']) ?></span><?php endif; ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($lama['email']) ?>">
        <?php if (isset($galat['email'])): ?><span class="galat"><?= e($galat['email']) ?></span><?php endif; ?>

        <label for="pesan">Pesan</label>
        <textarea id="pesan" name="pesan" rows="4"><?= e($lama['pesan']) ?></textarea>
        <?php if (isset($galat['pesan'])): ?><span class="galat"><?= e($galat['pesan']) ?></span><?php endif; ?>

        <button type="submit">Kirim Pesan</button>
    </form>

    <h2>Daftar Pesan</h2>
    
// ---------- Ambil data untuk ditampilkan ----------
try {
    $daftarPesan = $guestBook->ambilSemua();
} catch (PDOException $ex) {
    error_log($ex->getMessage());
    $daftarPesan   = [];
    $galat['umum'] = 'Gagal memuat daftar pesan.';
}
</body>
</html>