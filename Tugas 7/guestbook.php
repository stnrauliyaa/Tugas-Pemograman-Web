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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buku Tamu Perpustakaan</title>
</head>
<body>
    <h1>Buku Tamu Perpustakaan</h1>
</body>
</html>