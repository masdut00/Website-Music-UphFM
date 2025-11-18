<?php
require_once 'includes/db.php';
$page_title = 'Kontak Kami';
require_once 'includes/header.php';

// (Di sini Anda bisa menambahkan logika PHP untuk mengirim email formulir nanti)
$message_sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    // Logika kirim email (untuk nanti)
    // mail("admin@upfm.com", "Pesan dari Web", $_POST['message']);
    $message_sent = true;
}
?>

<div class="container page-container">
    <h1 class="page-main-title">Hubungi Kami</h1>
    <p class="page-subtitle">Kami senang mendengar dari Anda. Sampaikan pertanyaan atau masukan Anda di bawah.</p>

    <div class="contact-wrapper">
        <div class="contact-info">
            <h3>Punya Pertanyaan Mendesak?</h3>
            <p>Sebelum mengirim formulir, coba cek halaman FAQ kami. Kemungkinan besar jawaban yang Anda cari sudah ada di sana!</p>
            <a href="/upfm_web/faq.php" class="btn-standard">Lihat FAQ Sekarang</a>
            
            <hr class="contact-divider">
            
            <h4>Info Lainnya:</h4>
            <p><strong>Email:</strong> info@upfm.com</p>
            <p><strong>Media:</strong> press@upfm.com</p>
        </div>
        
        <div class="contact-form-container">
            <?php if ($message_sent): ?>
                <div class="alert success"><p>Terima kasih! Pesan Anda telah terkirim.</p></div>
            <?php else: ?>
                <form action="contact.php" method="POST" class="admin-form">
                    <div class="form-group">
                        <label for="name">Nama Anda</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Anda</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Pesan Anda</label>
                        <textarea id="message" name="message" rows="8" required></textarea>
                    </div>
                    <button type="submit" class="btn-standard">Kirim Pesan</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>