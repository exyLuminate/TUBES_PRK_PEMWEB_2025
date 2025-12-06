<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    echo "UNAUTHORIZED";
    exit;
}

$claim_id = $_POST['claim_id'];
$uid = $_SESSION['user_id'];

$claim = $conn->query("
    SELECT * FROM claims
    WHERE id='$claim_id' AND mahasiswa_id='$uid' AND status='pending'
")->fetch_assoc();

if (!$claim) {
    echo "INVALID";
    exit;
}

$conn->query("
    UPDATE food_stocks
    SET stok_tersedia = stok_tersedia + 1
    WHERE id='{$claim['food_id']}'
");

$conn->query("
    UPDATE claims
    SET status='batal', alasan_batal='Dibatalkan mahasiswa'
    WHERE id='$claim_id'
");

echo "OK";
?>
