<?php 
session_start();
include 'config/database.php'; 
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container mx-auto px-4 py-8 min-h-screen pt-24 md:pt-32">
    
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
                
                <form id="filterForm">
                    
                    <div class="mb-6">
                        <label class="text-sm font-medium text-gray-700 mb-2 block">Cari Nama</label>
                        <div class="relative">
                            <input type="text" name="q" class="w-full pl-3 pr-10 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none" placeholder="Nasi, Roti...">
                            <button type="submit" class="absolute right-2 top-2 text-gray-400 hover:text-green-600">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="text-sm font-medium text-gray-700 mb-2 block">Kategori</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            <?php 
                            $cats = mysqli_query($conn, "SELECT * FROM categories");
                            while($c = mysqli_fetch_assoc($cats)): 
                            ?>
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                <input type="checkbox" name="cat[]" value="<?= $c['id'] ?>" class="rounded text-green-600 focus:ring-green-500 w-4 h-4">
                                <span class="text-gray-600 text-sm"><?= $c['nama_kategori'] ?></span>
                            </label>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="text-sm font-medium text-gray-700 mb-2 block">Jenis Makanan</label>
                        <select name="jenis" class="w-full px-4 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-green-500">
                            <option value="">Semua</option>
                            <option value="halal">Halal</option>
                            <option value="non_halal">Non-Halal</option>
                        </select>
                    </div>
                    
                    <button type="reset" onclick="window.location.reload()" class="w-full mt-2 text-sm text-gray-500 hover:text-red-500 py-1">
                        Reset Filter
                    </button>
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
                          AND fs.batas_waktu > NOW() 
                          ORDER BY fs.created_at DESC";
                
                $result = mysqli_query($conn, $sql);

                if(mysqli_num_rows($result) > 0):
                    while($row = mysqli_fetch_assoc($result)):
                        
                        $batas = new DateTime($row['batas_waktu']);
                        $sekarang = new DateTime();
                        
                        if ($sekarang > $batas) {
                            $sisa_waktu = 'Expired';
                            $warna_waktu = 'text-gray-400';
                        } else {
                            $interval = $sekarang->diff($batas);
                            
                            if ($interval->d > 0) {
                                $sisa_waktu = $interval->d . ' Hari lagi';
                                $warna_waktu = 'text-green-600';
                            } elseif ($interval->h > 0) {
                                $sisa_waktu = $interval->h . ' Jam lagi';
                                $warna_waktu = 'text-orange-500';
                            } else {
                                $sisa_waktu = $interval->i . ' Menit lagi';
                                $warna_waktu = 'text-red-600'; 
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
                                <span class="font-semibold <?= $warna_waktu ?>"><?= $sisa_waktu ?></span>
                            </div>
                            <div class="flex flex-col text-right"><span class="text-xs text-gray-400">Lokasi</span><span class="font-semibold truncate w-24 block text-right"><?= $row['lokasi_pickup'] ?></span></div>
                        </div>
                        <a href="food_detail.php?id=<?= $row['id'] ?>" class="block w-full bg-green-50 text-green-700 text-center py-2.5 rounded-lg font-bold hover:bg-green-600 hover:text-white transition border border-green-200">Lihat Detail</a>
                    </div>
                </div>

                <?php 
                    endwhile; 
                else: 
                ?>
                    <div class="col-span-full text-center py-12">
                        <img src="assets/images/empty_state.svg" alt="Kosong" class="w-48 h-48 mx-auto opacity-50 mb-4">
                        <h3 class="text-xl font-bold text-gray-600">Belum ada makanan tersedia</h3>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</div>

<script src="assets/js/ajax_search.js"></script>

<?php include 'includes/footer.php'; ?>