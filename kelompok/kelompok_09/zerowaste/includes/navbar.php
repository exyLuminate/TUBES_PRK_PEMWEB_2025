<?php
$is_logged_in = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';
?>

<nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-sm shadow-sm z-50 py-4">
    <div class="container mx-auto px-4 md:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            
            <div class="flex justify-between items-center">
                <a class="font-extrabold text-2xl tracking-tighter text-primary flex items-center gap-2" href="index.php">
                    <i class="bi bi-recycle"></i> ZeroWaste
                </a>
            </div>

            <ul class="flex flex-col md:flex-row md:items-center gap-y-3 md:gap-y-0 md:gap-x-6 list-none p-0 m-0">
                <li><a class="text-slate-600 font-medium hover:text-primary transition" href="#hero">Beranda</a></li>
                <li><a class="text-slate-600 font-medium hover:text-primary transition" href="#how-it-works">Cara Kerja</a></li>
                <li><a class="text-slate-600 font-medium hover:text-primary transition" href="#features">Misi Kami</a></li>
                <li><a class="text-slate-600 font-medium hover:text-primary transition" href="#faq">FAQ</a></li>
                
                <li class="md:ml-4 mt-2 md:mt-0">
                    <?php if (!$is_logged_in): ?>
                        <div class="flex gap-2">
                            <a href="login.php" class="inline-block border border-primary text-primary rounded-full px-6 py-2 font-bold hover:bg-green-50 transition text-center">
                                Masuk
                            </a>
                            <a href="register.php" class="inline-block bg-primary text-white rounded-full px-6 py-2 font-bold hover:bg-primary-dark shadow-md transition text-center">
                                Daftar
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="<?= $role ?>/dashboard.php" class="inline-block bg-primary text-white rounded-full px-6 py-2 font-bold hover:bg-primary-dark shadow-md transition text-center">
                            Dashboard <?= ucfirst($role) ?>
                        </a>
                    <?php endif; ?>
                </li>
            </ul>

        </div>
    </div>
</nav>