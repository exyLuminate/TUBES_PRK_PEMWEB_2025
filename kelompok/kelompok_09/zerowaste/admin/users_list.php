<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
require_once '../config/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['user_id'])) {
        $user_id = (int)$_POST['user_id'];
        $action = $_POST['action'];
        
        
        if ($user_id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Anda tidak dapat menonaktifkan akun sendiri!';
            header('Location: users_list.php');
            exit();
        }
        
        if ($action === 'soft_delete') {
            
            $stmt = mysqli_prepare($conn, "UPDATE users SET deleted_at = NOW(), is_active = 0 WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                logActivity($conn, $_SESSION['user_id'], 'DELETE_USER', "Admin menghapus user ID: $user_id");
                $_SESSION['success'] = 'User berhasil dihapus (soft delete)!';
            }
            mysqli_stmt_close($stmt);
        } 
        elseif ($action === 'toggle_active') {
           
            $stmt = mysqli_prepare($conn, "UPDATE users SET is_active = NOT is_active WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                logActivity($conn, $_SESSION['user_id'], 'TOGGLE_USER', "Admin mengubah status user ID: $user_id");
                $_SESSION['success'] = 'Status user berhasil diubah!';
            }
            mysqli_stmt_close($stmt);
        }
        
        header('Location: users_list.php');
        exit();
    }
}


$users = mysqli_query($conn, "SELECT * FROM users WHERE deleted_at IS NULL ORDER BY created_at DESC");

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<div class="flex pt-16 bg-gray-50 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>
    
       <div class="flex flex-col w-full md:ml-64">
        <main class="flex-grow p-6">
            
            
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
                <h1 class="text-2xl font-bold mb-2">
                    <i class="fas fa-users-cog mr-2 text-green-600"></i>
                    Kelola Pengguna
                </h1>
                <p class="text-gray-600">Manajemen user sistem ZeroWaste</p>
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

           
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-list mr-2"></i>Daftar Pengguna
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terdaftar</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= $user['id'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-circle text-gray-400 mr-2"></i>
                                        <?= htmlspecialchars($user['username']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($user['nama_lengkap']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $badge_colors = [
                                        'admin' => 'bg-red-100 text-red-800',
                                        'donatur' => 'bg-green-100 text-green-800',
                                        'mahasiswa' => 'bg-blue-100 text-blue-800'
                                    ];
                                    $icons = [
                                        'admin' => 'fa-user-shield',
                                        'donatur' => 'fa-hands-helping',
                                        'mahasiswa' => 'fa-user-graduate'
                                    ];
                                    ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badge_colors[$user['role']] ?>">
                                        <i class="fas <?= $icons[$user['role']] ?> mr-1"></i>
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <i class="fas fa-phone text-gray-400 mr-1"></i>
                                    <?= htmlspecialchars($user['no_hp']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($user['is_active']): ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-ban mr-1"></i>Banned
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                    <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <div class="flex items-center justify-center space-x-2">
    
                                <form method="POST" class="inline-block" onsubmit="return confirm('Ubah status user?')">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="action" value="toggle_active">
                                    <button type="submit" class="text-blue-600 hover:bg-blue-50 px-3 py-1 rounded border border-blue-200 text-xs font-bold transition flex items-center gap-1">
                                        <i class="fas fa-toggle-on"></i> Status
                                    </button>
                                </form>
                                
                                <form method="POST" class="inline-block" onsubmit="return confirm('Hapus user ini?')">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="action" value="soft_delete">
                                    <button type="submit" class="text-red-600 hover:bg-red-50 px-3 py-1 rounded border border-red-200 text-xs font-bold transition flex items-center gap-1">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>

                            </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs italic">(Anda)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
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
    
    if(toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }
</script>
