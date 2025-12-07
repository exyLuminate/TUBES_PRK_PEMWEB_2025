<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

$kode = trim($_POST['kode_tiket'] ?? '');
$donatur_id = $_SESSION['user_id'];


if ($kode === '') {
    header("Location: ../donatur/verify_claim.php?status=error");
    exit();
}


$sql = "
    SELECT c.id, c.status
    FROM claims c
    JOIN food_stocks f ON c.food_id = f.id
    WHERE c.kode_tiket = ? AND f.donatur_id = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $kode, $donatur_id);
$stmt->execute();
$result = $stmt->get_result();
$claim = $result->fetch_assoc();

if (!$claim) {

    header("Location: ../donatur/verify_claim.php?status=notfound");
    exit();
}


if ($claim['status'] === 'diambil') {
    
    header("Location: ../donatur/verify_claim.php?status=invalid");
    exit();
}

if ($claim['status'] === 'batal' || $claim['status'] === 'expired') {
    
    header("Location: ../donatur/verify_claim.php?status=invalid");
    exit();
}


$now = date('Y-m-d H:i:s');
$update = $conn->prepare("UPDATE claims SET status = 'diambil', verified_at = ? WHERE id = ?");
$update->bind_param("si", $now, $claim['id']);

if ($update->execute()) {
    header("Location: ../donatur/verify_claim.php?status=success");
} else {
    header("Location: ../donatur/verify_claim.php?status=error");
}
exit();
