<?php  
session_start();
include 'includes/header.php';
?>

<!-- STEP 1: Pilih Role -->
<section class="min-h-screen flex items-center justify-center px-4 bg-gradient-to-br from-green-50 via-white to-slate-100">
  <div class="bg-white p-10 rounded-3xl shadow-xl w-full max-w-md text-center">

    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
      <i class="bi bi-person-plus-fill text-white text-3xl"></i>
    </div>

    <h1 class="text-3xl font-bold mb-2">Daftar Sebagai</h1>
    <p class="text-slate-600 mb-8">Pilih peran Anda terlebih dahulu</p>

    <div class="grid grid-cols-2 gap-4">
      <button type="button" onclick="pilihRole('mahasiswa')" class="p-6 border-2 rounded-2xl hover:border-primary transition text-center">
        <i class="bi bi-backpack text-4xl text-primary mb-2"></i>
        <p class="font-bold">Mahasiswa</p>
        <p class="text-xs text-slate-500">Penerima Makanan</p>
      </button>

      <button type="button" onclick="pilihRole('donatur')" class="p-6 border-2 rounded-2xl hover:border-primary transition text-center">
        <i class="bi bi-heart-fill text-4xl text-primary mb-2"></i>
        <p class="font-bold">Donatur</p>
        <p class="text-xs text-slate-500">Pemberi Makanan</p>
      </button>
    </div>

    <p class="text-sm mt-6 text-slate-600">
      Sudah punya akun? <a href="login.php" class="text-primary font-bold">Login</a>
    </p>

  </div>
</section>

<!-- STEP 2: Form Registrasi -->
<section id="formRegister" class="hidden min-h-screen flex items-center justify-center px-4 bg-slate-100">
  <div class="bg-white p-10 rounded-3xl shadow-xl w-full max-w-lg">
    <h2 class="text-2xl font-bold text-center mb-6" id="judulRole"></h2>

    <!-- Alert Error -->
    <?php if(isset($_SESSION['error'])): ?>
    <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded">
      <p class="text-red-700 text-sm font-medium"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    </div>
    <?php endif; ?>

    <form action="process_register.php" method="POST" class="space-y-4">

      <input type="hidden" name="role" id="roleInput">

      <input type="text" name="nama" required placeholder="Nama Lengkap"
        class="w-full p-3 border rounded-xl">

      <input type="email" name="email" required placeholder="Email"
        class="w-full p-3 border rounded-xl">

      <div id="nimField" class="block">
        <input type="text" name="nim" placeholder="NIM" class="w-full p-3 border rounded-xl">
      </div>

      <input type="tel" name="whatsapp" required placeholder="No WhatsApp"
        class="w-full p-3 border rounded-xl">

      <input type="password" name="password" required placeholder="Password"
        class="w-full p-3 border rounded-xl">

      <input type="password" name="confirm_password" required placeholder="Konfirmasi Password"
        class="w-full p-3 border rounded-xl">

      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="terms" required class="w-4 h-4 text-primary border rounded">
        Saya setuju dengan <a href="terms.php" class="text-primary font-bold">Syarat & Ketentuan</a> dan <a href="privacy.php" class="text-primary font-bold">Kebijakan Privasi</a>
      </label>

      <button type="submit"
        class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:bg-green-700 transition-all">
        Daftar Sekarang
      </button>
    </form>
  </div>
</section>

<script>
// Toggle Role dan Form
function pilihRole(role) {
  document.getElementById('formRegister').classList.remove('hidden');
  document.getElementById('roleInput').value = role;

  const nimField = document.getElementById('nimField');

  if(role === 'mahasiswa'){
    document.getElementById('judulRole').innerText = "Daftar sebagai Mahasiswa";
    nimField.classList.remove('hidden');
  } else {
    document.getElementById('judulRole').innerText = "Daftar sebagai Donatur";
    nimField.classList.add('hidden');
  }

  // Scroll ke form
  window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
}
</script>

<?php include 'includes/footer_simple.php'; ?>