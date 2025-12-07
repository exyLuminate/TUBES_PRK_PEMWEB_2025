<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'donatur';
    $_SESSION['nama_lengkap'] = 'Testing Donatur';
}

if ($_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

$donatur_id = $_SESSION['user_id'];

$sql = "
    SELECT f.id, f.judul, f.jumlah_awal, f.stok_tersedia, f.status,
           f.batas_waktu, f.created_at, f.foto_path,
           c.nama_kategori
    FROM food_stocks f
    LEFT JOIN categories c ON f.category_id = c.id
    WHERE f.donatur_id = ? AND f.deleted_at IS NULL
    ORDER BY f.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $donatur_id);
$stmt->execute();
$result = $stmt->get_result();

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<div class="flex pt-16 bg-gray-50 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex flex-col w-full md:ml-64 transition-all">
        <main class="flex-grow p-6">
            <h1 class="text-2xl font-bold mb-4">Kelola Donasi</h1>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
                <div class="mb-4 text-sm text-green-700 bg-green-100 border px-3 py-2 rounded">
                    Donasi berhasil dihapus.
                </div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'created'): ?>
                <div class="mb-4 text-sm text-green-700 bg-green-100 border px-3 py-2 rounded">
                    Donasi berhasil ditambahkan.
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg p-6 border">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Daftar Donasi Anda</h2>
                    <a href="add_food.php" class="px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                        + Tambah Donasi
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="py-2 px-2 text-left">Foto</th>
                                <th class="py-2 px-2 text-left">Judul</th>
                                <th class="py-2 px-2 text-left">Kategori</th>
                                <th class="py-2 px-2 text-left">Stok</th>
                                <th class="py-2 px-2 text-left">Status</th>
                                <th class="py-2 px-2 text-left">Batas Waktu</th>
                                <th class="py-2 px-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="border-b">
                                    <td class="py-2 px-2">
                                        <?php if (!empty($row['foto_path'])): ?>
                                        <img src="../uploads/food_images/<?= htmlspecialchars($row['foto_path']); ?>" alt="foto" class="w-14 h-14 object-cover rounded">                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2 px-2"><?= htmlspecialchars($row['judul']); ?></td>
                                    <td class="py-2 px-2"><?= htmlspecialchars($row['nama_kategori'] ?? '-'); ?></td>
                                    <td class="py-2 px-2">
                                        <?= (int)$row['stok_tersedia'] . ' / ' . (int)$row['jumlah_awal']; ?>
                                    </td>
                                    <td class="py-2 px-2">
                                        <span class="px-2 py-1 rounded text-xs
                                            <?= $row['status'] === 'tersedia' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'; ?>">
                                            <?= htmlspecialchars(ucfirst($row['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="py-2 px-2"><?= htmlspecialchars($row['batas_waktu']); ?></td>
                                    <td class="py-2 px-2 space-x-2">
                                        <a href="../actions/food_delete.php?id=<?= $row['id']; ?>"
                                           class="text-red-600 text-xs hover:underline"
                                           onclick="return confirm('Yakin ingin menghapus donasi ini?');">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-4 text-center text-gray-500">
                                    Belum ada donasi.
                                </td>
                            </tr>
                        <?php endif; ?>
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
if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
    });
}
</script>
