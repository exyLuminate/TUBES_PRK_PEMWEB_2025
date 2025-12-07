<?php
session_start();

// 1. Pastikan user adalah donatur yang login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

$kode = trim($_POST['kode_tiket'] ?? '');
$donatur_id = $_SESSION['user_id'];

// 2. Validasi input
if ($kode === '') {
    header("Location: ../donatur/verify_claim.php?status=error");
    exit();
}

// 3. Cek klaim berdasarkan kode tiket & kepemilikan donatur
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
    // Kode tidak ditemukan atau bukan milik donatur ini
    header("Location: ../donatur/verify_claim.php?status=notfound");
    exit();
}

// 4. Cek status klaim
if ($claim['status'] === 'diambil') {
    // Sudah pernah diverifikasi
    header("Location: ../donatur/verify_claim.php?status=invalid");
    exit();
}

if ($claim['status'] === 'batal' || $claim['status'] === 'expired') {
    // Klaim sudah tidak berlaku
    header("Location: ../donatur/verify_claim.php?status=invalid");
    exit();
}

// 5. Update status klaim saja (stok sudah dikurangi saat mahasiswa klaim)
$now = date('Y-m-d H:i:s');
$update = $conn->prepare("UPDATE claims SET status = 'diambil', verified_at = ? WHERE id = ?");
$update->bind_param("si", $now, $claim['id']);

if ($update->execute()) {
    header("Location: ../donatur/verify_claim.php?status=success");
} else {
    header("Location: ../donatur/verify_claim.php?status=error");
}
exit();
