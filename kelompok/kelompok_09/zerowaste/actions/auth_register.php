<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $role = trim($_POST['role']);
    $no_hp = trim($_POST['no_hp']); 
    
    
    if (empty($username) || empty($password) || empty($confirm_password) || 
        empty($nama_lengkap) || empty($role) || empty($no_hp)) {
        $_SESSION['error'] = 'Semua field harus diisi!';
        header('Location: ../register.php');
        exit();
    }
    
   
    if (!in_array($role, ['donatur', 'mahasiswa'])) {
        $_SESSION['error'] = 'Role tidak valid!';
        header('Location: ../register.php');
        exit();
    }
    
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Password dan konfirmasi password tidak cocok!';
        header('Location: ../register.php');
        exit();
    }
    
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Password minimal 6 karakter!';
        header('Location: ../register.php');
        exit();
    }
    
    
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error'] = 'Username sudah digunakan!';
        mysqli_stmt_close($stmt);
        header('Location: ../register.php');
        exit();
    }
    mysqli_stmt_close($stmt);
    
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, nama_lengkap, role, no_hp) 
                                    VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $username, $hashed_password, $nama_lengkap, $role, $no_hp);
    
    if (mysqli_stmt_execute($stmt)) {
        $user_id = mysqli_insert_id($conn);
        
        
        logActivity($conn, $user_id, 'REGISTER', "User baru registrasi sebagai $role");
        
        $_SESSION['success'] = 'Registrasi berhasil! Silakan login.';
        header('Location: ../login.php');
        exit();
    } else {
        $_SESSION['error'] = 'Terjadi kesalahan. Silakan coba lagi.';
        header('Location: ../register.php');
        exit();
    }
    
    mysqli_stmt_close($stmt);
} else {
    header('Location: ../register.php');
    exit();
}
?>