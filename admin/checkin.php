<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Validasi Tiket (Check-In)';
$result_msg = '';
$result_type = ''; // success, error, warning
$ticket_data = null;
$audio_file = ''; // Untuk suara beep

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['ticket_code']);

    if (!empty($code)) {
        // 1. Cari Tiket
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
                $result_msg = "⛔ TIKET BELUM LUNAS";
                $result_type = 'error';
                $audio_file = 'error.mp3';
            } 
            // 3. Cek Apakah Sudah Masuk
            elseif ($ticket_data['is_checked_in'] == 1) {
                $result_msg = "⚠️ SUDAH DIPAKAI";
                $result_type = 'warning';
                $audio_file = 'warning.mp3';
            } 
            // 4. LOLOS VALIDASI
            else {
                $update = $conn->prepare("UPDATE ticket_purchases SET is_checked_in = 1, checkin_time = NOW() WHERE id = ?");
                $update->bind_param("i", $ticket_data['id']);
                
                if ($update->execute()) {
                    $result_msg = "✅ VALID! SILAKAN MASUK";
                    $result_type = 'success';
                    $ticket_data['is_checked_in'] = 1;
                    $ticket_data['checkin_time'] = date('Y-m-d H:i:s');
                    $audio_file = 'success.mp3';
                } else {
                    $result_msg = "Error Database";
                    $result_type = 'error';
                }
            }
        } else {
            $result_msg = "❌ KODE TIDAK DITEMUKAN";
            $result_type = 'error';
            $audio_file = 'error.mp3';
        }
    }
}
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
        body { background-color: #f0f2f5; }
        
        .checkin-container {
            max-width: 600px;
            margin: 40px auto;
        }

        .checkin-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 40px;
            text-align: center;
        }

        .scan-icon {
            font-size: 4rem;
            color: #333;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        .form-control-lg {
            text-align: center;
            font-size: 1.5rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            border: 2px solid #ddd;
            border-radius: 10px;
        }
        .form-control-lg:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        /* Result Styles */
        .result-card {
            margin-top: 30px;
            border-radius: 12px;
            padding: 30px;
            color: white;
            animation: slideUp 0.3s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .res-success { background: linear-gradient(135deg, #2ecc71, #27ae60); }
        .res-error   { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .res-warning { background: linear-gradient(135deg, #f1c40f, #f39c12); color: #333; }

        .res-icon { font-size: 3rem; margin-bottom: 10px; }
        .res-title { font-weight: 800; font-size: 1.8rem; margin: 0; text-transform: uppercase; }

        .ticket-details {
            background: rgba(255,255,255,0.9);
            color: #333;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            text-align: left;
        }
        .ticket-details p { margin-bottom: 8px; font-size: 1.1rem; border-bottom: 1px dashed #ccc; padding-bottom: 5px; }
        .ticket-details p:last-child { border-bottom: none; }
        .ticket-details strong { width: 140px; display: inline-block; color: #555; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main-content">
        <div class="checkin-container">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold m-0"><i class="fas fa-qrcode text-primary"></i> Gatekeeper</h2>
                <span class="badge bg-dark"><?php echo date('d M Y'); ?></span>
            </div>

            <div class="checkin-card">
                <i class="fas fa-barcode scan-icon"></i>
                <h4 class="mb-3">Scan Barcode Tiket</h4>
                
                <form method="POST" action="" autocomplete="off">
                    <div class="mb-3">
                        <input type="text" name="ticket_code" class="form-control form-control-lg" placeholder="SCAN DISINI..." autofocus onblur="this.focus()">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold">CEK VALIDASI</button>
                </form>

                <?php if ($result_msg): ?>
                    <div class="result-card res-<?php echo $result_type; ?>">
                        <?php 
                            $icon = ($result_type == 'success') ? 'fa-check-circle' : (($result_type == 'warning') ? 'fa-exclamation-triangle' : 'fa-times-circle');
                        ?>
                        <i class="fas <?php echo $icon; ?> res-icon"></i>
                        <h2 class="res-title"><?php echo $result_msg; ?></h2>

                        <?php if ($ticket_data): ?>
                            <div class="ticket-details">
                                <p><strong>Nama:</strong> <?php echo htmlspecialchars($ticket_data['full_name']); ?></p>
                                <p><strong>Kategori:</strong> <?php echo htmlspecialchars($ticket_data['category_name']); ?></p>
                                <p><strong>Jumlah:</strong> <span class="badge bg-primary fs-6"><?php echo $ticket_data['quantity']; ?> Orang</span></p>
                                
                                <?php if($ticket_data['is_checked_in']): ?>
                                    <p class="text-danger fw-bold m-0">
                                        <i class="fas fa-clock"></i> Masuk: <?php echo date('H:i:s', strtotime($ticket_data['checkin_time'])); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
            
            <div class="text-center mt-3 text-muted">
                <small>Pastikan kursor selalu aktif di kolom input (Auto Focus)</small>
            </div>

        </div>
    </div>
</div>

<script>
    // Memaksa fokus ke input field agar scanner barcode langsung terbaca
    document.addEventListener("click", function() {
        document.querySelector("input[name='ticket_code']").focus();
    });
</script>

</body>
</html>