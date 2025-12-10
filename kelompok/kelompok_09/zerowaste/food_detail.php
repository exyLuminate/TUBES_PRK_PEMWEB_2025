<?php 
session_start();
include 'config/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Makanan tidak ditemukan!'); window.location='catalog.php';</script>";
    exit();
}

$id = intval($_GET['id']); 

$query = "SELECT fs.*, u.nama_lengkap as donatur, u.no_hp 
          FROM food_stocks fs 
          JOIN users u ON fs.donatur_id = u.id 
          WHERE fs.id = $id AND fs.deleted_at IS NULL";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<div class='container mx-auto py-20 text-center'>
            <h2 class='text-2xl font-bold text-gray-700'>Makanan tidak ditemukan atau sudah dihapus :(</h2>
            <a href='catalog.php' class='text-green-600 hover:underline mt-4 block'>Kembali ke Katalog</a>
          </div>";
    include 'includes/footer.php';
    exit();
}

$batas = new DateTime($data['batas_waktu']);
$sekarang = new DateTime();

if ($sekarang > $batas) {
    $sisa_waktu = 'Sudah Berakhir';
    $is_expired = true;
    $warna_waktu = 'text-gray-500';
} else {
    $is_expired = false;
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

<div class="container mx-auto px-4 py-12 min-h-screen">
    
    <nav class="text-sm mb-6 text-gray-500">
        <a href="index.php" class="hover:text-green-600">Home</a> 
        <span class="mx-2">/</span> 
        <a href="catalog.php" class="hover:text-green-600">Katalog</a>
        <span class="mx-2">/</span> 
        <span class="text-gray-800 font-semibold">Detail Makanan</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            
            <div class="h-96 md:h-full bg-gray-100 relative">
                <img src="uploads/food_images/<?= $data['foto_path'] ?>" alt="<?= $data['judul'] ?>" class="w-full h-full object-cover">
                
                <?php if($data['jenis_makanan'] == 'halal'): ?>
                    <span class="absolute top-4 left-4 bg-green-500 text-white px-4 py-1 rounded-full font-bold shadow-md">
                        <i class="bi bi-patch-check-fill"></i> Halal
                    </span>
                <?php endif; ?>
            </div>

            <div class="p-8 md:p-12 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                            Stok: <?= $data['stok_tersedia'] ?> Porsi
                        </span>
                        <span class="text-sm <?= $warna_waktu ?> font-semibold flex items-center gap-1">
                            <i class="bi bi-clock-history"></i> <?= $sisa_waktu ?>
                        </span>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                        <?= $data['judul'] ?>
                    </h1>

                    <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold">
                            <?= substr($data['donatur'], 0, 1) ?>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Didonasikan oleh</p>
                            <p class="font-bold text-gray-800"><?= $data['donatur'] ?></p>
                        </div>
                    </div>

                    <div class="space-y-4 text-gray-600 mb-8">
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Deskripsi</h3>
                            <p class="leading-relaxed"><?= nl2br($data['deskripsi']) ?></p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-green-600"></i> Lokasi Pengambilan
                            </h3>
                            <p><?= $data['lokasi_pickup'] ?></p>
                        </div>
                    </div>
                </div>

                <div>
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="block w-full bg-gray-800 text-white text-center py-4 rounded-xl font-bold hover:bg-gray-900 transition shadow-lg">
                            Login untuk Mengambil Makanan
                        </a>

                    <?php elseif($_SESSION['role'] != 'mahasiswa'): ?>
                        <button disabled class="block w-full bg-gray-300 text-gray-500 text-center py-4 rounded-xl font-bold cursor-not-allowed">
                            Hanya Mahasiswa yang bisa klaim
                        </button>

                    <?php elseif($data['stok_tersedia'] <= 0 || $is_expired): ?>
                        <button disabled class="block w-full bg-red-100 text-red-500 text-center py-4 rounded-xl font-bold cursor-not-allowed">
                            Yah, Makanan Tidak Tersedia :(
                        </button>

                    <?php else: ?>
                        <form action="actions/process_claim.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengklaim makanan ini? Pastikan Anda bisa mengambilnya tepat waktu.');">
                            <input type="hidden" name="food_id" value="<?= $data['id'] ?>">
                            <button type="submit" class="block w-full bg-green-600 text-white text-center py-4 rounded-xl font-bold hover:bg-green-700 transition shadow-lg transform hover:-translate-y-1">
                                Ambil Makanan Sekarang 🍛
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>