<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Jurnal/Artikel';
$message = '';
$message_type = '';

// --- CEK FLASH MESSAGE DARI SESSION ---
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

// --- LOGIKA HAPUS DATA PERMANEN ---
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    // 1. Ambil nama file gambar dulu
    $stmt_img = $conn->prepare("SELECT image_url FROM journal_articles WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();
    
    if ($result_img->num_rows > 0) {
        $img = $result_img->fetch_assoc();
        
        // 2. Hapus data dari database
        $stmt = $conn->prepare("DELETE FROM journal_articles WHERE id = ?");
        $stmt->bind_param("i", $id_to_delete);
        
        if ($stmt->execute()) {
            // 3. Hapus file fisik
            if ($img['image_url'] && file_exists('../assets/images/articles/' . $img['image_url'])) {
                unlink('../assets/images/articles/' . $img['image_url']);
            }
            
            $_SESSION['flash_message'] = 'Artikel berhasil dihapus permanen!';
            $_SESSION['flash_type'] = 'success';
            header("Location: kelola_artikel.php");
            exit();
            
        } else {
            $message = 'Gagal menghapus artikel: ' . $stmt->error;
            $message_type = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Data artikel tidak ditemukan.';
        $message_type = 'error';
    }
    $stmt_img->close();
}

// --- AMBIL SEMUA DATA ARTIKEL ---
$articles = $conn->query("SELECT * FROM journal_articles ORDER BY publish_date DESC")->fetch_all(MYSQLI_ASSOC);
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
        .dataTables_wrapper {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="admin-wrapper">
    
    <?php require_once '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main-content">
        <div class="admin-header d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-main-title">Kelola Artikel Jurnal</h1>
            <a href="edit_artikel.php" class="btn btn-primary">Tulis Artikel Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <table id="articleTable" class="table table-striped table-hover" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th>Judul Artikel</th>
                    <th>Kategori</th>
                    <th>Tanggal Terbit</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($articles as $article): ?>
                    <tr>
                        <td class="align-middle"><?php echo $no++; ?></td>
                        <td class="align-middle"><strong><?php echo htmlspecialchars($article['title']); ?></strong></td>
                        <td class="align-middle"><span class="badge bg-secondary"><?php echo htmlspecialchars($article['category']); ?></span></td>
                        <td class="align-middle"><?php echo date('d M Y', strtotime($article['publish_date'])); ?></td>
                        <td class="align-middle">
                            <a href="edit_artikel.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                            
                            <a href="kelola_artikel.php?action=hapus&id=<?php echo $article['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('PERINGATAN: Anda yakin ingin menghapus artikel ini secara permanen? Data tidak bisa dikembalikan.');">
                               Hapus
                            </a>
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
        $('#articleTable').DataTable({
            "language": {
                "search": "Cari Artikel:",
                "paginate": { "next": ">", "previous": "<" }
            }
        });
    });
</script>

</body>
</html>