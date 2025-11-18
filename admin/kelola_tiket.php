<?php
require_once '../includes/admin_auth.php'; // Keamanan
require_once '../includes/db.php';

$page_title = 'Kelola Tiket';
$message = '';
$message_type = '';

// Logika HAPUS TIKET
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    // Hati-hati: Menghapus tiket bisa merusak data di 'ticket_purchases'
    // Sebaiknya, kita nonaktifkan saja tiketnya daripada menghapus
    // $stmt = $conn->prepare("DELETE FROM tickets WHERE id = ?");
    
    // Cara yang LEBIH AMAN: Set quantity_available = 0 (menonaktifkan)
    $stmt = $conn->prepare("UPDATE tickets SET quantity_available = 0 WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    
    if ($stmt->execute()) {
        $message = 'Tiket telah dinonaktifkan (stok 0)!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menonaktifkan tiket: ' . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// Ambil semua data tiket
$sql = "SELECT t.*, COUNT(tt.id) AS type_count 
        FROM tickets t
        LEFT JOIN ticket_types tt ON t.id = tt.ticket_id
        GROUP BY t.id
        ORDER BY t.id DESC";
$tickets = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

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
        <div class="admin-header">
            <h1 class="page-main-title">Kelola Tiket</h1>
            <a href="edit_tiket.php" class="btn-standard">Tambah Tiket Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Tiket</th>
                    <th>Tag Filter</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Jml. Tipe</th> <th>Aksi</th>
                </tr>
            </thead>
           <tbody>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td><?php echo $ticket['id']; ?></td>
                        <td><?php echo htmlspecialchars($ticket['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['filter_tag']); ?></td>
                        <td>Rp <?php echo number_format($ticket['price']); ?></td>
                        <td><?php echo $ticket['quantity_available'] > 0 ? $ticket['quantity_available'] : 'Habis'; ?></td>
                        
                        <td><?php echo $ticket['type_count']; ?></td>

                        <td class="table-actions">
                            <a href="edit_tiket.php?id=<?php echo $ticket['id']; ?>" class="btn-edit">Edit</a>
                            <?php if ($ticket['quantity_available'] > 0): ?>
                                <a href="kelola_tiket.php?action=hapus&id=<?php echo $ticket['id']; ?>" class="btn-delete" onclick="return confirm('Anda yakin ingin menonaktifkan tiket ini (mengatur stok menjadi 0)?');">Nonaktifkan</a>
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