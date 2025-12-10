<?php 
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ZeroWaste</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded-full mb-4">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Daftar Akun Baru</h2>
                <p class="mt-2 text-gray-600">Bergabung dengan ZeroWaste</p>
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

            
            <div class="bg-white rounded-lg shadow-lg p-8">
                <form action="actions/auth_register.php" method="POST" id="registerForm" class="space-y-5">
                    
                   
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i> Username
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required
                            minlength="3"
                            maxlength="50"
                            pattern="[a-zA-Z\s]+"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            placeholder="Pilih username"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle"></i> Hanya huruf dan spasi
                        </p>
                        <p id="usernameError" class="mt-1 text-xs text-red-600 hidden">
                            <i class="fas fa-exclamation-triangle"></i> <span id="usernameErrorMsg"></span>
                        </p>
                    </div>

                    
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-1"></i> Nama Lengkap
                        </label>
                        <input 
                            type="text" 
                            id="nama_lengkap" 
                            name="nama_lengkap" 
                            required
                            minlength="3"
                            maxlength="100"
                            pattern="[a-zA-Z\s]+"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            placeholder="Nama lengkap Anda"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle"></i> Hanya huruf dan spasi
                        </p>
                        <p id="namaError" class="mt-1 text-xs text-red-600 hidden">
                            <i class="fas fa-exclamation-triangle"></i> <span id="namaErrorMsg"></span>
                        </p>
                    </div>

                    
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-1"></i> No. Telepon
                        </label>
                        <input 
                            type="tel" 
                            id="no_hp" 
                            name="no_hp" 
                            required
                            pattern="0[0-9]{9,13}"
                            minlength="10"
                            maxlength="14"
                            inputmode="numeric"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            placeholder="08xxxxxxxxxx"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle"></i> Format: 08xxxxxxxxxx (10-14 digit, hanya angka)
                        </p>
                        <p id="phoneError" class="mt-1 text-xs text-red-600 hidden">
                            <i class="fas fa-exclamation-triangle"></i> <span id="phoneErrorMsg"></span>
                        </p>
                    </div>

                    
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-users mr-1"></i> Daftar Sebagai
                        </label>
                        <select 
                            id="role" 
                            name="role" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        >
                            <option value="">-- Pilih Role --</option>
                            <option value="mahasiswa">Mahasiswa (Penerima Makanan)</option>
                            <option value="donatur">Donatur (Pemberi Makanan)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle"></i> Pilih sesuai kebutuhan Anda
                        </p>
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
                                minlength="6"
                                maxlength="255"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition pr-12"
                                placeholder="Min. 6 karakter"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password', 'togglePasswordIcon')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            >
                                <i id="togglePasswordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i> Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                required
                                minlength="6"
                                maxlength="255"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition pr-12"
                                placeholder="Ulangi password"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('confirm_password', 'toggleConfirmIcon')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            >
                                <i id="toggleConfirmIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        <p id="passwordMatch" class="mt-1 text-xs hidden">
                            <span id="passwordMatchMsg"></span>
                        </p>
                    </div>

                 
                    <button 
                        type="submit"
                        id="submitBtn"
                        class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-200 flex items-center justify-center"
                    >
                        <i class="fas fa-user-plus mr-2"></i>
                        Daftar Sekarang
                    </button>
                </form>

                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Sudah punya akun? 
                        <a href="login.php" class="text-green-600 font-semibold hover:text-green-700">
                            Login di sini
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

       
        function validateTextOnly(value, fieldName) {
            const textPattern = /^[a-zA-Z\s]+$/;
            
            if (!textPattern.test(value)) {
                return `${fieldName} hanya boleh berisi huruf dan spasi`;
            }
            
            if (value.trim().length === 0) {
                return `${fieldName} tidak boleh kosong`;
            }
            
            return null;
        }

        
        const usernameInput = document.getElementById('username');
        const usernameError = document.getElementById('usernameError');
        const usernameErrorMsg = document.getElementById('usernameErrorMsg');

        usernameInput.addEventListener('input', function(e) {
            let value = e.target.value;
            
            
            value = value.replace(/[^a-zA-Z\s]/g, '');
            e.target.value = value;
            
            
            if (value.length > 0) {
                const error = validateTextOnly(value, 'Username');
                if (error) {
                    showError(usernameError, usernameErrorMsg, usernameInput, error);
                } else {
                    hideError(usernameError, usernameInput);
                }
            } else {
                hideError(usernameError, usernameInput);
            }
        });

        
        const namaInput = document.getElementById('nama_lengkap');
        const namaError = document.getElementById('namaError');
        const namaErrorMsg = document.getElementById('namaErrorMsg');

        namaInput.addEventListener('input', function(e) {
            let value = e.target.value;
            
            
            value = value.replace(/[^a-zA-Z\s]/g, '');
            e.target.value = value;
           
            if (value.length > 0) {
                const error = validateTextOnly(value, 'Nama lengkap');
                if (error) {
                    showError(namaError, namaErrorMsg, namaInput, error);
                } else {
                    hideError(namaError, namaInput);
                }
            } else {
                hideError(namaError, namaInput);
            }
        });

        
        const phoneInput = document.getElementById('no_hp');
        const phoneError = document.getElementById('phoneError');
        const phoneErrorMsg = document.getElementById('phoneErrorMsg');

        phoneInput.addEventListener('input', function(e) {
            
            let value = e.target.value.replace(/\D/g, '');
            
            
            if (value.length > 0 && value[0] !== '0') {
                value = '0' + value;
            }
            
            
            if (value.length > 14) {
                value = value.substring(0, 14);
            }
            
            e.target.value = value;
            validatePhone(value);
        });

        phoneInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numericOnly = pastedText.replace(/\D/g, '');
            
            if (numericOnly.length > 0) {
                let value = numericOnly;
                if (value[0] !== '0') {
                    value = '0' + value;
                }
                if (value.length > 14) {
                    value = value.substring(0, 14);
                }
                phoneInput.value = value;
                validatePhone(value);
            }
        });

        function validatePhone(value) {
            hideError(phoneError, phoneInput);
            
            if (value.length === 0) {
                return;
            }
            
            if (value[0] !== '0') {
                showError(phoneError, phoneErrorMsg, phoneInput, 'Nomor harus dimulai dengan 0');
                return false;
            }
            
            if (value.length < 10) {
                showError(phoneError, phoneErrorMsg, phoneInput, 'Nomor minimal 10 digit');
                return false;
            }
            
            if (value.length > 14) {
                showError(phoneError, phoneErrorMsg, phoneInput, 'Nomor maksimal 14 digit');
                return false;
            }
            
            const validPrefixes = ['08', '02', '031', '021', '022', '024', '061', '062'];
            const hasValidPrefix = validPrefixes.some(prefix => value.startsWith(prefix));
            
            if (!hasValidPrefix) {
                showError(phoneError, phoneErrorMsg, phoneInput, 'Format nomor telepon Indonesia tidak valid');
                return false;
            }
            
            phoneInput.classList.add('border-green-500');
            return true;
        }

        function showError(errorElement, msgElement, inputElement, message) {
            msgElement.textContent = message;
            errorElement.classList.remove('hidden');
            inputElement.classList.add('border-red-500');
            inputElement.classList.remove('border-green-500');
        }

        function hideError(errorElement, inputElement) {
            errorElement.classList.add('hidden');
            inputElement.classList.remove('border-red-500');
        }

        
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            
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

        
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordMatch = document.getElementById('passwordMatch');
        const passwordMatchMsg = document.getElementById('passwordMatchMsg');

        function validatePasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword.length === 0) {
                passwordMatch.classList.add('hidden');
                confirmPasswordInput.classList.remove('border-red-500', 'border-green-500');
                return;
            }
            
            if (password === confirmPassword) {
                passwordMatch.classList.remove('hidden');
                passwordMatch.classList.remove('text-red-600');
                passwordMatch.classList.add('text-green-600');
                passwordMatchMsg.innerHTML = '<i class="fas fa-check-circle"></i> Password cocok';
                confirmPasswordInput.classList.remove('border-red-500');
                confirmPasswordInput.classList.add('border-green-500');
            } else {
                passwordMatch.classList.remove('hidden');
                passwordMatch.classList.remove('text-green-600');
                passwordMatch.classList.add('text-red-600');
                passwordMatchMsg.innerHTML = '<i class="fas fa-times-circle"></i> Password tidak cocok';
                confirmPasswordInput.classList.remove('border-green-500');
                confirmPasswordInput.classList.add('border-red-500');
            }
        }

        confirmPasswordInput.addEventListener('input', validatePasswordMatch);
        passwordInput.addEventListener('input', validatePasswordMatch);

      
        const form = document.getElementById('registerForm');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = usernameInput.value.trim();
            const namaLengkap = namaInput.value.trim();
            const phoneValue = phoneInput.value;
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            const usernameError = validateTextOnly(username, 'Username');
            if (usernameError) {
                showError(document.getElementById('usernameError'), usernameErrorMsg, usernameInput, usernameError);
                usernameInput.focus();
                return false;
            }
            
            
            const namaError = validateTextOnly(namaLengkap, 'Nama lengkap');
            if (namaError) {
                showError(document.getElementById('namaError'), namaErrorMsg, namaInput, namaError);
                namaInput.focus();
                return false;
            }
            
            
            if (!validatePhone(phoneValue)) {
                phoneInput.focus();
                return false;
            }
            
            
            if (password !== confirmPassword) {
                showError(phoneError, phoneErrorMsg, confirmPasswordInput, 'Password tidak cocok!');
                confirmPasswordInput.focus();
                return false;
            }
            
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mendaftar...';
            
            form.submit();
        });

        
        [usernameInput, namaInput].forEach(input => {
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                
                const cleanedText = pastedText.replace(/[^a-zA-Z\s]/g, '');
                
                
                const start = this.selectionStart;
                const end = this.selectionEnd;
                const currentValue = this.value;
                
                this.value = currentValue.substring(0, start) + cleanedText + currentValue.substring(end);
                
                
                const newPos = start + cleanedText.length;
                this.setSelectionRange(newPos, newPos);
                
                
                this.dispatchEvent(new Event('input'));
            });
        });
    </script>
</body>
</html>