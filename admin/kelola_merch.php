<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Merchandise';
$message = '';
$message_type = '';

// Logika hapus
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    $stmt = $conn->prepare("UPDATE merchandise SET stock = 0 WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    
    if ($stmt->execute()) {
        $message = 'Stok merchandise telah diatur ke 0 (nonaktif).';
        $message_type = 'success';
    } else {
        $message = 'Gagal menonaktifkan item: ' . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

$merch_items = $conn->query("SELECT * FROM merchandise ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

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
            <h1 class="page-main-title">Kelola Merchandise</h1>
            <a href="edit_merch.php" class="btn-standard">Tambah Item Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nama Item</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($merch_items as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td>
                            <img src="/upfm_web/assets/images/merch/<?php echo htmlspecialchars($item['image_url'] ?: 'default_merch.jpg'); ?>" alt="" class="table-thumbnail">
                        </td>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td>Rp <?php echo number_format($item['price']); ?></td>
                        <td><?php echo $item['stock'] > 0 ? $item['stock'] : 'Habis'; ?></td>
                        <td class="table-actions">
                            <a href="edit_merch.php?id=<?php echo $item['id']; ?>" class="btn-edit">Edit</a>
                            <?php if ($item['stock'] > 0): ?>
                                <a href="kelola_merch.php?action=hapus&id=<?php echo $item['id']; ?>" class="btn-delete" onclick="return confirm('Anda yakin ingin menonaktifkan item ini (stok = 0)?');">Nonaktifkan</a>
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