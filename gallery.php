<?php
require_once 'includes/db.php';
$page_title = 'Galeri Festival';
require_once 'includes/header.php';

// 1. Ambil Highlight
$slider_items = $conn->query("SELECT * FROM gallery WHERE is_highlight = 1 ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

// 2. Ambil SEMUA untuk album
$gallery_items = $conn->query("SELECT * FROM gallery ORDER BY album_name ASC, id DESC")->fetch_all(MYSQLI_ASSOC);

// 3. Kelompokkan array berdasarkan nama album
$grouped_albums = [];
foreach ($gallery_items as $item) {
    // Pastikan jika album_name kosong, masuk ke 'Lainnya' atau 'General'
    $album_key = !empty($item['album_name']) ? $item['album_name'] : 'General';
    $grouped_albums[$album_key][] = $item;
}
?>  <div class="gallery-page">
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
            <?php else: ?>
                <p style="text-align:center; color:white;">Belum ada foto highlight.</p>
            <?php endif; ?>

        </div>
    </section>

    <section class="photo-album container">
        <h2 class="section-title-explore">ALBUM FOTO</h2>
        
        <?php if (!empty($grouped_albums)): ?>
            <?php foreach ($grouped_albums as $album_name => $items): ?>
                
                <h3 style="margin-top: 40px; border-bottom: 2px solid #ddd; padding-bottom: 10px; text-transform: uppercase;">
                    📂 <?php echo htmlspecialchars($album_name); ?>
                </h3>

                <div class="album-grid">
                    <?php foreach ($items as $item): ?>
                        <a href="/upfm_web/assets/images/gallery/<?php echo htmlspecialchars($item['media_url']); ?>" 
                           class="album-item" 
                           data-lightbox="album-<?php echo str_replace(' ', '-', strtolower($album_name)); ?>" 
                           data-title="<?php echo htmlspecialchars($item['title']); ?>">
                            
                            <div class="album-thumbnail" style="background-image: url('/upfm_web/assets/images/gallery/<?php echo htmlspecialchars($item['media_url']); ?>');">
                                <?php if (isset($item['media_type']) && ($item['media_type'] === 'video' || $item['media_type'] === 'aftermovie')): ?>
                                    <div class="play-icon">▶</div>
                                <?php endif; ?>
                            </div>
                            </a>
                    <?php endforeach; ?>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center;">Belum ada foto di galeri.</p>
        <?php endif; ?>
    </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<?php
require_once 'includes/footer.php';
?>