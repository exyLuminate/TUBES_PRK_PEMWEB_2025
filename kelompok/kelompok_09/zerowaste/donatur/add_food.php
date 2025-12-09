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

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$old_judul = e($_SESSION['old_judul'] ?? '');
$old_category_id = e($_SESSION['old_category_id'] ?? '');
$old_deskripsi = e($_SESSION['old_deskripsi'] ?? '');
$old_jumlah_awal = e($_SESSION['old_jumlah_awal'] ?? '');
$old_jenis_makanan = e($_SESSION['old_jenis_makanan'] ?? 'halal');
$old_lokasi_pickup = e($_SESSION['old_lokasi_pickup'] ?? '');
$old_batas_waktu = e($_SESSION['old_batas_waktu'] ?? '');

unset($_SESSION['old_judul'], $_SESSION['old_category_id'], $_SESSION['old_deskripsi'], 
      $_SESSION['old_jumlah_awal'], $_SESSION['old_jenis_makanan'], $_SESSION['old_lokasi_pickup'], 
      $_SESSION['old_batas_waktu']);

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<div class="flex pt-16 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex flex-col w-full md:ml-64 transition-all duration-300">
        <main class="flex-grow p-4 sm:p-6 lg:p-8">
            <div class="max-w-4xl mx-auto">
                
                <div class="mb-6 sm:mb-8">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tambah Donasi</h1>
                            <p class="text-sm text-gray-500 mt-0.5">Bagikan makanan berlebih Anda kepada yang membutuhkan</p>
                        </div>
                    </div>
                </div>

           
                <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
                    <div class="mb-6 animate-shake">
                        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-red-800 mb-1">Terjadi Kesalahan</h3>
                                    <p class="text-sm text-red-700">
                                        <?= e($_GET['msg'] ?? 'Pastikan semua data sudah diisi dengan benar.'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

               
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="p-6 sm:p-8">
                        <form
                            action="../actions/food_create.php"
                            method="POST"
                            enctype="multipart/form-data"
                            class="space-y-6"
                            id="donationForm"
                        >
                         
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Judul Makanan
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="judul"
                                    required
                                    maxlength="255"
                                    value="<?= $old_judul; ?>"
                                    class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm
                                           focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10
                                           hover:border-gray-300 transition-all duration-200
                                           placeholder:text-gray-400 bg-gray-50 focus:bg-white"
                                    placeholder="Contoh: Nasi kotak ayam, Roti sisa toko"
                                >
                            </div>

                          
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                    </svg>
                                    Kategori
                                    <span class="text-red-500">*</span>
                                </label>
                                <select
                                    name="category_id"
                                    required
                                    class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm bg-gray-50
                                           focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10
                                           hover:border-gray-300 focus:bg-white transition-all duration-200
                                           cursor-pointer"
                                >
                                    <option value="">Pilih Kategori</option>
                                    <?php while ($cat = $categories->fetch_assoc()): ?>
                                        <option value="<?= (int)$cat['id']; ?>" <?= $old_category_id == $cat['id'] ? 'selected' : ''; ?>>
                                            <?= e($cat['nama_kategori']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                      
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                    </svg>
                                    Deskripsi
                                </label>
                                <textarea
                                    name="deskripsi"
                                    rows="4"
                                    maxlength="1000"
                                    class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm resize-y
                                           focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10
                                           hover:border-gray-300 transition-all duration-200
                                           placeholder:text-gray-400 bg-gray-50 focus:bg-white"
                                    placeholder="Jelaskan kondisi makanan, jenis kemasan, dan informasi tambahan lainnya..."
                                ><?= $old_deskripsi; ?></textarea>
                            </div>

                       
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                          
                                <div class="group">
                                    <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                        </svg>
                                        Jumlah Porsi
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="jumlah_awal"
                                        min="1"
                                        max="10000"
                                        required
                                        value="<?= $old_jumlah_awal; ?>"
                                        class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm
                                               focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10
                                               hover:border-gray-300 transition-all duration-200
                                               bg-gray-50 focus:bg-white"
                                        placeholder="Contoh: 20"
                                    >
                                </div>

                             
                                <div class="group">
                                    <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Jenis Makanan
                                    </label>
                                    <select
                                        name="jenis_makanan"
                                        class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm bg-gray-50
                                               focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10
                                               hover:border-gray-300 focus:bg-white transition-all duration-200
                                               cursor-pointer"
                                    >
                                        <option value="halal" <?= $old_jenis_makanan === 'halal' ? 'selected' : ''; ?>>Halal</option>
                                        <option value="non_halal" <?= $old_jenis_makanan === 'non_halal' ? 'selected' : ''; ?>>Non Halal</option>
                                    </select>
                                </div>
                            </div>

                         
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Lokasi Pickup
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    name="lokasi_pickup"
                                    rows="3"
                                    required
                                    maxlength="500"
                                    class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm resize-y
                                           focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10
                                           hover:border-gray-300 transition-all duration-200
                                           placeholder:text-gray-400 bg-gray-50 focus:bg-white"
                                    placeholder="Alamat lengkap dan keterangan waktu terbaik untuk pickup..."
                                ><?= $old_lokasi_pickup; ?></textarea>
                            </div>

                    
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Batas Waktu Pengambilan
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    name="batas_waktu"
                                    required
                                    value="<?= $old_batas_waktu; ?>"
                                    class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm
                                           focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10
                                           hover:border-gray-300 transition-all duration-200
                                           bg-gray-50 focus:bg-white cursor-pointer"
                                >
                            </div>

                       
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Foto Makanan
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="file"
                                        name="foto"
                                        accept="image/jpeg,image/jpg,image/png,image/webp"
                                        required
                                        id="fileInput"
                                        class="hidden"
                                    >
                                    <label
                                        for="fileInput"
                                        class="flex items-center justify-center w-full px-4 py-8 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer
                                               bg-gray-50 hover:bg-gray-100 hover:border-emerald-400 transition-all duration-200
                                               focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/10"
                                    >
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <p class="text-sm text-gray-600 font-medium mb-1">
                                                <span class="text-emerald-600 hover:text-emerald-700">Klik untuk upload</span> atau drag & drop
                                            </p>
                                            <p class="text-xs text-gray-500">JPG, JPEG, PNG, WEBP (Maks. 5MB)</p>
                                            <p id="fileName" class="text-xs text-emerald-600 font-medium mt-2 hidden"></p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                     
                            <div class="pt-4 flex flex-col sm:flex-row gap-3 border-t border-gray-100">
                                <button
                                    type="submit"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl
                                           bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-semibold shadow-lg shadow-emerald-500/30
                                           hover:from-emerald-700 hover:to-emerald-800 hover:shadow-xl hover:shadow-emerald-500/40
                                           focus:outline-none focus:ring-4 focus:ring-emerald-500/50
                                           active:scale-[0.98] transition-all duration-200"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Simpan Donasi
                                </button>

                                <a
                                    href="manage_food.php"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl
                                           border-2 border-gray-300 text-sm font-semibold text-gray-700 bg-white
                                           hover:bg-gray-50 hover:border-gray-400
                                           focus:outline-none focus:ring-4 focus:ring-gray-300/50
                                           active:scale-[0.98] transition-all duration-200"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="mt-6 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-4 border border-emerald-200">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-emerald-900 mb-1">Tips Donasi</h4>
                            <p class="text-xs text-emerald-800 leading-relaxed">
                                Pastikan makanan masih dalam kondisi baik dan layak konsumsi. Sertakan foto yang jelas dan informasi lengkap agar penerima dapat mempersiapkan pengambilan dengan baik.
                            </p>
                        </div>
                    </div>
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

const fileInput = document.getElementById('fileInput');
const fileName = document.getElementById('fileName');

if (fileInput && fileName) {
    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const maxSize = 5 * 1024 * 1024;
            
            if (file.size > maxSize) {
                alert('Ukuran file terlalu besar! Maksimal 5MB.');
                this.value = '';
                fileName.textContent = '';
                fileName.classList.add('hidden');
                return;
            }
            
            fileName.textContent = '✓ ' + file.name;
            fileName.classList.remove('hidden');
        } else {
            fileName.textContent = '';
            fileName.classList.add('hidden');
        }
    });
}

const form = document.getElementById('donationForm');
if (form) {
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
                field.classList.remove('border-gray-200');
            } else {
                field.classList.remove('border-red-500');
                field.classList.add('border-gray-200');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi (*)');
        }
    });
}

const batasWaktuInput = document.querySelector('input[name="batas_waktu"]');
if (batasWaktuInput) {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    batasWaktuInput.min = now.toISOString().slice(0, 16);
}
</script>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}
</style>

<?php
$conn->close();
?>