<?php 
session_start();

if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: admin/dashboard.php');
            break;
        case 'donatur':
            header('Location: donatur/dashboard.php');
            break;
        case 'mahasiswa':
            header('Location: mahasiswa/dashboard.php');
            break;
        default:
            header('Location: index.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ZeroWaste</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded-full mb-4">
                    <i class="fas fa-leaf text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Selamat Datang</h2>
                <p class="mt-2 text-gray-600">Masuk ke akun ZeroWaste Anda</p>
            </div>

           
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <p><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <p><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            
            <div class="bg-white rounded-lg shadow-lg p-8">
                <form action="actions/auth_login.php" method="POST" id="loginForm" class="space-y-6">
                   
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i> Username
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required
                            maxlength="50"
                            autocomplete="username"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            placeholder="Masukkan username"
                        >
                    </div>

                   
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i> Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                maxlength="255"
                                autocomplete="current-password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition pr-12"
                                placeholder="Masukkan password"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                tabindex="-1"
                            >
                                <i id="toggleIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                  
                    <button 
                        type="submit"
                        id="submitBtn"
                        class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-200 flex items-center justify-center"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk
                    </button>
                </form>

               
                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Belum punya akun? 
                        <a href="register.php" class="text-green-600 font-semibold hover:text-green-700">
                            Daftar Sekarang
                        </a>
                    </p>
                </div>

                
                <div class="mt-4 text-center">
                    <a href="index.php" class="text-gray-500 hover:text-gray-700 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        
        function sanitizeInput(input) {
            const div = document.createElement('div');
            div.textContent = input;
            return div.innerHTML;
        }

        
        const form = document.getElementById('loginForm');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const submitBtn = document.getElementById('submitBtn');

       
        usernameInput.addEventListener('input', function(e) {
            
            let value = e.target.value;
            
            
            value = value.replace(/[<>]/g, '');
            
            
            if (value.length > 50) {
                value = value.substring(0, 50);
            }
            
            e.target.value = value;
        });

       
        usernameInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            
            
            const cleanedText = pastedText.replace(/[<>]/g, '').substring(0, 50);
            
           
            const start = this.selectionStart;
            const end = this.selectionEnd;
            const currentValue = this.value;
            
            this.value = currentValue.substring(0, start) + cleanedText + currentValue.substring(end);
            
            
            const newPos = start + cleanedText.length;
            this.setSelectionRange(newPos, newPos);
        });

        
        function togglePassword() {
            const field = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

      
        form.addEventListener('submit', function(e) {
            const username = usernameInput.value.trim();
            const password = passwordInput.value;
            
            
            if (username.length === 0) {
                e.preventDefault();
                alert('Username tidak boleh kosong');
                usernameInput.focus();
                return false;
            }

            
            if (/<script|javascript:|onerror=|onload=/i.test(username)) {
                e.preventDefault();
                alert('Input tidak valid. Harap masukkan username yang benar.');
                usernameInput.value = '';
                usernameInput.focus();
                return false;
            }
            
            
            if (password.length === 0) {
                e.preventDefault();
                alert('Password tidak boleh kosong');
                passwordInput.focus();
                return false;
            }
            
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            
            return true;
        });

        passwordInput.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

       
        window.addEventListener('load', function() {
            form.reset();
        });
    </script>
</body>
</html>