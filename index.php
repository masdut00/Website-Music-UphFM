<?php
require_once 'includes/db.php';
$page_title = 'Selamat Datang di UpFM';
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <h2>Banner Logo</h2>
    </div>
</section>

<section class="gallery-preview">
    <div class="container">
        <div class="photo-grid">
            <?php
            echo '<div class="photo-card-placeholder"></div>';
            echo '<div class="photo-card-placeholder middle"></div>';
            echo '<div class="photo-card-placeholder"></div>';
            ?>
        </div>
        <div class="section-link">
            <a href="gallery.php">Shop All Gallery</a>
        </div>
    </div>
</section>

<section class="artist-highlight" id="lineup">
    <div class="container">
        <div class="artist-grid">
             <?php
            ?>
            <div class="artist-card">
                <div class="artist-image-placeholder"></div>
                <div class="card-info">
                    <h3>Denny Caknan</h3>
                </div>
            </div>
            <div class="artist-card">
                <div class="artist-image-placeholder"></div>
                <div class="card-info">
                    <h3>Isyana</h3>
                </div>
            </div>
            <div class="artist-card">
                <div class="artist-image-placeholder"></div>
                <div class="card-info">
                    <h3>Christo Goyang Ngebor</h3>
                </div>
            </div>
        </div>
        <div class="section-link">
            <a href="lineup.php">See all artist</a>
        </div>
    </div>
</section>

<?php
// footer
require_once 'includes/footer.php';
?>