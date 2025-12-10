<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';
require_once '../config/functions.php';

$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT c.*, f.judul, f.lokasi_pickup 
    FROM claims c 
    JOIN food_stocks f ON f.id = c.food_id 
    WHERE c.mahasiswa_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();

include '../includes/header.php';
include '../includes/navbar_dashboard.php'; 
?>

<div class="flex pt-16 bg-gray-50 min-h-screen">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex flex-col w-full md:ml-64 transition-all">
        <main class="flex-grow p-6">

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Tiket Saya</h1>
                <p class="text-gray-600 text-sm">Riwayat pengambilan makanan Anda.</p>
            </div>

            <?php if ($result->num_rows == 0): ?>
                <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                    <i class="fas fa-ticket-alt text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada riwayat tiket.</p>
                    <a href="../catalog.php" class="text-green-600 font-bold hover:underline mt-2 inline-block">Cari Makanan</a>
                </div>
            <?php else: ?>
                <div class="mb-4 flex justify-end">
                    <select id="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="semua">Semua</option>
                        <option value="aktif">Aktif (Pending)</option>
                        <option value="riwayat">Riwayat (Selesai/Batal/Expired)</option>
                    </select>
                </div>

                <div class="space-y-4" id="ticketsList">
                    <?php while ($t = $result->fetch_assoc()) { 

                        $badge_color = 'bg-gray-100 text-gray-800';
                        if($t['status'] == 'pending') $badge_color = 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                        if($t['status'] == 'diambil') $badge_color = 'bg-green-100 text-green-800 border border-green-200';
                        if($t['status'] == 'batal')   $badge_color = 'bg-red-100 text-red-800 border border-red-200';
                        if($t['status'] == 'expired') $badge_color = 'bg-gray-300 text-gray-900 border border-gray-400';
                    ?>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
                            <div class="flex flex-col md:flex-row justify-between md:items-start gap-4">

                                <div class="flex-grow">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide <?= $badge_color ?>">
                                            <?= $t['status'] ?>
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            <i class="far fa-clock mr-1"></i> <?= formatTanggal($t['created_at']) ?>
                                        </span>
                                    </div>

                                    <h3 class="font-bold text-lg text-gray-900 mb-1">
                                        <?= htmlspecialchars($t['judul']) ?>
                                    </h3>

                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p><span class="font-semibold w-24 inline-block">Kode Tiket:</span> 
                                            <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 rounded">
                                                <?= $t['kode_tiket'] ?>
                                            </span>
                                        </p>

                                        <p><span class="font-semibold w-24 inline-block">Lokasi:</span>
                                            <?= htmlspecialchars($t['lokasi_pickup']) ?>
                                        </p>

                                        <?php if ($t['status'] == 'pending'): ?>
                                            <p class="text-orange-600 font-semibold mt-1">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Batas Pengambilan: Jam <?= date('H:i', strtotime($t['created_at']) + 3600) ?>
                                            </p>
                                        <?php endif; ?>

                                        <?php if ($t['status'] == 'batal'): ?>
                                            <p class="text-red-600 mt-2 text-xs bg-red-50 p-2 rounded border border-red-100 inline-block">
                                                <strong>Alasan Batal:</strong> <?= htmlspecialchars($t['alasan_batal']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($t['status'] === 'pending'): ?>
                                    <div class="flex-shrink-0">
                                        <form action="../actions/claim_cancel.php" method="POST" onsubmit="return confirm('Yakin ingin membatalkan tiket ini? Stok akan dikembalikan.')">
                                            <input type="hidden" name="claim_id" value="<?= $t['id'] ?>">
                                            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-lg hover:bg-red-50 transition">
                                                Batalkan Pesanan
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php endif; ?>

        </main>

        <?php include '../includes/footer_simple.php'; ?>
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

    // PERBAIKAN FILTER
    document.getElementById('filterStatus').addEventListener('change', function() {
        const selected = this.value;
        const tickets = document.querySelectorAll('#ticketsList > div');

        tickets.forEach(ticket => {

            // Ambil badge status (lebih aman, tidak bergantung urutan class)
            const statusSpan = ticket.querySelector('span[class*="rounded-full"]');
            if (!statusSpan) return;

            const status = statusSpan.textContent.trim().toLowerCase();
            let show = false;

            if (selected === 'semua') {
                show = true;
            }
            else if (selected === 'aktif') {
                show = status === 'pending';
            }
            else if (selected === 'riwayat') {
                show = ['diambil', 'batal', 'expired'].includes(status);
            }

            ticket.style.display = show ? 'block' : 'none';
        });
    });
</script>
