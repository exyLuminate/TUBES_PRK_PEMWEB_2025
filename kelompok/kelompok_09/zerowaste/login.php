<?php 
session_start();
include 'includes/header.php';
?>

<section class="min-h-screen flex items-center justify-center py-20 px-4 bg-gradient-to-br from-green-50 via-white to-slate-100">
    <div class="container mx-auto max-w-5xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 rounded-3xl overflow-hidden shadow-2xl bg-white border-4 border-white">
            
            <!-- Left Side - Form Login -->
            <div class="p-10 lg:p-12">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="bi bi-box-arrow-in-right text-white text-3xl"></i>
                    </div>
                    <h1 class="text-4xl font-bold text-slate-900 mb-3">Masuk</h1>
                    <p class="text-slate-600">Gunaka akun Anda untuk melanjutkan</p>
                </div>

                <!-- Alert -->
                <?php if(isset($_SESSION['error'])): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <p class="text-red-700 text-sm font-medium"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                </div>
                <?php endif; ?>

                <?php if(isset($_SESSION['success'])): ?>
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                    <p class="text-green-700 text-sm font-medium"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
                </div>
                <?php endif; ?>

                <!-- Form -->
                <form action="actions/auth_login.php" method="POST" class="space-y-5">
                    
                    <div>
                        <input type="email" name="email" required
                            class="w-full px-5 py-4 bg-slate-100 border-0 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white transition-all text-slate-900"
                            placeholder="Email ">
                    </div>

                    <div>
                        <input type="password" name="password" required
                            class="w-full px-5 py-4 bg-slate-100 border-0 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white transition-all text-slate-900"
                            placeholder="Password">
                    </div>

                    <button type="submit" class="w-full bg-primary text-white py-4 rounded-full font-bold hover:bg-green-700 shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all uppercase tracking-wider">
    Masuk Sekarang
</button>

<!-- Belum punya akun -->
<p class="text-center text-slate-600 mt-4">
    Belum punya akun?
    <a href="register.php" class="text-primary font-bold hover:underline">
        Daftar di sini
    </a>
</p>


                </form>
            </div>

            <!-- Right Side - CTA Gradient -->
            <div class="bg-gradient-to-br from-primary to-green-600 p-10 lg:p-12 flex flex-col items-center justify-center text-white text-center relative">
                <div class="absolute inset-0 bg-primary blur-[100px] opacity-20"></div>
                <div class="relative z-10">
                    <div class="mb-6">
                        <i class="bi bi-heart-fill text-6xl mb-4 animate-pulse"></i>
                    </div>
                    <h2 class="text-4xl font-extrabold mb-4">Halo, Teman!</h2>
                    <p class="text-green-100 mb-8 max-w-xs text-lg">Daftarkan diri Anda dan mulai gunakan layanan kami segera</p>
                    <a href="register.php" class="inline-block border-2 border-white text-white px-10 py-3.5 rounded-full font-bold hover:bg-white hover:text-primary transition-all uppercase tracking-wider shadow-lg">
                        Daftar Sekarang
                    </a>
                    
                    <div class="mt-10 flex items-center justify-center gap-3 text-sm">
                        <div class="flex -space-x-2">
                            <img class="w-8 h-8 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=33" alt="User">
                            <img class="w-8 h-8 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=12" alt="User">
                            <img class="w-8 h-8 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=59" alt="User">
                        </div>
                        <p class="text-green-100"><span class="font-bold text-white">500+</span> Pengguna Aktif</p>
                    </div>
                </div>
            </div>

        </div>
        
        <!-- Back to Home -->
        <div class="text-center mt-8">
            <a href="index.php" class="text-slate-600 hover:text-primary font-medium flex items-center justify-center gap-2 transition-all">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer_simple.php'; ?>