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

</body>
</html>