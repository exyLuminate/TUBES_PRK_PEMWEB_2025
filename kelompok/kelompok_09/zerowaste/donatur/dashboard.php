<?php
session_start();

if ($_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

require_once '../actions/auto_expire_foods.php';

$donatur_id = $_SESSION['user_id'];

runAutoExpire($conn, $donatur_id);

$sql = "SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'tersedia' THEN 1 ELSE 0 END) AS available,
            SUM(CASE WHEN status = 'habis' THEN 1 ELSE 0 END) AS finished
        FROM food_stocks
        WHERE donatur_id = ? AND deleted_at IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $donatur_id);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();

$total_food     = $summary['total'] ?? 0;
$total_available= $summary['available'] ?? 0;
$total_finished = $summary['finished'] ?? 0;

$pending_sql = "SELECT COUNT(*) AS pending_count
                FROM claims c
                INNER JOIN food_stocks fs ON c.food_id = fs.id
                WHERE fs.donatur_id = ? 
                AND c.status = 'pending'
                AND fs.deleted_at IS NULL";
$pending_stmt = $conn->prepare($pending_sql);
$pending_stmt->bind_param('i', $donatur_id);
$pending_stmt->execute();
$pending_result = $pending_stmt->get_result()->fetch_assoc();
$pending_claims = $pending_result['pending_count'] ?? 0;

$list_sql = "SELECT judul, status, batas_waktu, created_at
             FROM food_stocks
             WHERE donatur_id = ? AND deleted_at IS NULL
             ORDER BY created_at DESC
             LIMIT 5";
$list_stmt = $conn->prepare($list_sql);
$list_stmt->bind_param('i', $donatur_id);
$list_stmt->execute();
$list_result = $list_stmt->get_result();

function formatTanggalIndonesia($date) {
    if (empty($date)) return '-';
    
    $bulan = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    
    $timestamp = strtotime($date);
    $tanggal = date('d', $timestamp);
    $bulan_num = date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    $jam = date('H:i', $timestamp);
    
    return $tanggal . ' ' . $bulan[$bulan_num] . ' ' . $tahun . ', ' . $jam;
}

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="flex pt-16 bg-gray-50 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex flex-col w-full md:ml-64 transition-all">
        <main class="flex-grow p-6">
            <h1 class="text-2xl font-bold mb-6">Dashboard Donatur</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            
                <div class="bg-white p-4 rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Total Donasi</p>
                            <p class="text-2xl font-bold text-gray-800"><?= $total_food; ?></p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-box text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

               
                <div class="bg-white p-4 rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Masih Tersedia</p>
                            <p class="text-2xl font-bold text-green-600"><?= $total_available; ?></p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Tiket Menunggu</p>
                            <p class="text-2xl font-bold text-orange-600"><?= $pending_claims; ?></p>
                        </div>
                        <div class="bg-orange-100 p-3 rounded-full">
                            <i class="fas fa-clock text-orange-600 text-xl"></i>
                        </div>
                    </div>
                    <?php if ($pending_claims > 0): ?>
                        <a href="verify_claim.php" class="text-xs text-orange-600 hover:underline mt-2 inline-block">
                            <i class="fas fa-arrow-right mr-1"></i>Verifikasi Sekarang
                        </a>
                    <?php endif; ?>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Sudah Habis</p>
                            <p class="text-2xl font-bold text-gray-600"><?= $total_finished; ?></p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-full">
                            <i class="fas fa-archive text-gray-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">
                        <i class="fas fa-list-ul mr-2 text-gray-600"></i>
                        Donasi Terbaru
                    </h2>
                    <a href="manage_food.php" class="text-sm text-blue-600 hover:underline flex items-center">
                        Lihat Semua
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">
                                    <i class="fas fa-utensils mr-2"></i>Judul
                                </th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">
                                    <i class="fas fa-calendar-times mr-2"></i>Kadaluarsa
                                </th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">
                                    <i class="fas fa-info-circle mr-2"></i>Status
                                </th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">
                                    <i class="fas fa-clock mr-2"></i>Tanggal Input
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($list_result->num_rows > 0): ?>
                            <?php while ($row = $list_result->fetch_assoc()): ?>
                                <tr class="border-b hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 font-medium">
                                        <?= htmlspecialchars($row['judul']); ?>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600">
                                        <?= formatTanggalIndonesia($row['batas_waktu']); ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center
                                            <?= $row['status'] === 'tersedia' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'; ?>">
                                            <i class="fas fa-circle text-xs mr-1"></i>
                                            <?= htmlspecialchars(ucfirst($row['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600">
                                        <?= formatTanggalIndonesia($row['created_at']); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 block text-gray-300"></i>
                                    <p>Belum ada donasi yang tersedia.</p>
                                    <a href="add_food.php" class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                                        <i class="fas fa-plus-circle mr-1"></i>Tambah Donasi Baru
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <?php include '../includes/footer_simple.php'; ?>
    </div>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
    });
}
</script>