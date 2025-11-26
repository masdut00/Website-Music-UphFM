<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = ($id > 0);
$page_title = $is_edit ? 'Edit Sponsor' : 'Tambah Sponsor Baru';
$message = '';
$message_type = '';

// Default Values
$name = ''; 
$current_image = '';

// --- 1. AMBIL DATA JIKA EDIT ---
if ($is_edit) {
    $stmt = $conn->prepare("SELECT * FROM sponsors WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    
    if ($data) {
        $name = $data['name'];
        $current_image = $data['image_url'];
    } else {
        header("Location: kelola_sponsor.php");
        exit();
    }
}

// --- 2. PROSES SIMPAN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $new_image_name = $current_image;

    // Handle Upload Gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/sponsors/";
        
        // Pastikan folder ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_image_name = time() . '_' . rand(100, 999) . '.' . $file_extension;
        $target_file = $target_dir . $new_image_name;
        
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg']; // Sponsor boleh SVG
        if (in_array(strtolower($file_extension), $allowed)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Hapus gambar lama jika edit
                if ($is_edit && !empty($current_image) && file_exists($target_dir . $current_image)) {
                    unlink($target_dir . $current_image);
                }
            } else {
                $message = "Gagal upload gambar."; $message_type = "error";
            }
        } else {
            $message = "Format file tidak didukung (Gunakan JPG, PNG, WEBP, atau SVG)."; $message_type = "error";
        }
    }

    if (empty($message)) {
        if ($is_edit) {
            $stmt = $conn->prepare("UPDATE sponsors SET name=?, image_url=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $new_image_name, $id);
        } else {
            // Validasi wajib upload gambar jika tambah baru
            if (empty($new_image_name)) {
                $message = "Wajib upload logo sponsor."; $message_type = "error";
            } else {
                $stmt = $conn->prepare("INSERT INTO sponsors (name, image_url) VALUES (?, ?)");
                $stmt->bind_param("ss", $name, $new_image_name);
            }
        }

        if (empty($message) && isset($stmt) && $stmt->execute()) {
            $_SESSION['flash_message'] = 'Data sponsor berhasil disimpan!';
            $_SESSION['flash_type'] = 'success';
            header("Location: kelola_sponsor.php");
            exit();
        } elseif (empty($message)) {
            $message = "Database Error: " . $conn->error; $message_type = "error";
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
            <a href="kelola_sponsor.php" class="btn-back">← Kembali</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-error" style="background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="card-admin" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Nama Sponsor / Partner</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required placeholder="Contoh: Bank BCA, Teh Botol Sosro" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Logo Sponsor</label>
                    
                    <?php if ($current_image): ?>
                        <div style="margin-bottom: 10px; padding: 10px; border: 1px dashed #ddd; width: fit-content; border-radius: 5px;">
                            <img src="../assets/images/sponsors/<?php echo $current_image; ?>" style="max-height: 80px; max-width: 200px;">
                            <p style="font-size:12px; color:#666; margin: 5px 0 0 0;">Logo saat ini.</p>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small style="color: #666;">Format disarankan: PNG (Transparan) atau SVG agar terlihat bagus di website.</small>
                </div>

                <button type="submit" class="btn-save" style="background: #007bff; color: white; border: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; cursor: pointer;">Simpan Sponsor</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>