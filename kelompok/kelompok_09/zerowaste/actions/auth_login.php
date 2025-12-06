<?php
session_start();
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Query user berdasarkan email
    $stmt = $conn->prepare("SELECT id, nama, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['success'] = "Login berhasil!";
            header("Location: ../index.php");
            exit;
        } else {
            $_SESSION['error'] = "Password salah!";
            header("Location: ../login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Email tidak ditemukan!";
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
