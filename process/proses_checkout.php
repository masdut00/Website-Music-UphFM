<?php
require_once 'includes/db.php'; 

if (!isset($_SESSION['user_id'])) {
    die("Anda harus login untuk checkout.");
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$user_id = $_SESSION['user_id'];

if (!empty($cart_items)) {
    
    $ticket_ids = array_keys($cart_items);
    $placeholders = implode(',', array_fill(0, count($ticket_ids), '?'));
    $sql = "SELECT id, price FROM tickets WHERE id IN ($placeholders)";
    $stmt_prices = $conn->prepare($sql);
    $stmt_prices->bind_param(str_repeat('i', count($ticket_ids)), ...$ticket_ids);
    $stmt_prices->execute();
    $prices_result = $stmt_prices->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $prices = array_column($prices_result, 'price', 'id');
    
    $stmt_insert = $conn->prepare("INSERT INTO ticket_purchases (user_id, ticket_id, quantity, total_price, payment_status, transaction_code) VALUES (?, ?, ?, ?, 'success', ?)");

    $new_purchase_ids = [];

    foreach ($cart_items as $ticket_id => $quantity) {
        
        if (isset($prices[$ticket_id])) {
            $price = $prices[$ticket_id];
            $total_price = $price * $quantity;
            $transaction_code = 'UPFM-' . $user_id . '-' . time() . '-' . $ticket_id;

            $stmt_insert->bind_param("iiids", $user_id, $ticket_id, $quantity, $total_price, $transaction_code);
            $stmt_insert->execute();
        }
        
    }

    unset($_SESSION['cart']);

    $_SESSION['last_purchase_ids'] = $new_purchase_ids;

    header('Location: pesanan_sukses.php');
    exit();
} else {
    header('Location: keranjang.php');
    exit();
}
?>