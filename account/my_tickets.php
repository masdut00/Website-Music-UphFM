<?php
require_once '../includes/db.php';

// Proteksi Halaman: Wajib login untuk mengakses halaman ini
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Silakan login untuk melihat tiket Anda.";
    header("Location: login.php");
    exit();
}

$page_title = 'Tiket Saya';
require_once '../includes/header.php';

// Ambil ID pengguna dari session
$user_id = $_SESSION['user_id'];

// Query untuk mengambil semua tiket yang pernah dibeli oleh pengguna ini
// Kita JOIN dengan tabel 'tickets' untuk mendapatkan nama tiketnya
$sql = "SELECT 
            p.transaction_code, 
            p.quantity, 
            p.total_price, 
            p.purchase_date,
            t.category_name,
            t.filter_tag
        FROM ticket_purchases p
        JOIN tickets t ON p.ticket_id = t.id
        WHERE p.user_id = ?
        ORDER BY p.purchase_date DESC"; // Tampilkan yang terbaru di atas

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$purchased_tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container page-container">
    <h1 class="page-main-title">Tiket Saya</h1>
    <p class="page-subtitle">Semua tiket yang telah Anda beli ditampilkan di sini.</p>

    <div class="my-tickets-grid">
        <?php if (!empty($purchased_tickets)): ?>
            <?php foreach ($purchased_tickets as $ticket): ?>
                <div class="ticket-card">
                    <div class="ticket-header">
                        <span class="ticket-event-day"><?php echo ucfirst($ticket['filter_tag']); ?></span>
                        <img src="assets/images/logo-small-white.png" alt="Logo UpFM">
                    </div>
                    <div class="ticket-body">
                        <h2 class="ticket-name"><?php echo htmlspecialchars($ticket['category_name']); ?></h2>
                        <div class="ticket-details">
                            <div class="detail-item">
                                <span>Jumlah</span>
                                <strong><?php echo $ticket['quantity']; ?> Tiket</strong>
                            </div>
                            <div class="detail-item">
                                <span>Tanggal Beli</span>
                                <strong><?php echo date('d M Y, H:i', strtotime($ticket['purchase_date'])); ?></strong>
                            </div>
                        </div>
                        <div class="ticket-qr">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo urlencode($ticket['transaction_code']); ?>" alt="QR Code">
                        </div>
                        <p class="ticket-code">Kode Transaksi: <strong><?php echo htmlspecialchars($ticket['transaction_code']); ?></strong></p>
                    </div>
                    <div class="ticket-footer">
                        <p>Tunjukkan QR Code ini pada petugas di pintu masuk.</p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-tickets-found">
                <p>Anda belum memiliki tiket. Jelajahi festival dan beli tiket Anda sekarang!</p>
                <a href="explore.php" class="btn-standard">Beli Tiket</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>