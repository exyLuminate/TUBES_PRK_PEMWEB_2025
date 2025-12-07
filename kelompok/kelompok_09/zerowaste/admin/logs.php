<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
require_once '../config/functions.php';


$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;


$filter_action = isset($_GET['action']) ? trim($_GET['action']) : '';
$filter_user = isset($_GET['user']) ? (int)$_GET['user'] : 0;


$where = [];
$params = [];
$types = '';

if (!empty($filter_action)) {
    $where[] = "al.action = ?";
    $params[] = $filter_action;
    $types .= 's';
}

if ($filter_user > 0) {
    $where[] = "al.user_id = ?";
    $params[] = $filter_user;
    $types .= 'i';
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$count_query = "SELECT COUNT(*) as total FROM activity_logs al $where_sql";
if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $count_query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $count_result = mysqli_stmt_get_result($stmt);
} else {
    $count_result = mysqli_query($conn, $count_query);
}
$total_logs = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_logs / $limit);


$logs_query = "SELECT al.*, u.username, u.nama_lengkap, u.role 
    FROM activity_logs al 
    JOIN users u ON al.user_id = u.id 
    $where_sql
    ORDER BY al.created_at DESC 
    LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($conn, $logs_query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$logs = mysqli_stmt_get_result($stmt);


$actions_result = mysqli_query($conn, "SELECT DISTINCT action FROM activity_logs ORDER BY action");


$users_result = mysqli_query($conn, "SELECT id, nama_lengkap FROM users WHERE deleted_at IS NULL ORDER BY nama_lengkap");

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<div class="flex pt-16 bg-gray-50 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex flex-col w-full md:ml-64">
        <main class="flex-grow p-6">
            
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">
                            <i class="fas fa-clipboard-list mr-2 text-green-600"></i>
                            Activity Logs
                        </h1>
                        <p class="text-gray-600">Riwayat aktivitas sistem ZeroWaste</p>
                    </div>
                    </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span><?= $_SESSION['success'] ?></span>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span><?= $_SESSION['error'] ?></span>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-md font-semibold text-gray-700 mb-4">
                    <i class="fas fa-filter mr-2"></i>Filter Logs
                </h3>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-bolt mr-1"></i>Filter Aksi
                        </label>
                        <select name="action" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                            <option value="">-- Semua Aksi --</option>
                            <?php while ($act = mysqli_fetch_assoc($actions_result)): ?>
                                <option value="<?= htmlspecialchars($act['action']) ?>" <?= $filter_action === $act['action'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($act['action']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i>Filter User
                        </label>
                        <select name="user" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                            <option value="0">-- Semua User --</option>
                            <?php while ($u = mysqli_fetch_assoc($users_result)): ?>
                                <option value="<?= $u['id'] ?>" <?= $filter_user === $u['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['nama_lengkap']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="flex items-end space-x-2">
                        <button 
                            type="submit" 
                            class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-semibold"
                        >
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                        
                        <a 
                            href="logs.php" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition font-semibold flex items-center"
                            title="Reset Filter"
                        >
                            <i class="fas fa-redo mr-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-list mr-2"></i>Log Aktivitas
                    </h2>
                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                        Total: <strong><?= $total_logs ?></strong> logs
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                    <i class="fas fa-clock mr-1"></i>Waktu
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    <i class="fas fa-user mr-1"></i>User
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    <i class="fas fa-user-tag mr-1"></i>Role
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                    <i class="fas fa-bolt mr-1"></i>Aksi
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-info-circle mr-1"></i>Deskripsi
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">
                                    <i class="fas fa-network-wired mr-1"></i>IP
                                </th>
                                </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (mysqli_num_rows($logs) > 0): ?>
                                <?php while ($log = mysqli_fetch_assoc($logs)): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <div class="whitespace-nowrap"><?= date('d/m/Y', strtotime($log['created_at'])) ?></div>
                                        <div class="text-xs text-gray-500"><?= date('H:i', strtotime($log['created_at'])) ?></div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-medium text-gray-900 truncate max-w-xs"><?= htmlspecialchars($log['nama_lengkap']) ?></div>
                                        <div class="text-gray-500 text-xs truncate">@<?= htmlspecialchars($log['username']) ?></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php 
                                        $badge_colors = [
                                            'admin' => 'bg-red-100 text-red-800',
                                            'donatur' => 'bg-green-100 text-green-800',
                                            'mahasiswa' => 'bg-blue-100 text-blue-800'
                                        ];
                                        ?>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badge_colors[$log['role']] ?>">
                                            <?= ucfirst($log['role']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded bg-blue-100 text-blue-800 whitespace-nowrap">
                                            <?= htmlspecialchars($log['action']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div class="line-clamp-2 max-w-md"><?= htmlspecialchars($log['description']) ?></div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <code class="bg-gray-100 px-2 py-1 rounded text-xs text-gray-700 block truncate">
                                            <?= htmlspecialchars($log['ip_address']) ?>
                                        </code>
                                    </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <i class="fas fa-inbox text-gray-300 text-5xl mb-4 block"></i>
                                        <p class="text-gray-500">Tidak ada log ditemukan</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Halaman <strong><?= $page ?></strong> dari <strong><?= $total_pages ?></strong>
                    </div>
                    <div class="flex space-x-2">
                        <?php if ($page > 1): ?>
                            <a 
                                href="?page=<?= $page - 1 ?><?= $filter_action ? '&action=' . urlencode($filter_action) : '' ?><?= $filter_user ? '&user=' . $filter_user : '' ?>" 
                                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold"
                            >
                                <i class="fas fa-chevron-left mr-2"></i>Prev
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a 
                                href="?page=<?= $page + 1 ?><?= $filter_action ? '&action=' . urlencode($filter_action) : '' ?><?= $filter_user ? '&user=' . $filter_user : '' ?>" 
                                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold"
                            >
                                Next<i class="fas fa-chevron-right ml-2"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </main>
        
        <?php include '../includes/footer_simple.php'; ?>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    if(toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }
    
</script>