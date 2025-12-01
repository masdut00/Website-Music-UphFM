<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Pesanan & Transaksi';
$message = '';
$message_type = '';

// --- LOGIKA UPDATE STATUS PEMBAYARAN ---
if (isset($_GET['action']) && isset($_GET['id']) && isset($_GET['type'])) {
    $id = (int)$_GET['id'];
    $type = $_GET['type'];
    $new_status = $_GET['action'];

    $table = ($type === 'ticket') ? 'ticket_purchases' : 'merch_purchases';
    
    $stmt = $conn->prepare("UPDATE $table SET payment_status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $id);
    
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Status berhasil diubah menjadi: " . strtoupper($new_status);
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = "Gagal mengubah status.";
        $_SESSION['flash_type'] = 'error';
    }
    header("Location: kelola_pesanan.php");
    exit();
}

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

// --- 1. AMBIL DATA PESANAN TIKET ---
$ticket_orders = [];
$sql_tickets = "SELECT tp.*, u.full_name, u.email, t.category_name 
                FROM ticket_purchases tp
                JOIN users u ON tp.user_id = u.id
                JOIN tickets t ON tp.ticket_id = t.id
                ORDER BY tp.created_at DESC";
$res_tickets = $conn->query($sql_tickets);
if ($res_tickets) $ticket_orders = $res_tickets->fetch_all(MYSQLI_ASSOC);

// --- 2. AMBIL DATA PESANAN MERCH ---
$merch_orders = [];
$check_merch = $conn->query("SHOW TABLES LIKE 'merch_purchases'");
if ($check_merch && $check_merch->num_rows > 0) {
    $sql_merch = "SELECT mp.*, u.full_name, u.email, m.item_name 
                  FROM merch_purchases mp
                  JOIN users u ON mp.user_id = u.id
                  JOIN merchandise m ON mp.merch_id = m.id
                  ORDER BY mp.created_at DESC";
    $res_merch = $conn->query($sql_merch);
    if ($res_merch) $merch_orders = $res_merch->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/css/admin_styles.css">
    <style>
        .nav-tabs .nav-link { color: #555; font-weight: 600; }
        .nav-tabs .nav-link.active { color: #007bff; border-bottom: 3px solid #007bff; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="admin-main-content">
        <div class="admin-header">
            <h1 class="page-main-title">Riwayat Transaksi</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ticket-content">🎫 Pesanan Tiket</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#merch-content">👕 Pesanan Merchandise</button></li>
        </ul>

        <div class="tab-content">
            
            <div class="tab-pane fade show active" id="ticket-content">
                <div class="card-admin p-3">
                    <table id="tableTickets" class="table table-striped table-hover" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Pembeli</th>
                                <th>Item</th>
                                <th>Jml</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ticket_orders)): ?>
                                <?php foreach ($ticket_orders as $order): ?>
                                    <tr>
                                        <td><small>#<?php echo $order['transaction_code']; ?></small></td>
                                        <td><strong><?php echo htmlspecialchars($order['full_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['category_name']); ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td>Rp <?php echo number_format($order['total_price']); ?></td>
                                        <td>
                                            <?php $st = $order['payment_status']; ?>
                                            <span class="badge bg-<?php echo ($st=='success'?'success':($st=='pending'?'warning':'danger')); ?>"><?php echo strtoupper($st); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/y H:i', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Status</button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item text-success" href="?type=ticket&id=<?php echo $order['id']; ?>&action=success">Success</a></li>
                                                    <li><a class="dropdown-item text-warning" href="?type=ticket&id=<?php echo $order['id']; ?>&action=pending">Pending</a></li>
                                                    <li><a class="dropdown-item text-danger" href="?type=ticket&id=<?php echo $order['id']; ?>&action=failed">Failed</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="merch-content">
                <div class="card-admin p-3">
                    <table id="tableMerch" class="table table-striped table-hover" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Pembeli</th>
                                <th>Item</th>
                                <th>Jml</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($merch_orders)): ?>
                                <?php foreach ($merch_orders as $order): ?>
                                    <tr>
                                        <td><small>#<?php echo $order['transaction_code']; ?></small></td>
                                        <td><strong><?php echo htmlspecialchars($order['full_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['item_name']); ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td>Rp <?php echo number_format($order['total_price']); ?></td>
                                        <td>
                                            <?php $st = $order['payment_status']; ?>
                                            <span class="badge bg-<?php echo ($st=='success'?'success':($st=='pending'?'warning':'danger')); ?>"><?php echo strtoupper($st); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/y H:i', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Status</button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item text-success" href="?type=merch&id=<?php echo $order['id']; ?>&action=success">Success</a></li>
                                                    <li><a class="dropdown-item text-warning" href="?type=merch&id=<?php echo $order['id']; ?>&action=pending">Pending</a></li>
                                                    <li><a class="dropdown-item text-danger" href="?type=merch&id=<?php echo $order['id']; ?>&action=failed">Failed</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables Tanpa Data Kosong (DataTables akan otomatis mendeteksi jika body kosong)
        $('#tableTickets').DataTable({ order: [[ 6, "desc" ]] }); 
        $('#tableMerch').DataTable({ order: [[ 6, "desc" ]] });
    });
</script>

</body>
</html>