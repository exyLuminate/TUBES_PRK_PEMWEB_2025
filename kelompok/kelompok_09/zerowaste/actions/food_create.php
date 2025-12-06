<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

$donatur_id    = $_SESSION['user_id'];
$judul         = $_POST['judul'] ?? '';
$category_id   = $_POST['category_id'] ?? '';
$deskripsi     = $_POST['deskripsi'] ?? '';
$jumlah_awal   = (int)($_POST['jumlah_awal'] ?? 0);
$lokasi        = $_POST['lokasi_pickup'] ?? '';
$batas_waktu   = $_POST['batas_waktu'] ?? '';
$jenis         = $_POST['jenis_makanan'] ?? 'halal';

if ($judul === '' || $category_id === '' || $jumlah_awal <= 0 || $lokasi === '' || $batas_waktu === '') {
    header("Location: ../donatur/add_food.php?status=error");
    exit();
}

$foto_path = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $tmp  = $_FILES['foto']['tmp_name'];
    $name = basename($_FILES['foto']['name']);
    $ext  = pathinfo($name, PATHINFO_EXTENSION);
    $newName = 'food_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;

    $uploadDir  = '../images/foods/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $dest = $uploadDir . $newName;

    if (move_uploaded_file($tmp, $dest)) {
        $foto_path = 'images/foods/' . $newName;
    }
}

if ($foto_path === null) {
    header("Location: ../donatur/add_food.php?status=error");
    exit();
}

$sql = "
    INSERT INTO food_stocks
    (donatur_id, category_id, judul, deskripsi, foto_path,
     jumlah_awal, stok_tersedia, lokasi_pickup, batas_waktu, jenis_makanan, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'tersedia')
";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'iisssiisss',
    $donatur_id,
    $category_id,
    $judul,
    $deskripsi,
    $foto_path,
    $jumlah_awal,
    $jumlah_awal,
    $lokasi,
    $batas_waktu,
    $jenis
);
$stmt->execute();

header("Location: ../donatur/manage_food.php?status=created");
exit();
