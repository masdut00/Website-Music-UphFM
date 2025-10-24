<?php
session_start();

if (isset($_POST['ticket_id']) && isset($_POST['quantity'])) {
    
    $ticket_id = (int)$_POST['ticket_id'];
    $quantity = (int)$_POST['quantity'];

    if ($ticket_id > 0 && $quantity > 0) {

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $_SESSION['cart'][$ticket_id] = $quantity;

    }
}

header('Location: /upfm_web/account/keranjang.php');
exit();
?>