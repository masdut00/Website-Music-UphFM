<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Artis / Lineup';
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
    $id_to_delete = (int)$_GET['id'];
    
    // A. Ambil nama file gambar
    $stmt_img = $conn->prepare("SELECT image_url FROM artists WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();

    if ($result_img->num_rows > 0) {
        $img = $result_img->fetch_assoc();

        // B. Hapus data dari database
        $stmt = $conn->prepare("DELETE FROM artists WHERE id = ?");
        $stmt->bind_param("i", $id_to_delete);
        
        if ($stmt->execute()) {
            // C. Hapus file fisik
            if ($img['image_url'] && file_exists('../assets/images/artists/' . $img['image_url'])) {
                unlink('../assets/images/artists/' . $img['image_url']);
            }

            $_SESSION['flash_message'] = 'Artis berhasil dihapus permanen!';
            $_SESSION['flash_type'] = 'success';
            header("Location: kelola_artis.php");
            exit();

        } else {
            $message = 'Gagal menghapus artis: ' . $stmt->error;
            $message_type = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Data artis tidak ditemukan.';
        $message_type = 'error';
    }
    $stmt_img->close();
}

// --- 3. AMBIL DATA ---
$artists = $conn->query("SELECT * FROM artists ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <link rel="stylesheet" href="../assets/css/admin_styles.css"> 

    <style>
        /* Sedikit penyesuaian agar wrapper admin tidak tertimpa Bootstrap */
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
            <h1 class="page-main-title">Kelola Artis / Lineup</h1>
            <a href="edit_artis.php" class="btn btn-primary">Tambah Artis Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <table id="myTable" class="table table-striped table-hover" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Foto</th>
                    <th>Nama Artis</th>
                    <th>Genre</th>
                    <th>Headliner</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($artists as $artist): ?>
                    <tr>
                        <td class="align-middle"><?php echo $no++; ?></td>
                        <td class="align-middle">
                            <img src="../assets/images/artists/<?php echo htmlspecialchars(!empty($artist['image_url']) ? $artist['image_url'] : 'default.jpg'); ?>" 
                                 alt="Foto" 
                                 class="table-thumbnail" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td class="align-middle"><strong><?php echo htmlspecialchars($artist['name']); ?></strong></td>
                        <td class="align-middle"><?php echo htmlspecialchars($artist['genre']); ?></td>
                        <td class="align-middle">
                            <?php if ($artist['is_headliner']): ?>
                                <span class="badge bg-success">Ya</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Tidak</span>
                            <?php endif; ?>
                        </td>
                        <td class="align-middle">
                            <a href="edit_artis.php?id=<?php echo $artist['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                            
                            <a href="kelola_artis.php?action=hapus&id=<?php echo $artist['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('PERINGATAN: Anda yakin ingin menghapus artis <?php echo htmlspecialchars($artist['name']); ?>?');">
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
        $('#myTable').DataTable({
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ baris",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(dari total _MAX_ data)",
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