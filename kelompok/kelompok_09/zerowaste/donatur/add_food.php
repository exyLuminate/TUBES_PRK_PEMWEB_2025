<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';


$categories = $conn->query("
    SELECT id, nama_kategori 
    FROM categories 
    WHERE deleted_at IS NULL
");

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<div class="flex pt-16 bg-gray-50 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex flex-col w-full md:ml-64 transition-all">
        <main class="flex-grow p-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border max-w-2xl">
                <h1 class="text-2xl font-bold mb-4">Tambah Donasi</h1>

                <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
                    <div class="mb-4 text-sm text-red-700 bg-red-100 border px-3 py-2 rounded">
                        Terjadi kesalahan. Pastikan semua data sudah diisi dengan benar.
                    </div>
                <?php endif; ?>

                <form action="../actions/food_create.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Judul Makanan</label>
                        <input type="text" name="judul" required
                            class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Kategori</label>
                        <select name="category_id" class="w-full border rounded px-3 py-2 text-sm" required>
                            <option value="">Pilih Kategori</option>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?= $cat['id']; ?>">
                                    <?= htmlspecialchars($cat['nama_kategori']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full border rounded px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Jumlah Porsi</label>
                            <input type="number" name="jumlah_awal" min="1" required
                                class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Jenis Makanan</label>
                            <select name="jenis_makanan" class="w-full border rounded px-3 py-2 text-sm">
                                <option value="halal">Halal</option>
                                <option value="non_halal">Non Halal</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Lokasi Pickup</label>
                        <textarea name="lokasi_pickup" rows="2" required
                            class="w-full border rounded px-3 py-2 text-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Batas Waktu Pengambilan</label>
                        <input type="datetime-local" name="batas_waktu" required
                            class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Foto Makanan</label>
                        <input type="file" name="foto" accept="image/*" required
                            class="w-full text-sm">
                    </div>

                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                        Simpan Donasi
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
