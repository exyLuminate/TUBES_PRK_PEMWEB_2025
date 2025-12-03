<?php
$nama_user = $_SESSION['nama_lengkap'] ?? 'User';
$role_user = ucfirst($_SESSION['role'] ?? 'Guest');
?>

<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            
            <div class="flex items-center justify-start">
                
                <button id="sidebarToggle" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <span class="sr-only">Open sidebar</span>
                    <i class="bi bi-list text-2xl"></i>
                </button>

                <a href="../index.php" class="flex ms-2 md:me-24">
                    <span class="self-center text-xl font-bold sm:text-2xl whitespace-nowrap text-green-600 flex items-center gap-2">
                        <i class="bi bi-recycle"></i> ZeroWaste
                    </span>
                </a>
            </div>

            <div class="flex items-center">
                <div class="flex items-center ms-3">
                    <div class="text-right mr-3 hidden md:block">
                        <p class="text-sm font-semibold text-gray-900"><?= $nama_user ?></p>
                        <p class="text-xs text-gray-500"><?= $role_user ?></p>
                    </div>
                    <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                        <span class="sr-only">Open user menu</span>
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold border border-green-200">
                            <?= substr($nama_user, 0, 1) ?>
                        </div>
                    </button>
                </div>
            </div>

        </div>
    </div>
</nav>