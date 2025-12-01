<?php
require_once '../includes/db.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Silakan login untuk melihat tiket Anda.";
    header("Location: /upfm_web/auth/login.php");
    exit();
}

$page_title = 'Tiket Saya';
require_once '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Query Tiket dengan status pembayaran 'success'
// Kita ambil juga kolom 'is_checked_in' untuk status validasi
$sql = "SELECT 
            p.transaction_code, 
            p.quantity, 
            p.total_price, 
            p.created_at,  /* Menggunakan created_at sesuai DB baru */
            p.is_checked_in, /* Status Check-in */
            t.category_name,
            t.filter_tag
        FROM ticket_purchases p
        JOIN tickets t ON p.ticket_id = t.id
        WHERE p.user_id = ? AND p.payment_status = 'success'
        ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$purchased_tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container page-container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 class="page-main-title">E-TICKET SAYA</h1>
        <p class="page-subtitle">Tunjukkan QR Code ini kepada panitia saat masuk ke venue.</p>
    </div>

    <?php if (empty($purchased_tickets)): ?>
        <div class="no-tickets-found" style="text-align: center; padding: 50px; background: #f9f9f9; border-radius: 10px;">
            <h3>🎟️ Belum Ada Tiket</h3>
            <p style="color: #666; margin-bottom: 20px;">Anda belum membeli tiket atau pembayaran belum selesai.</p>
            <a href="/upfm_web/explore.php" class="btn-standard">Beli Tiket Sekarang</a>
        </div>
    <?php else: ?>
        <div class="my-tickets-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <?php foreach ($purchased_tickets as $ticket): ?>
                <div class="ticket-card" style="border: 1px solid #ddd; border-radius: 12px; overflow: hidden; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    
                    <div class="ticket-header" style="background: #333; color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
                        <span class="ticket-event-day" style="background: #e74c3c; padding: 3px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">
                            <?php echo strtoupper($ticket['filter_tag']); ?>
                        </span>
                        <span style="font-size: 0.9rem; font-weight: bold;">UPFM 2025</span>
                    </div>

                    <div class="ticket-body" style="padding: 25px; text-align: center;">
                        <h2 class="ticket-name" style="margin: 0 0 10px; font-size: 1.4rem; color: #333;"><?php echo htmlspecialchars($ticket['category_name']); ?></h2>
                        
                        <div class="ticket-details" style="display: flex; justify-content: space-around; margin-bottom: 20px; font-size: 0.9rem; color: #666; border-bottom: 1px dashed #ddd; padding-bottom: 15px;">
                            <div>
                                <span>Jumlah</span><br>
                                <strong style="color: #333; font-size: 1.1rem;"><?php echo $ticket['quantity']; ?> Org</strong>
                            </div>
                            <div>
                                <span>Tanggal Beli</span><br>
                                <strong style="color: #333;"><?php echo date('d M Y', strtotime($ticket['created_at'])); ?></strong>
                            </div>
                        </div>

                        <div class="ticket-qr" style="margin-bottom: 15px;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($ticket['transaction_code']); ?>" 
                                 alt="QR Code" style="border: 5px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                        </div>
                        
                        <p class="ticket-code" style="font-family: monospace; font-size: 1rem; color: #555; background: #f0f0f0; padding: 5px; border-radius: 4px; display: inline-block;">
                            <?php echo htmlspecialchars($ticket['transaction_code']); ?>
                        </p>

                        <div style="margin-top: 20px;">
                            <?php if ($ticket['is_checked_in']): ?>
                                <div style="background: #e0e0e0; color: #555; padding: 10px; border-radius: 5px; font-weight: bold; border: 1px solid #ccc;">
                                    ❌ SUDAH DIGUNAKAN
                                </div>
                            <?php else: ?>
                                <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; font-weight: bold; border: 1px solid #c3e6cb;">
                                    ✅ TIKET AKTIF
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="ticket-footer" style="background: #f8f9fa; padding: 10px; text-align: center; font-size: 0.8rem; color: #888; border-top: 1px solid #eee;">
                        Screen capture halaman ini sebagai bukti tiket.
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once '../includes/footer.php';
?>