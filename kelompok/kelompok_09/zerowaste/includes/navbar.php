<?php
$is_logged_in = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';
?>

<nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-sm shadow-sm z-50 py-4">
    <div class="container mx-auto px-4 md:px-8">
        <div class="flex items-center justify-between">
            
            <a class="font-extrabold text-2xl tracking-tighter text-primary flex items-center gap-2" href="index.php">
                <i class="bi bi-recycle"></i> ZeroWaste
            </a>

            <button id="mobile-menu-btn" class="md:hidden text-slate-600 focus:outline-none">
                <i class="bi bi-list text-3xl"></i>
            </button>

            <ul class="hidden md:flex flex-row items-center gap-x-6 list-none p-0 m-0">
                <li><a class="text-slate-600 font-medium hover:text-primary transition" href="index.php">Beranda</a></li>
                <li><a class="text-slate-600 font-medium hover:text-primary transition" href="catalog.php">Cari Makanan</a></li>
                <li><a class="text-slate-600 font-medium hover:text-primary transition" href="index.php#how-it-works">Cara Kerja</a></li>
                
                <li class="ml-4">
                    <?php if (!$is_logged_in): ?>
                        <div class="flex gap-2">
                            <a href="login.php" class="border border-primary text-primary rounded-full px-5 py-2 font-bold hover:bg-green-50 transition">Masuk</a>
                            <a href="register.php" class="bg-primary text-white rounded-full px-5 py-2 font-bold hover:bg-green-700 shadow-md transition">Daftar</a>
                        </div>
                    <?php else: ?>
                        <a href="<?= $role ?>/dashboard.php" class="bg-primary text-white rounded-full px-5 py-2 font-bold hover:bg-green-700 shadow-md transition">
                            Dashboard
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>

        <div id="mobile-menu" class="hidden md:hidden mt-4 bg-white border-t border-gray-100 pt-4 pb-4">
            <ul class="flex flex-col gap-4 list-none p-0 m-0 text-center">
                <li><a class="block text-slate-600 font-medium hover:text-primary" href="index.php">Beranda</a></li>
                <li><a class="block text-slate-600 font-medium hover:text-primary" href="catalog.php">Cari Makanan</a></li>
                <li><a class="block text-slate-600 font-medium hover:text-primary" href="index.php#how-it-works">Cara Kerja</a></li>
                <li>
                    <?php if (!$is_logged_in): ?>
                        <a href="login.php" class="block text-primary font-bold">Masuk / Daftar</a>
                    <?php else: ?>
                        <a href="<?= $role ?>/dashboard.php" class="block text-primary font-bold">Ke Dashboard</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');

    btn.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });
</script>