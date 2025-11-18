<?php
require_once 'includes/db.php';
$page_title = 'Selamat Datang di UpFM';
require_once 'includes/header.php';

$headliners = $conn->query(
    "SELECT * FROM artists 
     WHERE is_headliner = 1 
     ORDER BY name ASC 
     LIMIT 3"
)->fetch_all(MYSQLI_ASSOC);

$gallery_preview = $conn->query(
    "SELECT * FROM gallery 
     WHERE media_type = 'photo' 
     ORDER BY id DESC 
     LIMIT 3"
)->fetch_all(MYSQLI_ASSOC);
?>

<section class="hero">
    <div class="hero-content">
        <h2>Banner Logo</h2>
        </div>
</section>

<section class="gallery-preview">
    <div class="container">
        <div class="photo-grid">
            <?php if (!empty($gallery_preview)): ?>
                <?php foreach ($gallery_preview as $photo): ?>
                    <a href="/upfm_web/assets/images/gallery/<?php echo htmlspecialchars($photo['media_url']); ?>" 
                       data-lightbox="homepage-gallery" 
                       data-title="<?php echo htmlspecialchars($photo['title']); ?>">
                        
                        <div class="photo-card" style="background-image: url('/upfm_web/assets/images/gallery/<?php echo htmlspecialchars($photo['media_url']); ?>');"></div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="photo-card-placeholder"></div>
                <div class="photo-card-placeholder middle"></div>
                <div class="photo-card-placeholder"></div>
            <?php endif; ?>
        </div>
        <div class="section-link">
            <a href="/upfm_web/gallery.php">Lihat Semua Galeri</a>
        </div>
    </div>
</section>

<section class="artist-highlight" id="lineup">
    <div class="container">
        <h2 class="section-title-explore">HIGHLIGHT ARTIS</h2>
        
        <div class="artist-grid">
             <?php if (!empty($headliners)): ?>
                <?php foreach ($headliners as $artist): ?>
                    
                    <a href="#" class="artist-card-link"> <div class="artist-card">
                            <div class="artist-image" style="background-image: url('/upfm_web/assets/images/artists/<?php echo htmlspecialchars($artist['image_url']); ?>');"></div>
                            <div class="card-info">
                                <h3><?php echo htmlspecialchars($artist['name']); ?></h3>
                            </div>
                        </div>
                    </a>
                    
                <?php endforeach; ?>
            <?php else: ?>
                <div class="artist-card"><div class="artist-image-placeholder"></div><div class="card-info"><h3>Segera Hadir</h3></div></div>
                <div class="artist-card"><div class="artist-image-placeholder"></div><div class="card-info"><h3>Segera Hadir</h3></div></div>
                <div class="artist-card"><div class="artist-image-placeholder"></div><div class="card-info"><h3>Segera Hadir</h3></div></div>
            <?php endif; ?>
        </div>
        <div class="section-link">
            <a href="#">Lihat Semua Artis</a>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<?php
require_once 'includes/footer.php';
?>