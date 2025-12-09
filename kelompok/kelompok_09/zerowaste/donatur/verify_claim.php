<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

$donatur_id = (int)$_SESSION['user_id'];

$log_sql = "SELECT 
                c.kode_tiket,
                c.updated_at as waktu_ambil,
                u.nama_lengkap as nama_mahasiswa,
                fs.judul as nama_makanan
            FROM claims c
            INNER JOIN users u ON c.mahasiswa_id = u.id
            INNER JOIN food_stocks fs ON c.food_id = fs.id
            WHERE fs.donatur_id = ? 
            AND c.status = 'diambil'
            AND DATE(c.updated_at) = CURDATE()
            ORDER BY c.updated_at DESC
            LIMIT 10";

$log_stmt = $conn->prepare($log_sql);
$log_stmt->bind_param('i', $donatur_id);
$log_stmt->execute();
$log_result = $log_stmt->get_result();

include '../includes/header.php';
include '../includes/navbar_dashboard.php';
?>

<div class="flex pt-16 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex flex-col w-full md:ml-64 transition-all duration-300">
        <main class="flex-grow p-4 sm:p-6 lg:p-8">
            <div class="max-w-6xl mx-auto space-y-6">
                
                
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Verifikasi Pengambilan</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Scan dan verifikasi tiket mahasiswa</p>
                    </div>
                </div>

               
                <?php if (isset($_GET['status'])): ?>
                    <?php if ($_GET['status'] === 'success'): ?>
                        <div class="animate-fade-in bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm text-green-700 font-medium">✅ Tiket berhasil diverifikasi. Makanan telah diserahkan ke mahasiswa.</p>
                            </div>
                        </div>
                    <?php elseif ($_GET['status'] === 'error'): ?>
                        <div class="animate-fade-in bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm text-red-700 font-medium"><?= htmlspecialchars($_GET['msg'] ?? 'Terjadi kesalahan saat verifikasi.'); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                        <h2 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            Input Kode Tiket
                        </h2>
                    </div>

                    <div class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                Masukkan Kode Tiket
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-3">
                                <input 
                                    type="text" 
                                    id="kode_tiket" 
                                    placeholder="Contoh: FR-ABCD123"
                                    autofocus
                                    autocomplete="off"
                                    class="flex-1 uppercase rounded-xl border-2 border-gray-200 px-4 py-3 text-sm font-mono font-bold
                                           focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10
                                           hover:border-gray-300 transition-all duration-200
                                           bg-gray-50 focus:bg-white"
                                    style="text-transform: uppercase;"
                                >
                                <button 
                                    type="button"
                                    onclick="cekKodeTiket()"
                                    class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-semibold shadow-lg shadow-emerald-500/30
                                           hover:from-emerald-700 hover:to-emerald-800 hover:shadow-xl hover:shadow-emerald-500/40
                                           focus:outline-none focus:ring-4 focus:ring-emerald-500/50
                                           active:scale-[0.98] transition-all duration-200
                                           flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Cek Kode
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Klik "Cek Kode" untuk memvalidasi tiket sebelum verifikasi
                            </p>
                        </div>

                        <div id="loading" class="hidden text-center py-8">
                            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600"></div>
                            <p class="text-sm text-gray-600 mt-3">Memvalidasi tiket...</p>
                        </div>

                        <div id="preview_card" class="hidden">
                        </div>

                        <form id="form_konfirmasi" action="../actions/proses_verify.php" method="POST" class="hidden">
                            <input type="hidden" name="kode_tiket" id="kode_tiket_confirm">
                            <input type="hidden" name="claim_id" id="claim_id_confirm">
                            
                            <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 mb-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="text-xs text-yellow-800">
                                        <p class="font-semibold mb-1">⚠️ Konfirmasi Penyerahan Makanan</p>
                                        <p>Pastikan mahasiswa benar-benar hadir sebelum klik tombol di bawah. Setelah diverifikasi, tiket tidak bisa digunakan lagi.</p>
                                    </div>
                                </div>
                            </div>

                            <button 
                                type="submit"
                                class="w-full px-6 py-4 rounded-xl bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-semibold shadow-lg shadow-green-500/30
                                       hover:from-green-700 hover:to-green-800 hover:shadow-xl hover:shadow-green-500/40
                                       focus:outline-none focus:ring-4 focus:ring-green-500/50
                                       active:scale-[0.98] transition-all duration-200
                                       flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Verifikasi & Serahkan Makanan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Daily Transaction Log -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Riwayat Penukaran Hari Ini
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Daftar tiket yang telah diverifikasi hari ini</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase">Kode Tiket</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase">Mahasiswa</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase">Makanan</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase">Jumlah</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase">Waktu Ambil</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            <?php if ($log_result->num_rows > 0): ?>
                                <?php while ($row = $log_result->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-3 px-4">
                                            <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                                <?= htmlspecialchars($row['kode_tiket']); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($row['nama_mahasiswa']); ?>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-700">
                                            <?= htmlspecialchars($row['nama_makanan']); ?>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-700">
                                            1 porsi
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            <?= date('H:i', strtotime($row['waktu_ambil'])); ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-8 text-center">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-sm text-gray-500">Belum ada penukaran hari ini</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
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


function cekKodeTiket() {
    const kodeInput = document.getElementById('kode_tiket');
    const kode = kodeInput.value.trim().toUpperCase();
    
    if (!kode) {
        alert('Masukkan kode tiket terlebih dahulu!');
        kodeInput.focus();
        return;
    }


    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('preview_card').classList.add('hidden');
    document.getElementById('form_konfirmasi').classList.add('hidden');

    fetch('../actions/check_ticket.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'kode_tiket=' + encodeURIComponent(kode)
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading').classList.add('hidden');
        
        if (data.success) {
            showPreview(data.ticket);
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        document.getElementById('loading').classList.add('hidden');
        showError('Terjadi kesalahan koneksi. Coba lagi.');
        console.error('Error:', error);
    });
}

function showPreview(ticket) {
    const previewCard = document.getElementById('preview_card');
    const statusClass = ticket.status === 'diambil' ? 'red' : 'green';
    const statusIcon = ticket.status === 'diambil' ? '❌' : '✅';
    const statusText = ticket.status === 'diambil' ? 'SUDAH TERPAKAI' : 'VALID';
    
    previewCard.innerHTML = `
        <div class="bg-${statusClass}-50 border-2 border-${statusClass}-500 rounded-xl p-6 shadow-lg">
            <div class="flex items-start justify-between mb-4">
                <h3 class="text-lg font-bold text-${statusClass}-900">${statusIcon} Tiket ${statusText}</h3>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-${statusClass}-600 text-white">
                    ${ticket.status.toUpperCase()}
                </span>
            </div>
            
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Nama Mahasiswa</p>
                        <p class="font-semibold text-gray-900">${ticket.nama_mahasiswa}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Menu Makanan</p>
                        <p class="font-semibold text-gray-900">${ticket.nama_makanan}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Jumlah</p>
                        <p class="font-semibold text-gray-900">${ticket.jumlah_diminta} porsi</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Kode Tiket</p>
                        <p class="font-mono text-sm font-bold text-blue-600">${ticket.kode_tiket}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    previewCard.classList.remove('hidden');
    
    if (ticket.status === 'pending') {
        document.getElementById('kode_tiket_confirm').value = ticket.kode_tiket;
        document.getElementById('claim_id_confirm').value = ticket.id;
        document.getElementById('form_konfirmasi').classList.remove('hidden');
    }
}

function showError(message) {
    const previewCard = document.getElementById('preview_card');
    previewCard.innerHTML = `
        <div class="bg-red-50 border-2 border-red-500 rounded-xl p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-red-900 mb-1">❌ Tiket Tidak Valid</h3>
                    <p class="text-sm text-red-700">${message}</p>
                </div>
            </div>
        </div>
    `;
    previewCard.classList.remove('hidden');
    document.getElementById('form_konfirmasi').classList.add('hidden');
}

setTimeout(() => {
    const alerts = document.querySelectorAll('.animate-fade-in');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease-out';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);

document.getElementById('kode_tiket').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        cekKodeTiket();
    }
});
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>

<?php
$conn->close();
?>