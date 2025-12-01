<?php
// Hitung item keranjang
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>

<header id="main-header">
    <canvas id="header-canvas"></canvas>

    <div class="container header-container">
        <a href="/upfm_web/index.php" class="logo-link">
            <img src="/upfm_web/assets/images/logo.png" alt="UpFM Logo" class="logo" style="filter: brightness(0) invert(1);"> 
        </a>
        
        <button class="mobile-nav-toggle" aria-controls="primary-navigation" aria-expanded="false" style="filter: invert(1);">
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
                <li><a href="/upfm_web/get_involved.php" class="<?php echo ($current_page == 'get_involved.php' || $current_page == 'contact.php') ? 'active' : ''; ?>">JOIN US</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-dropdown">
                        <a href="#" class="dropdown-toggle" style="display:flex; align-items:center; gap:8px;">
                            <i class="fas fa-user-circle" style="font-size:1.2rem;"></i>
                            <span><?php echo htmlspecialchars(strtok($_SESSION['user_name'], " ")); ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/upfm_web/account/my_account.php" style="color:#333 !important;">Akun Saya</a></li>
                            <li><a href="/upfm_web/account/my_tickets.php" style="color:#333 !important;">Tiket Saya</a></li>
                            <li style="border-top:1px solid #eee;"><a href="/upfm_web/auth/logout.php" style="color:red !important;">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="/upfm_web/auth/login.php" class="btn-nav-login" style="background:white !important; color:black !important;">LOGIN</a></li>
                <?php endif; ?>

                <li>
                    <a href="/upfm_web/account/keranjang.php" class="cart-icon-wrapper">
                        <i class="fas fa-shopping-cart cart-icon"></i>
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

<script>
    const canvas = document.getElementById('header-canvas');
    const ctx = canvas.getContext('2d');
    const header = document.getElementById('main-header');

    let width, height;
    let waves = [];

    // Warna Ombak (Hitam ke Orange)
    const colors = [
        ['rgba(0, 0, 0, 1)', 'rgba(20, 20, 20, 1)'],       // Base Gelap
        ['rgba(255, 69, 0, 0.4)', 'rgba(0, 0, 0, 0)'],     // Orange-Merah
        ['rgba(255, 140, 0, 0.3)', 'rgba(0, 0, 0, 0)']     // Orange Terang
    ];

    function resize() {
        width = canvas.width = header.offsetWidth;
        height = canvas.height = header.offsetHeight;
    }

    class Wave {
        constructor(color, yOffset, speed, amplitude) {
            this.color = color;
            this.yOffset = yOffset;
            this.speed = speed;
            this.amplitude = amplitude;
            this.frequency = 0.02; 
            this.phase = Math.random() * Math.PI * 2;
        }

        draw(ctx, time) {
            ctx.beginPath();
            ctx.moveTo(0, height);

            for (let x = 0; x <= width; x += 5) {
                const y = Math.sin(x * this.frequency + time * this.speed + this.phase) * this.amplitude 
                          + (height * this.yOffset);
                ctx.lineTo(x, y);
            }

            ctx.lineTo(width, height);
            ctx.lineTo(0, height);
            ctx.closePath();

            const gradient = ctx.createLinearGradient(0, 0, width, 0); // Horizontal Gradient
            gradient.addColorStop(0, this.color[0]);
            gradient.addColorStop(1, this.color[1]);
            
            ctx.fillStyle = gradient;
            ctx.fill();
        }
    }

    function init() {
        resize();
        waves = [];
        // Setup Ombak agar pas di header pendek
        waves.push(new Wave(colors[0], 0.5, 0.02, 15)); 
        waves.push(new Wave(colors[1], 0.6, 0.03, 10)); 
        waves.push(new Wave(colors[2], 0.7, 0.04, 8)); 
    }

    let time = 0;
    function animate() {
        ctx.fillStyle = '#111'; // Warna dasar header
        ctx.fillRect(0, 0, width, height);
        
        time += 0.5;
        waves.forEach(wave => wave.draw(ctx, time));
        requestAnimationFrame(animate);
    }

    window.addEventListener('resize', resize);
    init();
    animate();

    // Mobile Toggle
    const navToggle = document.querySelector('.mobile-nav-toggle');
    const primaryNav = document.querySelector('.primary-nav ul');
    if(navToggle) {
        navToggle.addEventListener('click', () => {
            const visibility = primaryNav.getAttribute('data-visible');
            primaryNav.setAttribute('data-visible', visibility === "false" ? true : false);
            navToggle.setAttribute('aria-expanded', visibility === "false" ? true : false);
        });
    }
</script>