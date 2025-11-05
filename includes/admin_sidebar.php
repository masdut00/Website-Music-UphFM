<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<div class="admin-sidebar">
    <div class="admin-logo">
        <a href="dashboard.php">UPFM Admin</a>
    </div>
    <nav class="admin-nav">
        <a href="dashboard.php" class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            Dashboard
        </a>
        
        <span class="nav-separator">Kelola Konten</span>
        <a href="kelola_artis.php" class="nav-item <?php echo ($current_page == 'kelola_artis.php' || $current_page == 'edit_artis.php') ? 'active' : ''; ?>">Artis & Lineup</a>
        <a href="kelola_tiket.php" class="nav-item <?php echo ($current_page == 'kelola_tiket.php' || $current_page == 'edit_tiket.php') ? 'active' : ''; ?>">Tiket</a>
        <a href="kelola_merch.php" class="nav-item <?php echo ($current_page == 'kelola_merch.php' || $current_page == 'edit_merch.php') ? 'active' : ''; ?>">Merchandise</a>
        <a href="#" class="nav-item">Jurnal</a>
        <a href="#" class="nav-item">Galeri</a>
        
        <span class="nav-separator">Lainnya</span>
        <a href="#" class="nav-item">Pendaftar Volunteer</a>
        <a href="/upfm_web/index.php" class="nav-item nav-item-extra" target="_blank">Lihat Website</a>
        <a href="/upfm_web/auth/logout.php" class="nav-item nav-item-extra">Logout</a>
    </nav>
</div>