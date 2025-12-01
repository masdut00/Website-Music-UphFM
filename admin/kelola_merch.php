<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Merchandise';
$message = '';
$message_type = '';

// --- 1. CEK FLASH MESSAGE DARI SESSION ---
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

// --- 2. LOGIKA NONAKTIFKAN ITEM (STOK = 0) ---
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    // Set stok jadi 0 (Soft Delete)
    $stmt = $conn->prepare("UPDATE merchandise SET stock = 0 WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = 'Item berhasil dinonaktifkan (Stok diatur ke 0).';
        $_SESSION['flash_type'] = 'success';
        header("Location: kelola_merch.php");
        exit();
    } else {
        $message = 'Gagal menonaktifkan item: ' . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// --- 3. AMBIL DATA MERCHANDISE ---
$merch_items = $conn->query("SELECT * FROM merchandise ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
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
            <h1 class="page-main-title">Kelola Merchandise</h1>
            <a href="edit_merch.php" class="btn btn-primary">Tambah Item Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <table id="merchTable" class="table table-striped table-hover" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Foto</th>
                    <th>Nama Item</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($merch_items)): ?>
                    <?php $no = 1; foreach ($merch_items as $item): ?>
                        <tr>
                            <td class="align-middle"><?php echo $no++; ?></td>
                            <td class="align-middle">
                                <img src="../assets/images/merch/<?php echo htmlspecialchars(!empty($item['image_url']) ? $item['image_url'] : 'default_merch.jpg'); ?>" 
                                     alt="Item" 
                                     class="table-thumbnail"
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6;">
                            </td>
                            <td class="align-middle"><strong><?php echo htmlspecialchars($item['item_name']); ?></strong></td>
                            <td class="align-middle">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                            <td class="align-middle">
                                <?php if($item['stock'] > 0): ?>
                                    <span class="badge bg-success" style="font-size: 0.9em;"><?php echo $item['stock']; ?> pcs</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Habis / Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle">
                                <a href="edit_merch.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                                
                                <?php if ($item['stock'] > 0): ?>
                                    <a href="kelola_merch.php?action=hapus&id=<?php echo $item['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Apakah Anda yakin ingin menonaktifkan item ini (Stok akan diubah jadi 0)?');">
                                       Nonaktifkan
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>Nonaktif</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
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
        $('#merchTable').DataTable({
            "language": {
                "search": "Cari Produk:",
                "lengthMenu": "Show  _MENU_ item",
                "zeroRecords": "Data merchandise tidak ditemukan",
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