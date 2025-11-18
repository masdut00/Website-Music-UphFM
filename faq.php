<?php
require_once 'includes/db.php';
$page_title = 'FAQ - Informasi';
require_once 'includes/header.php';

// Ambil semua data FAQ, diurutkan berdasarkan kategori
$all_faqs = $conn->query("SELECT * FROM faq ORDER BY category, id")->fetch_all(MYSQLI_ASSOC);

// Kelompokkan FAQ berdasarkan Kategori
$faqs_by_category = [];
foreach ($all_faqs as $faq) {
    $faqs_by_category[$faq['category']][] = $faq;
}
?>

<div class="container page-container">
    <h1 class="page-main-title">Frequently Asked Questions (FAQ)</h1>
    <p class="page-subtitle">Punya pertanyaan? Mungkin jawabannya sudah ada di sini.</p>

    <div class="faq-container">
        <?php foreach ($faqs_by_category as $category => $faqs): ?>
            <div class="faq-category-group">
                <h2 class="faq-category-title"><?php echo htmlspecialchars($category); ?></h2>
                
                <?php foreach ($faqs as $faq): ?>
                    <details class="faq-item">
                        <summary class="faq-question">
                            <?php echo htmlspecialchars($faq['question']); ?>
                        </summary>
                        <div class="faq-answer">
                            <p><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></p>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>