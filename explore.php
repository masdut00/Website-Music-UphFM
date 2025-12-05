<?php
require_once 'includes/db.php';
$page_title = 'Jelajahi Festival - Tiket & Merch';
require_once 'includes/header.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$sql_tickets = "SELECT t.*, 
                (SELECT image_url FROM ticket_images WHERE ticket_id = t.id ORDER BY id LIMIT 1) AS image_url
                FROM tickets t 
                WHERE t.quantity_available > 0";

if ($filter === 'presale') {
    $sql_tickets .= " AND (t.category_name LIKE '%Presale%' OR t.filter_tag = 'presale')";
} elseif ($filter === 'day1') {
    $sql_tickets .= " AND (t.category_name LIKE '%Day 1%' OR t.filter_tag = 'day1')";
} elseif ($filter === 'day2') {
    $sql_tickets .= " AND (t.category_name LIKE '%Day 2%' OR t.filter_tag = 'day2')";
}

$sql_tickets .= " ORDER BY t.price ASC";
$tickets = $conn->query($sql_tickets)->fetch_all(MYSQLI_ASSOC);

$sql_merch = "SELECT * FROM merchandise WHERE stock > 0 ORDER BY id DESC";
$merch_items = $conn->query($sql_merch)->fetch_all(MYSQLI_ASSOC);
?>

<div class="container page-container">
    
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 class="page-main-title">EXPLORE FESTIVAL</h1>
        <p class="page-subtitle">Temukan tiket dan merchandise resmi UPH Festival Music 2025</p>
    </div>

    <section class="explore-section">
        <h2 class="section-title-explore">TIKET FESTIVAL</h2>
        
        <div class="filter-nav">
            <a href="explore.php?filter=all" class="<?php echo ($filter === 'all') ? 'active' : ''; ?>">Semua</a>
            <a href="explore.php?filter=presale" class="<?php echo ($filter === 'presale') ? 'active' : ''; ?>">Presale</a>
            <a href="explore.php?filter=day1" class="<?php echo ($filter === 'day1') ? 'active' : ''; ?>">Day 1</a>
            <a href="explore.php?filter=day2" class="<?php echo ($filter === 'day2') ? 'active' : ''; ?>">Day 2</a>
        </div>
        
        <div class="product-grid">
            <?php if (!empty($tickets)): ?> 
                <?php foreach ($tickets as $ticket): ?>
                    <div class="product-card">
                        <div class="product-image" style="background-image: url('assets/images/tickets/<?php echo htmlspecialchars($ticket['image_url'] ?: 'default_ticket.jpg'); ?>');"></div>
                        
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($ticket['category_name']); ?></span>
                            <p class="product-price">Rp <?php echo number_format($ticket['price'], 0, ',', '.'); ?></p>
                            <small style="color: #888;">Stok: <?php echo $ticket['quantity_available']; ?></small>
                        </div>
                        
                        <a href="account/beli_tiket.php?id=<?php echo $ticket['id']; ?>" class="btn-buy">Beli Tiket</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #777;">
                    <p>Tiket untuk kategori ini belum tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="explore-section" style="background-color: #f9f9f9; padding: 40px; border-radius: 8px;">
        <h2 class="section-title-explore" style="border: none;">CARA MEMBELI</h2>
        <div class="flow-steps">
            <div class="flow-step">
                <div class="step-number">1</div>
                <h3>Pilih Tiket</h3>
                <p>Pilih kategori dan jumlah tiket yang diinginkan.</p>
            </div>
            <div class="flow-step">
                <div class="step-number">2</div>
                <h3>Login & Data</h3>
                <p>Masuk ke akun Anda dan lengkapi data pemesanan.</p>
            </div>
            <div class="flow-step">
                <div class="step-number">3</div>
                <h3>Bayar</h3>
                <p>Lakukan pembayaran dan E-Ticket dikirim ke email.</p>
            </div>
        </div>
    </section>

    <section class="explore-section">
        <h2 class="section-title-explore">OFFICIAL MERCH</h2>
        
        <div class="product-grid">
            <?php if (!empty($merch_items)): ?>
                <?php foreach ($merch_items as $item): ?>
                    <div class="product-card">
                        <div class="product-image merch-style" 
                             style="background-image: url('assets/images/merch/<?php echo htmlspecialchars($item['image_url'] ?: 'default_merch.jpg'); ?>');">
                        </div>
                        
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($item['item_name']); ?></span>
                            <p class="product-price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                        </div>
                        
                        <a href="account/beli_merch.php?id=<?php echo $item['id']; ?>" class="btn-buy" style="background-color: #333;">Lihat Detail</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; color: #777; padding: 40px;">
                    <p>Merchandise belum tersedia. Stay tuned!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php
require_once 'includes/footer.php';
?>