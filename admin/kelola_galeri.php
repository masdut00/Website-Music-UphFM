<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Galeri Foto';
$message = '';
$message_type = '';

// --- 1. CEK FLASH MESSAGE ---
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

// --- 2. LOGIKA HAPUS FOTO ---
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    // Ambil nama file gambar dulu sebelum hapus DB
    $stmt_img = $conn->prepare("SELECT media_url FROM gallery WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $img = $stmt_img->get_result()->fetch_assoc();
    $stmt_img->close();

    // Hapus Data Database
    $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    
    if ($stmt->execute()) {
        // Hapus File Fisik di Folder
        if ($img && !empty($img['media_url'])) {
            $path = '../assets/images/gallery/' . $img['media_url'];
            if (file_exists($path)) { unlink($path); }
        }
        
        $_SESSION['flash_message'] = 'Foto berhasil dihapus!';
        $_SESSION['flash_type'] = 'success';
        
        // PENTING: Redirect kembali ke file ini (kelola_galeri.php)
        header("Location: kelola_galeri.php");
        exit();
    } else {
        $message = 'Gagal menghapus: ' . $stmt->error;
        $message_type = 'error';
    }
}

// --- 3. AMBIL DATA GALERI ---
$gallery_items = $conn->query("SELECT * FROM gallery ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
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
        a { text-decoration: none; }
        .dataTables_wrapper { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main-content">
        <div class="admin-header d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-main-title">Kelola Galeri</h1>
            <a href="edit_gallery.php" class="btn btn-primary">Upload Foto Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <table id="galleryTable" class="table table-striped table-hover" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Preview</th>
                    <th>Judul</th>
                    <th>Nama Album</th>
                    <th>Highlight?</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($gallery_items as $item): ?>
                    <tr>
                        <td class="align-middle"><?php echo $no++; ?></td>
                        <td class="align-middle">
                            <img src="../assets/images/gallery/<?php echo htmlspecialchars($item['media_url']); ?>" 
                                 class="table-thumbnail" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td class="align-middle"><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                        
                        <td class="align-middle">
                            <span class="badge bg-info text-dark">📂 <?php echo htmlspecialchars($item['album_name']); ?></span>
                        </td>

                        <td class="align-middle">
                            <?php if ($item['is_highlight']): ?>
                                <span class="badge bg-warning text-dark">⭐ Highlight</span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="align-middle">
                            <a href="edit_gallery.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                            
                            <a href="kelola_galeri.php?action=hapus&id=<?php echo $item['id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Hapus foto ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#galleryTable').DataTable({
            "language": {
                "search": "Cari Foto:",
                "paginate": { "next": ">", "previous": "<" }
            }
        });
    });
</script>

</body>
</html>