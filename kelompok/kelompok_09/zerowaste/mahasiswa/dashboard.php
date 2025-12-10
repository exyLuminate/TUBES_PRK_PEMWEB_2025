<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
require_once '../config/functions.php'; 

$uid = $_SESSION['user_id'];
$today = date("Y-m-d");


$limit_query = "SELECT COUNT(*) AS total FROM claims WHERE mahasiswa_id=? AND DATE(created_at)=? AND status IN('pending', 'diambil')"; 
$stmt = $conn->prepare($limit_query);
$stmt->bind_param("is", $uid, $today);
$stmt->execute();
$limit = $stmt->get_result()->fetch_assoc()['total'];


$pending_query = "SELECT c.*, f.judul
                  FROM claims c
                  JOIN food_stocks f ON f.id = c.food_id
                  WHERE c.mahasiswa_id=? AND c.status='pending'
                  ORDER BY c.created_at DESC";
$stmt = $conn->prepare($pending_query);
$stmt->bind_param("i", $uid);
$stmt->execute();
$pending = $stmt->get_result();


$history_query = "SELECT c.*, f.judul
                  FROM claims c
                  JOIN food_stocks f ON f.id = c.food_id
                  WHERE c.mahasiswa_id=? AND c.status IN('diambil', 'batal', 'expired')
                  ORDER BY c.created_at DESC
                  LIMIT 5";
$stmt = $conn->prepare($history_query);
$stmt->bind_param("i", $uid);
$stmt->execute();
$history = $stmt->get_result();


include '../includes/header.php';
include '../includes/navbar_dashboard.php'; 
?>

<div class="flex pt-16 bg-gray-50 min-h-screen">
    
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex flex-col w-full md:ml-64 transition-all">
        <main class="flex-grow p-6">
            
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Mahasiswa</h1>
                    <p class="text-gray-600 text-sm mt-1">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Mahasiswa') ?> 👋</p>
                </div>
                <a href="../catalog.php" class="mt-4 md:mt-0 inline-flex items-center justify-center px-5 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-md">
                    <i class="fas fa-search mr-2"></i> Cari Makanan
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden">
                    <div class="relative z-10">
                        <h2 class="text-gray-500 font-bold text-xs uppercase tracking-wide mb-1">Limit Klaim Hari Ini</h2>
                        <div class="flex items-baseline gap-1">
                            <?php if ($limit >= 2): ?>
                                <span class="text-3xl font-extrabold text-red-600">Kuota Habis</span>
                            <?php else: ?>
                                <span class="text-3xl font-extrabold text-green-600"><?= $limit ?></span>
                                <span class="text-gray-400 font-medium">/ 2</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Reset setiap jam 00:00</p>
                    </div>
                    <div class="absolute right-0 bottom-0 p-4 opacity-10">
                        <i class="fas fa-calendar-day text-6xl text-green-600"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden">
                    <div class="relative z-10">
                        <h2 class="text-gray-500 font-bold text-xs uppercase tracking-wide mb-1">Tiket Menunggu Diambil</h2>
                        <span class="text-3xl font-extrabold text-blue-600"><?= $pending->num_rows ?></span>
                        <p class="text-xs text-gray-400 mt-2">Segera ambil sebelum kadaluarsa</p>
                    </div>
                    <div class="absolute right-0 bottom-0 p-4 opacity-10">
                        <i class="fas fa-ticket-alt text-6xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-clock text-yellow-500"></i> Tiket Aktif (Pending)
                    </h2>
                    <?php if($pending->num_rows > 0): ?>
                        <a href="my_tickets.php" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua Tiket →</a>
                    <?php endif; ?>
                </div>

                <div class="p-6">
                    <?php if ($pending->num_rows == 0): ?>
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4 text-gray-400">
                                <i class="fas fa-ticket-alt text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-600">Tidak ada tiket aktif</h3>
                            <p class="text-gray-500 text-sm mb-4">Kamu belum mengklaim makanan apapun hari ini.</p>
                            <a href="../catalog.php" class="inline-block px-4 py-2 bg-green-50 text-green-700 font-bold rounded-lg hover:bg-green-100 transition">
                                Mulai Mencari
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="grid gap-4">
                            <?php while ($t = $pending->fetch_assoc()): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-all bg-white hover:shadow-sm flex flex-col md:flex-row justify-between md:items-center gap-4">
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-800 mb-1"><?= htmlspecialchars($t['judul']) ?></h3>
                                        <div class="flex items-center gap-3 text-sm text-gray-500">
                                            <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-mono font-bold border border-blue-100">
                                                <?= htmlspecialchars($t['kode_tiket']) ?>
                                            </span>
                                            <span><i class="far fa-clock mr-1"></i> <?= formatTanggal($t['created_at']) ?></span>
                                        </div>
                                        <p class="text-sm text-orange-600 font-semibold mt-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Ambil Sebelum Pukul: <?= date('H:i', strtotime($t['created_at']) + 3600) ?>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="my_tickets.php" class="w-full md:w-auto text-center px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-gray-200 transition text-sm">
                                            Detail & Pembatalan
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Riwayat Terakhir Section -->
            <?php if ($history->num_rows > 0): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-history text-purple-500"></i> Riwayat Terakhir
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <?php while ($h = $history->fetch_assoc()): ?>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($h['judul']) ?></p>
                                        <p class="text-xs text-gray-500">
                                            <i class="far fa-clock mr-1"></i> <?= formatTanggal($h['created_at']) ?> •
                                            <span class="capitalize"><?= $h['status'] ?></span>
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xs font-mono text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                    <?= htmlspecialchars($h['kode_tiket']) ?>
                                </span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

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