<?php 
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<div class="flex pt-16 bg-gray-50 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex flex-col w-full md:ml-64 transition-all">
        <main class="flex-grow p-6">

            <div class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
                <h1 class="text-2xl font-bold mb-4">Verifikasi Pengambilan</h1>

                <?php if (isset($_GET['status'])): ?>
                    <?php if ($_GET['status'] === 'success'): ?>
                        <div class="mb-4 text-sm text-green-700 bg-green-100 border px-3 py-2 rounded">
                            ✅ Tiket berhasil diverifikasi. Silakan serahkan makanan ke mahasiswa.
                        </div>
                    <?php elseif ($_GET['status'] === 'notfound'): ?>
                        <div class="mb-4 text-sm text-red-700 bg-red-100 border px-3 py-2 rounded">
                            ❌ Kode tiket tidak ditemukan atau bukan milik donasi Anda.
                        </div>
                    <?php elseif ($_GET['status'] === 'invalid'): ?>
                        <div class="mb-4 text-sm text-yellow-800 bg-yellow-100 border px-3 py-2 rounded">
                            ⚠️ Tiket ini sudah tidak valid (sudah diambil / dibatalkan / expired).
                        </div>
                    <?php else: ?>
                        <div class="mb-4 text-sm text-red-700 bg-red-100 border px-3 py-2 rounded">
                            Terjadi kesalahan saat memproses verifikasi. Coba lagi.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form action="../actions/proses_verify.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Kode Tiket</label>
                        <input type="text" name="kode_tiket" required
                            class="w-full border rounded px-3 py-2 text-sm"
                            placeholder="Contoh: FR-ABC123">
                    </div>

                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                        Verifikasi Pengambilan
                    </button>
                </form>
            </div>

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
</script>
