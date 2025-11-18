<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    header("Location: /upfm_web/auth/login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$admin_name = $_SESSION['user_name'];
?>