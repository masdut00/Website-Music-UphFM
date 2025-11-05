<?php
require_once '../includes/admin_auth.php'; // Keamanan admin
require_once '../includes/db.php';

$page_title = 'Kelola Artis';
$message = '';
$message_type = '';

// LOGIKA HAPUS DATA (DELETE)
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    // Hapus juga gambar dari server (opsional tapi bagus)
    $stmt_img = $conn->prepare("SELECT image_url FROM artists WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $img = $stmt_img->get_result()->fetch_assoc();
    if ($img && file_exists('../assets/images/artists/' . $img['image_url'])) {
        unlink('../assets/images/artists/' . $img['image_url']);
    }
    
    // Hapus data dari database
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

// LOGIKA AMBIL DATA (READ)
$artists = $conn->query("SELECT * FROM artists ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

require_once '../includes/header.php';
?>

<div class="container page-container">
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
                <th>ID</th>
                <th>Foto</th>
                <th>Nama Artis</th>
                <th>Genre</th>
                <th>Headliner?</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($artists)): ?>
                <?php foreach ($artists as $artist): ?>
                    <tr>
                        <td><?php echo $artist['id']; ?></td>
                        <td>
                            <img src="/upfm_web/assets/images/artists/<?php echo htmlspecialchars($artist['image_url'] ?: 'default.jpg'); ?>" alt="" class="table-thumbnail">
                        </td>
                        <td><?php echo htmlspecialchars($artist['name']); ?></td>
                        <td><?php echo htmlspecialchars($artist['genre']); ?></td>
                        <td><?php echo $artist['is_headliner'] ? 'Ya' : 'Tidak'; ?></td>
                        <td class="table-actions">
                            <a href="edit_artis.php?id=<?php echo $artist['id']; ?>" class="btn-edit">Edit</a>
                            <a href="kelola_artis.php?action=hapus&id=<?php echo $artist['id']; ?>" class="btn-delete" onclick="return confirm('Anda yakin ingin menghapus artis ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Belum ada data artis. Silakan tambah baru.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../includes/footer.php';
?>