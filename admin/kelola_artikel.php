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
            // 3. Jika DB sukses dihapus, baru hapus file gambarnya
            if ($img['image_url'] && file_exists('../assets/images/articles/' . $img['image_url'])) {
                unlink('../assets/images/articles/' . $img['image_url']);
            }
            
            // Set pesan sukses dan redirect agar URL bersih
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin_styles.css"> 
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
    <style>
        .dataTables_wrapper {
            margin-top: 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        table.dataTable thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            color: #333;
        }
        .dataTables_filter input {
            border: 1px solid #ddd;
            padding: 5px 10px;
            border-radius: 4px;
        }
    </style>
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
            <div class="alert alert-<?php echo $message_type; ?>" style="background: <?php echo $message_type == 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $message_type == 'success' ? '#155724' : '#721c24'; ?>; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <table id="myTable" class="display admin-table" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Artikel</th>
                    <th>Kategori</th>
                    <th>Tanggal Terbit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($articles as $article): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo htmlspecialchars($article['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($article['category']); ?></td>
                        <td><?php echo date('d M Y', strtotime($article['publish_date'])); ?></td>
                        <td class="table-actions">
                            <a href="edit_artikel.php?id=<?php echo $article['id']; ?>" class="btn-edit" style="margin-right: 5px;">Edit</a>
                            
                            <a href="kelola_artikel.php?action=hapus&id=<?php echo $article['id']; ?>" 
                               class="btn-delete" 
                               style="background-color: #dc3545; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.9em;"
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            "language": {
                "search": "Cari Artikel:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Lanjut",
                    "previous": "Mundur"
                }
            }
        });
    });
</script>

</body>
</html>