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
    <div class="container">
        <a href="index.php" class="logo-link">
            <img src="assets/images/logo.png" alt="UpFM Logo" class="logo">
        </a>
        <nav>
            <ul>
                <li><a href="/upfm_web/index.php">HOME</a></li>
                <li><a href="/upfm_web/about.php">ABOUT</a></li>
                <li><a href="/upfm_web/explore.php">EXPLORE</a></li>
                <li><a href="#">JOURNAL</a></li>
                <li><a href="/upfm_web/gallery.php">GALLERY</a></li>
                <li><a href="#">CONTACT US</a></li>

                <li class="nav-separator"></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="account/my_account.php"><strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></a></li>
                    <li><a href="../auth/logout.php">LOGOUT</a></li>
                <?php else: ?>
                    <li><a href="auth/login.php">LOGIN</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main>