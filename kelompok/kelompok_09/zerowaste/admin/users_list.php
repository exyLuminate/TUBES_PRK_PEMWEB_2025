<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../includes/header.php';
include '../includes/navbar.php';
require '../config/database.php';

// Hapus user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id=$id");
    $_SESSION['success'] = "User berhasil dihapus";
    header("Location: manage_users.php");
    exit();
}

// Ambil semua user
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<section class="p-10 bg-slate-50 min-h-screen">
    <h1 class="text-3xl font-bold mb-6">Manage Users</h1>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 rounded">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <table class="w-full table-auto bg-white rounded-xl shadow overflow-hidden">
        <thead class="bg-slate-200 text-left">
            <tr>
                <th class="p-3">ID</th>
                <th class="p-3">Nama</th>
                <th class="p-3">Email</th>
                <th class="p-3">Role</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($user = $result->fetch_assoc()): ?>
            <tr class="border-b">
                <td class="p-3"><?= $user['id'] ?></td>
                <td class="p-3"><?= $user['nama'] ?></td>
                <td class="p-3"><?= $user['email'] ?></td>
                <td class="p-3"><?= ucfirst($user['role']) ?></td>
                <td class="p-3">
                    <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Yakin ingin menghapus user ini?')"
                       class="text-red-500 font-bold">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

<?php include '../includes/footer.php'; ?>
