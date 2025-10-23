<?php
require_once 'includes/db.php';

// Proteksi halaman: Wajib login DAN rolenya harus 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // Jika bukan admin, tendang ke halaman utama
    header("Location: index.php");
    exit();
}

$page_title = 'Admin Dashboard';
require_once 'includes/header.php';
?>

<div class="container">
    <h1>Admin Dashboard</h1>
    <p>Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! Anda memiliki hak akses sebagai <strong>Admin</strong>.</p>
    
    <div class="admin-menu">
        <h2>Menu Manajemen</h2>
        <ul>
            <li><a href="#">Kelola Artis & Lineup</a></li>
            <li><a href="#">Kelola Jadwal Acara</a></li>
            <li><a href="#">Kelola Jurnal/Artikel</a></li>
            <li><a href="#">Lihat Pendaftar Volunteer</a></li>
        </ul>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>