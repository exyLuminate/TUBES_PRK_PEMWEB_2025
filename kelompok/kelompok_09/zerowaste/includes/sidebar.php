<aside id="sidebar" class="fixed left-0 top-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 md:translate-x-0">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
        <ul class="space-y-2 font-medium">
            
            <?php 
            // Ambil role dari session, default ke 'guest' jika belum login
            $role = $_SESSION['role'] ?? 'guest'; 
            ?>

            <?php if($role == 'admin'): ?>
                <li>
                    <a href="../admin/dashboard.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-50 group">
                        <span class="ml-3">Dashboard Admin</span>
                    </a>
                </li>
                <li>
                    <a href="../admin/users_list.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-50 group">
                        <span class="ml-3">Kelola User</span>
                    </a>
                </li>
                <li>
                    <a href="../admin/categories.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-50 group">
                        <span class="ml-3">Kategori Makanan</span>
                    </a>
                </li>
                <li>
                    <a href="../admin/logs.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-50 group">
                        <span class="ml-3">Activity Logs</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if($role == 'donatur'): ?>
                <li>
                    <a href="../donatur/dashboard.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-50 group">
                        <span class="ml-3">Dashboard Donatur</span>
                    </a>
                </li>
                <li>
                    <a href="../donatur/add_food.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-50 group">
                        <span class="ml-3">Tambah Makanan</span>
                    </a>
                </li>
                <li>
                    <a href="../donatur/manage_food.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-50 group">
                        <span class="ml-3">Kelola Makanan</span>
                    </a>
                </li>
                <li>
                    <a href="../donatur/verify_claim.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-50 group">
                        <span class="ml-3">Verifikasi Tiket</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if($role == 'mahasiswa'): ?>
                <li>
                    <a href="../mahasiswa/dashboard.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-50 group">
                        <span class="ml-3">Dashboard Saya</span>
                    </a>
                </li>
                <li>
                    <a href="../catalog.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-50 group">
                        <span class="ml-3">Cari Makanan</span>
                    </a>
                </li>
                <li>
                    <a href="../mahasiswa/my_tickets.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-50 group">
                        <span class="ml-3">Tiket Saya</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="border-t mt-4 pt-4">
                <a href="../actions/auth_logout.php" class="flex items-center p-2 text-red-600 rounded-lg hover:bg-red-50 group">
                    <span class="ml-3">Logout</span>
                </a>
            </li>

        </ul>
    </div>
</aside>