<?php
require_once 'includes/db.php';
$page_title = 'Jurnal UpFM';
require_once 'includes/header.php';

// Ambil semua artikel dari database, yang terbaru di atas
$articles = $conn->query("SELECT * FROM journal_articles ORDER BY publish_date DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container page-container">
    <h1 class="page-main-title">Jurnal UpFM</h1>
    <p class="page-subtitle">Berita terbaru, wawancara eksklusif, dan cerita di balik layar festival.</p>

    <div class="journal-grid">
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                
                <a href="baca_artikel.php?id=<?php echo $article['id']; ?>" class="article-card">
                    <div class="article-image" style="background-image: url('/upfm_web/assets/images/articles/<?php echo htmlspecialchars($article['image_url'] ?: 'default_article.jpg'); ?>');">
                        <span class="article-category"><?php echo htmlspecialchars($article['category']); ?></span>
                    </div>
                    <div class="article-content">
                        <h3 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                        <p class="article-snippet">
                            <?php
                            // Ambil 100 karakter pertama dari konten sebagai cuplikan
                            echo htmlspecialchars(substr($article['content'], 0, 100)) . '...'; 
                            ?>
                        </p>
                        <span class="article-read-more">Baca Selengkapnya →</span>
                    </div>
                </a>

            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">Belum ada artikel yang dipublikasikan. Cek kembali nanti!</p>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>