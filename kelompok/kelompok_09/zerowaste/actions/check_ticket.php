<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit();
}

$kode_tiket = isset($_POST['kode_tiket']) ? trim(strtoupper($_POST['kode_tiket'])) : '';
$donatur_id = (int)$_SESSION['user_id'];

if (empty($kode_tiket)) {
    echo json_encode([
        'success' => false,
        'message' => 'Kode tiket tidak boleh kosong'
    ]);
    exit();
}

$sql = "SELECT 
            c.id,
            c.kode_tiket,
            c.status,
            c.created_at,
            u.nama_lengkap as nama_mahasiswa,
            fs.judul as nama_makanan,
            fs.donatur_id
        FROM claims c
        INNER JOIN users u ON c.mahasiswa_id = u.id
        INNER JOIN food_stocks fs ON c.food_id = fs.id
        WHERE c.kode_tiket = ? 
        AND fs.donatur_id = ?
        AND fs.deleted_at IS NULL
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $kode_tiket, $donatur_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Kode tiket tidak ditemukan atau bukan milik donasi Anda.'
    ]);
    exit();
}

$ticket = $result->fetch_assoc();

if ($ticket['status'] === 'diambil') {
    echo json_encode([
        'success' => true,
        'ticket' => [
            'id' => $ticket['id'],
            'kode_tiket' => $ticket['kode_tiket'],
            'nama_mahasiswa' => $ticket['nama_mahasiswa'],
            'nama_makanan' => $ticket['nama_makanan'],
            'jumlah_diminta' => 1,
            'status' => $ticket['status']
        ],
        'message' => '⚠️ TIKET SUDAH TERPAKAI! Tiket ini sudah pernah diverifikasi sebelumnya.'
    ]);
    exit();
}

if ($ticket['status'] === 'ditolak') {
    echo json_encode([
        'success' => false,
        'message' => 'Tiket ini sudah ditolak dan tidak dapat digunakan.'
    ]);
    exit();
}

if ($ticket['status'] === 'batal') {
    echo json_encode([
        'success' => false,
        'message' => 'Tiket ini telah dibatalkan oleh mahasiswa.'
    ]);
    exit();
}

if ($ticket['status'] === 'expired') {
    echo json_encode([
        'success' => false,
        'message' => 'Tiket ini sudah kadaluarsa/expired.'
    ]);
    exit();
}

if ($ticket['status'] === 'pending') {
    echo json_encode([
        'success' => true,
        'ticket' => [
            'id' => $ticket['id'],
            'kode_tiket' => $ticket['kode_tiket'],
            'nama_mahasiswa' => $ticket['nama_mahasiswa'],
            'nama_makanan' => $ticket['nama_makanan'],
            'jumlah_diminta' => 1,
            'status' => $ticket['status']
        ]
    ]);
    exit();
}

echo json_encode([
    'success' => false,
    'message' => 'Status tiket tidak valid: ' . $ticket['status']
]);

$conn->close();
?>