<?php
require_once '../includes/db.php';

// Proteksi halaman: Wajib login dan harus ada data pesanan di session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['last_purchase_ids'])) {
    header("Location: index.php");
    exit();
}

$page_title = 'Pesanan Berhasil';
require_once '../includes/header.php';

// Ambil ID pesanan dari session
$purchase_ids = $_SESSION['last_purchase_ids'];
$order_details = [];

if (!empty($purchase_ids)) {
    $placeholders = implode(',', array_fill(0, count($purchase_ids), '?'));
    
    // Query untuk mengambil detail pesanan yang baru saja dibuat
    $sql = "SELECT p.quantity, p.total_price, t.category_name 
            FROM ticket_purchases p
            JOIN tickets t ON p.ticket_id = t.id
            WHERE p.id IN ($placeholders)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($purchase_ids)), ...$purchase_ids);
    $stmt->execute();
    $order_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Hapus session setelah data diambil agar tidak bisa di-refresh
unset($_SESSION['last_purchase_ids']);
?>

<div class="container page-container">
    <div class="success-container">
        <div class="success-icon">&#10004;</div> <h1>Terima Kasih Atas Pesanan Anda!</h1>
        <p class="success-subtitle">Pesanan Anda telah berhasil dikonfirmasi. E-ticket akan segera dikirimkan ke email Anda.</p>

        <div class="order-summary">
            <h3>Ringkasan Pesanan</h3>
            <?php if (!empty($order_details)): 
                $grand_total = 0;
            ?>
                <ul class="order-summary-list">
                    <?php foreach ($order_details as $item): 
                        $grand_total += $item['total_price'];
                    ?>
                        <li>
                            <span><?php echo htmlspecialchars($item['category_name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                            <span>Rp <?php echo number_format($item['total_price']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="order-summary-total">
                    <span>Total Pembayaran</span>
                    <span>Rp <?php echo number_format($grand_total); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="next-steps">
            <p>Anda dapat melihat semua tiket yang telah Anda beli di halaman **"Tiket Saya"** pada profil Anda.</p>
            <div class="action-buttons">
                <a href="/upfm_web/index.php" class="btn-secondary">Kembali ke Beranda</a>
                <a href="my_tickets.php" class="btn-standard">Lihat Tiket Saya</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>