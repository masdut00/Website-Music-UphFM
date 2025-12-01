<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Tim (About Us)';
$message = '';
$message_type = '';

// --- 1. CEK FLASH MESSAGE ---
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

// --- 2. LOGIKA HAPUS DATA ---
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Ambil info gambar dulu
    $stmt = $conn->prepare("SELECT image_url FROM teams WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Hapus dari DB
    $del = $conn->prepare("DELETE FROM teams WHERE id = ?");
    $del->bind_param("i", $id);
    
    if ($del->execute()) {
        // Hapus file fisik
        if ($data['image_url'] && file_exists('../assets/images/team/' . $data['image_url'])) {
            unlink('../assets/images/team/' . $data['image_url']);
        }
        
        $_SESSION['flash_message'] = 'Anggota tim berhasil dihapus.';
        $_SESSION['flash_type'] = 'success';
        header("Location: kelola_tim.php");
        exit();
    } else {
        $message = 'Gagal menghapus: ' . $conn->error;
        $message_type = 'error';
    }
}

// --- 3. AMBIL DATA ---
$teams = $conn->query("SELECT * FROM teams ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
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
        /* Style agar tabel ada di dalam kotak putih */
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
            <h1 class="page-main-title">Kelola Tim</h1>
            <a href="edit_tim.php" class="btn btn-primary">Tambah Anggota</a>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <table id="teamTable" class="table table-striped table-hover" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th width="10%">Foto</th>
                    <th>Nama Lengkap</th>
                    <th>Jabatan (Role)</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $t): ?>
                <tr>
                    <td class="align-middle">
                        <img src="../assets/images/team/<?php echo htmlspecialchars($t['image_url']); ?>" 
                             alt="Foto"
                             class="table-thumbnail" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 1px solid #dee2e6;">
                    </td>
                    <td class="align-middle"><strong><?php echo htmlspecialchars($t['name']); ?></strong></td>
                    <td class="align-middle"><span class="badge bg-secondary"><?php echo htmlspecialchars($t['role']); ?></span></td>
                    <td class="align-middle">
                        <a href="edit_tim.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                        <a href="kelola_tim.php?action=hapus&id=<?php echo $t['id']; ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Yakin ingin menghapus anggota tim ini?');">
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
        $('#teamTable').DataTable({
            "language": {
                "search": "Cari Anggota:",
                "paginate": { "next": ">", "previous": "<" }
            }
        });
    });
</script>

</body>
</html>