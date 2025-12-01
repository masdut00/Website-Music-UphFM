<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($id > 0);
$page_title = $is_edit_mode ? 'Edit Merchandise' : 'Tambah Merchandise';
$message = '';
$message_type = '';

// Default values
$item_name = ''; 
$price = ''; 
$stock = ''; 
$description = ''; 
$current_image = '';

// --- 1. AMBIL DATA JIKA EDIT ---
if ($is_edit_mode) {
    $stmt = $conn->prepare("SELECT * FROM merchandise WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    
    if ($data) {
        $item_name = $data['item_name'];
        $price = $data['price'];
        $stock = $data['stock'];
        $description = $data['description'];
        $current_image = $data['image_url'];
    } else {
        header("Location: kelola_merch.php");
        exit();
    }
    $stmt->close();
}

// --- 2. PROSES SIMPAN DATA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $new_image_name = $current_image;

    // Handle Upload Gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/merch/";
        
        // Buat folder jika belum ada
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_image_name = time() . '_' . rand(100, 999) . '.' . $file_extension;
        $target_file = $target_dir . $new_image_name;
        
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array(strtolower($file_extension), $allowed)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Hapus gambar lama jika ada dan ini mode edit
                if ($is_edit_mode && !empty($current_image) && file_exists($target_dir . $current_image)) {
                    unlink($target_dir . $current_image);
                }
            } else {
                $message = 'Gagal upload gambar.';
                $message_type = 'error';
            }
        } else {
            $message = 'Format file tidak didukung (JPG, PNG, WEBP).';
            $message_type = 'error';
        }
    }

    // Simpan ke Database
    if (empty($message)) {
        if ($is_edit_mode) {
            $sql = "UPDATE merchandise SET item_name=?, price=?, stock=?, description=?, image_url=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siissi", $item_name, $price, $stock, $description, $new_image_name, $id);
        } else {
            $sql = "INSERT INTO merchandise (item_name, price, stock, description, image_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siiss", $item_name, $price, $stock, $description, $new_image_name);
        }

        if ($stmt->execute()) {
            $_SESSION['flash_message'] = 'Data merchandise berhasil disimpan!';
            $_SESSION['flash_type'] = 'success';
            header("Location: kelola_merch.php");
            exit();
        } else {
            $message = 'Database Error: ' . $stmt->error;
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
            <a href="kelola_merch.php" class="btn-back">← Kembali</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-error" style="background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="card-admin" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Nama Item / Produk</label>
                    <input type="text" name="item_name" class="form-control" value="<?php echo htmlspecialchars($item_name); ?>" required placeholder="Contoh: Kaos Band UPFM 2025" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                
                <div class="form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Harga Satuan (Rp)</label>
                        <input type="number" name="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>" required placeholder="150000" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    </div>

                    <div class="form-group">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Stok Awal</label>
                        <input type="number" name="stock" class="form-control" value="<?php echo htmlspecialchars($stock); ?>" required placeholder="50" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Deskripsi Produk</label>
                    <textarea name="description" rows="5" class="form-control" placeholder="Jelaskan detail ukuran, bahan, dll..." style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-family:inherit;"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                
                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Foto Produk</label>
                    
                    <?php if ($current_image): ?>
                        <div style="margin-bottom: 10px; padding:10px; border:1px solid #eee; display:inline-block; border-radius:5px;">
                            <img src="../assets/images/merch/<?php echo $current_image; ?>" alt="Foto saat ini" style="width: 150px; border-radius: 4px;">
                            <p style="font-size: 0.85em; color: #666; margin: 5px 0 0 0;">Foto saat ini.</p>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small style="color: #888;">Format: JPG, PNG, WEBP. Biarkan kosong jika tidak ingin mengubah.</small>
                </div>
                
                <button type="submit" class="btn-save" style="background: #007bff; color: white; border: none; padding: 12px 30px; border-radius: 5px; font-weight: bold; cursor: pointer;">Simpan Data</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>