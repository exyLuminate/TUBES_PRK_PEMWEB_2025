<?php
session_start();
require '../config/database.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$claim_id = $_POST['claim_id'] ?? 0;
$uid = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT id, food_id FROM claims WHERE id = ? AND mahasiswa_id = ? AND status = 'pending'");
$stmt->bind_param("ii", $claim_id, $uid);
$stmt->execute();
$claim = $stmt->get_result()->fetch_assoc();

if (!$claim) {
    
    header("Location: ../mahasiswa/my_tickets.php?msg=invalid");
    exit();
}


$update_stock = $conn->prepare("UPDATE food_stocks SET stok_tersedia = stok_tersedia + 1 WHERE id = ?");
$update_stock->bind_param("i", $claim['food_id']);
$update_stock->execute();


$update_claim = $conn->prepare("UPDATE claims SET status = 'batal', alasan_batal = 'Dibatalkan mahasiswa' WHERE id = ?");
$update_claim->bind_param("i", $claim_id);
$update_claim->execute();


header("Location: ../mahasiswa/my_tickets.php?msg=cancelled");
exit();
?>