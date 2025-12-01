<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Tiket';
$message = '';
$message_type = '';

// --- 1. CEK FLASH MESSAGE DARI SESSION ---
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

// --- 2. LOGIKA NONAKTIFKAN TIKET (Stok = 0) ---
if (isset($_GET['action']) && $_GET['action'] == 'nonaktifkan' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("UPDATE tickets SET quantity_available = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = 'Tiket berhasil dinonaktifkan (Stok diatur ke 0).';
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = 'Gagal update status: ' . $stmt->error;
        $_SESSION['flash_type'] = 'error';
    }
    header("Location: kelola_tiket.php");
    exit();
}

// --- 3. LOGIKA HAPUS PERMANEN ---
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];

    // Coba Hapus Database
    $stmt = $conn->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);

    try {
        if ($stmt->execute()) {
            // Hapus gambar terkait jika ada
            $conn->query("DELETE FROM ticket_images WHERE ticket_id = $id_to_delete");
            
            $_SESSION['flash_message'] = 'Tiket BERHASIL dihapus permanen!';
            $_SESSION['flash_type'] = 'success';
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        // Tangkap Error jika tiket sudah pernah dibeli (Foreign Key)
        $_SESSION['flash_message'] = 'GAGAL MENGHAPUS: Tiket ini sudah ada di riwayat transaksi. Silakan gunakan tombol "Nonaktifkan" saja.';
        $_SESSION['flash_type'] = 'error'; 
    }

    header("Location: kelola_tiket.php");
    exit();
}

// --- 4. AMBIL DATA TIKET ---
$sql = "SELECT t.*, 
        (SELECT image_url FROM ticket_images WHERE ticket_id = t.id LIMIT 1) as main_image 
        FROM tickets t ORDER BY t.id DESC";
$tickets = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
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
            <h1 class="page-main-title">Kelola Tiket</h1>
            <a href="edit_tiket.php" class="btn btn-primary">Tambah Tiket Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <strong><?php echo ($message_type == 'success') ? 'Sukses!' : 'Peringatan!'; ?></strong> <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <table id="ticketTable" class="table table-striped table-hover" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Foto</th>
                    <th>Nama Tiket</th>
                    <th>Tag / Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th width="20%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tickets)): ?>
                    <?php $no = 1; foreach ($tickets as $ticket): ?>
                        <tr>
                            <td class="align-middle"><?php echo $no++; ?></td>
                            <td class="align-middle">
                                <img src="../assets/images/tickets/<?php echo htmlspecialchars(!empty($ticket['main_image']) ? $ticket['main_image'] : 'default_ticket.jpg'); ?>" 
                                     alt="Tiket" 
                                     class="table-thumbnail"
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6;">
                            </td>
                            <td class="align-middle"><strong><?php echo htmlspecialchars($ticket['category_name']); ?></strong></td>
                            <td class="align-middle"><span class="badge bg-secondary"><?php echo htmlspecialchars($ticket['filter_tag']); ?></span></td>
                            <td class="align-middle">Rp <?php echo number_format($ticket['price'], 0, ',', '.'); ?></td>
                            <td class="align-middle">
                                <?php if($ticket['quantity_available'] > 0): ?>
                                    <span class="badge bg-success" style="font-size: 0.9em;"><?php echo $ticket['quantity_available']; ?> pcs</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Habis / Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle">
                                <div class="btn-group" role="group">
                                    <a href="edit_tiket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                                    
                                    <?php if ($ticket['quantity_available'] > 0): ?>
                                        <a href="kelola_tiket.php?action=nonaktifkan&id=<?php echo $ticket['id']; ?>" 
                                           class="btn btn-sm btn-secondary" 
                                           onclick="return confirm('Nonaktifkan tiket ini? Stok akan diubah menjadi 0.');" 
                                           title="Set Stok ke 0">
                                           Nonaktif
                                        </a>
                                    <?php endif; ?>

                                    <a href="kelola_tiket.php?action=hapus&id=<?php echo $ticket['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('BAHAYA: Hapus permanen tiket ini? Data tidak bisa dikembalikan.');"
                                       title="Hapus Permanen">
                                       Hapus
                                    </a>
                                </div>
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
        $('#ticketTable').DataTable({
            "language": {
                "search": "Cari Tiket:",
                "paginate": { "next": ">", "previous": "<" }
            }
        });
    });
</script>

</body>
</html>