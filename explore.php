<?php
require_once 'includes/db.php';
$page_title = 'Jelajahi Festival';
require_once 'includes/header.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$sql_tickets = "SELECT 
                    t.id, 
                    t.category_name, 
                    t.price,
                    (SELECT image_url FROM ticket_images WHERE ticket_id = t.id ORDER BY id LIMIT 1) AS image_url
                FROM tickets t
                WHERE t.quantity_available > 0";

if ($filter !== 'all') {

    $sql_tickets .= " AND (t.filter_tag = ? OR t.filter_tag = 'all-access')";
}
$sql_tickets .= " ORDER BY t.id";

$stmt = $conn->prepare($sql_tickets);
if ($filter !== 'all') {
    $stmt->bind_param("s", $filter);
}
$stmt->execute();
$tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$sql_merch = "SELECT id, item_name, price, image_url FROM merchandise WHERE stock > 0 ORDER BY id";
$merchandise_items = $conn->query($sql_merch)->fetch_all(MYSQLI_ASSOC);
?>

<div class="container page-container">
    <section class="explore-section">
        <h2 class="section-title-explore">TIKET FESTIVAL</h2>
        
        <div class="filter-nav">
            <a href="/upfm_web/explore.php?filter=all" class="<?php echo ($filter === 'all') ? 'active' : ''; ?>">Semua Tiket</a>
            <a href="/upfm_web/explore.php?filter=presale" class="<?php echo ($filter === 'presale') ? 'active' : ''; ?>">Presale</a>
            <a href="/upfm_web/explore.php?filter=day1" class="<?php echo ($filter === 'day1') ? 'active' : ''; ?>">Day 1</a>
            <a href="/upfm_web/explore.php?filter=day2" class="<?php echo ($filter === 'day2') ? 'active' : ''; ?>">Day 2</a>
        </div>
        
        <div class="product-grid">
            <?php if (!empty($tickets)): ?> 
                <?php foreach ($tickets as $ticket): ?>
                    <div class="product-card">
                        <div class="product-image" style="background-image: url('/upfm_web/assets/images/tickets/<?php echo htmlspecialchars($ticket['image_url'] ?: 'default_ticket.jpg'); ?>');"></div>
                        
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($ticket['category_name']); ?></span>
                            <p class="product-price">Rp <?php echo number_format($ticket['price'], 0, ',', '.'); ?></p>
                        </div>
                        
                        <a href="/upfm_web/account/beli_tiket.php?id=<?php echo $ticket['id']; ?>" class="btn-buy">Lihat Detail</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada tiket yang tersedia untuk kategori ini. Coba lihat di kategori lain!</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php
require_once 'includes/footer.php';
?>