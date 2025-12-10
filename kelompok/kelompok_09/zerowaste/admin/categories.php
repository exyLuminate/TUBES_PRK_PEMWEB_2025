<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
require_once '../config/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $nama_kategori = trim($_POST['nama_kategori']);
        
        if (!empty($nama_kategori)) {
            
            $check_stmt = mysqli_prepare($conn, "SELECT id FROM categories WHERE LOWER(nama_kategori) = LOWER(?) AND deleted_at IS NULL");
            mysqli_stmt_bind_param($check_stmt, "s", $nama_kategori);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if (mysqli_num_rows($check_result) > 0) {
                $_SESSION['error'] = 'Kategori dengan nama tersebut sudah ada!';
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO categories (nama_kategori) VALUES (?)");
                mysqli_stmt_bind_param($stmt, "s", $nama_kategori);
                
                if (mysqli_stmt_execute($stmt)) {
                    logActivity($conn, $_SESSION['user_id'], 'ADD_CATEGORY', "Menambah kategori: $nama_kategori");
                    $_SESSION['success'] = 'Kategori berhasil ditambahkan!';
                } else {
                    $_SESSION['error'] = 'Gagal menambahkan kategori!';
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_stmt_close($check_stmt);
        } else {
            $_SESSION['error'] = 'Nama kategori tidak boleh kosong!';
        }
    }
    elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $nama_kategori = trim($_POST['nama_kategori']);
        
        if (!empty($nama_kategori)) {
            
            $check_stmt = mysqli_prepare($conn, "SELECT id FROM categories WHERE LOWER(nama_kategori) = LOWER(?) AND id != ? AND deleted_at IS NULL");
            mysqli_stmt_bind_param($check_stmt, "si", $nama_kategori, $id);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if (mysqli_num_rows($check_result) > 0) {
                $_SESSION['error'] = 'Kategori dengan nama tersebut sudah ada!';
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE categories SET nama_kategori = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "si", $nama_kategori, $id);
                
                if (mysqli_stmt_execute($stmt)) {
                    logActivity($conn, $_SESSION['user_id'], 'EDIT_CATEGORY', "Mengedit kategori ID: $id menjadi: $nama_kategori");
                    $_SESSION['success'] = 'Kategori berhasil diperbarui!';
                } else {
                    $_SESSION['error'] = 'Gagal memperbarui kategori!';
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_stmt_close($check_stmt);
        } else {
            $_SESSION['error'] = 'Nama kategori tidak boleh kosong!';
        }
    }
    elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        
       
        $check_usage = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM food_stocks WHERE category_id = ? AND deleted_at IS NULL");
        mysqli_stmt_bind_param($check_usage, "i", $id);
        mysqli_stmt_execute($check_usage);
        $usage_result = mysqli_stmt_get_result($check_usage);
        $usage_data = mysqli_fetch_assoc($usage_result);
        
        if ($usage_data['total'] > 0) {
            $_SESSION['error'] = 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $usage_data['total'] . ' makanan!';
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE categories SET deleted_at = NOW() WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if (mysqli_stmt_execute($stmt)) {
                logActivity($conn, $_SESSION['user_id'], 'DELETE_CATEGORY', "Menghapus kategori ID: $id");
                $_SESSION['success'] = 'Kategori berhasil dihapus!';
            } else {
                $_SESSION['error'] = 'Gagal menghapus kategori!';
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check_usage);
    }
    
    header('Location: categories.php' . (isset($_GET['page']) ? '?page=' . $_GET['page'] : ''));
    exit();
}


$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;


$count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM categories WHERE deleted_at IS NULL");
$total_categories = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_categories / $limit);


