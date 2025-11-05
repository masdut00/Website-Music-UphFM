<?php
require_once 'includes/db.php';
$page_title = 'Galeri Festival';
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">

<div class="gallery-page">
    <section class="slider-container">
        <h2 class="section-title-explore">HIGHLIGHT MOMENTS</h2>
        <div class="image-slider">
            <div class="slide">
                <a href="/upfm_web/assets/images/image1.jpeg" data-lightbox="slider-highlights" data-title="Highlight Momen 1">
                    <div class="slide-placeholder" style="background-image: url('/upfm_web/assets/images/image1.jpeg');"></div>
                </a>
            </div>
            <div class="slide">
                <a href="/upfm_web/assets/images/image1.jpeg" data-lightbox="slider-highlights" data-title="Highlight Momen 1">
                    <div class="slide-placeholder" style="background-image: url('/upfm_web/assets/images/image1.jpeg');"></div>
                </a>
            </div>
            <div class="slide">
                <a href="/upfm_web/assets/images/image1.jpeg" data-lightbox="slider-highlights" data-title="Highlight Momen 1">
                    <div class="slide-placeholder" style="background-image: url('/upfm_web/assets/images/image1.jpeg');"></div>
                </a>
            </div>
            <div class="slide">
                <a href="/upfm_web/assets/images/image1.jpeg" data-lightbox="slider-highlights" data-title="Highlight Momen 1">
                    <div class="slide-placeholder" style="background-image: url('/upfm_web/assets/images/image1.jpeg');"></div>
                </a>
            </div>
            <div class="slide">
                <a href="/upfm_web/assets/images/image1.jpeg" data-lightbox="slider-highlights" data-title="Highlight Momen 1">
                    <div class="slide-placeholder" style="background-image: url('/upfm_web/assets/images/image1.jpeg');"></div>
                </a>
            </div>
            <div class="slide">
                <a href="/upfm_web/assets/images/image1.jpeg" data-lightbox="slider-highlights" data-title="Highlight Momen 1">
                    <div class="slide-placeholder" style="background-image: url('/upfm_web/assets/images/image1.jpeg');"></div>
                </a>
            </div>
            <div class="slide">
                <a href="/upfm_web/assets/images/image1.jpeg" data-lightbox="slider-highlights" data-title="Highlight Momen 1">
                    <div class="slide-placeholder" style="background-image: url('/upfm_web/assets/images/image1.jpeg');"></div>
                </a>
            </div>
            <div class="slide">
                <a href="/upfm_web/assets/images/image1.jpeg" data-lightbox="slider-highlights" data-title="Highlight Momen 1">
                    <div class="slide-placeholder" style="background-image: url('/upfm_web/assets/images/image1.jpeg');"></div>
                </a>
            </div>
            <div class="slide">
                <a href="/upfm_web/assets/images/image1.jpeg" data-lightbox="slider-highlights" data-title="Highlight Momen 1">
                    <div class="slide-placeholder" style="background-image: url('/upfm_web/assets/images/image1.jpeg');"></div>
                </a>
            </div>
            <div class="slide">
                <a href="/upfm_web/assets/images/image1.jpeg" data-lightbox="slider-highlights" data-title="Highlight Momen 1">
                    <div class="slide-placeholder" style="background-image: url('/upfm_web/assets/images/image1.jpeg');"></div>
                </a>
            </div>
        </div>
    </section>

    <section class="photo-album container">
        <h2 class="section-title-explore">ALBUM FOTO</h2>
        <div class="album-grid">
            <a href="assets/images/gallery/photo1_full.jpg" data-lightbox="album-foto" data-title="UPH Fest 2020 - Suasana Malam">
                <div class="album-item">
                    <div class="album-thumbnail" style="background-image: url('/upfm_web/assets/images/image1.jpeg');"></div>
                    <span class="album-title">UPH FEST 2020</span>
                </div>
            </a>
            <a href="assets/images/gallery/photo2_full.jpg" data-lightbox="album-foto" data-title="Penampilan Band Indie">
                <div class="album-item">
                    <div class="album-thumbnail" style="background-image: url('assets/images/gallery/photo2.jpg');"></div>
                    <span class="album-title">UPH FEST 2021</span>
                </div>
            </a>
            <a href="assets/images/gallery/photo1_full.jpg" data-lightbox="album-foto" data-title="UPH Fest 2020 - Suasana Malam">
                <div class="album-item">
                    <div class="album-thumbnail" style="background-image: url('assets/images/gallery/photo1.jpg');"></div>
                    <span class="album-title">UPH FEST 2022</span>
                </div>
            </a>
            <a href="assets/images/gallery/photo2_full.jpg" data-lightbox="album-foto" data-title="Penampilan Band Indie">
                <div class="album-item">
                    <div class="album-thumbnail" style="background-image: url('assets/images/gallery/photo2.jpg');"></div>
                    <span class="album-title">UPH FEST 2023</span>
                </div>
            </a>
        </div>
    </section>
</div>

<?php
require_once 'includes/footer.php';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>