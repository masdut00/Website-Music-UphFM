<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

// Inisialisasi Variabel
$page_title = 'Tambah Artis Baru';
$artist_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($artist_id > 0);
$message = '';
$message_type = '';

// Default value form
$name = ''; 
$genre = ''; 
$description = ''; 
$is_headliner = 0; 
$current_image = '';

// --- LOGIKA 1: AMBIL DATA (JIKA EDIT) ---
if ($is_edit_mode) {
    $page_title = 'Edit Artis';
    $stmt = $conn->prepare("SELECT * FROM artists WHERE id = ?");
    $stmt->bind_param("i", $artist_id);
    $stmt->execute();
    $artist = $stmt->get_result()->fetch_assoc();
    
    if ($artist) {
        $name = $artist['name'];
        $genre = $artist['genre'];
        $description = $artist['description'];
        $is_headliner = $artist['is_headliner'];
        $current_image = $artist['image_url'];
    } else {
        // Jika ID tidak ditemukan
        header("Location: kelola_artis.php");
        exit();
    }
    $stmt->close();
}

// --- LOGIKA 2: SIMPAN DATA (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];
    $is_headliner = isset($_POST['is_headliner']) ? 1 : 0;
    $new_image_name = $current_image;

    // Handle Upload Gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/artists/";
        
        // Buat nama file unik
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_image_name = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_image_name;

        // Validasi tipe file (Opsional tapi disarankan)
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array(strtolower($file_extension), $allowed_types)) {
            $message = 'Hanya format JPG, JPEG, PNG, dan WEBP yang diperbolehkan.';
            $message_type = 'error';
        } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Hapus gambar lama jika ada dan ini mode edit
            if ($is_edit_mode && !empty($current_image) && file_exists($target_dir . $current_image)) {
                unlink($target_dir . $current_image);
            }
        } else {
            $message = 'Gagal mengunggah gambar ke server.';
            $message_type = 'error';
        }
    }

    // Jika tidak ada error, simpan ke Database
    if (empty($message)) {
        if ($is_edit_mode) {
            // Query UPDATE
            $sql = "UPDATE artists SET name = ?, genre = ?, description = ?, is_headliner = ?, image_url = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssisi", $name, $genre, $description, $is_headliner, $new_image_name, $artist_id);
        } else {
            // Query INSERT
            $sql = "INSERT INTO artists (name, genre, description, is_headliner, image_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssis", $name, $genre, $description, $is_headliner, $new_image_name);
        }
        
        if ($stmt->execute()) {
            // Set Flash Message untuk ditampilkan di halaman kelola
            $_SESSION['flash_message'] = 'Data artis berhasil disimpan!';
            $_SESSION['flash_type'] = 'success';
            
            header("Location: kelola_artis.php");
            exit();
        } else {
            $message = 'Gagal menyimpan database: ' . $stmt->error;
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
            <h1 class="page-main-title"><?php echo $page_title; ?></h1>
            <a href="kelola_artis.php" class="btn-back">← Kembali ke Daftar Artis</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <div class="card-admin" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <form class="admin-form" action="edit_artis.php?id=<?php echo $artist_id; ?>" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="name">Nama Artis</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required placeholder="Masukkan nama artis/band">
                </div>
                
                <div class="form-group">
                    <label for="genre">Genre Musik</label>
                    <input type="text" id="genre" name="genre" class="form-control" value="<?php echo htmlspecialchars($genre); ?>" placeholder="Contoh: Pop, Rock, Jazz">
                </div>
                
                <div class="form-group">
                    <label for="description">Deskripsi / Bio Singkat</label>
                    <textarea id="description" name="description" rows="5" class="form-control"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                
                <div class="form-group form-group-check" style="margin: 15px 0;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" id="is_headliner" name="is_headliner" value="1" <?php echo $is_headliner ? 'checked' : ''; ?> style="width: auto;">
                        <strong>Jadikan sebagai Artis Utama (Headliner)?</strong>
                    </label>
                </div>
                
                <div class="form-group">
                    <label for="image">Foto Artis</label>
                    
                    <?php if ($current_image): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="../assets/images/artists/<?php echo $current_image; ?>" alt="Foto saat ini" style="width: 150px; border-radius: 8px; border: 1px solid #ddd;">
                            <p style="font-size: 0.9em; color: #666;">Foto saat ini. Upload baru untuk mengganti.</p>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" id="image" name="image" accept="image/*" class="form-control">
                </div>
                
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn-standard">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>