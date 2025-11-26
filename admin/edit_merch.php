<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Tambah Merchandise';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($id > 0);
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
    $page_title = 'Edit Merchandise';
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
        $target_dir = "../assets/images/merch/"; // Pastikan folder ini benar
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        
        // Buat nama unik agar tidak bentrok
        $new_image_name = time() . '_' . rand(100, 999) . '.' . $file_extension;
        $target_file = $target_dir . $new_image_name;
        
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

    // Simpan ke Database jika tidak ada error upload
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
            <a href="kelola_merch.php" class="btn-back">← Kembali ke Daftar Merchandise</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <div class="card-admin" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <form class="admin-form" action="edit_merch.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="item_name">Nama Item / Produk</label>
                    <input type="text" id="item_name" name="item_name" class="form-control" value="<?php echo htmlspecialchars($item_name); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="price">Harga Satuan (Rp)</label>
                    <input type="number" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stok Awal</label>
                    <input type="number" id="stock" name="stock" class="form-control" value="<?php echo htmlspecialchars($stock); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="5" class="form-control"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image">Foto Produk</label>
                    
                    <?php if ($current_image): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="../assets/images/merch/<?php echo $current_image; ?>" alt="Foto saat ini" class="table-thumbnail" style="width: 150px; border-radius: 8px; border: 1px solid #ddd;">
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