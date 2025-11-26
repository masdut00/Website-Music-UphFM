<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Tim (About Us)';

// Hapus Data
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT image_url FROM teams WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    
    $del = $conn->prepare("DELETE FROM teams WHERE id = ?");
    $del->bind_param("i", $id);
    if ($del->execute()) {
        if ($data['image_url'] && file_exists('../assets/images/team/' . $data['image_url'])) {
            unlink('../assets/images/team/' . $data['image_url']);
        }
        $_SESSION['flash_message'] = 'Anggota tim berhasil dihapus.';
        $_SESSION['flash_type'] = 'success';
        header("Location: kelola_tim.php");
        exit();
    }
}

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
</head>
<body>
<div class="admin-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="admin-main-content">
        <div class="admin-header d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-main-title">Kelola Tim</h1>
            <a href="edit_tim.php" class="btn btn-primary">Tambah Anggota</a>
        </div>
        
        <table id="teamTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Jabatan (Role)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $t): ?>
                <tr>
                    <td><img src="../assets/images/team/<?php echo $t['image_url']; ?>" style="width:50px; height:50px; object-fit:cover; border-radius:50%;"></td>
                    <td><?php echo htmlspecialchars($t['name']); ?></td>
                    <td><?php echo htmlspecialchars($t['role']); ?></td>
                    <td>
                        <a href="edit_tim.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                        <a href="kelola_tim.php?action=hapus&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?');">Hapus</a>
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
<script> $(document).ready(function() { $('#teamTable').DataTable(); }); </script>
</body>
</html>