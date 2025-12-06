<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

$kode = $_POST['kode_tiket'] ?? '';
$donatur = $_SESSION['user_id'];

if ($kode === '') {
    header("Location: ../donatur/verify_claim.php?status=error");
    exit();
}

$sql = "
    SELECT c.id AS claim_id, c.food_id, f.stok_tersedia
    FROM claims c
    JOIN food_stocks f ON c.food_id = f.id
    WHERE c.kode_tiket = ? AND c.status = 'pending' AND f.donatur_id = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $kode, $donatur);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: ../donatur/verify_claim.php?status=notfound");
    exit();
}

$d = $res->fetch_assoc();

if ($d['stok_tersedia'] <= 0) {
    header("Location: ../donatur/verify_claim.php?status=stock");
    exit();
}

$conn->begin_transaction();

$u1 = $conn->prepare("UPDATE claims SET status='diambil', verified_at=NOW() WHERE id=?");
$u1->bind_param('i', $d['claim_id']);
$u1->execute();

$u2 = $conn->prepare("
    UPDATE food_stocks 
    SET stok_tersedia = stok_tersedia - 1,
        status = CASE WHEN stok_tersedia - 1 <= 0 THEN 'habis' ELSE status END
    WHERE id=?
");
$u2->bind_param('i', $d['food_id']);
$u2->execute();

$conn->commit();

header("Location: ../donatur/verify_claim.php?status=success");
exit();
