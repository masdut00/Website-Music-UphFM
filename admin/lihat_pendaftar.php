<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Lihat Pendaftar';
$message = '';
$message_type = '';

// --- LOGIKA UPDATE STATUS (APPROVE / REJECT) ---
if (isset($_GET['action']) && isset($_GET['type']) && isset($_GET['id'])) {
    $action = $_GET['action']; // 'approve' atau 'reject'
    $type = $_GET['type'];     // 'volunteer' atau 'tenant'
    $id = (int)$_GET['id'];
    $new_status = ($action === 'approve') ? 'approved' : 'rejected';

    if (!empty($new_status)) {
        $table_name = ($type === 'volunteer') ? 'volunteers' : 'tenants';
        
        $stmt = $conn->prepare("UPDATE $table_name SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $id);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = 'Status pendaftar berhasil diperbarui menjadi ' . strtoupper($new_status);
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Gagal memperbarui status: ' . $stmt->error;
            $_SESSION['flash_type'] = 'error';
        }
        $stmt->close();
        
        header("Location: lihat_pendaftar.php");
        exit();
    }
}

// Cek Flash Message
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

// 1. Ambil Data Volunteer
$volunteers = [];
$sql_vol = "SELECT v.*, u.email AS user_email 
            FROM volunteers v
            LEFT JOIN users u ON v.user_id = u.id
            ORDER BY v.submission_date DESC";
$res_vol = $conn->query($sql_vol);
if($res_vol) $volunteers = $res_vol->fetch_all(MYSQLI_ASSOC);

// 2. Ambil Data Tenant
$tenants = [];
$sql_ten = "SELECT t.*, u.email AS user_email 
            FROM tenants t
            LEFT JOIN users u ON t.user_id = u.id
            ORDER BY t.submission_date DESC";
$res_ten = $conn->query($sql_ten);
if($res_ten) $tenants = $res_ten->fetch_all(MYSQLI_ASSOC);
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
        .btn-action { padding: 2px 8px; font-size: 0.8rem; border-radius: 4px; text-decoration: none; margin-right: 3px; display: inline-block; color: white; }
        .btn-approve { background-color: #28a745; }
        .btn-reject { background-color: #dc3545; }
        .btn-approve:hover, .btn-reject:hover { color: white; opacity: 0.8; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main-content">
        <div class="admin-header">
            <h1 class="page-main-title">Pendaftaran Masuk</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <ul class="nav nav-tabs mb-4" id="registrantTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="volunteer-tab" data-bs-toggle="tab" data-bs-target="#volunteer-content">
                    🤝 Volunteer (<?php echo count($volunteers); ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tenant-tab" data-bs-toggle="tab" data-bs-target="#tenant-content">
                    🏪 Tenant/Booth (<?php echo count($tenants); ?>)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            
            <div class="tab-pane fade show active" id="volunteer-content">
                <div class="card-admin p-3">
                    <table id="tableVolunteer" class="table table-striped table-hover" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>Kontak</th>
                                <th>Alasan</th>
                                <th>Akun User</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($volunteers as $v): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($v['full_name']); ?></strong></td>
                                    <td>
                                        <small>Email: <?php echo htmlspecialchars($v['email']); ?></small><br>
                                        <small>Telp: <?php echo htmlspecialchars($v['phone_number']); ?></small>
                                    </td>
                                    <td>
                                        <span title="<?php echo htmlspecialchars($v['reason_to_join']); ?>">
                                            <?php echo htmlspecialchars(substr($v['reason_to_join'], 0, 50)) . (strlen($v['reason_to_join']) > 50 ? '...' : ''); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($v['user_email'] ?? '-'); ?></td>
                                    <td>
                                        <?php 
                                            $st = $v['status']; 
                                            $badge = ($st=='approved')?'success':(($st=='rejected')?'danger':'warning');
                                        ?>
                                        <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($st); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($v['status'] == 'pending'): ?>
                                            <a href="?action=approve&type=volunteer&id=<?php echo $v['id']; ?>" class="btn-action btn-approve" onclick="return confirm('Setujui pendaftar ini?');">✓</a>
                                            <a href="?action=reject&type=volunteer&id=<?php echo $v['id']; ?>" class="btn-action btn-reject" onclick="return confirm('Tolak pendaftar ini?');">✗</a>
                                        <?php else: ?>
                                            <span class="text-muted small">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tenant-content">
                <div class="card-admin p-3">
                    <table id="tableTenant" class="table table-striped table-hover" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Brand</th>
                                <th>Narahubung</th>
                                <th>Kontak</th>
                                <th>Jenis Booth</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tenants as $t): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($t['brand_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($t['contact_person']); ?></td>
                                    <td>
                                        <small><?php echo htmlspecialchars($t['email']); ?></small><br>
                                        <small><?php echo htmlspecialchars($t['phone_number']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($t['booth_type']); ?></td>
                                    <td>
                                        <?php 
                                            $st = $t['status']; 
                                            $badge = ($st=='approved')?'success':(($st=='rejected')?'danger':'warning');
                                        ?>
                                        <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($st); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($t['status'] == 'pending'): ?>
                                            <a href="?action=approve&type=tenant&id=<?php echo $t['id']; ?>" class="btn-action btn-approve">✓</a>
                                            <a href="?action=reject&type=tenant&id=<?php echo $t['id']; ?>" class="btn-action btn-reject">✗</a>
                                        <?php else: ?>
                                            <span class="text-muted small">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div> </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tableVolunteer').DataTable();
        $('#tableTenant').DataTable();
    });
</script>

</body>
</html>