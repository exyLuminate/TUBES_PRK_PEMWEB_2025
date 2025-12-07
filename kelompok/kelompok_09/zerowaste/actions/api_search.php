<?php
require '../config/database.php';


$q = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';
$cat = isset($_GET['cat']) ? $_GET['cat'] : []; 


$sql = "SELECT fs.*, u.nama_lengkap as donatur 
        FROM food_stocks fs 
        JOIN users u ON fs.donatur_id = u.id 
        WHERE fs.deleted_at IS NULL 
        AND fs.stok_tersedia > 0 
        AND fs.batas_waktu > NOW()";


if ($q != '') {
    $sql .= " AND fs.judul LIKE '%$q%'";
}


if ($jenis != '' && $jenis != 'semua') { 
    $sql .= " AND fs.jenis_makanan = '$jenis'";
}


if (!empty($cat)) {
    
    $cats_id = implode(",", array_map('intval', $cat)); 
    if(!empty($cats_id)) {
        $sql .= " AND fs.category_id IN ($cats_id)";
    }
}

$sql .= " ORDER BY fs.created_at DESC";
$result = mysqli_query($conn, $sql);


if(mysqli_num_rows($result) > 0):
    while($row = mysqli_fetch_assoc($result)):
        $batas = new DateTime($row['batas_waktu']);
        $sekarang = new DateTime();
        $interval = $sekarang->diff($batas);
        
        
        if ($sekarang > $batas) {
            $sisa_waktu = "Expired";
        } else {
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
        <h3 class="text-xl font-bold text-gray-600">Tidak ditemukan :(</h3>
        <p class="text-gray-500">Coba ganti kata kunci lain.</p>
    </div>
<?php endif; ?>