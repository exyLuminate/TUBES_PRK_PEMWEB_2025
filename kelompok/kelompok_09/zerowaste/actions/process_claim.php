<?php
session_start();
require '../config/database.php';
require '../config/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    echo "UNAUTHORIZED";
    exit;
}

$food_id = $_POST['food_id'];
$uid = $_SESSION['user_id'];
$today = date("Y-m-d");

$cek_limit = $conn->query("
    SELECT COUNT(*) AS total 
    FROM claims 
    WHERE mahasiswa_id='$uid' AND DATE(created_at)='$today'
")->fetch_assoc()['total'];

if ($cek_limit >= 2) {
    echo "LIMIT";
    exit;
}

$cek_stok = $conn->query("
    SELECT stok_tersedia 
    FROM food_stocks 
    WHERE id='$food_id' AND deleted_at IS NULL
")->fetch_assoc();

if (!$cek_stok || $cek_stok['stok_tersedia'] <= 0) {
    echo "HABIS";
    exit;
}

$conn->query("
    UPDATE food_stocks
    SET stok_tersedia = stok_tersedia - 1
    WHERE id='$food_id' AND stok_tersedia > 0
");

$kode = generateTicketCode();

$conn->query("
    INSERT INTO claims(food_id, mahasiswa_id, kode_tiket, status)
    VALUES('$food_id', '$uid', '$kode', 'pending')
");

echo "OK";
?>
