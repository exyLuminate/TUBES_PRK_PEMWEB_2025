<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'donatur';
    $_SESSION['nama_lengkap'] = 'Testing Donatur';
}

if ($_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

require_once '../actions/auto_expire_foods.php';

$donatur_id = (int)$_SESSION['user_id'];

runAutoExpire($conn, $donatur_id);

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$donatur_id = (int)$_SESSION['user_id'];

$items_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $items_per_page;

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$allowed_statuses = ['all', 'tersedia', 'habis'];
if (!in_array($status_filter, $allowed_statuses)) {
    $status_filter = 'all';
}

$where_clauses = ["f.donatur_id = ? AND f.deleted_at IS NULL"];
$params = [$donatur_id];
$param_types = 'i';

if ($status_filter !== 'all') {
    $where_clauses[] = "f.status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

$where_sql = implode(' AND ', $where_clauses);

$count_sql = "
    SELECT COUNT(*) as total
    FROM food_stocks f
    WHERE $where_sql
";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param($param_types, ...$params);
$count_stmt->execute();
$total_items = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);


$sql = "
    SELECT f.id, f.judul, f.jumlah_awal, f.stok_tersedia, f.status,
           f.batas_waktu, f.created_at, f.foto_path,
           c.nama_kategori
    FROM food_stocks f
    LEFT JOIN categories c ON f.category_id = c.id
    WHERE $where_sql
    ORDER BY f.created_at DESC
    LIMIT ? OFFSET ?
";

$params[] = $items_per_page;
$params[] = $offset;
$param_types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<div class="flex pt-16 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex flex-col w-full md:ml-64 transition-all duration-300">
        <main class="flex-grow p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header Section -->
                <div class="mb-6 sm:mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Kelola Donasi</h1>
                                <p class="text-sm text-gray-500 mt-0.5">Manage dan monitor donasi makanan Anda</p>
                            </div>
                        </div>
                        <a href="add_food.php" 
                           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                                  bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-semibold shadow-lg shadow-emerald-500/30
                                  hover:from-emerald-700 hover:to-emerald-800 hover:shadow-xl hover:shadow-emerald-500/40
                                  focus:outline-none focus:ring-4 focus:ring-emerald-500/50
                                  active:scale-[0.98] transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Donasi
                        </a>
                    </div>
                </div>

                <?php if (isset($_GET['status'])): ?>
                    <?php if ($_GET['status'] === 'deleted'): ?>
                        <div class="mb-6 animate-fade-in">
                            <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-sm text-green-700 font-medium">Donasi berhasil dihapus.</p>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($_GET['status'] === 'created'): ?>
                        <div class="mb-6 animate-fade-in">
                            <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-sm text-green-700 font-medium">Donasi berhasil ditambahkan.</p>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($_GET['status'] === 'updated'): ?>
                        <div class="mb-6 animate-fade-in">
                            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 shadow-sm">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-sm text-blue-700 font-medium">Stok berhasil diperbarui.</p>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($_GET['status'] === 'error'): ?>
                        <div class="mb-6 animate-fade-in">
                            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-sm text-red-700 font-medium"><?= e($_GET['msg'] ?? 'Terjadi kesalahan.'); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-6">
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                     
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-medium text-gray-700">Filter:</span>
                                <a href="?status=all&page=1" 
                                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                                          <?= $status_filter === 'all' ? 'bg-emerald-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                                    Semua
                                </a>
                                <a href="?status=tersedia&page=1" 
                                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                                          <?= $status_filter === 'tersedia' ? 'bg-emerald-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                                    Tersedia
                                </a>
                                <a href="?status=habis&page=1" 
                                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                                          <?= $status_filter === 'habis' ? 'bg-emerald-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                                    Habis
                                </a>
                            </div>

                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span class="font-medium"><?= $total_items; ?></span> donasi ditemukan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="py-4 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Foto</th>
                                    <th class="py-4 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Judul</th>
                                    <th class="py-4 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kategori</th>
                                    <th class="py-4 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Stok</th>
                                    <th class="py-4 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="py-4 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Batas Waktu</th>
                                    <th class="py-4 px-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="py-4 px-4">
                                            <?php if (!empty($row['foto_path'])): ?>
                                                <img src="../uploads/food_images/<?= e($row['foto_path']); ?>" 
                                                     alt="foto" 
                                                     class="w-16 h-16 object-cover rounded-lg shadow-sm ring-2 ring-gray-100">
                                            <?php else: ?>
                                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= e($row['judul']); ?>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <?= date('d M Y, H:i', strtotime($row['created_at'])); ?>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800">
                                                <?= e($row['nama_kategori'] ?? '-'); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="text-sm font-semibold text-gray-900">
                                                <?= (int)$row['stok_tersedia']; ?> / <?= (int)$row['jumlah_awal']; ?>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                                <?php 
                                                    $percentage = $row['jumlah_awal'] > 0 ? ($row['stok_tersedia'] / $row['jumlah_awal']) * 100 : 0;
                                                    $color_class = $percentage > 50 ? 'bg-green-500' : ($percentage > 0 ? 'bg-yellow-500' : 'bg-red-500');
                                                ?>
                                                <div class="<?= $color_class; ?> h-1.5 rounded-full transition-all duration-300" 
                                                     style="width: <?= $percentage; ?>%"></div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold
                                                <?= $row['status'] === 'tersedia' 
                                                    ? 'bg-green-100 text-green-800 ring-1 ring-green-600/20' 
                                                    : 'bg-gray-100 text-gray-800 ring-1 ring-gray-600/20'; ?>">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 <?= $row['status'] === 'tersedia' ? 'bg-green-600' : 'bg-gray-600'; ?>"></span>
                                                <?= e(ucfirst($row['status'])); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="text-sm text-gray-900">
                                                <?= date('d M Y', strtotime($row['batas_waktu'])); ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?= date('H:i', strtotime($row['batas_waktu'])); ?>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <button onclick="openEditModal(<?= $row['id']; ?>, '<?= addslashes(e($row['judul'])); ?>', <?= (int)$row['stok_tersedia']; ?>, <?= (int)$row['jumlah_awal']; ?>)"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium
                                                               bg-blue-50 text-blue-700 hover:bg-blue-100
                                                               focus:outline-none focus:ring-2 focus:ring-blue-500/50
                                                               transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    Edit
                                                </button>
                                                <a href="../actions/food_delete.php?id=<?= $row['id']; ?>"
                                                   onclick="return confirm('Yakin ingin menghapus donasi ini?');"
                                                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium
                                                          bg-red-50 text-red-700 hover:bg-red-100
                                                          focus:outline-none focus:ring-2 focus:ring-red-500/50
                                                          transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <p class="text-gray-500 font-medium">Belum ada donasi</p>
                                            <p class="text-sm text-gray-400 mt-1">Mulai berbagi dengan menambahkan donasi baru</p>
                                            <a href="add_food.php" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Tambah Donasi
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-700">
                                    Halaman <span class="font-semibold"><?= $current_page; ?></span> dari <span class="font-semibold"><?= $total_pages; ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <?php if ($current_page > 1): ?>
                                        <a href="?status=<?= e($status_filter); ?>&page=<?= $current_page - 1; ?>" 
                                           class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                            Previous
                                        </a>
                                    <?php endif; ?>

                                    <div class="flex gap-1">
                                        <?php
                                        $start_page = max(1, $current_page - 2);
                                        $end_page = min($total_pages, $current_page + 2);
                                        
                                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                                            <a href="?status=<?= e($status_filter); ?>&page=<?= $i; ?>" 
                                               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                                      <?= $i === $current_page 
                                                          ? 'bg-emerald-600 text-white shadow-md' 
                                                          : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'; ?>">
                                                <?= $i; ?>
                                            </a>
                                        <?php endfor; ?>
                                    </div>

                                    <?php if ($current_page < $total_pages): ?>
                                        <a href="?status=<?= e($status_filter); ?>&page=<?= $current_page + 1; ?>" 
                                           class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                            Next
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <?php include '../includes/footer_simple.php'; ?>
    </div>
</div>

<div id="editModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all scale-95" id="modalContent">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Stok Donasi
                </h3>
                <button onclick="closeEditModal()" type="button"
                        class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <form action="../actions/food_update.php" method="POST" class="p-6 space-y-5">
            <input type="hidden" name="id" id="edit_id">
            
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Judul Donasi</label>
                <div class="px-4 py-3 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-900 font-medium" id="edit_judul"></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Jumlah Awal</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-sm text-gray-900 font-medium" id="edit_jumlah_awal"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                        Stok Tersedia
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="stok_tersedia"
                        id="edit_stok_tersedia"
                        min="0"
                        required
                        class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm font-medium
                               focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10
                               hover:border-gray-300 transition-all duration-200
                               bg-gray-50 focus:bg-white"
                    >
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-xs text-blue-800">
                        <p class="font-semibold mb-1">Informasi Auto-Status:</p>
                        <ul class="space-y-1 list-disc list-inside">
                            <li>Stok = 0 → Status otomatis <span class="font-semibold">"habis"</span></li>
                            <li>Stok > 0 → Status otomatis <span class="font-semibold">"tersedia"</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button
                    type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                           bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-semibold shadow-lg shadow-emerald-500/30
                           hover:from-emerald-700 hover:to-emerald-800 hover:shadow-xl hover:shadow-emerald-500/40
                           focus:outline-none focus:ring-4 focus:ring-emerald-500/50
                           active:scale-[0.98] transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Stok
                </button>
                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="px-5 py-3 rounded-xl border-2 border-gray-300 text-sm font-semibold text-gray-700 bg-white
                           hover:bg-gray-50 hover:border-gray-400
                           focus:outline-none focus:ring-4 focus:ring-gray-300/50
                           active:scale-[0.98] transition-all duration-200">
                    Batal
                </button>
            </div>
        </form>
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

function openEditModal(id, judul, stokTersedia, jumlahAwal) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_judul').textContent = judul;
    document.getElementById('edit_jumlah_awal').textContent = jumlahAwal;
    document.getElementById('edit_stok_tersedia').value = stokTersedia;
    document.getElementById('edit_stok_tersedia').max = jumlahAwal;
    
    const modal = document.getElementById('editModal');
    const modalContent = document.getElementById('modalContent');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }, 10);
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    const modalContent = document.getElementById('modalContent');
    
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});

setTimeout(() => {
    const alerts = document.querySelectorAll('.animate-fade-in');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease-out';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>

<?php
$conn->close();
?>