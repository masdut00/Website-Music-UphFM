<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Jadwal';
$message = '';
$message_type = '';

// --- CEK FLASH MESSAGE (Dari edit/add) ---
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

// --- LOGIKA HAPUS DATA ---
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = 'Jadwal berhasil dihapus!';
        $_SESSION['flash_type'] = 'success';
        header("Location: kelola_jadwal.php");
        exit();
    } else {
        $message = 'Gagal menghapus jadwal: '. $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// --- AMBIL DATA JADWAL (JOIN ARTIS & PANGGUNG) ---
$sql = "SELECT 
            s.id, 
            s.event_day, 
            s.start_time, 
            s.end_time,
            a.name AS artist_name,
            st.name AS stage_name
        FROM schedules s
        JOIN artists a ON s.artist_id = a.id
        JOIN stages st ON s.stage_id = st.id
        ORDER BY s.event_day ASC, s.start_time ASC";
            
$schedules = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
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
            <h1 class="page-main-title">Kelola Jadwal Acara</h1>
            <a href="edit_jadwal.php" class="btn btn-primary">Tambah Jadwal Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <table id="scheduleTable" class="table table-striped table-hover" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Hari/Tanggal</th>
                    <th>Jam</th>
                    <th>Artis</th>
                    <th>Panggung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td data-sort="<?php echo $schedule['event_day']; ?>">
                            <?php 
                                $date = strtotime($schedule['event_day']);
                                // Array hari indonesia manual agar pasti benar
                                $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                echo $days[date('w', $date)] . ', ' . date('d M Y', $date);
                            ?>
                        </td>
                        
                        <td class="fw-bold text-primary">
                            <?php echo date('H:i', strtotime($schedule['start_time'])); ?> - 
                            <?php echo date('H:i', strtotime($schedule['end_time'])); ?>
                        </td>
                        
                        <td><strong><?php echo htmlspecialchars($schedule['artist_name']); ?></strong></td>
                        
                        <td>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($schedule['stage_name']); ?></span>
                        </td>
                        
                        <td>
                            <a href="edit_jadwal.php?id=<?php echo $schedule['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                            
                            <a href="kelola_jadwal.php?action=hapus&id=<?php echo $schedule['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Anda yakin ingin menghapus jadwal untuk <?php echo htmlspecialchars($schedule['artist_name']); ?>?');">
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
        $('#scheduleTable').DataTable({
            "order": [[ 0, "asc" ], [ 1, "asc" ]] // Urutkan default berdasarkan Hari lalu Jam
        });
    });
</script>

</body>
</html>