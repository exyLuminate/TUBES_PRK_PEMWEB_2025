document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById('filterForm');
    const container = document.getElementById('foodContainer');

    // Fungsi Utama: Ambil Data dari Server
    const loadData = () => {
        // 1. Ambil semua data di form (Otomatis handle checkbox array & select)
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();

        // 2. Tampilkan efek loading (Opsional, biar user tau sistem bekerja)
        container.style.opacity = '0.5'; 

        // 3. Request ke API PHP
        fetch(`actions/api_search.php?${params}`)
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
                container.style.opacity = '1'; // Balikin opacity
            })
            .catch(err => console.error('Error:', err));
    };

    // --- EVENT LISTENER ---

    // 1. Agar Tombol "Cari" tidak reload halaman
    form.addEventListener('submit', (e) => {
        e.preventDefault(); // Mencegah refresh
        loadData(); // Panggil AJAX
    });

    // 2. Deteksi ketikan di Search Bar (Live Typing)
    const searchInput = form.querySelector('input[name="q"]');
    if (searchInput) {
        searchInput.addEventListener('keyup', loadData);
    }

    // 3. Deteksi perubahan pada Checkbox (Kategori) & Select (Jenis)
    // Kita ambil semua input selain text
    const filters = form.querySelectorAll('input[type="checkbox"], select');
    filters.forEach(input => {
        input.addEventListener('change', () => {
            loadData(); // Langsung update saat diklik/diganti
        });
    });
    
    // Load pertama kali saat halaman dibuka
    loadData();
});