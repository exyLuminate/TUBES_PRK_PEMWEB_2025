<?php 
session_start();
include 'includes/header.php';
include 'includes/navbar.php';
?>

<section id="hero" class="min-h-screen flex items-center pt-32 pb-20 bg-gradient-to-br from-green-50 via-white to-slate-100">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1">
                <span class="inline-flex items-center py-1.5 px-3 rounded-full bg-green-100 text-green-700 text-sm font-bold mb-6 gap-2">
                    <i class="bi bi-globe-americas"></i> #StopFoodWaste Kampus
                </span>
                <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 mb-6 leading-tight">
                    Berbagi Makanan,<br> <span class="text-primary">Kurangi Limbah.</span>
                </h1>
                <p class="text-lg text-slate-600 mb-8 max-w-lg">
                    Platform penghubung donatur makanan berlebih dengan mahasiswa. Gratis, transparan, dan berdampak nyata bagi lingkungan.
                </p>
                <div class="flex gap-3 flex-wrap">
                    <a href="catalog.php" class="bg-primary text-white px-8 py-3.5 rounded-xl hover:bg-green-700 font-bold shadow-lg hover:-translate-y-1 transition-all flex items-center gap-2">
                        Cari Makanan <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="register.php" class="bg-white text-slate-700 px-8 py-3.5 rounded-xl hover:bg-gray-50 font-bold border border-slate-200 hover:border-primary transition-all flex items-center gap-2">
                        Jadi Donatur <i class="bi bi-heart-fill text-red-500"></i>
                    </a>
                </div>
                
                <div class="mt-10 flex items-center gap-4 text-sm font-medium text-slate-500">
                    <div class="flex -space-x-3">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=33" alt="Mhs">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=12" alt="Mhs">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=59" alt="Mhs">
                    </div>
                    <p><span class="text-slate-900 font-bold">100+</span> Makanan terselamatkan bulan ini.</p>
                </div>
            </div>
            <div class="order-1 lg:order-2 relative">
                <div class="absolute inset-0 bg-primary blur-[120px] opacity-20 rounded-full"></div>
                <img src="https://www.culinaryhill.com/wp-content/uploads/2022/11/Food-Donations-Culinary-Hill-1200x800-1.jpg" alt="Ilustrasi Berbagi" class="relative rounded-3xl shadow-2xl border-4 border-white transform rotate-2 hover:rotate-0 transition-all duration-500 w-full object-cover h-[500px]">
            </div>
        </div>
    </div>
</section>

<section id="how-it-works" class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Cara Kerja ZeroWaste</h2>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">Sistem kami dirancang agar makanan cepat sampai ke yang membutuhkan sebelum basi.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="p-6">
                <div class="w-16 h-16 bg-green-100 text-primary rounded-2xl text-2xl font-bold flex items-center justify-center mx-auto mb-6">1</div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Donatur Posting</h3>
                <p class="text-slate-600">Punya sisa makanan event? Foto dan upload ke aplikasi dalam hitungan detik.</p>
            </div>
            <div class="p-6">
                <div class="w-16 h-16 bg-green-100 text-primary rounded-2xl text-2xl font-bold flex items-center justify-center mx-auto mb-6">2</div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Mahasiswa Klaim</h3>
                <p class="text-slate-600">Mahasiswa memilih makanan di katalog dan mendapatkan Tiket Kode Unik.</p>
            </div>
            <div class="p-6">
                <div class="w-16 h-16 bg-green-100 text-primary rounded-2xl text-2xl font-bold flex items-center justify-center mx-auto mb-6">3</div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Ambil di Lokasi</h3>
                <p class="text-slate-600">Datang ke lokasi donatur, tunjukkan kode, dan selamatkan makanannya!</p>
            </div>
        </div>
    </div>
</section>

<section id="features" class="py-24 bg-slate-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <span class="text-primary font-bold uppercase tracking-wider text-sm">Kenapa ZeroWaste?</span>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mt-3">Solusi Menang-Menang</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-8 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl transition-all hover:-translate-y-2">
                <div class="text-4xl mb-4 text-green-500">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-slate-900">Update Real-time</h3>
                <p class="text-slate-600">Stok makanan berkurang otomatis saat diklaim. Tidak ada PHP (Pemberi Harapan Palsu) stok habis.</p>
            </div>
            <div class="p-8 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl transition-all hover:-translate-y-2">
                <div class="text-4xl mb-4 text-blue-500">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-slate-900">Validasi Kode</h3>
                <p class="text-slate-600">Sistem tiket unik memastikan makanan diambil oleh orang yang benar-benar booking.</p>
            </div>
            <div class="p-8 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl transition-all hover:-translate-y-2">
                <div class="text-4xl mb-4 text-yellow-500">
                    <i class="bi bi-gift-fill"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-slate-900">100% Gratis</h3>
                <p class="text-slate-600">Tidak ada biaya sepeserpun. Murni gerakan sosial untuk kesejahteraan mahasiswa.</p>
            </div>
        </div>
    </div>
