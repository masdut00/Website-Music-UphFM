<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Tambah Tiket';
$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($ticket_id > 0);
$message = '';
$message_type = '';

// Inisialisasi variabel
$category_name = ''; $filter_tag = ''; $price = ''; $quantity_available = ''; $description = '';

// Ambil data lama jika ini mode edit
if ($is_edit_mode) {
    $page_title = 'Edit Tiket';
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
    $stmt->close();
}

// Logika Simpan Data (Create / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        // Jika ini tiket baru, ambil ID-nya
        $new_ticket_id = $is_edit_mode ? $ticket_id : $stmt->insert_id;

        // Penanganan upload gambar (disimpan ke ticket_images)
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $target_dir = "../assets/images/tickets/";
            
            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $new_image_name = time() . '_' . basename($name);
                    $target_file = $target_dir . $new_image_name;
                    
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
                        // Simpan nama file ke database ticket_images
                        $img_stmt = $conn->prepare("INSERT INTO ticket_images (ticket_id, image_url) VALUES (?, ?)");
                        $img_stmt->bind_param("is", $new_ticket_id, $new_image_name);
                        $img_stmt->execute();
                        $img_stmt->close();
                    }
                }
            }
        }
        header("Location: kelola_tiket.php");
        exit();
    } else {
        $message = 'Gagal menyimpan data: ' . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// Ambil gambar yang sudah ada untuk tiket ini
$current_images = [];
if ($is_edit_mode) {
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
        <a href="kelola_tiket.php" class="btn-back">← Kembali ke Daftar Tiket</a>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <form class="admin-form" action="edit_tiket.php?id=<?php echo $ticket_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="category_name">Nama Tiket</label>
                <input type="text" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category_name); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="filter_tag">Tag Filter (untuk explore page)</label>
                <select id="filter_tag" name="filter_tag">
                    <option value="presale" <?php echo ($filter_tag == 'presale') ? 'selected' : ''; ?>>Presale</option>
                    <option value="day1" <?php echo ($filter_tag == 'day1') ? 'selected' : ''; ?>>Day 1</option>
                    <option value="day2" <?php echo ($filter_tag == 'day2') ? 'selected' : ''; ?>>Day 2</option>
                    <option value="all-access" <?php echo ($filter_tag == 'all-access') ? 'selected' : ''; ?>>All-Access (2 Hari)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Harga (contoh: 125000)</label>
                <input type="number" step="1000" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>">
            </div>

            <div class="form-group">
                <label for="quantity_available">Stok</label>
                <input type="number" id="quantity_available" name="quantity_available" value="<?php echo htmlspecialchars($quantity_available); ?>">
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Gambar Saat Ini</label>
                <div class="current-images-grid">
                    <?php if (!empty($current_images)): ?>
                        <?php foreach ($current_images as $img): ?>
                            <div class="current-image-item">
                                <img src="/upfm_web/assets/images/tickets/<?php echo $img['image_url']; ?>" class="table-thumbnail">
                                </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Belum ada gambar.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="images">Tambah Gambar Baru</label>
                <input type="file" id="images" name="images[]" multiple accept="image/*">
            </div>
            
            <button type="submit" class="btn-standard">Simpan Perubahan</button>
        </form>
    </div>
</div>
</body>
</html>