<?php
session_start();
require '../config/database.php';
require '../config/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

$uid = $_SESSION['user_id'];

$tiket = $conn->query("
    SELECT c.*, f.judul
    FROM claims c
    JOIN food_stocks f ON f.id = c.food_id
    WHERE c.mahasiswa_id='$uid'
    ORDER BY c.created_at DESC
");
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<section class="pt-32 pb-20 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-6">

        <h1 class="text-3xl font-extrabold text-slate-900 mb-10">
            Tiket Saya
        </h1>

        <?php while ($t = $tiket->fetch_assoc()) { ?>
            <div class="p-6 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-all mb-6">

                <div class="flex justify-between items-start">

                    <div>
                        <div class="font-bold text-xl text-primary mb-2">
                            <?php echo $t['judul']; ?>
                        </div>

                        <div class="mb-1 text-slate-700">
                            Kode Tiket:
                            <span class="font-semibold text-blue-600"><?php echo $t['kode_tiket']; ?></span>
                        </div>

                        <div class="mb-1 text-slate-700">
                            Status:
                            <span class="font-semibold text-slate-900"><?php echo $t['status']; ?></span>
                        </div>

                        <div class="text-slate-600 mb-1">
                            Waktu: <?php echo formatTanggal($t['created_at']); ?>
                        </div>

                        <?php if ($t['status'] == 'batal') { ?>
                            <div class="text-red-600 font-medium mt-2">
                                Alasan: <?php echo $t['alasan_batal']; ?>
                            </div>
                        <?php } ?>
                    </div>

                    <?php if ($t['status'] === 'pending') { ?>
                        <form action="../actions/claim_cancel.php" method="POST">
                            <input type="hidden" name="claim_id" value="<?php echo $t['id']; ?>">
                            <button class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all shadow-sm">
                                Batalkan
                            </button>
                        </form>
                    <?php } ?>

                </div>

            </div>
        <?php } ?>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
