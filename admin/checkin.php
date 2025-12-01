<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Validasi Tiket (Check-In)';
$result_msg = '';
$result_type = ''; // success, error, warning
$ticket_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['ticket_code']);

    if (!empty($code)) {
        // 1. Cari Tiket di Database
        $stmt = $conn->prepare("
            SELECT tp.*, u.full_name, t.category_name 
            FROM ticket_purchases tp
            JOIN users u ON tp.user_id = u.id
            JOIN tickets t ON tp.ticket_id = t.id
            WHERE tp.transaction_code = ?
        ");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $ticket_data = $res->fetch_assoc();

            // 2. Cek Status Pembayaran
            if ($ticket_data['payment_status'] !== 'success') {
                $result_msg = "⛔ TIKET BELUM LUNAS! Status: " . strtoupper($ticket_data['payment_status']);
                $result_type = 'error';
            } 
            // 3. Cek Apakah Sudah Masuk Sebelumnya
            elseif ($ticket_data['is_checked_in'] == 1) {
                $result_msg = "⚠️ PERINGATAN: Tiket Sudah Dipakai Masuk pada: " . $ticket_data['checkin_time'];
                $result_type = 'warning';
            } 
            // 4. LOLOS VALIDASI -> Update Database
            else {
                $update = $conn->prepare("UPDATE ticket_purchases SET is_checked_in = 1, checkin_time = NOW() WHERE id = ?");
                $update->bind_param("i", $ticket_data['id']);
                
                if ($update->execute()) {
                    $result_msg = "✅ VALID! Silakan Masuk.";
                    $result_type = 'success';
                    // Update data tampilan agar terlihat sudah checkin
                    $ticket_data['is_checked_in'] = 1;
                    $ticket_data['checkin_time'] = date('Y-m-d H:i:s');
                } else {
                    $result_msg = "Error sistem: Gagal update database.";
                    $result_type = 'error';
                }
            }
        } else {
            $result_msg = "❌ KODE TIDAK DITEMUKAN!";
            $result_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/admin_styles.css">
    <style>
        .checkin-box {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
            padding: 40px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .input-code {
            font-size: 1.5rem;
            padding: 15px;
            text-align: center;
            width: 100%;
            border: 2px solid #333;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }
        .result-box {
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            color: white;
        }
        .res-success { background-color: #2ecc71; } /* Hijau */
        .res-error { background-color: #e74c3c; }   /* Merah */
        .res-warning { background-color: #f39c12; } /* Kuning */
        
        .ticket-info {
            text-align: left;
            background: #f9f9f9;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main-content">
        <div class="admin-header">
            <h1 class="page-main-title">Validasi Tiket (Gatekeeper)</h1>
        </div>

        <div class="checkin-box">
            <h3>Scan / Input Kode Tiket</h3>
            <p>Gunakan barcode scanner atau ketik manual.</p>
            
            <form method="POST" action="">
                <input type="text" name="ticket_code" class="input-code" placeholder="UPFM-T-XXX..." autofocus autocomplete="off">
                <button type="submit" class="btn-standard" style="width:100%; font-size:1.2rem;">CEK TIKET</button>
            </form>

            <?php if ($result_msg): ?>
                <div class="result-box res-<?php echo $result_type; ?>">
                    <h2 style="margin:0;"><?php echo $result_msg; ?></h2>
                </div>

                <?php if ($ticket_data): ?>
                    <div class="ticket-info">
                        <p><strong>Nama Pengunjung:</strong> <?php echo htmlspecialchars($ticket_data['full_name']); ?></p>
                        <p><strong>Kategori Tiket:</strong> <?php echo htmlspecialchars($ticket_data['category_name']); ?></p>
                        <p><strong>Jumlah:</strong> <?php echo $ticket_data['quantity']; ?> Orang</p>
                        <p><strong>Waktu Pembelian:</strong> <?php echo $ticket_data['created_at']; ?></p>
                        <?php if($ticket_data['is_checked_in']): ?>
                            <p style="color:red; font-weight:bold;">Waktu Check-in: <?php echo $ticket_data['checkin_time']; ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>