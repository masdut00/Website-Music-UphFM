<?php
require_once '../includes/db.php'; // Mulai session dan koneksi DB

// Proteksi Halaman: Wajib login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Anda harus login untuk melihat detail merchandise.";
    header("Location: /upfm_web/auth/login.php");
    exit();
}

// Ambil ID item dari URL
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($item_id <= 0) { die("ID Item tidak valid."); }

// Ambil detail merchandise dari database
$stmt = $conn->prepare("SELECT * FROM merchandise WHERE id = ? AND stock > 0");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

$page_title = $item ? $item['item_name'] : 'Item Tidak Ditemukan';
require_once '../includes/header.php';
?>

<div class="container page-container">

    <?php 
    // DITAMBAHKAN: Tampilkan pesan sukses jika ada
    if (isset($_SESSION['cart_message'])) {
        echo '<div class="alert success"><p>' . htmlspecialchars($_SESSION['cart_message']) . '</p></div>';
        unset($_SESSION['cart_message']);
    }
    ?>

    <?php if ($item): // Jika item ditemukan dan stoknya ada ?>
        <div class="product-page-wrapper">
            
            <div class="product-gallery-single">
                <div class="gallery-image" style="background-image: url('/upfm_web/assets/images/merch/<?php echo htmlspecialchars($item['image_url']); ?>');"></div>
            </div>

            <div class="product-details">
                <div class="product-actions">
                    <span>&#x2661;</span> </div>
                <h1><?php echo htmlspecialchars($item['item_name']); ?></h1>
                <p class="product-price">Rp. <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                <p class="product-description"><?php echo htmlspecialchars($item['description'] ?: 'Deskripsi untuk item ini akan segera tersedia.'); ?></p>
                <p class="product-seller">by UpFM Official</p>

                <form action="/upfm_web/process/tambah_keranjang.php" method="POST">
                    
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <input type="hidden" name="item_type" value="merch">
                    
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
    <?php else: // Jika item tidak ditemukan atau stok habis ?>
        <div class="ticket-not-found">
            <h2>Oops! Item tidak ditemukan.</h2>
            <p>Mohon maaf, item yang Anda cari mungkin sudah habis terjual atau link yang Anda masukkan salah.</p>
            <a href="/upfm_web/explore.php" class="btn-standard">Kembali ke Halaman Explore</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const minusBtn = document.getElementById('minus-btn');
    const plusBtn = document.getElementById('plus-btn');
    const quantityInput = document.getElementById('quantity-input');

    if (minusBtn && plusBtn && quantityInput) {
        minusBtn.addEventListener('click', function() {
            let currentVal = parseInt(quantityInput.value);
            if (currentVal > 1) {
                quantityInput.value = currentVal - 1;
            }
        });

        plusBtn.addEventListener('click', function() {
            let currentVal = parseInt(quantityInput.value);
            quantityInput.value = currentVal + 1;
        });
    }
});
</script>