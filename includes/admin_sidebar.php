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
        
        <a href="kelola_artis.php" class="nav-item <?php echo ($current_page == 'kelola_artis.php' || $current_page == 'edit_artis.php') ? 'active' : ''; ?>">
            Artis & Lineup
        </a>
        
        <a href="kelola_tiket.php" class="nav-item <?php echo ($current_page == 'kelola_tiket.php' || $current_page == 'edit_tiket.php') ? 'active' : ''; ?>">
            Tiket
        </a>
        
        <a href="kelola_merch.php" class="nav-item <?php echo ($current_page == 'kelola_merch.php' || $current_page == 'edit_merch.php') ? 'active' : ''; ?>">
            Merchandise
        </a>
        
        <a href="kelola_artikel.php" class="nav-item <?php echo ($current_page == 'kelola_artikel.php' || $current_page == 'edit_artikel.php') ? 'active' : ''; ?>">
            Jurnal / Artikel
        </a>
        
        <a href="kelola_faq.php" class="nav-item <?php echo ($current_page == 'kelola_faq.php' || $current_page == 'edit_faq.php') ? 'active' : ''; ?>">
            Kelola FAQ
        </a>
        
        <a href="kelola_galeri.php" class="nav-item <?php echo ($current_page == 'kelola_galeri.php' || $current_page == 'edit_gallery.php') ? 'active' : ''; ?>">
            Kelola Galeri
        </a>
        
        <a href="kelola_jadwal.php" class="nav-item <?php echo ($current_page == 'kelola_jadwal.php' || $current_page == 'edit_jadwal.php') ? 'active' : ''; ?>">
            Kelola Jadwal
        </a>

        <a href="kelola_tim.php" class="nav-item <?php echo ($current_page == 'kelola_tim.php' || $current_page == 'edit_tim.php') ? 'active' : ''; ?>">
            Kelola Tim
        </a>

        <a href="kelola_sponsor.php" class="nav-item <?php echo ($current_page == 'kelola_sponsor.php' || $current_page == 'edit_sponsor.php') ? 'active' : ''; ?>">
            Kelola Sponsor
        </a>

        <a href="lihat_pendaftar.php" class="nav-item <?php echo ($current_page == 'lihat_pendaftar.php') ? 'active' : ''; ?>">
            Lihat Pendaftar
        </a>

        <span class="nav-separator">Lainnya</span>
        <a href="#" class="nav-item">Pendaftar Volunteer</a>
        <a href="/upfm_web/index.php" class="nav-item nav-item-extra" target="_blank">Lihat Website</a>
        <a href="/upfm_web/auth/logout.php" class="nav-item nav-item-extra">Logout</a>
    </nav>
</div>