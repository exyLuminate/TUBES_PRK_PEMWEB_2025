<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
require_once '../config/functions.php';


$stats = [];
$result = mysqli_query($conn, "SELECT role, COUNT(*) as total FROM users WHERE deleted_at IS NULL GROUP BY role");
while ($row = mysqli_fetch_assoc($result)) {
    $stats[$row['role']] = $row['total'];
}


$result = mysqli_query($conn, "SELECT 
    COUNT(*) as total_food,
    SUM(CASE WHEN status = 'tersedia' THEN 1 ELSE 0 END) as available,
    SUM(CASE WHEN status = 'habis' THEN 1 ELSE 0 END) as sold_out
FROM food_stocks WHERE deleted_at IS NULL");
$food_stats = mysqli_fetch_assoc($result);


$result = mysqli_query($conn, "SELECT status, COUNT(*) as total FROM claims WHERE deleted_at IS NULL GROUP BY status");
$claim_stats = [];
while ($row = mysqli_fetch_assoc($result)) {
    $claim_stats[$row['status']] = $row['total'];
}


$recent_logs = mysqli_query($conn, "SELECT al.*, u.nama_lengkap, u.username 
    FROM activity_logs al 
    JOIN users u ON al.user_id = u.id 
    ORDER BY al.created_at DESC 
    LIMIT 10");

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<div class="flex pt-16 bg-gray-50 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex flex-col w-full md:ml-64">
        <main class="flex-grow p-6">
            
            
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
                <h1 class="text-2xl font-bold mb-2">
                    <i class="fas fa-tachometer-alt mr-2 text-green-600"></i>
                    Dashboard Admin
                </h1>
                <p class="text-gray-600">Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?></p>
            </div>

          
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
              
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Total Mahasiswa</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">
                                <?= $stats['mahasiswa'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="bg-blue-100 rounded-full p-4">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Total Donatur</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">
                                <?= $stats['donatur'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="bg-green-100 rounded-full p-4">
                            <i class="fas fa-hands-helping text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Makanan Tersedia</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">
                                <?= $food_stats['available'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-4">
                            <i class="fas fa-utensils text-yellow-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

               
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Total Klaim</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">
                                <?= array_sum($claim_stats) ?>
                            </h3>
                        </div>
                        <div class="bg-purple-100 rounded-full p-4">
                            <i class="fas fa-ticket-alt text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">
                        <i class="fas fa-chart-pie mr-2"></i>Status Klaim
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <span class="text-gray-700">
                                <i class="fas fa-clock text-yellow-500 mr-2"></i>Pending
                            </span>
                            <span class="font-bold text-yellow-600 text-lg"><?= $claim_stats['pending'] ?? 0 ?></span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <span class="text-gray-700">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>Diambil
                            </span>
                            <span class="font-bold text-green-600 text-lg"><?= $claim_stats['diambil'] ?? 0 ?></span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <span class="text-gray-700">
                                <i class="fas fa-times-circle text-red-500 mr-2"></i>Batal
                            </span>
                            <span class="font-bold text-red-600 text-lg"><?= $claim_stats['batal'] ?? 0 ?></span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-700">
                                <i class="fas fa-hourglass-end text-gray-500 mr-2"></i>Expired
                            </span>
                            <span class="font-bold text-gray-600 text-lg"><?= $claim_stats['expired'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>

                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">
                        <i class="fas fa-history mr-2"></i>Aktivitas Terbaru
                    </h3>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        <?php if (mysqli_num_rows($recent_logs) > 0): ?>
                            <?php while ($log = mysqli_fetch_assoc($recent_logs)): ?>
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-circle text-gray-400 text-2xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($log['nama_lengkap']) ?>
                                        <span class="text-gray-500 text-xs">(@<?= htmlspecialchars($log['username']) ?>)</span>
                                    </p>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($log['description']) ?></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-clock mr-1"></i><?= formatTanggal($log['created_at']) ?>
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <?= $log['action'] ?>
                                </span>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-gray-500 text-center py-4">Belum ada aktivitas</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </main>
        
        <?php include '../includes/footer_simple.php'; ?>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    if(toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }
</script>
