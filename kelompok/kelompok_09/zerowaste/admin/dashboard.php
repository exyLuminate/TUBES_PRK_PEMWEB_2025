$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['nama_lengkap'] = 'Admin Test';


<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../includes/header.php';
include '../includes/navbar.php';
require '../config/database.php';

// Statistik
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_mahasiswa = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='mahasiswa'")->fetch_assoc()['total'];
$total_donatur = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='donatur'")->fetch_assoc()['total'];
$total_food_posts = $conn->query("SELECT COUNT(*) as total FROM food_posts")->fetch_assoc()['total'];
?>

<section class="p-10 bg-slate-50 min-h-screen">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <h2 class="font-bold text-lg mb-2">Total Users</h2>
            <p class="text-2xl font-bold"><?= $total_users ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <h2 class="font-bold text-lg mb-2">Mahasiswa</h2>
            <p class="text-2xl font-bold"><?= $total_mahasiswa ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <h2 class="font-bold text-lg mb-2">Donatur</h2>
            <p class="text-2xl font-bold"><?= $total_donatur ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <h2 class="font-bold text-lg mb-2">Food Posts</h2>
            <p class="text-2xl font-bold"><?= $total_food_posts ?></p>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
