<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Tambah Jadwal';
$schedule_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($schedule_id > 0);
$message = '';
$message_type = '';

// --- Ambil data untuk <select> dropdown ---
$artists = $conn->query("SELECT id, name FROM artists ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$stages = $conn->query("SELECT id, name FROM stages ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Inisialisasi variabel
$artist_id = ''; $stage_id = ''; $event_day = ''; $start_time = ''; $end_time = '';

// Ambil data lama jika ini mode edit
if ($is_edit_mode) {
    $page_title = 'Edit Jadwal';
    $stmt = $conn->prepare("SELECT * FROM schedules WHERE id = ?");
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $schedule = $stmt->get_result()->fetch_assoc();
    if ($schedule) {
        $artist_id = $schedule['artist_id'];
        $stage_id = $schedule['stage_id'];
        $event_day = $schedule['event_day'];
        $start_time = $schedule['start_time'];
        $end_time = $schedule['end_time'];
    }
    $stmt->close();
}

// Logika Simpan Data (Create / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artist_id = $_POST['artist_id'];
    $stage_id = $_POST['stage_id'];
    $event_day = $_POST['event_day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if ($is_edit_mode) {
        $sql = "UPDATE schedules SET artist_id = ?, stage_id = ?, event_day = ?, start_time = ?, end_time = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssi", $artist_id, $stage_id, $event_day, $start_time, $end_time, $schedule_id);
    } else {
        $sql = "INSERT INTO schedules (artist_id, stage_id, event_day, start_time, end_time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $artist_id, $stage_id, $event_day, $start_time, $end_time);
    }
    
    if ($stmt->execute()) {
        header("Location: kelola_jadwal.php");
        exit();
    } else {
        $message = 'Gagal menyimpan jadwal: ' . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}
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
        <h1 class="page-main-title"><?php echo $page_title; ?></h1>
        <a href="kelola_jadwal.php" class="btn-back">← Kembali ke Daftar Jadwal</a>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <form class="admin-form" action="edit_jadwal.php?id=<?php echo $schedule_id; ?>" method="POST">
            
            <div class="form-group">
                <label for="artist_id">Pilih Artis</label>
                <select id="artist_id" name="artist_id" required>
                    <option value="">-- Pilih Artis --</option>
                    <?php foreach ($artists as $artist): ?>
                        <option value="<?php echo $artist['id']; ?>" <?php echo ($artist_id == $artist['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($artist['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="stage_id">Pilih Panggung</label>
                <select id="stage_id" name="stage_id" required>
                    <option value="">-- Pilih Panggung --</option>
                    <?php foreach ($stages as $stage): ?>
                        <option value="<?php echo $stage['id']; ?>" <?php echo ($stage_id == $stage['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($stage['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="event_day">Hari/Tanggal</label>
                <input type="date" id="event_day" name="event_day" value="<?php echo htmlspecialchars($event_day); ?>" required>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="start_time">Jam Mulai</label>
                    <input type="time" id="start_time" name="start_time" value="<?php echo htmlspecialchars($start_time); ?>" required>
                </div>
                <div class="form-group">
                    <label for="end_time">Jam Selesai</label>
                    <input type="time" id="end_time" name="end_time" value="<?php echo htmlspecialchars($end_time); ?>" required>
                </div>
            </div>
            
            <button type="submit" class="btn-standard">Simpan Jadwal</button>
        </form>
    </div>
</div>
</body>
</html>