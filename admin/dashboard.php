<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php'; 

$page_title = 'Admin Dashboard';

// --- 1. HITUNG STATISTIK UTAMA ---

// A. Total Pendapatan (Tiket + Merch) - Hanya yang statusnya 'success'
$sql_rev_ticket = "SELECT SUM(total_price) AS total FROM ticket_purchases WHERE payment_status = 'success'";
$rev_ticket = $conn->query($sql_rev_ticket)->fetch_assoc()['total'] ?? 0;

$sql_rev_merch = "SELECT SUM(total_price) AS total FROM merch_purchases WHERE payment_status = 'success'";
$rev_merch = $conn->query($sql_rev_merch)->fetch_assoc()['total'] ?? 0;

$total_revenue = $rev_ticket + $rev_merch;

// B. Counter Data
$total_users = $conn->query("SELECT COUNT(id) AS total FROM users WHERE role = 'user'")->fetch_assoc()['total'];
$total_tickets_sold = $conn->query("SELECT SUM(quantity) AS total FROM ticket_purchases WHERE payment_status = 'success'")->fetch_assoc()['total'] ?? 0;
$total_merch_sold = $conn->query("SELECT SUM(quantity) AS total FROM merch_purchases WHERE payment_status = 'success'")->fetch_assoc()['total'] ?? 0;

// --- 2. AMBIL 5 TRANSAKSI TIKET TERBARU ---
// Join tabel ticket_purchases dengan users dan tickets agar datanya lengkap
$sql_recent = "SELECT tp.id, tp.transaction_code, tp.total_price, tp.payment_status, 
                      u.full_name, t.category_name
               FROM ticket_purchases tp
               JOIN users u ON tp.user_id = u.id
               JOIN tickets t ON tp.ticket_id = t.id
               ORDER BY tp.id DESC LIMIT 5";
$recent_orders = $conn->query($sql_recent)->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin_styles.css">

    <style>
        /* CSS Khusus Dashboard Card agar lebih berwarna */
        .stat-card {
            border: none;
            border-radius: 10px;
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card h3 { font-size: 0.9rem; text-transform: uppercase; opacity: 0.8; margin-bottom: 5px; }
        .stat-card p { font-size: 1.8rem; font-weight: bold; margin: 0; }
        .stat-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 3rem;
            opacity: 0.2;
        }
        
        /* Warna Kartu */
        .bg-primary-gradient { background: linear-gradient(45deg, #4e73df, #224abe); }
        .bg-success-gradient { background: linear-gradient(45deg, #1cc88a, #13855c); }
        .bg-warning-gradient { background: linear-gradient(45deg, #f6c23e, #dda20a); }
        .bg-danger-gradient { background: linear-gradient(45deg, #e74a3b, #be2617); }

        /* Tabel Dashboard */
        .table-responsive { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .section-header { font-size: 1.2rem; font-weight: bold; color: #333; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        
        /* Quick Actions */
        .quick-action-btn {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            background: white; padding: 15px; border-radius: 8px; text-decoration: none; color: #555;
            border: 1px solid #eee; transition: 0.3s; height: 100%;
        }
        .quick-action-btn:hover { background: #f8f9fa; color: #007bff; border-color: #007bff; }
        .quick-action-btn i { font-size: 1.5rem; margin-bottom: 8px; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    
    <?php require_once '../includes/admin_sidebar.php';?>

    <div class="admin-main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-main-title" style="margin-bottom: 0;">Dashboard</h1>
                <p class="text-muted mb-0">Selamat datang kembali, <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></strong>!</p>
            </div>
            <span class="badge bg-dark p-2"><?php echo date('d M Y'); ?></span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-success-gradient">
                    <h3>Total Pendapatan</h3>
                    <p>Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></p>
                    <i class="fas fa-money-bill-wave stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-primary-gradient">
                    <h3>Tiket Terjual</h3>
                    <p><?php echo number_format($total_tickets_sold); ?></p>
                    <i class="fas fa-ticket-alt stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-warning-gradient">
                    <h3>Merch Terjual</h3>
                    <p><?php echo number_format($total_merch_sold); ?></p>
                    <i class="fas fa-tshirt stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-danger-gradient">
                    <h3>Total User</h3>
                    <p><?php echo number_format($total_users); ?></p>
                    <i class="fas fa-users stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <div class="section-header d-flex justify-content-between">
                        <span>Transaksi Tiket Terbaru</span>
                        <a href="kelola_pesanan.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID Transaksi</th>
                                <th>User</th>
                                <th>Tiket</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_orders)): ?>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td><small class="text-muted">#<?php echo substr($order['transaction_code'], -8); ?></small></td>
                                        <td><strong><?php echo htmlspecialchars($order['full_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['category_name']); ?></td>
                                        <td>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($order['payment_status'] == 'success'): ?>
                                                <span class="badge bg-success">Lunas</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted">Belum ada transaksi.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="table-responsive" style="height: 100%;">
                    <div class="section-header">Aksi Cepat</div>
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="edit_artis.php" class="quick-action-btn">
                                <i class="fas fa-microphone-alt text-primary"></i>
                                <span>Tambah Artis</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="edit_tiket.php" class="quick-action-btn">
                                <i class="fas fa-ticket-alt text-success"></i>
                                <span>Tambah Tiket</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="edit_gallery.php" class="quick-action-btn">
                                <i class="fas fa-images text-warning"></i>
                                <span>Upload Galeri</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="edit_merch.php" class="quick-action-btn">
                                <i class="fas fa-box-open text-danger"></i>
                                <span>Stok Merch</span>
                            </a>
                        </div>
                        <div class="col-12 mt-2">
                            <a href="edit_jadwal.php" class="btn btn-dark w-100">
                                <i class="fas fa-calendar-alt me-2"></i> Atur Jadwal Konser
                            </a>
                        </div>
                        <div class="col-12 mb-2">
                            <a href="checkin.php" class="btn btn-warning w-100 py-3 text-dark fw-bold shadow-sm" style="border: 2px solid #333;">
                                <i class="fas fa-qrcode fa-lg me-2"></i> MODE GATEKEEPER (SCAN TIKET)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> 
</div> 

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>