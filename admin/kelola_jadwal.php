<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Kelola Jadwal';
$message = '';
$message_type = '';

// Logika hapus
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    if ($stmt->execute()) {
        $message = 'Jadwal berhasil dihapus!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menghapus jadwal: '. $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// Ambil semua data jadwal pake JOIN
$sql = "SELECT 
            s.id, 
            s.event_day, 
            s.start_time, 
            s.end_time,
            a.name AS artist_name,
            st.name AS stage_name
        FROM 
            schedules s
        JOIN 
            artists a ON s.artist_id = a.id
        JOIN 
            stages st ON s.stage_id = st.id
        ORDER BY 
            s.event_day, s.start_time";
            
$schedules = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin_styles.css"> 
</head>
<body>
<div class="admin-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="admin-main-content">
        <div class="admin-header">
            <h1 class="page-main-title">Kelola Jadwal Acara</h1>
            <a href="edit_jadwal.php" class="btn-standard">Tambah Jadwal Baru</a>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Artis</th>
                    <th>Panggung</th>
                    <th>Hari/Tanggal</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($schedule['artist_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($schedule['stage_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($schedule['event_day'])); ?></td>
                        <td><?php echo date('H:i', strtotime($schedule['start_time'])); ?></td>
                        <td><?php echo date('H:i', strtotime($schedule['end_time'])); ?></td>
                        <td class="table-actions">
                            <a href="edit_jadwal.php?id=<?php echo $schedule['id']; ?>" class="btn-edit">Edit</a>
                            <a href="kelola_jadwal.php?action=hapus&id=<?php echo $schedule['id']; ?>" class="btn-delete" onclick="return confirm('Anda yakin ingin menghapus jadwal ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>