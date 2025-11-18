<?php
require_once '../includes/admin_auth.php'; // Keamanan
require_once '../includes/db.php';

$page_title = 'Kelola Galeri Foto';
$message = '';
$message_type = '';

// Logika HAPUS (DELETE)
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    // Ambil nama file gambar untuk dihapus dari server
    $stmt_img = $conn->prepare("SELECT media_url FROM gallery WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $img = $stmt_img->get_result()->fetch_assoc();
    $image_file_to_delete = $img['media_url'] ?? null;
    $stmt_img->close();

    // Hapus data dari database
    $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    if ($stmt->execute()) {
        // Hapus file fisik jika ada
        if ($image_file_to_delete && file_exists('../assets/images/gallery/' . $image_file_to_delete)) {
            unlink('../assets/images/gallery/' . $image_file_to_delete);
        }
        $message = 'Gambar galeri berhasil dihapus!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menghapus gambar: ' . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// Ambil semua data galeri
$gallery_items = $conn->query("SELECT * FROM gallery ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin_styles.css"> 
    <style>
        /* CSS khusus untuk tampilan grid galeri di admin */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .gallery-item-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: relative;
        }
        .gallery-item-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
        }
        .gallery-item-info {
            padding: 10px;
        }
        .gallery-item-info h4 {
            margin: 0 0 5px 0;
            font-size: 1.1em;
            color: var(--dark-color);
        }
        .gallery-item-info p {
            font-size: 0.85em;
            color: #666;
            margin-bottom: 10px;
        }
        .gallery-item-actions {
            display: flex;
            justify-content: space-between;
            padding: 0 10px 10px;
        }
        .gallery-item-card .btn-edit,
        .gallery-item-card .btn-delete {
            padding: 5px 10px;
            font-size: 0.8em;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="admin-main-content">
        <div class="admin-header">
            <h1 class="page-main-title">Kelola Galeri Foto</h1>
            <a href="upload_gambar.php" class="btn-standard">Unggah Gambar Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <?php if (!empty($gallery_items)): ?>
            <div class="gallery-grid">
                <?php foreach ($gallery_items as $item): ?>
                    <div class="gallery-item-card">
                        <img src="/upfm_web/assets/images/gallery/<?php echo htmlspecialchars($item['media_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="gallery-item-info">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p>Jenis: <strong><?php echo htmlspecialchars($item['media_type']); ?></strong></p>
                            <p style="font-size: 0.8em; color: #777;">Tahun: <?php echo htmlspecialchars($item['year']); ?></p>
                        </div>
                        <div class="gallery-item-actions">
                            <a href="upload_gambar.php?id=<?php echo $item['id']; ?>" class="btn-edit">Edit</a>
                            <a href="kelola_galeri.php?action=hapus&id=<?php echo $item['id']; ?>" class="btn-delete" onclick="return confirm('Anda yakin ingin menghapus gambar ini? Ini akan menghapus file fisiknya juga!');">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Belum ada gambar di galeri. Silakan unggah yang pertama!</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>