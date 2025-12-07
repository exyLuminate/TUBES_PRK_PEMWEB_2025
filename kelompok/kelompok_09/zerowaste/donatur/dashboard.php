<?php
session_start();


if ($_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

$donatur_id = $_SESSION['user_id'];

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

$list_sql = "SELECT judul, status, batas_waktu, created_at
             FROM food_stocks
             WHERE donatur_id = ? AND deleted_at IS NULL
             ORDER BY created_at DESC
             LIMIT 5";
$list_stmt = $conn->prepare($list_sql);
$list_stmt->bind_param('i', $donatur_id);
$list_stmt->execute();
$list_result = $list_stmt->get_result();

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<div class="flex pt-16 bg-gray-50 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex flex-col w-full md:ml-64 transition-all">
        <main class="flex-grow p-6">
            <h1 class="text-2xl font-bold mb-6">Dashboard Donatur</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow-sm border">
                    <p class="text-gray-500 text-sm">Total Donasi</p>
                    <p class="text-2xl font-bold"><?= $total_food; ?></p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border">
                    <p class="text-gray-500 text-sm">Masih Tersedia</p>
                    <p class="text-2xl font-bold text-green-600"><?= $total_available; ?></p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border">
                    <p class="text-gray-500 text-sm">Sudah Habis</p>
                    <p class="text-2xl font-bold text-blue-600"><?= $total_finished; ?></p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Donasi Terbaru</h2>
                    <a href="manage_food.php" class="text-sm text-blue-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left py-2 px-2">Judul</th>
                                <th class="text-left py-2 px-2">Kadaluarsa</th>
                                <th class="text-left py-2 px-2">Status</th>
                                <th class="text-left py-2 px-2">Tanggal Input</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($list_result->num_rows > 0): ?>
                            <?php while ($row = $list_result->fetch_assoc()): ?>
                                <tr class="border-b">
                                    <td class="py-2 px-2"><?= htmlspecialchars($row['judul']); ?></td>
                                    <td class="py-2 px-2"><?= htmlspecialchars($row['batas_waktu']); ?></td>
                                    <td class="py-2 px-2">
                                        <span class="px-2 py-1 rounded text-xs
                                            <?= $row['status'] === 'tersedia' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'; ?>">
                                            <?= htmlspecialchars(ucfirst($row['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="py-2 px-2"><?= htmlspecialchars($row['created_at']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">
                                    Belum ada donasi.
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
