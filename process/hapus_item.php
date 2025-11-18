<?php
session_start();

// Kita butuh 2 parameter: tipe (ticket/merch) dan kuncinya
if (isset($_GET['type']) && isset($_GET['key'])) {
    $type = $_GET['type'];
    $key = $_GET['key'];

    if ($type === 'ticket') {
        // Hapus dari ember tiket
        if (isset($_SESSION['cart']['tickets'][$key])) {
            unset($_SESSION['cart']['tickets'][$key]);
        }
    } elseif ($type === 'merch') {
        // Hapus dari ember merch
        if (isset($_SESSION['cart']['merch'][$key])) {
            unset($_SESSION['cart']['merch'][$key]);
        }
    }
}

header('Location: /upfm_web/account/keranjang.php');
exit();
?>  