<?php
session_start();
require '../config/database.php';
require '../config/functions.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$food_id = $_POST['food_id'] ?? 0;
$uid = $_SESSION['user_id'];
$today = date("Y-m-d");


$stmt_limit = $conn->prepare("SELECT COUNT(*) AS total FROM claims WHERE mahasiswa_id = ? AND DATE(created_at) = ? AND status IN ('pending', 'diambil')");
$stmt_limit->bind_param("is", $uid, $today);
$stmt_limit->execute();
$cek_limit = $stmt_limit->get_result()->fetch_assoc()['total'];

if ($cek_limit >= 2) {
    echo "<script>alert('Limit harian habis! (Maks 2)'); window.history.back();</script>";
    exit();
}


$stmt_stok = $conn->prepare("SELECT stok_tersedia FROM food_stocks WHERE id = ? AND deleted_at IS NULL");
$stmt_stok->bind_param("i", $food_id);
$stmt_stok->execute();
$data_stok = $stmt_stok->get_result()->fetch_assoc();

if (!$data_stok || $data_stok['stok_tersedia'] <= 0) {
    echo "<script>alert('Yah, stok baru saja habis!'); window.location='../catalog.php';</script>";
    exit();
}


$update_stok = $conn->prepare("UPDATE food_stocks SET stok_tersedia = stok_tersedia - 1 WHERE id = ? AND stok_tersedia > 0");
$update_stok->bind_param("i", $food_id);
$update_stok->execute();


if ($update_stok->affected_rows > 0) {
   
    $kode = generateTicketCode(); 
    
    $insert = $conn->prepare("INSERT INTO claims (food_id, mahasiswa_id, kode_tiket, status) VALUES (?, ?, ?, 'pending')");
    $insert->bind_param("iis", $food_id, $uid, $kode);
    $insert->execute();


    header("Location: ../mahasiswa/my_tickets.php?msg=success");
} else {
   
    echo "<script>alert('Gagal klaim, stok tidak cukup.'); window.location='../catalog.php';</script>";
}
exit();
?>