$categories = mysqli_query($conn, "SELECT c.*, 
    (SELECT COUNT(*) FROM food_stocks fs WHERE fs.category_id = c.id AND fs.deleted_at IS NULL) as food_count
    FROM categories c 
    WHERE c.deleted_at IS NULL 
    ORDER BY c.nama_kategori ASC 
    LIMIT $limit OFFSET $offset");

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<div class="flex pt-16 bg-gray-50 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex flex-col w-full md:ml-64">
        <main class="flex-grow p-6">
            
         
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">
                            <i class="fas fa-tags mr-2 text-green-600"></i>
                            Kelola Kategori
                        </h1>
                        <p class="text-gray-600">Manajemen kategori makanan</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Total Kategori</p>
                        <p class="text-2xl font-bold text-green-600"><?= $total_categories ?></p>
                    </div>
                </div>
            </div>

            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span><?= $_SESSION['success'] ?></span>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span><?= $_SESSION['error'] ?></span>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
               
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-plus-circle mr-2 text-green-600"></i>Tambah Kategori
                    </h2>
                    <form method="POST" id="addCategoryForm" class="space-y-4">
                        <input type="hidden" name="action" value="add">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-1"></i>Nama Kategori
                            </label>
                            <input 
                                type="text" 
                                name="nama_kategori" 
                                id="nama_kategori"
                                required
                                minlength="3"
                                maxlength="50"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                placeholder="Contoh: Nasi Box"
                            >
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-info-circle"></i> Min. 3 karakter, max. 50 karakter
                            </p>
                        </div>
                        <button 
                            type="submit" 
                            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-semibold"
                        >
                            <i class="fas fa-plus mr-2"></i>Tambah Kategori
                        </button>
                    </form>
                </div>

                
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-list mr-2"></i>Daftar Kategori
                        </h2>
                        <?php if ($total_pages > 1): ?>
                            <span class="text-sm text-gray-500">
                                Halaman <?= $page ?> dari <?= $total_pages ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <?php if (mysqli_num_rows($categories) > 0): ?>
                            <div class="space-y-3">
                                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-200">
                                    <div class="flex items-center flex-1">
                                        <div class="bg-green-100 rounded-full p-2 mr-3">
                                            <i class="fas fa-tag text-green-600"></i>
                                        </div>
                                        <div>
                                            <span class="text-gray-800 font-medium"><?= htmlspecialchars($cat['nama_kategori']) ?></span>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-utensils mr-1"></i><?= $cat['food_count'] ?> makanan
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button 
                                            onclick="editCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['nama_kategori'], ENT_QUOTES) ?>')"
                                            class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded transition text-sm font-semibold"
                                        >
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>
                                        
                                        <form method="POST" class="inline" onsubmit="return confirmDelete(<?= $cat['food_count'] ?>)">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                            <button 
                                                type="submit" 
                                                class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1 rounded transition text-sm font-semibold"
                                            >
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>

                           
                            <?php if ($total_pages > 1): ?>
                            <div class="mt-6 flex items-center justify-between border-t pt-4">
                                <div class="text-sm text-gray-700">
                                    Menampilkan <?= min($offset + 1, $total_categories) ?> - <?= min($offset + $limit, $total_categories) ?> dari <?= $total_categories ?> kategori
                                </div>
                                <div class="flex space-x-2">
                                    <?php if ($page > 1): ?>
                                        <a 
                                            href="?page=<?= $page - 1 ?>" 
                                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold"
                                        >
                                            <i class="fas fa-chevron-left mr-2"></i>Prev
                                        </a>
                                    <?php endif; ?>
                                    
                                    
                                    <?php 
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_pages, $page + 2);
                                    
                                    for ($i = $start_page; $i <= $end_page; $i++): 
                                    ?>
                                        <a 
                                            href="?page=<?= $i ?>" 
                                            class="px-4 py-2 <?= $i == $page ? 'bg-green-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' ?> rounded-lg transition font-semibold"
                                        >
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <a 
                                            href="?page=<?= $page + 1 ?>" 
                                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold"
                                        >
                                            Next<i class="fas fa-chevron-right ml-2"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="text-center py-12">
                                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                <p class="text-gray-500">Belum ada kategori</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </main>
        <?php include '../includes/footer_simple.php'; ?>
    </div>
</div>


<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-edit mr-2 text-blue-600"></i>Edit Kategori
            </h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" id="editCategoryForm">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori</label>
                <input 
                    type="text" 
                    name="nama_kategori" 
                    id="edit_nama"
                    required
                    minlength="3"
                    maxlength="50"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle"></i> Min. 3 karakter, max. 50 karakter
                </p>
            </div>
            <div class="flex space-x-2">
                <button 
                    type="submit" 
                    class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-semibold"
                >
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
                <button 
                    type="button" 
                    onclick="closeEditModal()" 
                    class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 transition font-semibold"
                >
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    if(toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }

    function editCategory(id, nama) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });

    
    function confirmDelete(foodCount) {
        if (foodCount > 0) {
            return confirm(`PERINGATAN: Kategori ini masih digunakan oleh ${foodCount} makanan!\n\nYakin ingin menghapus kategori ini?`);
        }
        return confirm('Yakin ingin menghapus kategori ini?');
    }

    
    function capitalizeWords(input) {
        return input.value.split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ');
    }

  
    const namaKategoriInput = document.getElementById('nama_kategori');
    namaKategoriInput.addEventListener('blur', function() {
        this.value = capitalizeWords(this);
    });

    
    const editNamaInput = document.getElementById('edit_nama');
    editNamaInput.addEventListener('blur', function() {
        this.value = capitalizeWords(this);
    });
</script>