<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Tambah Merchandise';
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($item_id > 0);
$message = '';
$message_type = '';

$item_name = ''; $price = ''; $description = ''; $stock = 0; $current_image = '';

// Ambil data lama jika ini mode edit
if ($is_edit_mode) {
    $page_title = 'Edit Merchandise';
    $stmt = $conn->prepare("SELECT * FROM merchandise WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    if ($item) {
        $item_name = $item['item_name'];
        $price = $item['price'];
        $description = $item['description'];
        $stock = $item['stock'];
        $current_image = $item['image_url'];
    }
    $stmt->close();
}

// Logika Simpan Data (Create / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = (int)$_POST['stock'];
    $new_image_name = $current_image;

    // Logika Upload Foto
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/merch/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $image_name = basename($_FILES["image"]["name"]);
        $new_image_name = time() . '_' . $image_name; // Buat nama unik
        $target_file = $target_dir . $new_image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            if ($is_edit_mode && !empty($current_image) && file_exists($target_dir . $current_image)) {
                unlink($target_dir . $current_image);
            }
        } else {
            $message = 'Gagal mengunggah gambar.';
            $message_type = 'error';
        }
    }

    if (empty($message)) {
        if ($is_edit_mode) {
            $sql = "UPDATE merchandise SET item_name = ?, price = ?, description = ?, stock = ?, image_url = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdisis", $item_name, $price, $description, $stock, $new_image_name, $item_id);
        } else {
            $sql = "INSERT INTO merchandise (item_name, price, description, stock, image_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdisi", $item_name, $price, $description, $stock, $new_image_name);
        }
        
        if ($stmt->execute()) {
            header("Location: kelola_merch.php");
            exit();
        } else {
            $message = 'Gagal menyimpan data: ' . $stmt->error;
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
        <h1 class="page-main-title"><?php echo $page_title; ?></h1>
        <a href="kelola_merch.php" class="btn-back">← Kembali ke Daftar Merchandise</a>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <form class="admin-form" action="edit_merch.php?id=<?php echo $item_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="item_name">Nama Item</label>
                <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($item_name); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="price">Harga (contoh: 75000)</label>
                <input type="number" step="1000" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>">
            </div>

            <div class="form-group">
                <label for="stock">Stok</label>
                <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($stock); ?>">
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Foto Item</label>
                <?php if ($current_image): ?>
                    <img src="/upfm_web/assets/images/merch/<?php echo $current_image; ?>" alt="Foto saat ini" class="table-thumbnail" style="margin-bottom: 10px;">
                    <p>Ganti foto (opsional):</p>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            
            <button type="submit" class="btn-standard">Simpan Perubahan</button>
        </form>
    </div>
</div>
</body>
</html>