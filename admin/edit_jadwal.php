<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Tambah Jadwal';
$schedule_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($schedule_id > 0);
$message = '';
$message_type = '';

// Default values
$artist_id = ''; 
$stage_name = ''; // Kita pakai nama, bukan ID untuk tampilan
$event_day = ''; 
$start_time = ''; 
$end_time = '';

// --- 1. AMBIL DATA UNTUK DROPDOWN/LIST ---
$artists = $conn->query("SELECT id, name FROM artists ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
// Ambil nama panggung unik untuk saran (datalist)
$existing_stages = $conn->query("SELECT DISTINCT name FROM stages ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// --- 2. AMBIL DATA JADWAL JIKA EDIT ---
if ($is_edit_mode) {
    $page_title = 'Edit Jadwal';
    // Join ke tabel stages untuk mengambil nama panggungnya
    $sql = "SELECT s.*, st.name as stage_name 
            FROM schedules s 
            LEFT JOIN stages st ON s.stage_id = st.id 
            WHERE s.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $schedule = $stmt->get_result()->fetch_assoc();
    
    if ($schedule) {
        $artist_id = $schedule['artist_id'];
        $stage_name = $schedule['stage_name']; // Nama panggung dari DB
        $event_day = $schedule['event_day'];
        $start_time = $schedule['start_time'];
        $end_time = $schedule['end_time'];
    } else {
        header("Location: kelola_jadwal.php");
        exit();
    }
    $stmt->close();
}

// --- 3. PROSES SIMPAN DATA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artist_id = $_POST['artist_id'];
    $input_stage_name = trim($_POST['stage_name']); // Nama panggung yang diketik
    $event_day = $_POST['event_day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // --- LOGIKA PINTAR PANGGUNG (Stage) ---
    // Cek apakah nama panggung ini sudah ada di tabel 'stages'?
    $check_stage = $conn->prepare("SELECT id FROM stages WHERE name = ?");
    $check_stage->bind_param("s", $input_stage_name);
    $check_stage->execute();
    $res_stage = $check_stage->get_result();
    
    if ($res_stage->num_rows > 0) {
        // Jika ADA: Ambil ID-nya
        $row = $res_stage->fetch_assoc();
        $final_stage_id = $row['id'];
    } else {
        // Jika TIDAK ADA: Buat baru di tabel stages
        $insert_stage = $conn->prepare("INSERT INTO stages (name) VALUES (?)");
        $insert_stage->bind_param("s", $input_stage_name);
        if ($insert_stage->execute()) {
            $final_stage_id = $insert_stage->insert_id; // Ambil ID baru
        } else {
            $message = "Gagal membuat panggung baru.";
            $message_type = "error";
        }
        $insert_stage->close();
    }
    $check_stage->close();

    // --- SIMPAN KE SCHEDULES ---
    if (empty($message)) {
        if ($is_edit_mode) {
            $sql = "UPDATE schedules SET artist_id = ?, stage_id = ?, event_day = ?, start_time = ?, end_time = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisssi", $artist_id, $final_stage_id, $event_day, $start_time, $end_time, $schedule_id);
        } else {
            $sql = "INSERT INTO schedules (artist_id, stage_id, event_day, start_time, end_time) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisss", $artist_id, $final_stage_id, $event_day, $start_time, $end_time);
        }
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = 'Jadwal berhasil disimpan!';
            $_SESSION['flash_type'] = 'success';
            header("Location: kelola_jadwal.php");
            exit();
        } else {
            $message = 'Gagal menyimpan jadwal: ' . $stmt->error;
            $message_type = 'error';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/admin_styles.css"> 
</head>
<body>

<div class="admin-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main-content">
        <div class="admin-header">
            <h1 class="page-main-title"><?php echo $page_title; ?></h1>
            <a href="kelola_jadwal.php" class="btn-back">← Kembali</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-error" style="background: #f8d7da; padding: 15px; border-radius: 5px; margin-bottom:20px; color: #721c24;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="card-admin" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <form class="admin-form" action="" method="POST">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Pilih Artis</label>
                    <select id="artist_id" name="artist_id" class="form-control" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                        <option value="">-- Pilih Artis --</option>
                        <?php foreach ($artists as $artist): ?>
                            <option value="<?php echo $artist['id']; ?>" <?php echo ($artist_id == $artist['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($artist['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Nama Panggung</label>
                    
                    <input type="text" 
                           name="stage_name" 
                           class="form-control" 
                           list="stage_options" 
                           value="<?php echo htmlspecialchars($stage_name); ?>" 
                           placeholder="Pilih panggung atau ketik nama baru..." 
                           required 
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">

                    <datalist id="stage_options">
                        <?php foreach ($existing_stages as $stg): ?>
                            <option value="<?php echo htmlspecialchars($stg['name']); ?>">
                        <?php endforeach; ?>
                    </datalist>
                    
                    <small style="color:#666; font-size:0.85em; margin-top:5px; display:block;">
                        <i>Tips:</i> Ketik nama panggung baru untuk menambahkannya secara otomatis.
                    </small>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Hari/Tanggal</label>
                    <input type="date" id="event_day" name="event_day" class="form-control" value="<?php echo htmlspecialchars($event_day); ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>

                <div class="form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div class="form-group">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Jam Mulai</label>
                        <input type="time" id="start_time" name="start_time" class="form-control" value="<?php echo htmlspecialchars($start_time); ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    </div>
                    <div class="form-group">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Jam Selesai</label>
                        <input type="time" id="end_time" name="end_time" class="form-control" value="<?php echo htmlspecialchars($end_time); ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    </div>
                </div>
                
                <button type="submit" class="btn-save" style="background: #007bff; color: white; border: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; cursor: pointer;">Simpan Jadwal</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>