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