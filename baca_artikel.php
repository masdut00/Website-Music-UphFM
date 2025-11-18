<?php
require_once 'includes/db.php';

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($article_id <= 0) {
    header("Location: journal.php");
    exit();
}

$stmt = $conn->prepare(
    "SELECT j.*, u.full_name AS author_name 
     FROM journal_articles j
     LEFT JOIN users u ON j.user_id = u.id
     WHERE j.id = ?"
);
$stmt->bind_param("i", $article_id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$article) {
    header("Location: journal.php");
    exit();
}

$page_title = $article['title'];
require_once 'includes/header.php';
?>

<div class="container page-container article-container">
    
    <div class="article-header">
        <span class="article-category-single"><?php echo htmlspecialchars($article['category']); ?></span>
        <h1><?php echo htmlspecialchars($article['title']); ?></h1>
        <div class="article-meta">
            Dipublikasikan pada <?php echo date('d F Y', strtotime($article['publish_date'])); ?>
            <?php if (!empty($article['author_name'])): ?>
                oleh <strong><?php echo htmlspecialchars($article['author_name']); ?></strong>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($article['image_url'])): ?>
        <img class="article-cover-image" src="/upfm_web/assets/images/articles/<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
    <?php endif; ?>

    <div class="article-full-content">
        <?php
        echo nl2br(htmlspecialchars($article['content'])); 
        ?>
    </div>
    
    <a href="journal.php" class="btn-back">← Kembali ke Jurnal</a>
    
</div>

<?php
require_once 'includes/footer.php';
?>