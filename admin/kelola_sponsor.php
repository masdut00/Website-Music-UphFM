<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';
$page_title = 'Kelola Sponsor';

// Hapus logic
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT image_url FROM sponsors WHERE id = ?");
    $stmt->bind_param("i", $id); $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    
    $del = $conn->prepare("DELETE FROM sponsors WHERE id = ?");
    $del->bind_param("i", $id);
    if ($del->execute()) {
        if ($data['image_url']) unlink('../assets/images/sponsors/' . $data['image_url']);
        header("Location: kelola_sponsor.php"); exit();
    }
}
$sponsors = $conn->query("SELECT * FROM sponsors ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
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
            <h1 class="page-main-title">Kelola Sponsor & Partner</h1>
            <a href="edit_sponsor.php" class="btn btn-primary">Tambah Logo</a>
        </div>
        <table id="sponsorTable" class="table table-striped">
            <thead><tr><th>Logo</th><th>Nama Sponsor</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach ($sponsors as $s): ?>
                <tr>
                    <td><img src="../assets/images/sponsors/<?php echo $s['image_url']; ?>" style="height:50px; object-fit:contain;"></td>
                    <td><?php echo htmlspecialchars($s['name']); ?></td>
                    <td>
                        <a href="edit_sponsor.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                        <a href="kelola_sponsor.php?action=hapus&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?');">Hapus</a>
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
<script> $(document).ready(function() { $('#sponsorTable').DataTable(); }); </script>
</body>
</html>