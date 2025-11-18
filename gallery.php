<?php
require_once 'includes/db.php';
$page_title = 'Galeri Festival';
require_once 'includes/header.php';

$slider_items = $conn->query("SELECT * FROM gallery WHERE media_type = 'photo' ORDER BY id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$gallery_items = $conn->query("SELECT * FROM gallery ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="gallery-page">
    <div class="gallery-banner">
        <h2>Banner UPH Music</h2>
    </div>

    <section class="slider-container">
        <h2 class="section-title-explore">HIGHLIGHT MOMENTS</h2>
        <div class="image-slider">
            
            <?php if (!empty($slider_items)): ?>
                <?php foreach ($slider_items as $item): ?>
                    <div class="slide">
                        <a href="/upfm_web/assets/images/gallery/<?php echo htmlspecialchars($item['media_url']); ?>" 
                           data-lightbox="slider-highlights" 
                           data-title="<?php echo htmlspecialchars($item['title']); ?>">
                            <div class="slide-image" style="background-image: url('/upfm_web/assets/images/gallery/<?php echo htmlspecialchars($item['media_url']); ?>');"></div>
                        </a>
                    </div>
                <?php endforeach; ?>
                
                <?php foreach ($slider_items as $item): ?>
                    <div class="slide">
                        <a href="/upfm_web/assets/images/gallery/<?php echo htmlspecialchars($item['media_url']); ?>" 
                           data-lightbox="slider-highlights" 
                           data-title="<?php echo htmlspecialchars($item['title']); ?>">
                            <div class="slide-image" style="background-image: url('/upfm_web/assets/images/gallery/<?php echo htmlspecialchars($item['media_url']); ?>');"></div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </section>

    <section class="photo-album container">
        <h2 class="section-title-explore">ALBUM FOTO</h2>
        <div class="album-grid">
            
            <?php if (!empty($gallery_items)): ?>
                <?php foreach ($gallery_items as $item): ?>
                    
                    <a href="/upfm_web/assets/images/gallery/<?php echo htmlspecialchars($item['media_url']); ?>" 
                       class="album-item" 
                       data-lightbox="main-gallery" 
                       data-title="<?php echo htmlspecialchars($item['title']); ?> (Tahun: <?php echo htmlspecialchars($item['year']); ?>)">
                        
                        <div class="album-thumbnail" style="background-image: url('/upfm_web/assets/images/gallery/<?php echo htmlspecialchars($item['media_url']); ?>');">
                            <?php if ($item['media_type'] === 'video' || $item['media_type'] === 'aftermovie'): ?>
                                <div class="play-icon">&#9658;</div>
                            <?php endif; ?>
                        </div>
                        <span class="album-title"><?php echo htmlspecialchars($item['title']); ?></span>
                    </a>

                <?php endforeach; ?>
            <?php else: ?>
                <p>Galeri masih kosong. Foto-foto akan segera diunggah!</p>
            <?php endif; ?>

        </div>
    </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<?php
require_once 'includes/footer.php';
?>