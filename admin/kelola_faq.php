<?php
require_once '../includes/admin_auth.php'; // Keamanan
require_once '../includes/db.php';

$page_title = 'Kelola FAQ';
$message = '';
$message_type = '';

// Logika HAPUS (DELETE)
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    // Ini adalah hard delete, yang aman untuk FAQ
    $stmt = $conn->prepare("DELETE FROM faq WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    if ($stmt->execute()) {
        $message = 'Item FAQ berhasil dihapus!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menghapus item: '. $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// Ambil semua data FAQ
$faqs = $conn->query("SELECT * FROM faq ORDER BY category, id DESC")->fetch_all(MYSQLI_ASSOC);

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
            <h1 class="page-main-title">Kelola FAQ</h1>
            <a href="edit_faq.php" class="btn-standard">Tambah FAQ Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Pertanyaan</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($faqs as $faq): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($faq['question']); ?></strong></td>
                        <td><?php echo htmlspecialchars($faq['category']); ?></td>
                        <td class="table-actions">
                            <a href="edit_faq.php?id=<?php echo $faq['id']; ?>" class="btn-edit">Edit</a>
                            <a href="kelola_faq.php?action=hapus&id=<?php echo $faq['id']; ?>" class="btn-delete" onclick="return confirm('Anda yakin ingin menghapus permanen pertanyaan ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>