<?php
require_once 'includes/db.php';
$page_title = 'Keranjang Belanja';
require_once 'includes/header.php';

// Ambil item di keranjang dari session
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$tickets_in_cart = [];
$grand_total = 0;

if (!empty($cart_items)) {
    // Ambil semua ID tiket dari keranjang
    $ticket_ids = array_keys($cart_items);
    
    // Siapkan placeholder '?' untuk query IN (...)
    $placeholders = implode(',', array_fill(0, count($ticket_ids), '?'));
    
    // Query untuk mengambil detail semua tiket di keranjang dalam satu kali panggilan
    $sql = "SELECT id, category_name, price FROM tickets WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    // Bind semua ID tiket ke statement
    $stmt->bind_param(str_repeat('i', count($ticket_ids)), ...$ticket_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $tickets_in_cart[] = $row;
    }
}
?>

<div class="container page-container">
    <h1 class="cart-title">Keranjang Anda</h1>

    <?php if (!empty($tickets_in_cart)): ?>
        <form action="proses_checkout.php" method="POST">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Tiket</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets_in_cart as $ticket): ?>
                        <?php
                        $quantity = $cart_items[$ticket['id']];
                        $subtotal = $ticket['price'] * $quantity;
                        $grand_total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ticket['category_name']); ?></td>
                            <td>Rp <?php echo number_format($ticket['price']); ?></td>
                            <td><?php echo $quantity; ?></td>
                            <td>Rp <?php echo number_format($subtotal); ?></td>
                            <td>
                                <a href="./process/hapus_item.php?id=<?php echo $ticket['id']; ?>" class="cart-remove-btn">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total</strong></td>
                        <td colspan="2"><strong>Rp <?php echo number_format($grand_total); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <div class="cart-actions">
                <a href="explore.php" class="btn-secondary">Lanjut Belanja</a>
                <button type="submit" class="btn-checkout">Checkout Sekarang</button>
            </div>
        </form>
    <?php else: ?>
        <div class="cart-empty">
            <p>Keranjang belanja Anda masih kosong.</p>
            <a href="explore.php" class="btn-standard">Mulai Belanja</a>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php';
?>