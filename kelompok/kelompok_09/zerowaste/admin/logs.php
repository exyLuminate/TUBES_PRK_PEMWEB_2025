<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../includes/header.php';
include '../includes/navbar.php';
require '../config/database.php';

// Ambil logs
$result = $conn->query("SELECT * FROM logs ORDER BY created_at DESC");
?>

<section class="p-10 bg-slate-50 min-h-screen">
    <h1 class="text-3xl font-bold mb-6">Activity Logs</h1>

    <table class="w-full table-auto bg-white rounded-xl shadow overflow-hidden">
        <thead class="bg-slate-200 text-left">
            <tr>
                <th class="p-3">ID</th>
                <th class="p-3">User</th>
                <th class="p-3">Activity</th>
                <th class="p-3">Waktu</th>
            </tr>
        </thead>
        <tbody>
            <?php while($log = $result->fetch_assoc()): ?>
            <tr class="border-b">
                <td class="p-3"><?= $log['id'] ?></td>
                <td class="p-3"><?= $log['user_id'] ?></td>
                <td class="p-3"><?= $log['activity'] ?></td>
                <td class="p-3"><?= $log['created_at'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

<?php include '../includes/footer.php'; ?>
