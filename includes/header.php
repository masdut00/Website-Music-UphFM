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
                <li><a href="/upfm_web/index.php">HOME</a></li>
                <li><a href="/upfm_web/about.php">ABOUT</a></li>
                <li><a href="/upfm_web/explore.php">EXPLORE</a></li>
                <li><a href="#">JOURNAL</a></li>
                <li><a href="/upfm_web/gallery.php">GALLERY</a></li>
                <li><a href="#">CONTACT US</a></li>

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
            </ul>
        </nav>
    </div>
</header>

<main>