</section>

<section id="faq" class="py-24 bg-white">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="text-center mb-16">
            <span class="text-primary font-bold uppercase tracking-wider text-sm">Pusat Bantuan</span>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mt-3">Pertanyaan Umum</h2>
            <p class="text-slate-600 mt-4">Bingung cara kerjanya? Temukan jawaban Anda di sini.</p>
        </div>

        <div class="space-y-4">
            
            <details class="bg-slate-50 p-6 rounded-2xl group cursor-pointer border border-transparent hover:border-green-100 transition-all open:bg-white open:shadow-md open:border-green-100">
                <summary class="font-bold text-slate-900 flex justify-between items-center list-none">
                    <span>Apakah saya harus membayar untuk mengambil makanan?</span>
                    <span class="transition-transform duration-300 group-open:rotate-180 text-primary">
                        <i class="bi bi-chevron-down"></i>
                    </span>
                </summary>
                <p class="text-slate-600 mt-4 leading-relaxed">
                    Tidak sama sekali! Semua makanan di ZeroWaste 100% GRATIS. Tujuan kami adalah menyelamatkan makanan agar tidak terbuang sia-sia.
                </p>
            </details>

            <details class="bg-slate-50 p-6 rounded-2xl group cursor-pointer border border-transparent hover:border-green-100 transition-all open:bg-white open:shadow-md open:border-green-100">
                <summary class="font-bold text-slate-900 flex justify-between items-center list-none">
                    <span>Siapa saja yang boleh menjadi Donatur?</span>
                    <span class="transition-transform duration-300 group-open:rotate-180 text-primary">
                        <i class="bi bi-chevron-down"></i>
                    </span>
                </summary>
                <p class="text-slate-600 mt-4 leading-relaxed">
                    Seluruh civitas akademika! Mulai dari Dosen, Organisasi Mahasiswa (Hima/UKM) yang memiliki sisa konsumsi acara, hingga Pemilik Kantin yang ingin berbagi berkah. Cukup daftar sebagai Donatur.
                </p>
            </details>

            <details class="bg-slate-50 p-6 rounded-2xl group cursor-pointer border border-transparent hover:border-green-100 transition-all open:bg-white open:shadow-md open:border-green-100">
                <summary class="font-bold text-slate-900 flex justify-between items-center list-none">
                    <span>Apakah ada batasan jumlah makanan yang bisa saya ambil?</span>
                    <span class="transition-transform duration-300 group-open:rotate-180 text-primary">
                        <i class="bi bi-chevron-down"></i>
                    </span>
                </summary>
                <p class="text-slate-600 mt-4 leading-relaxed">
Ya, demi pemerataan, setiap mahasiswa dibatasi maksimal 2 klaim per hari Jika jatah hari ini habis, Anda baru bisa klaim lagi besok. Kuota harian hanya akan kembali jika Anda membatalkan klaim yang belum diambil.                </p>
            </details>

            <details class="bg-slate-50 p-6 rounded-2xl group cursor-pointer border border-transparent hover:border-green-100 transition-all open:bg-white open:shadow-md open:border-green-100">
                <summary class="font-bold text-slate-900 flex justify-between items-center list-none">
                    <span>Apa yang terjadi jika saya sudah klaim tapi tidak datang?</span>
                    <span class="transition-transform duration-300 group-open:rotate-180 text-primary">
                        <i class="bi bi-chevron-down"></i>
                    </span>
                </summary>
                <p class="text-slate-600 mt-4 leading-relaxed">
                    Tiket Anda akan hangus (expired) setelah melewati batas waktu pengambilan. Sistem akan otomatis mengembalikan stok makanan agar bisa diselamatkan oleh mahasiswa lain.
                </p>
            </details>

            <details class="bg-slate-50 p-6 rounded-2xl group cursor-pointer border border-transparent hover:border-green-100 transition-all open:bg-white open:shadow-md open:border-green-100">
                <summary class="font-bold text-slate-900 flex justify-between items-center list-none">
                    <span>Apakah makanan dijamin Halal dan Higienis?</span>
                    <span class="transition-transform duration-300 group-open:rotate-180 text-primary">
                        <i class="bi bi-chevron-down"></i>
                    </span>
                </summary>
                <p class="text-slate-600 mt-4 leading-relaxed">
                    Donatur wajib mencantumkan status Halal/Non-Halal pada setiap postingan. Meskipun kami memverifikasi donatur, tanggung jawab kualitas makanan tetap pada penyedia. Kami sarankan untuk cek kondisi fisik saat pengambilan.
                </p>
            </details>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>