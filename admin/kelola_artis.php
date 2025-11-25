<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Artis / Lineup';
$message = '';
$message_type = '';

// --- LOGIKA HAPUS DATA ---
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    // 1. Ambil nama file gambar untuk dihapus dari server
    $stmt_img = $conn->prepare("SELECT image_url FROM artists WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $img = $stmt_img->get_result()->fetch_assoc();
    
    // Cek dan hapus file gambar jika ada
    if ($img && !empty($img['image_url']) && file_exists('../assets/images/artists/' . $img['image_url'])) {
        unlink('../assets/images/artists/' . $img['image_url']);
    }
    $stmt_img->close();
    
    // 2. Hapus data dari database
    $stmt = $conn->prepare("DELETE FROM artists WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    
    if ($stmt->execute()) {
        $message = 'Artis berhasil dihapus!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menghapus artis: ' . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// --- AMBIL DATA ARTIS ---
$artists = $conn->query("SELECT * FROM artists ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
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
            <h1 class="page-main-title">Kelola Artis / Lineup</h1>
            <a href="edit_artis.php" class="btn-standard">Tambah Artis Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Artis</th>
                    <th>Genre</th>
                    <th>Headliner</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($artists)): ?>
                    <?php foreach ($artists as $artist): ?>
                        <tr>
                            <td>
                                <img src="../assets/images/artists/<?php echo htmlspecialchars(!empty($artist['image_url']) ? $artist['image_url'] : 'default.jpg'); ?>" 
                                     alt="Foto" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><strong><?php echo htmlspecialchars($artist['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($artist['genre']); ?></td>
                            <td>
                                <?php echo $artist['is_headliner'] ? '<span style="color: green; font-weight: bold;">Ya</span>' : 'Tidak'; ?>
                            </td>
                            <td class="table-actions">
                                <a href="edit_artis.php?id=<?php echo $artist['id']; ?>" class="btn-edit">Edit</a>
                                <a href="kelola_artis.php?action=hapus&id=<?php echo $artist['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Anda yakin ingin menghapus artis ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Belum ada data artis.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>