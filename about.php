<?php
require_once 'includes/db.php';
$page_title = 'Tentang Kami';
require_once 'includes/header.php';

// 1. Ambil Data Tim
$teams = $conn->query("SELECT * FROM teams")->fetch_all(MYSQLI_ASSOC);

// 2. Ambil Data Sponsor
$sponsors = $conn->query("SELECT * FROM sponsors")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container page-container">

    <section class="about-intro">
        <div class="intro-text">
            <h2>UPH FESTIVAL MUSIC</h2>
            <p>UPFM hadir sebagai platform festival musik digital yang menyatukan berbagai genre dalam satu ruang, memberikan informasi lengkap seputar lineup, jadwal, dan aktivitas acara secara real-time.</p>
            <p>Kami bertujuan memudahkan generasi Z dalam merencanakan pengalaman menonton musisi favorit mereka sekaligus mendukung ekosistem musik lokal lewat akses informasi dan pembelian tiket yang cepat, inklusif, dan mudah digunakan.</p>
        </div>
        <div class="intro-image">
            <div class="image-placeholder-large" style="background-image: url('assets/images/about_banner.jpg'); background-size: cover;"></div>
        </div>
    </section>

    <section class="our-team">
        <h2 class="section-title">OUR TEAM</h2>
        <div class="team-grid">
            <?php if (!empty($teams)): ?>
                <?php foreach ($teams as $member): ?>
                    <div class="team-member">
                        <div class="team-photo" style="margin-bottom: 10px;">
                            <img src="assets/images/team/<?php echo htmlspecialchars($member['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($member['name']); ?>" 
                                 style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;">
                        </div>
                        <h4 style="margin: 5px 0;"><?php echo htmlspecialchars($member['name']); ?></h4>
                        <span style="color: #666;"><?php echo htmlspecialchars($member['role']); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Data tim belum tersedia.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="sponsors">
        <h2 class="section-title">SPONSORS & PARTNERS</h2>
        <div class="sponsor-grid">
            <?php if (!empty($sponsors)): ?>
                <?php foreach ($sponsors as $sponsor): ?>
                    <div class="sponsor-logo">
                        <img src="assets/images/sponsors/<?php echo htmlspecialchars($sponsor['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($sponsor['name']); ?>"
                             style="max-width: 100%; height: auto; max-height: 80px;">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada sponsor.</p>
            <?php endif; ?>
        </div>
    </section>

</div>

<?php
require_once 'includes/footer.php';
?>