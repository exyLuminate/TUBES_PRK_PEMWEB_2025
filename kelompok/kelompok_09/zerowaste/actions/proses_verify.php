<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../donatur/verify_claim.php?status=error&msg=Invalid request");
    exit();
}

$kode_tiket = isset($_POST['kode_tiket']) ? trim(strtoupper($_POST['kode_tiket'])) : '';
$claim_id = isset($_POST['claim_id']) ? (int)$_POST['claim_id'] : 0;
$donatur_id = (int)$_SESSION['user_id'];

if (empty($kode_tiket) || $claim_id <= 0) {
    header("Location: ../donatur/verify_claim.php?status=error&msg=Data tidak lengkap");
    exit();
}

$conn->begin_transaction();

try {
    $check_sql = "SELECT 
                    c.id,
                    c.kode_tiket,
                    c.status,
                    c.food_id,
                    fs.stok_tersedia,
                    fs.donatur_id
                FROM claims c
                INNER JOIN food_stocks fs ON c.food_id = fs.id
                WHERE c.id = ? 
                AND c.kode_tiket = ?
                AND fs.donatur_id = ?
                AND fs.deleted_at IS NULL
                FOR UPDATE"; 

    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('isi', $claim_id, $kode_tiket, $donatur_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Tiket tidak ditemukan atau bukan milik donasi Anda');
    }

    $claim = $result->fetch_assoc();

    if ($claim['status'] !== 'pending') {
        $status_msg = $claim['status'] === 'diambil' ? 'sudah terpakai' : 'tidak valid';
        throw new Exception("Tiket ini $status_msg");
    }

    if ($claim['stok_tersedia'] < 1) {
        throw new Exception('Stok tidak mencukupi untuk klaim ini');
    }

    $update_claim_sql = "UPDATE claims 
                         SET status = 'diambil', 
                             verified_at = NOW(),
                             updated_at = NOW() 
                         WHERE id = ?";
    $update_claim_stmt = $conn->prepare($update_claim_sql);
    $update_claim_stmt->bind_param('i', $claim_id);
    
    if (!$update_claim_stmt->execute()) {
        throw new Exception('Gagal mengupdate status claim');
    }

    $new_stok = $claim['stok_tersedia'] - 1;
    $new_status = ($new_stok > 0) ? 'tersedia' : 'habis';

    $update_stock_sql = "UPDATE food_stocks 
                         SET stok_tersedia = ?,
                             status = ?,
                             updated_at = NOW()
                         WHERE id = ?";
    $update_stock_stmt = $conn->prepare($update_stock_sql);
    $update_stock_stmt->bind_param('isi', $new_stok, $new_status, $claim['food_id']);
    
    if (!$update_stock_stmt->execute()) {
        throw new Exception('Gagal mengupdate stok makanan');
    }

    $conn->commit();

    header("Location: ../donatur/verify_claim.php?status=success");
    exit();

} catch (Exception $e) {
   
    $conn->rollback();
    
    header("Location: ../donatur/verify_claim.php?status=error&msg=" . urlencode($e->getMessage()));
    exit();
}