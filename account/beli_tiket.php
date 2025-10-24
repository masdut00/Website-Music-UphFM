<?php
// Pastikan session sudah dimulai jika Anda menggunakannya untuk proteksi
session_start();

require_once '../includes/db.php';

// Proteksi halaman
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Anda harus login untuk melihat detail tiket.";
    header("Location: /upfm_web/auth/login.php");
    exit();
}

$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($ticket_id <= 0) { die("ID Tiket tidak valid."); }

// 1. Ambil detail utama tiket
$stmt = $conn->prepare("SELECT category_name, price, description FROM tickets WHERE id = ?");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 2. Ambil semua tipe tiket yang tersedia (Perlu untuk mendapatkan ID Tipe jika Anda menggunakannya di keranjang)
$types_stmt = $conn->prepare("SELECT id, type_name FROM ticket_types WHERE ticket_id = ? ORDER BY id");
$types_stmt->bind_param("i", $ticket_id);
$types_stmt->execute();
$ticket_types = $types_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$types_stmt->close();

// 3. Ambil semua gambar tiket
$images_stmt = $conn->prepare("SELECT image_url FROM ticket_images WHERE ticket_id = ? LIMIT 4");
$images_stmt->bind_param("i", $ticket_id);
$images_stmt->execute();
$ticket_images = $images_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$images_stmt->close();

$page_title = $ticket ? $ticket['category_name'] : 'Tiket Tidak Ditemukan';
require_once '../includes/header.php';
?>

<div class="container page-container">
    <?php if ($ticket): ?>
        <div class="product-page-wrapper">
            <div class="product-gallery">
                <?php if (!empty($ticket_images)): ?>
                    <?php foreach ($ticket_images as $image): ?>
                        <div class="gallery-image" style="background-image: url('assets/images/tickets/<?php echo htmlspecialchars($image['image_url']); ?>');"></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="gallery-image-placeholder"></div>
                <?php endif; ?>
            </div>

            <div class="product-details">
                <div class="product-actions">
                    <span>&#x2661;</span> <span>&#x2197;</span>
                </div>
                <h1><?php echo htmlspecialchars($ticket['category_name']); ?></h1>
                <p class="product-price">Rp. <?php echo number_format($ticket['price'], 0, ',', '.'); ?></p>
                <p class="product-description"><?php echo htmlspecialchars($ticket['description'] ?: 'Deskripsi tidak tersedia.'); ?></p>
                <p class="product-seller">by UpFM</p>

                <form action="/upfm_web/process/tambah_keranjang.php" method="POST">
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                    
                    <input type="hidden" name="type_name" id="type-name-input" value="<?php echo !empty($ticket_types) ? htmlspecialchars($ticket_types[0]['type_name']) : ''; ?>">
                    
                    <?php if (!empty($ticket_types)): ?>
                        <div class="detail-group">
                            <label>Type</label>
                            <div class="type-selector">
                                <?php foreach ($ticket_types as $index => $type): ?>
                                    <button type="button" 
                                            class="type-btn <?php echo $index === 0 ? 'active' : ''; ?>"
                                            data-type-name="<?php echo htmlspecialchars($type['type_name']); ?>"> 
                                        <?php echo htmlspecialchars($type['type_name']); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="detail-group">
                        <label>Quantity</label>
                        <div class="quantity-selector">
                            <button type="button" class="quantity-btn" id="minus-btn">-</button>
                            <input type="text" id="quantity-input" name="quantity" value="1" readonly>
                            <button type="button" class="quantity-btn" id="plus-btn">+</button>
                        </div>
                    </div>
                    <button type="submit" class="btn-add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="ticket-not-found">
            <h2>Oops! Tiket tidak ditemukan.</h2>
            <a href="./explore.php" class="btn-standard">Kembali ke Halaman Explore</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === Logic untuk Pemilihan Tipe Tiket ===
    const typeButtons = document.querySelectorAll('.type-btn');
    const typeInput = document.getElementById('type-name-input');

    if (typeButtons.length > 0) {
        // Jika ada tipe, pastikan tipe pertama (default) sudah diatur di input hidden (sudah dilakukan di PHP)
        
        typeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // 1. Kelola kelas 'active' visual
                typeButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // 2. SET NILAI INPUT HIDDEN untuk dikirim ke server
                const selectedTypeName = this.getAttribute('data-type-name');
                typeInput.value = selectedTypeName;
            });
        });
    }

    // === Logic untuk Kuantitas ===
    const minusBtn = document.getElementById('minus-btn');
    const plusBtn = document.getElementById('plus-btn');
    const quantityInput = document.getElementById('quantity-input');

    if (minusBtn && plusBtn && quantityInput) {
        minusBtn.addEventListener('click', function() {
            let currentVal = parseInt(quantityInput.value);
            // Pastikan kuantitas tidak kurang dari 1
            if (currentVal > 1) {
                quantityInput.value = currentVal - 1;
            }
        });

        plusBtn.addEventListener('click', function() {
            let currentVal = parseInt(quantityInput.value);
            // Anda mungkin ingin menambahkan batasan stok di sini, tapi defaultnya cukup tambahkan 1
            quantityInput.value = currentVal + 1;
        });
    }
});
</script>