<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola FAQ';
$message = '';
$message_type = '';

// --- CEK FLASH MESSAGE ---
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

// --- LOGIKA HAPUS DATA ---
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    // Perhatikan: Nama tabel adalah 'faq'
    $stmt = $conn->prepare("DELETE FROM faq WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = 'Item FAQ berhasil dihapus!';
        $_SESSION['flash_type'] = 'success';
        header("Location: kelola_faq.php");
        exit();
    } else {
        $message = 'Gagal menghapus item: '. $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// --- AMBIL DATA FAQ ---
// Perhatikan: Nama tabel adalah 'faq'
$faqs = [];
$sql = "SELECT * FROM faq ORDER BY category ASC, id DESC";
$result = $conn->query($sql);
if ($result) {
    $faqs = $result->fetch_all(MYSQLI_ASSOC);
}
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
        .dataTables_wrapper { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .answer-preview { max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #666; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    
    <?php require_once '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main-content">
        <div class="admin-header d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-main-title">Kelola FAQ</h1>
            <a href="edit_faq.php" class="btn btn-primary">Tambah FAQ Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <table id="faqTable" class="table table-striped table-hover" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Kategori</th>
                    <th width="25%">Pertanyaan</th>
                    <th>Jawaban (Preview)</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($faqs as $faq): ?>
                    <tr>
                        <td class="align-middle"><?php echo $no++; ?></td>
                        <td class="align-middle">
                            <span class="badge bg-secondary"><?php echo htmlspecialchars(!empty($faq['category']) ? $faq['category'] : 'General'); ?></span>
                        </td>
                        <td class="align-middle"><strong><?php echo htmlspecialchars($faq['question']); ?></strong></td>
                        <td class="align-middle">
                            <div class="answer-preview" title="<?php echo htmlspecialchars($faq['answer']); ?>">
                                <?php echo htmlspecialchars(substr($faq['answer'], 0, 80)) . (strlen($faq['answer']) > 80 ? '...' : ''); ?>
                            </div>
                        </td>
                        <td class="align-middle">
                            <a href="edit_faq.php?id=<?php echo $faq['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                            <a href="kelola_faq.php?action=hapus&id=<?php echo $faq['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Anda yakin ingin menghapus pertanyaan ini?');">Hapus</a>
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
        $('#faqTable').DataTable({
            "language": {
                "search": "Cari Pertanyaan:",
                "paginate": { "next": ">", "previous": "<" }
            }
        });
    });
</script>

</body>
</html>