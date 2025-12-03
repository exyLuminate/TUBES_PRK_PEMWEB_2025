<?php
session_start();
require '../config/database.php';
require '../config/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$today = date("Y-m-d");

$limit = $conn->query("
    SELECT COUNT(*) AS total
    FROM claims
    WHERE mahasiswa_id='$uid' AND DATE(created_at)='$today'
")->fetch_assoc()['total'];

$pending = $conn->query("
    SELECT c.*, f.judul
    FROM claims c
    JOIN food_stocks f ON f.id = c.food_id
    WHERE c.mahasiswa_id='$uid' AND c.status='pending'
");
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<section class="pt-32 pb-20 bg-gradient-to-br from-green-50 via-white to-slate-100 min-h-screen">
    <div class="max-w-5xl mx-auto px-6">

        <h1 class="text-3xl font-extrabold text-slate-900 mb-10">
            Dashboard Mahasiswa
        </h1>

        <!-- CARD SUMMARY -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">

            <div class="p-6 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg transition-all">
                <h2 class="font-semibold text-slate-700 mb-2">Klaim Hari Ini</h2>
                <p class="text-4xl font-bold text-green-600"><?php echo $limit; ?>/2</p>
            </div>

            <div class="p-6 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg transition-all">
                <h2 class="font-semibold text-slate-700 mb-2">Tiket Pending</h2>
                <p class="text-4xl font-bold text-blue-600"><?php echo $pending->num_rows; ?></p>
            </div>
        </div>

        <h2 class="text-2xl font-bold text-slate-900 mb-4">Tiket Aktif</h2>

        <?php if ($pending->num_rows == 0) { ?>
            <p class="text-slate-600 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                Tidak ada tiket pending.
            </p>
        <?php } ?>

        <?php while ($t = $pending->fetch_assoc()) { ?>
            <div class="p-6 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-all mb-4">
                <div class="font-bold text-lg text-primary mb-2"><?php echo $t['judul']; ?></div>

                <div class="text-slate-700 mb-1">
                    Kode Tiket:
                    <span class="font-semibold text-blue-600"><?php echo $t['kode_tiket']; ?></span>
                </div>

                <div class="text-slate-600">
                    Dibuat: <?php echo formatTanggal($t['created_at']); ?>
                </div>
            </div>
        <?php } ?>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
