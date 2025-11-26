<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Tambah Foto Galeri';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = ($id > 0);
$message = '';
$message_type = '';

// Default Values
$title = '';
$album_name = ''; // Default album
$is_highlight = 0;       
$current_media = '';
$media_type = 'photo';   

// --- 1. QUERY PENTING: AMBIL LIST ALBUM YANG SUDAH ADA ---
// Gunakan DISTINCT agar nama yang sama tidak muncul berkali-kali
// Filter "!= ''" agar nama album kosong tidak ikut diambil
$existing_albums = $conn->query("SELECT DISTINCT album_name FROM gallery WHERE album_name != '' AND album_name IS NOT NULL ORDER BY album_name ASC")->fetch_all(MYSQLI_ASSOC);

// --- 2. AMBIL DATA JIKA EDIT MODE ---
if ($is_edit) {
    $page_title = 'Edit Galeri';
    $stmt = $conn->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    
    if ($data) {
        $title = $data['title'];
        $album_name = $data['album_name'];
        $is_highlight = $data['is_highlight'];
        $current_media = $data['media_url'];
        $media_type = $data['media_type'];
    } else {
        header("Location: kelola_galeri.php");
        exit();
    }
    $stmt->close();
}

// --- 3. PROSES SIMPAN (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $album_name = $_POST['album_name']; // Ambil dari input
    $is_highlight = isset($_POST['is_highlight']) ? 1 : 0;
    
    $new_media_name = $current_media;
    
    // Upload File
    if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
        $target_dir = "../assets/images/gallery/";
        $file_extension = pathinfo($_FILES["media"]["name"], PATHINFO_EXTENSION);
        $new_media_name = time() . '_' . rand(100, 999) . '.' . $file_extension;
        $target_file = $target_dir . $new_media_name;
        
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'mp4']; 
        if (in_array(strtolower($file_extension), $allowed)) {
            if (move_uploaded_file($_FILES["media"]["tmp_name"], $target_file)) {
                if ($is_edit && !empty($current_media) && file_exists($target_dir . $current_media)) {
                    unlink($target_dir . $current_media);
                }
            } else {
                $message = "Gagal upload file."; $message_type = "error";
            }
        } else {
            $message = "Format file tidak didukung."; $message_type = "error";
        }
    }

    if (empty($message)) {
        if ($is_edit) {
            $sql = "UPDATE gallery SET title=?, media_type=?, media_url=?, album_name=?, is_highlight=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssii", $title, $media_type, $new_media_name, $album_name, $is_highlight, $id);
        } else {
            $sql = "INSERT INTO gallery (title, media_type, media_url, album_name, is_highlight) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $title, $media_type, $new_media_name, $album_name, $is_highlight);
        }

        if ($stmt->execute()) {
            $_SESSION['flash_message'] = 'Galeri berhasil disimpan!';
            $_SESSION['flash_type'] = 'success';
            header("Location: kelola_galeri.php"); 
            exit();
        } else {
            $message = "Database Error: " . $stmt->error; $message_type = "error";
        }
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
            <a href="kelola_galeri.php" class="btn-back">← Kembali</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-error" style="background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="card-admin" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Judul Foto</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required placeholder="Contoh: Keseruan Penonton di Depan Panggung">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Nama Album / Kategori</label>
                    
                    <input type="text" 
                           id="album_name" 
                           name="album_name" 
                           class="form-control" 
                           list="album_list_options" 
                           value="<?php echo htmlspecialchars($album_name); ?>" 
                           placeholder="Pilih album yang ada atau ketik baru..." 
                           autocomplete="off" 
                           required>

                    <datalist id="album_list_options">
                        <?php if (!empty($existing_albums)): ?>
                            <?php foreach ($existing_albums as $alb): ?>
                                <option value="<?php echo htmlspecialchars($alb['album_name']); ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </datalist>

                    <small style="color: #666; font-size: 0.85em; margin-top: 5px; display: block;">
                        Klik kolom di atas untuk melihat album yang sudah ada. Ketik nama baru untuk membuat album baru.
                    </small>
                </div>

                <div class="form-group form-group-check" style="background: #fff3cd; padding: 15px; border-radius: 6px; border: 1px solid #ffeeba; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" id="is_highlight" name="is_highlight" value="1" <?php echo $is_highlight ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer;">
                    <label for="is_highlight" style="cursor:pointer; font-weight:bold; color:#856404; margin:0;">
                        Tampilkan di Slider Highlight (Paling Atas)?
                    </label>
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">File Foto</label>
                    
                    <?php if ($current_media): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="../assets/images/gallery/<?php echo $current_media; ?>" width="150" style="border-radius: 5px; border: 1px solid #ddd;">
                            <p style="font-size:12px; color:#666;">Foto saat ini.</p>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" name="media" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn-save" style="background: #007bff; color: white; border: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; cursor: pointer;">Simpan Galeri</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>