<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Unggah Media Galeri';
$image_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($image_id > 0);
$message = '';
$message_type = '';

// Inisialisasi variabel SESUAI DATABASE ANDA
$title = ''; 
$media_type = 'photo'; // Default
$year = date('Y'); // Default tahun ini
$current_media_url = '';

// Ambil data lama jika ini mode edit
if ($is_edit_mode) {
    $page_title = 'Edit Media Galeri';
    $stmt = $conn->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $image_data = $stmt->get_result()->fetch_assoc();
    if ($image_data) {
        // --- BLOK INI KITA UBAH ---
        $title = isset($image_data['title']) ? $image_data['title'] : '';
        $media_type = isset($image_data['media_type']) ? $image_data['media_type'] : 'photo';
        $year = isset($image_data['year']) ? $image_data['year'] : date('Y');
        $current_media_url = isset($image_data['media_url']) ? $image_data['media_url'] : '';
        // --- AKHIR BLOK ---
    }
    $stmt->close();
}

// Logika Simpan Data (Create / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $media_type = $_POST['media_type'];
    $year = $_POST['year'];
    $new_media_name = $current_media_url; // Default ke gambar lama

    // Logika Upload Foto (Hanya jika file baru diunggah)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/gallery/";
        
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }

        $image_name = basename($_FILES["image"]["name"]);
        $new_media_name = time() . '_' . rand(1000,9999) . '_' . $image_name;
        $target_file = $target_dir . $new_media_name;
        
        // Validasi sederhana (bisa ditambahkan)
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Hapus gambar lama jika ini mode edit
            if ($is_edit_mode && !empty($current_media_url) && file_exists($target_dir . $current_media_url)) {
                unlink($target_dir . $current_media_url);
            }
        } else {
            $message = 'Gagal mengunggah gambar.';
            $message_type = 'error';
        }
    }

    if (empty($message)) { // Lanjut jika tidak ada error upload
        if ($is_edit_mode) {
            // QUERY UPDATE BARU
            $sql = "UPDATE gallery SET title = ?, media_type = ?, year = ?, media_url = ?, thumbnail_url = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            // Untuk thumbnail, kita samakan saja dengan media_url
            $stmt->bind_param("sssssi", $title, $media_type, $year, $new_media_name, $new_media_name, $image_id);
        } else {
            // QUERY INSERT BARU
            $sql = "INSERT INTO gallery (title, media_type, year, media_url, thumbnail_url, uploaded_by_user_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            // Untuk thumbnail, kita samakan saja dengan media_url
            $stmt->bind_param("sssssi", $title, $media_type, $year, $new_media_name, $new_media_name, $admin_id);
        }
        
        // Baris 80 Anda ada di sekitar sini
        if ($stmt && $stmt->execute()) {
            header("Location: kelola_galeri.php");
            exit();
        } else {
            $message = 'Gagal menyimpan data: ' . $conn->error; // Tampilkan error SQL
            $message_type = 'error';
        }
    }
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
        <a href="kelola_galeri.php" class="btn-back">← Kembali ke Galeri</a>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <form class="admin-form" action="upload_gambar.php?id=<?php echo $image_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Judul Media</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="media_type">Jenis Media</label>
                <select id="media_type" name="media_type" required>
                    <option value="photo" <?php echo ($media_type == 'photo') ? 'selected' : ''; ?>>Foto</option>
                    <option value="video" <?php echo ($media_type == 'video') ? 'selected' : ''; ?>>Video</option>
                    <option value="aftermovie" <?php echo ($media_type == 'aftermovie') ? 'selected' : ''; ?>>Aftermovie</option>
                </select>
            </div>

            <div class="form-group">
                <label for="year">Tahun</label>
                <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($year); ?>" placeholder="Contoh: 2024">
            </div>
            
            <div class="form-group">
                <label for="image">File Media (media_url)</label>
                <?php if ($current_media_url): ?>
                    <img src="/upfm_web/assets/images/gallery/<?php echo $current_media_url; ?>" alt="Gambar saat ini" class="table-thumbnail" style="margin-bottom: 10px; max-width: 200px; height: auto;">
                    <p>Ganti file (opsional):</p>
                <?php else: ?>
                    <p>Pilih file untuk diunggah:</p>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*" <?php echo $is_edit_mode ? '' : 'required'; ?>>
                <small>Ini akan disimpan di 'media_url' dan 'thumbnail_url'</small>
            </div>
            
            <button type="submit" class="btn-standard">Simpan Media</button>
        </form>
    </div>
</div>
</body>
</html>