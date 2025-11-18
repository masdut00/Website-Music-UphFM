<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php'; 

$page_title = 'Admin Dashboard';

$total_users = $conn->query("SELECT COUNT(id) AS total FROM users")->fetch_assoc()['total'];
$total_tickets = $conn->query("SELECT COUNT(id) AS total FROM tickets")->fetch_assoc()['total'];
$total_purchases = $conn->query("SELECT COUNT(id) AS total FROM ticket_purchases")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin_styles.css">
</head>
<body>

<div class="admin-wrapper">
    
    <?php require_once '../includes/admin_sidebar.php';?>

    <div class="admin-main-content">
        <div class="admin-header">
            <h1 class="page-main-title">Dashboard</h1>
            <span>Selamat datang, <?php echo htmlspecialchars($admin_name); ?>!</span>
        </div>

        <div class="admin-stats-grid">
            <div class="stat-card">
                <h3>Total Pengguna</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="stat-card">
                <h3>Jenis Tiket</h3>
                <p><?php echo $total_tickets; ?></p>
            </div>
            <div class="stat-card">
                <h3>Tiket Terjual</h3>
                <p><?php echo $total_purchases; ?></p>
            </div>
        </div>
        
    </div> </div> </body>
</html>