<?php
session_start();
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $nim = trim($_POST['nim']);
    $whatsapp = trim($_POST['whatsapp']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi password
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Password dan konfirmasi tidak cocok!";
        header("Location: ../register.php");
        exit;
    }
    
    // Cek email sudah dipakai?
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0){
        $_SESSION['error'] = "Email sudah terdaftar!";
        header("Location: ../register.php");
        exit;
    }
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert ke database
    $stmt = $conn->prepare("INSERT INTO users (role, nama, email, nim_nip, whatsapp, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $role, $nama, $email, $nim, $whatsapp, $passwordHash);
    if($stmt->execute()){
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        header("Location: ../login.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal registrasi, coba lagi!";
        header("Location: ../register.php");
        exit;
    }

} else {
    header("Location: ../register.php");
    exit;
}
