<?php
session_start();

if (isset($_GET['id'])) {
    $ticket_id_to_remove = (int)$_GET['id'];
    
    if (isset($_SESSION['cart'][$ticket_id_to_remove])) {
        unset($_SESSION['cart'][$ticket_id_to_remove]);
    }
}

header('Location: keranjang.php');
exit();
?>