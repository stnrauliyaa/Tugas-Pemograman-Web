<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/Transaction.php';

// --- Inisialisasi state sesi ---------------------------------------------

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 0.0;
}

if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

$errors = [];
$successMessage = null;

// --- Proses form (POST) ---------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!is_string($submittedToken) || !hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        $errors[] = 'Token keamanan (CSRF) tidak valid atau sudah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.';
    } else {
        $typeInput = (string) ($_POST['type'] ?? '');
        $amountInput = trim((string) ($_POST['amount'] ?? ''));

        // Cocokkan jenis transaksi memakai ekspresi match (PHP 8)
        $type = match ($typeInput) {
            'deposit' => 'deposit',
            'withdraw' => 'withdraw',
            default => null,
        };

        if ($type === null) {
            $errors[] = 'Jenis transaksi tidak valid. Pilih deposit atau penarikan.';
        }

        // Validasi jumlah: harus angka desimal positif (maks. 2 digit di belakang koma)
        $amount = null;
        if ($amountInput === '' || !preg_match('/^\d+(\.\d{1,2})?$/', $amountInput)) {
            $errors[] = 'Jumlah transaksi harus berupa angka desimal positif (contoh: 50000 atau 50000.50).';
        } else {
            $amount = (float) $amountInput;
            if ($amount <= 0.0) {
                $errors[] = 'Jumlah transaksi harus lebih besar dari nol.';
            }
        }
    }

    // Regenerasi token setelah setiap submit agar tidak bisa dipakai ulang (mencegah replay)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];
$balance = (float) $_SESSION['balance'];
$history = $_SESSION['history'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistem Manajemen Keuangan Sederhana</title>
</head>
<body>

<h1>Sistem Manajemen Keuangan Sederhana</h1>
<p>Prototipe modul deposit &amp; penarikan berbasis sesi (per-browser), dengan validasi CSRF dan input.</p>

<div class="saldo">
  Saldo saat ini: <span>Rp <?= htmlspecialchars(number_format($balance, 2, ',', '.'), ENT_QUOTES, 'UTF-8') ?></span>
</div>

<form method="post" action="finance.php" novalidate>
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

  <label for="type">Jenis Transaksi</label>
  <select id="type" name="type" required>
    <option value="deposit">Deposit</option>
    <option value="withdraw">Penarikan</option>
  </select>

  <label for="amount">Jumlah (angka desimal positif)</label>
  <input type="text" id="amount" name="amount" inputmode="decimal" placeholder="contoh: 50000.00" required>

  <button type="submit">Proses Transaksi</button>
</form>

</body>
</html>