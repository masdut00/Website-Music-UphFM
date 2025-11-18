<?php
session_start();

// Validasi dasar
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['quantity']) || !isset($_POST['item_type'])) {
    header('Location: /upfm_web/explore.php');
    exit();
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        'tickets' => [],
        'merch' => []
    ];
}

$quantity = (int)$_POST['quantity'];
if ($quantity <= 0) $quantity = 1;
$item_type = $_POST['item_type'];

if ($item_type === 'ticket' && isset($_POST['ticket_id'])) {
    // --- Logika untuk TIKET ---
    $item_id = (int)$_POST['ticket_id'];
    $type_name = !empty($_POST['type_name']) ? $_POST['type_name'] : 'Regular';
    $cart_key = $item_id . '_' . $type_name; 
    
    if (isset($_SESSION['cart']['tickets'][$cart_key])) {
        $existing_quantity = (int)$_SESSION['cart']['tickets'][$cart_key]['quantity'];
        $_SESSION['cart']['tickets'][$cart_key]['quantity'] = $existing_quantity + $quantity;
    } else {
        $_SESSION['cart']['tickets'][$cart_key] = ['id' => $item_id, 'quantity' => $quantity, 'type' => $type_name];
    }

} elseif ($item_type === 'merch' && isset($_POST['item_id'])) {
    // --- Logika untuk MERCHANDISE ---
    $item_id = (int)$_POST['item_id'];
    $cart_key = $item_id;
    
    if (isset($_SESSION['cart']['merch'][$cart_key])) {
        $existing_quantity = (int)$_SESSION['cart']['merch'][$cart_key]['quantity'];
        $_SESSION['cart']['merch'][$cart_key]['quantity'] = $existing_quantity + $quantity;
    } else {
        $_SESSION['cart']['merch'][$cart_key] = ['id' => $item_id, 'quantity' => $quantity];
    }
}

// 1. Buat pesan sukses
$_SESSION['cart_message'] = "Item berhasil ditambahkan ke keranjang!";

// 2. Arahkan pengguna KEMBALI ke halaman sebelumnya
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>