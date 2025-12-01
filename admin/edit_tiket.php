<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Tambah Tiket';
$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($ticket_id > 0);
$message = '';
$message_type = '';

// --- LOGIKA HAPUS GAMBAR ---
if (isset($_GET['hapus_gambar'])) {
    $img_id = (int)$_GET['hapus_gambar'];
    
    // Ambil nama file dulu
    $stmt = $conn->prepare("SELECT image_url FROM ticket_images WHERE id = ?");
    $stmt->bind_param("i", $img_id);
    $stmt->execute();
    $img_data = $stmt->get_result()->fetch_assoc();
    
    if ($img_data) {
        // Hapus dari DB
        $conn->query("DELETE FROM ticket_images WHERE id = $img_id");
        // Hapus file fisik
        if (file_exists("../assets/images/tickets/" . $img_data['image_url'])) {
            unlink("../assets/images/tickets/" . $img_data['image_url']);
        }
        $message = "Gambar berhasil dihapus.";
        $message_type = "success";
    }
}

// --- LOGIKA SIMPAN DATA (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Simpan Data Utama Tiket
    $category_name = $_POST['category_name'];
    $filter_tag = $_POST['filter_tag'];
    $price = $_POST['price'];
    $quantity_available = $_POST['quantity_available'];
    $description = $_POST['description'];

    if ($is_edit_mode) {
        $sql = "UPDATE tickets SET category_name = ?, filter_tag = ?, price = ?, quantity_available = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdisi", $category_name, $filter_tag, $price, $quantity_available, $description, $ticket_id);
    } else {
        $sql = "INSERT INTO tickets (category_name, filter_tag, price, quantity_available, description) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdis", $category_name, $filter_tag, $price, $quantity_available, $description);
    }
    
    if ($stmt->execute()) {
        if (!$is_edit_mode) {
            $ticket_id = $stmt->insert_id; // Ambil ID baru jika insert
            $is_edit_mode = true;
        }
        $message = 'Data tiket berhasil disimpan!';
        $message_type = 'success';
        
        // 2. Proses Upload Gambar (JIKA ADA)
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $total_files = count($_FILES['images']['name']);
            $target_dir = "../assets/images/tickets/";
            
            // Buat folder jika belum ada
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            for ($i = 0; $i < $total_files; $i++) {
                $file_name = $_FILES['images']['name'][$i];
                $file_tmp = $_FILES['images']['tmp_name'][$i];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                
                // Rename file agar unik
                $new_file_name = time() . "_" . uniqid() . "." . $file_ext;
                $target_file = $target_dir . $new_file_name;
                
                if (move_uploaded_file($file_tmp, $target_file)) {
                    // Simpan ke tabel ticket_images
                    $stmt_img = $conn->prepare("INSERT INTO ticket_images (ticket_id, image_url) VALUES (?, ?)");
                    $stmt_img->bind_param("is", $ticket_id, $new_file_name);
                    $stmt_img->execute();
                }
            }
        }
        
    } else {
        $message = 'Gagal menyimpan: ' . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// --- AMBIL DATA ---
$category_name = ''; $filter_tag = ''; $price = ''; $quantity_available = ''; $description = '';
$current_images = [];

if ($is_edit_mode) {
    $page_title = 'Edit Tiket';
    
    // Data Utama
    $stmt = $conn->prepare("SELECT * FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $ticket = $stmt->get_result()->fetch_assoc();
    if ($ticket) {
        $category_name = $ticket['category_name'];
        $filter_tag = $ticket['filter_tag'];
        $price = $ticket['price'];
        $quantity_available = $ticket['quantity_available'];
        $description = $ticket['description'];
    }
    
    // Data Gambar
    $img_stmt = $conn->prepare("SELECT id, image_url FROM ticket_images WHERE ticket_id = ?");
    $img_stmt->bind_param("i", $ticket_id);
    $img_stmt->execute();
    $current_images = $img_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
            <a href="kelola_tiket.php" class="btn-back">← Kembali</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'error'; ?>" style="background: <?php echo ($message_type == 'success') ? '#d4edda' : '#f8d7da'; ?>; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="card-admin" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block;">Nama Kategori Tiket</label>
                    <input type="text" name="category_name" class="form-control" value="<?php echo htmlspecialchars($category_name); ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                
                <div class="form-grid-2" style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label style="font-weight:bold; display:block;">Filter Tag (Grup)</label>
                        <select name="filter_tag" class="form-control" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                            <option value="presale" <?php echo ($filter_tag == 'presale') ? 'selected' : ''; ?>>Presale</option>
                            <option value="day1" <?php echo ($filter_tag == 'day1') ? 'selected' : ''; ?>>Day 1</option>
                            <option value="day2" <?php echo ($filter_tag == 'day2') ? 'selected' : ''; ?>>Day 2</option>
                            <option value="all-access" <?php echo ($filter_tag == 'all-access') ? 'selected' : ''; ?>>All-Access</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:bold; display:block;">Harga (Rp)</label>
                        <input type="number" name="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block;">Stok Tiket</label>
                    <input type="number" name="quantity_available" class="form-control" value="<?php echo htmlspecialchars($quantity_available); ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block;">Deskripsi / Benefit Tiket</label>
                    <textarea name="description" rows="4" class="form-control" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;"><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 25px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
                    <label style="font-weight:bold; display:block; margin-bottom: 10px;">Gambar Tiket</label>
                    
                    <?php if (!empty($current_images)): ?>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px;">
                            <?php foreach ($current_images as $img): ?>
                                <div style="position: relative; display: inline-block;">
                                    <img src="../assets/images/tickets/<?php echo $img['image_url']; ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                                    <a href="edit_tiket.php?id=<?php echo $ticket_id; ?>&hapus_gambar=<?php echo $img['id']; ?>" 
                                       onclick="return confirm('Hapus gambar ini?');"
                                       style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px; text-decoration: none; font-size: 12px;">&times;</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <input type="file" name="images[]" multiple accept="image/*" class="form-control">
                    <small style="color: #666;">Bisa pilih lebih dari 1 file sekaligus.</small>
                </div>
                
                <button type="submit" class="btn-save" style="background: #007bff; color: white; border: none; padding: 12px 30px; border-radius: 5px; font-weight: bold; cursor: pointer;">Simpan Tiket</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>