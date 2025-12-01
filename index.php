<?php
require_once 'includes/db.php';
$page_title = 'Home - UPH Festival Music';
require_once 'includes/header.php';

// 1. Ambil Data Headliner (Artis Utama)
$headliners = $conn->query("SELECT * FROM artists WHERE is_headliner = 1 ORDER BY name ASC LIMIT 3")->fetch_all(MYSQLI_ASSOC);

// 2. Ambil Preview Galeri (Foto Terbaru)
$gallery_preview = $conn->query("SELECT * FROM gallery WHERE media_type = 'photo' ORDER BY id DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);

// 3. Ambil Data Sponsor
$sponsors = $conn->query("SELECT * FROM sponsors LIMIT 6")->fetch_all(MYSQLI_ASSOC);
?>

<style>
    /* --- HERO SECTION --- */
    .hero {
        height: 90vh; /* Tinggi layar penuh */
        background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.8)), url('assets/images/hero-bg.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed; /* Efek Parallax */
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
    }
    .hero h1 { font-size: 4rem; margin-bottom: 10px; font-weight: 900; letter-spacing: 3px; text-transform: uppercase; text-shadow: 0 5px 15px rgba(0,0,0,0.5); }
    .hero p { font-size: 1.3rem; margin-bottom: 40px; font-weight: 300; letter-spacing: 1px; color: #f0f0f0; }
    
    .hero-btn {
        padding: 15px 40px;
        background-color: #e74c3c;
        color: white;
        text-decoration: none;
        font-weight: bold;
        font-size: 1.1rem;
        border-radius: 50px;
        transition: 0.3s;
        border: 2px solid #e74c3c;
        display: inline-block;
    }
    .hero-btn:hover { background-color: transparent; color: #e74c3c; }

    /* --- GLOBAL SECTION --- */
    section { padding: 80px 0; }
    .section-title-home {
        text-align: center;
        margin-bottom: 50px;
        font-size: 2.5rem;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 2px;
        color: #333;
    }

    /* --- ARTIST HIGHLIGHT --- */
    .artist-highlight { background-color: #111; color: white; }
    .artist-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .artist-card {
        height: 400px; position: relative; overflow: hidden; border-radius: 10px; cursor: pointer;
    }
    .artist-bg {
        width: 100%; height: 100%; background-size: cover; background-position: center;
        transition: transform 0.5s ease;
    }
    .artist-overlay {
        position: absolute; bottom: 0; left: 0; width: 100%; padding: 30px;
        background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
    }
    .artist-card:hover .artist-bg { transform: scale(1.1); }
    
    /* --- TICKET CTA BANNER (PENGGANTI KARTU) --- */
    .ticket-cta {
        background-color: #e74c3c; /* Warna Merah Brand */
        color: white;
        text-align: center;
        padding: 80px 0;
    }
    .btn-white {
        background: white; color: #e74c3c; padding: 15px 40px; 
        font-weight: bold; font-size: 1.2rem; border-radius: 50px; 
        text-decoration: none; transition: 0.3s; display: inline-block;
        margin-top: 20px;
    }
    .btn-white:hover { background: #333; color: white; }

    /* --- GALLERY --- */
    .photo-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
    .photo-item {
        height: 300px; background-size: cover; background-position: center;
        border-radius: 8px; transition: 0.3s; filter: grayscale(30%);
    }
    .photo-item:hover { filter: grayscale(0%); transform: scale(1.02); }

    /* --- SPONSOR --- */
    .sponsor-logo-home {
        height: 50px; object-fit: contain; filter: grayscale(100%); opacity: 0.5; transition: 0.3s;
    }
    .sponsor-logo-home:hover { filter: grayscale(0%); opacity: 1; }

    /* Responsive */
    @media (max-width: 768px) {
        .hero h1 { font-size: 2.5rem; }
        .artist-grid, .photo-grid { grid-template-columns: 1fr; }
        .artist-card { height: 300px; }
    }
</style>

<section class="hero">
    <div class="container">
        <h1>UPH FESTIVAL MUSIC 2025</h1>
        <p>Experience the Sound, Art, and Soul of Generation Z</p>
        <a href="#lineup" class="hero-btn">Explore Festival</a>
    </div>
</section>

<section class="artist-highlight" id="lineup">
    <div class="container">
        <h2 class="section-title-home" style="color: white;">LINEUP UTAMA</h2>
        
        <div class="artist-grid">
             <?php if (!empty($headliners)): ?>
                <?php foreach ($headliners as $artist): ?>
                    <div class="artist-card">
                        <div class="artist-bg" style="background-image: url('assets/images/artists/<?php echo htmlspecialchars($artist['image_url']); ?>');"></div>
                        <div class="artist-overlay">
                            <h3 style="margin: 0; font-size: 1.5rem;"><?php echo htmlspecialchars($artist['name']); ?></h3>
                            <p style="margin: 5px 0 0; color: #ccc;"><?php echo htmlspecialchars($artist['genre']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; grid-column: 1/-1; color: #777;">Lineup Coming Soon.</p>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="jadwal.php" style="color: white; text-decoration: underline; font-weight: bold;">Lihat Semua Artis & Jadwal &rarr;</a>
        </div>
    </div>
</section>

<section class="ticket-cta">
    <div class="container">
        <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 15px;">AMANKAN TIKETMU SEKARANG</h2>
        <p style="font-size: 1.2rem; margin-bottom: 0;">Jangan sampai kehabisan. Kuota terbatas untuk setiap kategori.</p>
        <a href="explore.php" class="btn-white">Beli Tiket</a>
    </div>
</section>

<section class="gallery-preview">
    <div class="container">
        <h2 class="section-title-home">MOMENTS</h2>
        <div class="photo-grid">
            <?php if (!empty($gallery_preview)): ?>
                <?php foreach ($gallery_preview as $photo): ?>
                    <a href="assets/images/gallery/<?php echo htmlspecialchars($photo['media_url']); ?>" 
                       data-lightbox="homepage-gallery" 
                       data-title="<?php echo htmlspecialchars($photo['title']); ?>">
                        <div class="photo-item" style="background-image: url('assets/images/gallery/<?php echo htmlspecialchars($photo['media_url']); ?>');"></div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div style="text-align: center; margin-top: 30px;">
            <a href="gallery.php" style="color: #333; font-weight: bold; text-decoration: none;">Lihat Galeri Lengkap &rarr;</a>
        </div>
    </div>
</section>

<section class="sponsors" style="background: #f9f9f9; border-top: 1px solid #eee; padding: 40px 0;">
    <div class="container" style="text-align: center;">
        <h4 style="color: #999; margin-bottom: 30px; letter-spacing: 2px; font-size: 0.9rem;">OFFICIAL PARTNERS</h4>
        <div style="display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; align-items: center;">
            <?php if (!empty($sponsors)): ?>
                <?php foreach ($sponsors as $s): ?>
                    <img src="assets/images/sponsors/<?php echo htmlspecialchars($s['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($s['name']); ?>" 
                         class="sponsor-logo-home">
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<?php
require_once 'includes/footer.php';
?>