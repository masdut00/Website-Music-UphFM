<?php
require_once '../includes/db.php';
$page_title = 'Keranjang Belanja';
require_once '../includes/header.php';

$cart_tickets = isset($_SESSION['cart']['tickets']) ? $_SESSION['cart']['tickets'] : [];
$cart_merch = isset($_SESSION['cart']['merch']) ? $_SESSION['cart']['merch'] : [];

$items_in_cart = [];
$grand_total = 0;

// 1. tiket
if (!empty($cart_tickets)) {
    $ticket_ids = array_column($cart_tickets, 'id');
    $placeholders = implode(',', array_fill(0, count($ticket_ids), '?'));
    
    $sql_tickets = "SELECT id, category_name, price FROM tickets WHERE id IN ($placeholders)";
    $stmt_tickets = $conn->prepare($sql_tickets);
    $stmt_tickets->bind_param(str_repeat('i', count($ticket_ids)), ...$ticket_ids);
    $stmt_tickets->execute();
    $tickets_data = $stmt_tickets->get_result()->fetch_all(MYSQLI_ASSOC);
    $tickets_prices = array_column($tickets_data, 'price', 'id');
    $tickets_names = array_column($tickets_data, 'category_name', 'id');

    foreach ($cart_tickets as $key => $item) {
        $price = $tickets_prices[$item['id']] ?? 0;
        $items_in_cart[] = [
            'type' => 'ticket',
            'key' => $key,
            'name' => $tickets_names[$item['id']] . ' (' . htmlspecialchars($item['type']) . ')',
            'price' => $price,
            'quantity' => $item['quantity'],
            'subtotal' => $price * $item['quantity']
        ];
        $grand_total += $price * $item['quantity'];
    }
}

if (!empty($cart_merch)) {
    $merch_ids = array_column($cart_merch, 'id');
    $placeholders = implode(',', array_fill(0, count($merch_ids), '?'));
    
    $sql_merch = "SELECT id, item_name, price FROM merchandise WHERE id IN ($placeholders)";
    $stmt_merch = $conn->prepare($sql_merch);
    $stmt_merch->bind_param(str_repeat('i', count($merch_ids)), ...$merch_ids);
    $stmt_merch->execute();
    $merch_data = $stmt_merch->get_result()->fetch_all(MYSQLI_ASSOC);
    $merch_prices = array_column($merch_data, 'price', 'id');
    $merch_names = array_column($merch_data, 'item_name', 'id');

    foreach ($cart_merch as $key => $item) {
        $price = $merch_prices[$item['id']] ?? 0;
        $items_in_cart[] = [
            'type' => 'merch',
            'key' => $key,
            'name' => $merch_names[$item['id']],
            'price' => $price,
            'quantity' => $item['quantity'],
            'subtotal' => $price * $item['quantity']
        ];
        $grand_total += $price * $item['quantity'];
    }
}
?>

<div class="container page-container">
    <h1 class="cart-title">Keranjang Anda</h1>

    <?php if (!empty($items_in_cart)): ?>
        <form action="/upfm_web/process/proses_checkout.php" method="POST">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items_in_cart as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>Rp <?php echo number_format($item['price']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>Rp <?php echo number_format($item['subtotal']); ?></td>
                            <td>
                                <a href="/upfm_web/process/hapus_item.php?type=<?php echo $item['type']; ?>&key=<?php echo $item['key']; ?>" class="cart-remove-btn">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total Keseluruhan</strong></td>
                        <td colspan="2"><strong>Rp <?php echo number_format($grand_total); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <div class="cart-actions">
                <a href="/upfm_web/explore.php" class="btn-standard">Lanjut Belanja</a>
                <button type="submit" class="btn-checkout">Checkout Sekarang</button>
            </div>
        </form>
    <?php else: ?>
        <div class="cart-empty">
            <p>Keranjang belanja Anda masih kosong.</p>
            <a href="/upfm_web/explore.php" class="btn-standard">Mulai Belanja</a>
        </div>
    <?php endif; ?>
</div>

<?php
require_once '../includes/footer.php';
?>