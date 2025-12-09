<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Invalid request method"));
    exit();
}

$donatur_id  = (int)$_SESSION['user_id'];
$judul       = trim($_POST['judul'] ?? '');
$category_id = (int)($_POST['category_id'] ?? 0);
$deskripsi   = trim($_POST['deskripsi'] ?? '');
$jumlah_awal = (int)($_POST['jumlah_awal'] ?? 0);
$lokasi      = trim($_POST['lokasi_pickup'] ?? '');
$raw_batas   = trim($_POST['batas_waktu'] ?? '');
$jenis       = trim($_POST['jenis_makanan'] ?? 'halal');

$_SESSION['old_judul']         = $judul;
$_SESSION['old_category_id']   = $category_id;
$_SESSION['old_deskripsi']     = $deskripsi;
$_SESSION['old_jumlah_awal']   = $jumlah_awal;
$_SESSION['old_lokasi_pickup'] = $lokasi;
$_SESSION['old_batas_waktu']   = $raw_batas;
$_SESSION['old_jenis_makanan'] = $jenis;

$batas_waktu = '';
if ($raw_batas !== '') {
    
    $batas_waktu = str_replace('T', ' ', $raw_batas) . ':00';
}

if ($judul === '') {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Judul makanan wajib diisi"));
    exit();
}

if ($category_id <= 0) {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Kategori wajib dipilih"));
    exit();
}

if ($jumlah_awal <= 0) {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Jumlah porsi harus lebih dari 0"));
    exit();
}

if ($lokasi === '') {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Lokasi pickup wajib diisi"));
    exit();
}

if ($batas_waktu === '' || $batas_waktu === ':00') {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Batas waktu pengambilan wajib diisi"));
    exit();
}

$allowed_jenis = ['halal', 'non_halal'];
if (!in_array($jenis, $allowed_jenis)) {
    $jenis = 'halal';
}

$batas_timestamp = strtotime($batas_waktu);
if ($batas_timestamp === false || $batas_timestamp < time()) {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Batas waktu tidak boleh di masa lalu"));
    exit();
}

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Foto makanan wajib diupload"));
    exit();
}

$tmp_name  = $_FILES['foto']['tmp_name'];
$file_name = basename($_FILES['foto']['name']);
$file_size = $_FILES['foto']['size'];
$file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

$allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
if (!in_array($file_ext, $allowed_ext)) {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Format file tidak valid. Gunakan JPG, JPEG, PNG, atau WEBP"));
    exit();
}

$max_size = 5 * 1024 * 1024; 
if ($file_size > $max_size) {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Ukuran file terlalu besar. Maksimal 5MB"));
    exit();
}

$allowed_mime = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $tmp_name);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_mime)) {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("File bukan gambar yang valid"));
    exit();
}

$foto_path = null;


$new_file_name = 'food_' . time() . '_' . mt_rand(1000, 9999) . '.' . $file_ext;


$upload_dir = '../uploads/food_images/';


if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Gagal membuat direktori upload"));
        exit();
    }
}

$upload_path = $upload_dir . $new_file_name;

if (move_uploaded_file($tmp_name, $upload_path)) {
    $foto_path = $new_file_name;
} else {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Gagal mengupload foto"));
    exit();
}

if ($foto_path === null) {
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Foto gagal diproses"));
    exit();
}

$sql = "
    INSERT INTO food_stocks 
    (donatur_id, category_id, judul, deskripsi, foto_path, 
     jumlah_awal, stok_tersedia, lokasi_pickup, batas_waktu, 
     jenis_makanan, status, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'tersedia', NOW())
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
   
    if (file_exists($upload_path)) {
        unlink($upload_path);
    }
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode("Database error: Prepare failed"));
    exit();
}


$stmt->bind_param(
    "iisssiisss",
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

if ($stmt->execute()) {
    
    unset(
        $_SESSION['old_judul'],
        $_SESSION['old_category_id'],
        $_SESSION['old_deskripsi'],
        $_SESSION['old_jumlah_awal'],
        $_SESSION['old_lokasi_pickup'],
        $_SESSION['old_batas_waktu'],
        $_SESSION['old_jenis_makanan']
    );
    
    $stmt->close();
    $conn->close();
    
    header("Location: ../donatur/manage_food.php?status=created");
    exit();
    
} else {
    if (file_exists($upload_path)) {
        unlink($upload_path);
    }
    
    $error_msg = "Database error: " . $stmt->error;
    $stmt->close();
    $conn->close();
    
    header("Location: ../donatur/add_food.php?status=error&msg=" . urlencode($error_msg));
    exit();
}
?>