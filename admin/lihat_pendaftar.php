<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Lihat Pendaftar';
$message = '';
$message_type = '';

// untuk update status
if (isset($_GET['action']) && isset($_GET['type']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $type = $_GET['type'];
    $id = (int)$_GET['id'];
    $new_status = '';

    if ($action === 'approve') {
        $new_status = 'approved';
    } elseif ($action === 'reject') {
        $new_status = 'rejected';
    }

    if (!empty($new_status)) {
        $table_name = '';
        if ($type === 'volunteer') {
            $table_name = 'volunteers';
        } elseif ($type === 'tenant') {
            $table_name = 'tenants';
        }

        if ($table_name) {
            $stmt = $conn->prepare("UPDATE $table_name SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $new_status, $id);
            if ($stmt->execute()) {
                $message = 'Status pendaftar berhasil diperbarui!';
                $message_type = 'success';
            } else {
                $message = 'Gagal memperbarui status: ' . $stmt->error;
                $message_type = 'error';
            }
            $stmt->close();
        }
    }
}


// Ambil data Volunteers
$sql_volunteers = "SELECT v.*, u.email AS user_email 
                   FROM volunteers v
                   LEFT JOIN users u ON v.user_id = u.id
                   ORDER BY v.submission_date DESC";
$volunteers = $conn->query($sql_volunteers)->fetch_all(MYSQLI_ASSOC);

// Ambil data Tenants
$sql_tenants = "SELECT t.*, u.email AS user_email 
                FROM tenants t
                LEFT JOIN users u ON t.user_id = u.id
                ORDER BY t.submission_date DESC";
$tenants = $conn->query($sql_tenants)->fetch_all(MYSQLI_ASSOC);

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
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="admin-main-content">
        
        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <h1 class="page-main-title">Pendaftar Volunteer</h1>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email / Telepon</th>
                    <th>Alasan</th>
                    <th>Akun User</th>
                    <th>Status</th> <th>Aksi</th> </tr>
            </thead>
            <tbody>
                <?php foreach ($volunteers as $v): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($v['full_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($v['email']); ?><br><?php echo htmlspecialchars($v['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars(substr($v['reason_to_join'], 0, 100)); ?>...</td>
                        <td><?php echo htmlspecialchars($v['user_email'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $v['status']; ?>">
                                <?php echo ucfirst($v['status']); ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <?php if ($v['status'] == 'pending'): ?>
                                <a href="lihat_pendaftar.php?action=approve&type=volunteer&id=<?php echo $v['id']; ?>" class="btn-approve">Setujui</a>
                                <a href="lihat_pendaftar.php?action=reject&type=volunteer&id=<?php echo $v['id']; ?>" class="btn-reject">Tolak</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr style="margin: 3rem 0;">

        <h1 class="page-main-title">Pendaftar Tenant</h1>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama Brand</th>
                    <th>Narahubung</th>
                    <th>Email / Telepon</th>
                    <th>Jenis Booth</th>
                    <th>Akun User</th>
                    <th>Status</th> <th>Aksi</th> </tr>
            </thead>
            <tbody>
                <?php foreach ($tenants as $t): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($t['brand_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($t['contact_person']); ?></td>
                        <td><?php echo htmlspecialchars($t['email']); ?><br><?php echo htmlspecialchars($t['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($t['booth_type']); ?></td>
                        <td><?php echo htmlspecialchars($t['user_email'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $t['status']; ?>">
                                <?php echo ucfirst($t['status']); ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <?php if ($t['status'] == 'pending'): ?>
                                <a href="lihat_pendaftar.php?action=approve&type=tenant&id=<?php echo $t['id']; ?>" class="btn-approve">Setujui</a>
                                <a href="lihat_pendaftar.php?action=reject&type=tenant&id=<?php echo $t['id']; ?>" class="btn-reject">Tolak</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
</body>
</html>