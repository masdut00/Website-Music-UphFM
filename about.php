<?php
require_once 'includes/db.php';
$page_title = 'Tentang Kami';
require_once 'includes/header.php';

$teams = $conn->query("SELECT * FROM teams")->fetch_all(MYSQLI_ASSOC);
$sponsors = $conn->query("SELECT * FROM sponsors")->fetch_all(MYSQLI_ASSOC);
$faqs = $conn->query("SELECT * FROM faq ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
?>

<style>
    /* Styling Accordion FAQ agar serasi dengan About */
    .faq-section {
        margin-top: 60px;
        margin-bottom: 60px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .faq-item {
        background: #fff;
        border-radius: 8px;
        margin-bottom: 10px;
        border: 1px solid #eee;
        overflow: hidden;
    }

    .faq-question {
        background: #fff;
        padding: 15px 20px;
        width: 100%;
        text-align: left;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        color: #333;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.3s;
    }

    .faq-question:hover {
        background-color: #f8f9fa;
        color: #007bff;
    }

    .faq-question.active {
        background-color: #f1f3f5;
        color: #007bff;
    }

    .faq-icon {
        transition: transform 0.3s ease;
        font-size: 0.8rem;
        color: #999;
    }
    
    .faq-question.active .faq-icon {
        transform: rotate(180deg);
        color: #007bff;
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        background-color: #fff;
    }

    .faq-answer p {
        padding: 20px;
        margin: 0;
        color: #666;
        line-height: 1.6;
        font-size: 0.95rem;
        border-top: 1px solid #f1f1f1;
    }
</style>

<div class="container page-container">

    <section class="about-intro">
        <div class="intro-text">
            <h2>UPH FESTIVAL MUSIC</h2>
            <p>UPFM hadir sebagai platform festival musik digital yang menyatukan berbagai genre dalam satu ruang, memberikan informasi lengkap seputar lineup, jadwal, dan aktivitas acara secara real-time.</p>
            <p>Kami bertujuan memudahkan generasi Z dalam merencanakan pengalaman menonton musisi favorit mereka sekaligus mendukung ekosistem musik lokal.</p>
        </div>
        <div class="intro-image">
            <div class="image-placeholder-large" style="background-color: #ddd;">
                </div>
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
                                 style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
                        </div>
                        <h4 style="margin: 5px 0; font-size: 1.1rem;"><?php echo htmlspecialchars($member['name']); ?></h4>
                        <span style="color: #666; font-size: 0.9rem;"><?php echo htmlspecialchars($member['role']); ?></span>
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

    <section class="faq-section">
        <h2 class="section-title">PERTANYAAN UMUM (FAQ)</h2>
        <p style="text-align: center; margin-bottom: 30px; color: #666;">Informasi penting seputar penukaran tiket dan acara.</p>
        
        <div class="faq-list">
            <?php if (!empty($faqs)): ?>
                <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item">
                        <button class="faq-question">
                            <?php echo htmlspecialchars($faq['question']); ?>
                            <span class="faq-icon">▼</span>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #999;">Belum ada FAQ.</p>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <p style="color: #666;">Tidak menemukan jawaban?</p>
            <a href="https://wa.me/62812345678" class="btn-standard" style="padding: 10px 25px;">Hubungi Panitia</a>
        </div>
    </section>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questions = document.querySelectorAll('.faq-question');
        questions.forEach(question => {
            question.addEventListener('click', function() {
                this.classList.toggle('active');
                const answer = this.nextElementSibling;
                if (answer.style.maxHeight) {
                    answer.style.maxHeight = null;
                } else {
                    answer.style.maxHeight = answer.scrollHeight + "px";
                }
            });
        });
    });
</script>

<?php
require_once 'includes/footer.php';
?>