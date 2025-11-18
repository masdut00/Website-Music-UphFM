<?php
require_once '../includes/admin_auth.php'; // Keamanan
require_once '../includes/db.php';

$page_title = 'Kelola Jurnal/Artikel';
$message = '';
$message_type = '';

// Logika HAPUS (DELETE)
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    // Ambil nama file gambar untuk dihapus dari server
    $stmt_img = $conn->prepare("SELECT image_url FROM journal_articles WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $img = $stmt_img->get_result()->fetch_assoc();
    if ($img && !empty($img['image_url']) && file_exists('../assets/images/articles/' . $img['image_url'])) {
        unlink('../assets/images/articles/' . $img['image_url']);
    }
    
    // Hapus data dari database
    $stmt = $conn->prepare("DELETE FROM journal_articles WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    if ($stmt->execute()) {
        $message = 'Artikel berhasil dihapus!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menghapus artikel: ' . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// Ambil semua data artikel
$articles = $conn->query("SELECT * FROM journal_articles ORDER BY publish_date DESC")->fetch_all(MYSQLI_ASSOC);

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
            <h1 class="page-main-title">Kelola Artikel Jurnal</h1>
            <a href="edit_artikel.php" class="btn-standard">Tulis Artikel Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Judul Artikel</th>
                    <th>Kategori</th>
                    <th>Tanggal Terbit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($article['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($article['category']); ?></td>
                        <td><?php echo date('d M Y', strtotime($article['publish_date'])); ?></td>
                        <td class="table-actions">
                            <a href="edit_artikel.php?id=<?php echo $article['id']; ?>" class="btn-edit">Edit</a>
                            <a href="kelola_artikel.php?action=hapus&id=<?php echo $article['id']; ?>" class="btn-delete" onclick="return confirm('Anda yakin ingin menghapus permanen artikel ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>