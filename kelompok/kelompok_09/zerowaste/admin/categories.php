<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../includes/header.php';
include '../includes/navbar.php';
require '../config/database.php';

// Tambah kategori
if (isset($_POST['add'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $conn->query("INSERT INTO categories (name) VALUES ('$name')");
    $_SESSION['success'] = "Kategori berhasil ditambahkan";
    header("Location: manage_categories.php");
    exit();
}

// Hapus kategori
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE id=$id");
    $_SESSION['success'] = "Kategori berhasil dihapus";
    header("Location: manage_categories.php");
    exit();
}

// Ambil semua kategori
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<section class="p-10 bg-slate-50 min-h-screen">
    <h1 class="text-3xl font-bold mb-6">Manage Categories</h1>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 rounded">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="mb-6 flex gap-2">
        <input type="text" name="name" required placeholder="Nama Kategori"
               class="p-2 border rounded flex-1">
        <button type="submit" name="add" class="bg-primary text-white px-4 rounded">Tambah</button>
    </form>

    <table class="w-full table-auto bg-white rounded-xl shadow overflow-hidden">
        <thead class="bg-slate-200 text-left">
            <tr>
                <th class="p-3">ID</th>
                <th class="p-3">Nama</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($cat = $result->fetch_assoc()): ?>
            <tr class="border-b">
                <td class="p-3"><?= $cat['id'] ?></td>
                <td class="p-3"><?= $cat['name'] ?></td>
                <td class="p-3">
                    <a href="?delete=<?= $cat['id'] ?>" onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                       class="text-red-500 font-bold">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

<?php include '../includes/footer.php'; ?>
