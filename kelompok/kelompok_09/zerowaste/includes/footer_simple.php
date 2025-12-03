<footer class="bg-white border-t border-gray-200 mt-auto py-4">
    <div class="container mx-auto px-6 text-center text-sm text-gray-500">
        &copy; <?= date('Y') ?> ZeroWaste App. 
        <span class="hidden md:inline"> | Logged in as <strong><?= $_SESSION['nama_lengkap'] ?? 'User' ?></strong></span>
    </div>
</footer>

<script src="../assets/js/script.js"></script>
</body>
</html>