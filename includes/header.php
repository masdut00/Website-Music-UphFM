<?php
$total_items_in_cart = 0;
if (!empty($_SESSION['cart']['tickets'])) {
    $total_items_in_cart += count($_SESSION['cart']['tickets']);
}
if (!empty($_SESSION['cart']['merch'])) {
    $total_items_in_cart += count($_SESSION['cart']['merch']);
}
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - UpFM' : 'UpFM Festival'; ?></title>
    <link rel="stylesheet" href="/upfm_web/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
</head>
<body>

<header>
    <div class="container header-container">
        <a href="/upfm_web/index.php" class="logo-link">
            <img src="/upfm_web/assets/images/logo.png" alt="UpFM Logo" class="logo">
        </a>
        
        <button class="mobile-nav-toggle" aria-controls="primary-navigation" aria-expanded="false">
            <span class="sr-only">Menu</span>
        </button>

        <nav class="primary-nav">
            <ul id="primary-navigation" data-visible="false">
                
                <li><a href="/upfm_web/index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">HOME</a></li>
                <li><a href="/upfm_web/about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">ABOUT</a></li>
                <li><a href="/upfm_web/explore.php" class="<?php echo ($current_page == 'explore.php') ? 'active' : ''; ?>">EXPLORE</a></li>
                <li><a href="/upfm_web/schedule.php" class="<?php echo ($current_page == 'schedule.php') ? 'active' : ''; ?>">JADWAL</a></li>
                <li><a href="/upfm_web/journal.php" class="<?php echo ($current_page == 'journal.php' || $current_page == 'baca_artikel.php') ? 'active' : ''; ?>">JOURNAL</a></li>
                <li><a href="/upfm_web/gallery.php" class="<?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>">GALLERY</a></li>
                <li><a href="/upfm_web/get_involved.php" class="<?php echo ($current_page == 'get_involved.php' || $current_page == 'contact.php') ? 'active' : ''; ?>">GET INVOLVED</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-dropdown">
                        <a href="#" class="dropdown-toggle">
                            <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/upfm_web/account/my_account.php">Akun Saya</a></li>
                            <li><a href="/upfm_web/account/my_tickets.php">Tiket Saya</a></li>
                            <li><a href="/upfm_web/auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="/upfm_web/auth/login.php" class="btn-nav-login">LOGIN</a></li>
                <?php endif; ?>

                <li>
                    <a href="/upfm_web/account/keranjang.php" class="cart-icon-wrapper">
                        <span class="cart-icon">&#128722;</span>
                        <?php if ($total_items_in_cart > 0): ?>
                            <span class="cart-badge"><?php echo $total_items_in_cart; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>
<main>