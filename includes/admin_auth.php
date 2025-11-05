<?php
// Selalu mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cek apakah pengguna sudah login
// 2. Cek apakah role pengguna adalah 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // Jika tidak, tendang mereka ke halaman login
    $_SESSION['error_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    header("Location: /upfm_web/auth/login.php");
    exit();
}

// Jika lolos, simpan info admin untuk digunakan di halaman
$admin_id = $_SESSION['user_id'];
$admin_name = $_SESSION['user_name'];
?>