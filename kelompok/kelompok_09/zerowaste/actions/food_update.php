<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../donatur/manage_food.php?status=error&msg=Invalid request method");
    exit();
}

$food_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$stok_tersedia = isset($_POST['stok_tersedia']) ? (int)$_POST['stok_tersedia'] : -1;
$donatur_id = (int)$_SESSION['user_id'];

if ($food_id <= 0) {
    header("Location: ../donatur/manage_food.php?status=error&msg=ID donasi tidak valid");
    exit();
}

if ($stok_tersedia < 0) {
    header("Location: ../donatur/manage_food.php?status=error&msg=Stok tidak boleh negatif");
    exit();
}

$check_sql = "SELECT id, jumlah_awal, stok_tersedia, status 
              FROM food_stocks 
              WHERE id = ? AND donatur_id = ? AND deleted_at IS NULL";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param('ii', $food_id, $donatur_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../donatur/manage_food.php?status=error&msg=Donasi tidak ditemukan atau bukan milik Anda");
    exit();
}

$food_data = $result->fetch_assoc();
$jumlah_awal = (int)$food_data['jumlah_awal'];

if ($stok_tersedia > $jumlah_awal) {
    header("Location: ../donatur/manage_food.php?status=error&msg=Stok tersedia tidak boleh lebih dari jumlah awal ($jumlah_awal)");
    exit();
}

$new_status = ($stok_tersedia > 0) ? 'tersedia' : 'habis';

$update_sql = "UPDATE food_stocks 
               SET stok_tersedia = ?, 
                   status = ?,
                   updated_at = NOW()
               WHERE id = ? AND donatur_id = ?";

$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param('isii', $stok_tersedia, $new_status, $food_id, $donatur_id);

if ($update_stmt->execute()) {

    $old_stock = (int)$food_data['stok_tersedia'];
    $old_status = $food_data['status'];
    
    header("Location: ../donatur/manage_food.php?status=updated");
    exit();
} else {

    header("Location: ../donatur/manage_food.php?status=error&msg=" . urlencode($conn->error));
    exit();
}