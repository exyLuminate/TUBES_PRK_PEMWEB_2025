<?php 
session_start();
include 'config/database.php'; 
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container mx-auto px-4 py-8 min-h-screen">
    
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-800">Katalog Makanan</h1>
        <p class="text-gray-600 mt-2">Temukan makanan gratis di sekitarmu.</p>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        
        <aside class="w-full md:w-64 flex-shrink-0">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-24">
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                    <i class="bi bi-funnel"></i> Filter
                </h3>
                
                <form action="" method="GET">
                    
                    <div class="mb-6">
                        <label class="text-sm font-medium text-gray-700 mb-2 block">Cari Nama</label>
                        <input type="text" name="q" value="<?= $_GET['q'] ?? '' ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none" placeholder="Nasi, Roti...">
                    </div>

                    <div class="mb-6">
                        <label class="text-sm font-medium text-gray-700 mb-2 block">Kategori</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            <?php 
                            $cats = mysqli_query($conn, "SELECT * FROM categories");
                            while($c = mysqli_fetch_assoc($cats)): 
                                $checked = (isset($_GET['cat']) && in_array($c['id'], $_GET['cat'])) ? 'checked' : '';
                            ?>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="cat[]" value="<?= $c['id'] ?>" <?= $checked ?> class="rounded text-green-600 focus:ring-green-500">
                                <span class="text-gray-600 text-sm"><?= $c['nama_kategori'] ?></span>
                            </label>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="text-sm font-medium text-gray-700 mb-2 block">Jenis Makanan</label>
                        <select name="jenis" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Semua</option>
                            <option value="halal" <?= (isset($_GET['jenis']) && $_GET['jenis'] == 'halal') ? 'selected' : '' ?>>Halal</option>
                            <option value="non_halal" <?= (isset($_GET['jenis']) && $_GET['jenis'] == 'non_halal') ? 'selected' : '' ?>>Non-Halal</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg font-bold hover:bg-green-700 transition">
                        Terapkan Filter
                    </button>
                    
                    <?php if(!empty($_GET)): ?>
                        <a href="catalog.php" class="block text-center mt-2 text-sm text-red-500 hover:underline">Reset Filter</a>
                    <?php endif; ?>
                </form>
            </div>
        </aside>

        <main class="flex-grow">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="foodContainer">
                
                <?php
                $sql = "SELECT fs.*, u.nama_lengkap as donatur 
                          FROM food_stocks fs 
                          JOIN users u ON fs.donatur_id = u.id 
                          WHERE fs.deleted_at IS NULL 
                          AND fs.stok_tersedia > 0 
                          AND fs.batas_waktu > NOW()";

                if (!empty($_GET['q'])) {
                    $keyword = mysqli_real_escape_string($conn, $_GET['q']);
                    $sql .= " AND fs.judul LIKE '%$keyword%'";
                }

                if (!empty($_GET['jenis'])) {
                    $jenis = mysqli_real_escape_string($conn, $_GET['jenis']);
                    $sql .= " AND fs.jenis_makanan = '$jenis'";
                }

                if (!empty($_GET['cat']) && is_array($_GET['cat'])) {
                    $cats_id = implode(",", array_map('intval', $_GET['cat']));
                    
                    if (!empty($cats_id)) {
                        $sql .= " AND fs.category_id IN ($cats_id)";
                    }
                }

                $sql .= " ORDER BY fs.created_at DESC";
                
                $result = mysqli_query($conn, $sql);

                if(mysqli_num_rows($result) > 0):
                    while($row = mysqli_fetch_assoc($result)):
                        $batas = new DateTime($row['batas_waktu']);
                        $sekarang = new DateTime();
                        
                        if ($sekarang > $batas) {
                            $sisa_waktu = "Expired";
                        } else {
                            $interval = $sekarang->diff($batas);
                            if ($interval->h > 0) {
                                $sisa_waktu = $interval->format('%h Jam lagi');
                            } else {
                                $sisa_waktu = $interval->format('%i Menit lagi');
                            }
                        }
                ?>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition group">
                    <div class="relative h-48 bg-gray-200">
                        <img src="uploads/food_images/<?= $row['foto_path'] ?>" alt="<?= $row['judul'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        
                        <span class="absolute top-3 right-3 bg-white/90 backdrop-blur text-green-700 text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                            Sisa: <?= $row['stok_tersedia'] ?>
                        </span>

                        <?php if($row['jenis_makanan'] == 'halal'): ?>
                            <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                <i class="bi bi-patch-check-fill"></i> Halal
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-5">
                        <h3 class="text-lg font-bold text-gray-800 mb-1 truncate"><?= $row['judul'] ?></h3>
                        <p class="text-sm text-gray-500 mb-4 flex items-center gap-1">
                            <i class="bi bi-person-circle"></i> <?= $row['donatur'] ?>
                        </p>
                        
                        <div class="flex justify-between items-center text-sm text-gray-600 mb-4 bg-gray-50 p-3 rounded-lg">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Batas Waktu</span>
                                <span class="font-semibold text-red-500"><?= $sisa_waktu ?></span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-xs text-gray-400">Lokasi</span>
                                <span class="font-semibold truncate w-24 block text-right"><?= $row['lokasi_pickup'] ?></span>
                            </div>
                        </div>

                        <a href="food_detail.php?id=<?= $row['id'] ?>" class="block w-full bg-green-50 text-green-700 text-center py-2.5 rounded-lg font-bold hover:bg-green-600 hover:text-white transition border border-green-200">
                            Lihat Detail
                        </a>
                    </div>
                </div>

                <?php 
                    endwhile; 
                else: 
                ?>
                    <div class="col-span-full text-center py-12">
                        <img src="assets/images/empty_state.svg" alt="Kosong" class="w-48 h-48 mx-auto opacity-50 mb-4">
                        <h3 class="text-xl font-bold text-gray-600">Yah, makanan tidak ditemukan :(</h3>
                        <p class="text-gray-500">Coba ganti kata kunci atau filter lainnya.</p>
                        <a href="catalog.php" class="inline-block mt-4 text-green-600 font-semibold hover:underline">Reset Filter</a>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>