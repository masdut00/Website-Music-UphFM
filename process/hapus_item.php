<?php
session_start();

if (isset($_GET['type']) && isset($_GET['key'])) {
    $type = $_GET['type'];
    $key = $_GET['key'];

    if ($type === 'ticket') {
        // Hapus dari wadah tiket
        if (isset($_SESSION['cart']['tickets'][$key])) {
            unset($_SESSION['cart']['tickets'][$key]);
        }
    } elseif ($type === 'merch') {
        // Hapus dari wadah merch
        if (isset($_SESSION['cart']['merch'][$key])) {
            unset($_SESSION['cart']['merch'][$key]);
        }
    }
}

header('Location: /upfm_web/account/keranjang.php');
exit();
?>  