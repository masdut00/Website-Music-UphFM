<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Anda harus login untuk checkout.");
}
if (!isset($_SESSION['cart']) || (empty($_SESSION['cart']['tickets']) && empty($_SESSION['cart']['merch']))) {
    header('Location: /upfm_web/account/keranjang.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_tickets = $_SESSION['cart']['tickets'];
$cart_merch = $_SESSION['cart']['merch'];
$new_purchase_ids = [];

if (!empty($cart_tickets)) {
    $ticket_ids = array_column($cart_tickets, 'id');
    
    if(count($ticket_ids) > 0) {
        $placeholders = implode(',', array_fill(0, count($ticket_ids), '?'));
        
        $sql = "SELECT id, price FROM tickets WHERE id IN ($placeholders)";
        $stmt_prices = $conn->prepare($sql);
        $stmt_prices->bind_param(str_repeat('i', count($ticket_ids)), ...$ticket_ids);
        $stmt_prices->execute();
        $prices_result = $stmt_prices->get_result()->fetch_all(MYSQLI_ASSOC);
        $prices = array_column($prices_result, 'price', 'id');
        $stmt_prices->close();

        $stmt_insert = $conn->prepare("INSERT INTO ticket_purchases (user_id, ticket_id, quantity, total_price, payment_status, transaction_code) VALUES (?, ?, ?, ?, 'success', ?)");
        $stmt_update_stock = $conn->prepare("UPDATE tickets SET quantity_available = quantity_available - ? WHERE id = ?");

        if (!$stmt_update_stock) {
            die("Error prepare update stock: " . $conn->error);
        }

        foreach ($cart_tickets as $item) {
            if (isset($prices[$item['id']])) {
                $price = $prices[$item['id']];
                $total_price = $price * $item['quantity'];
                $transaction_code = 'UPFM-T-' . $user_id . '-' . time() . '-' . $item['id'];
                
                $stmt_insert->bind_param("iiids", $user_id, $item['id'], $item['quantity'], $total_price, $transaction_code);
                $stmt_insert->execute();
                $new_purchase_ids[] = $stmt_insert->insert_id; 

                $stmt_update_stock->bind_param("ii", $item['quantity'], $item['id']);
                $stmt_update_stock->execute();
            }
        }
        
        $stmt_insert->close();
        $stmt_update_stock->close();
    }
}

if (!empty($cart_merch)) {
    $merch_ids = array_column($cart_merch, 'id');
    $placeholders = implode(',', array_fill(0, count($merch_ids), '?'));
    $sql = "SELECT id, price FROM merchandise WHERE id IN ($placeholders)";
    $stmt_prices = $conn->prepare($sql);
    $stmt_prices->bind_param(str_repeat('i', count($merch_ids)), ...$merch_ids);
    $stmt_prices->execute();
    $prices_result = $stmt_prices->get_result()->fetch_all(MYSQLI_ASSOC);
    $prices = array_column($prices_result, 'price', 'id');

    $stmt_insert = $conn->prepare("INSERT INTO merch_purchases (user_id, merch_id, quantity, total_price, payment_status, transaction_code) VALUES (?, ?, ?, ?, 'success', ?)");

    foreach ($cart_merch as $item) {
        if (isset($prices[$item['id']])) {
            $price = $prices[$item['id']];
            $total_price = $price * $item['quantity'];
            $transaction_code = 'UPFM-M-' . $user_id . '-' . time() . '-' . $item['id'];
            
            $stmt_insert->bind_param("iiids", $user_id, $item['id'], $item['quantity'], $total_price, $transaction_code);
            $stmt_insert->execute();
        }
    }
}

unset($_SESSION['cart']);

$_SESSION['last_purchase_ids'] = $new_purchase_ids; 
header('Location: /upfm_web/account/pesanan_sukses.php');
exit();
?>