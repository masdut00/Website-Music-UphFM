<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Tambah Tiket';
$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($ticket_id > 0);
$message = '';
$message_type = '';

// --- LOGIKA HAPUS TIPE TIKET (DELETE) ---
if (isset($_GET['hapus_tipe'])) {
    $type_id_to_delete = (int)$_GET['hapus_tipe'];
    $stmt_del = $conn->prepare("DELETE FROM ticket_types WHERE id = ? AND ticket_id = ?");
    $stmt_del->bind_param("ii", $type_id_to_delete, $ticket_id);
    if ($stmt_del->execute()) {
        $message = 'Tipe tiket berhasil dihapus.'; $message_type = 'success';
    } else {
        $message = 'Gagal menghapus tipe.'; $message_type = 'error';
    }
}

// --- LOGIKA SIMPAN DATA (CREATE / UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- A. Logika untuk SIMPAN TIKET UTAMA ---
    if (isset($_POST['simpan_tiket'])) {
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
            $message = 'Data tiket berhasil disimpan!'; $message_type = 'success';
            if (!$is_edit_mode) {
                // Jika ini tiket baru, arahkan ke mode edit
                $new_id = $stmt->insert_id;
                header("Location: edit_tiket.php?id=$new_id");
                exit();
            }
        } else {
            $message = 'Gagal menyimpan data tiket: ' . $stmt->error; $message_type = 'error';
        }
        $stmt->close();
    }

    // --- B. Logika untuk TAMBAH TIPE BARU ---
    if (isset($_POST['tambah_tipe'])) {
        $type_name = $_POST['type_name'];
        if (!empty($type_name) && $is_edit_mode) {
            $sql = "INSERT INTO ticket_types (ticket_id, type_name) VALUES (?, ?)";
            $stmt_add = $conn->prepare($sql);
            $stmt_add->bind_param("is", $ticket_id, $type_name);
            if ($stmt_add->execute()) {
                $message = 'Tipe baru berhasil ditambahkan!'; $message_type = 'success';
            } else {
                $message = 'Gagal menambah tipe: ' . $stmt_add->error; $message_type = 'error';
            }
        }
    }
}

// --- LOGIKA BACA DATA (READ) ---
// Inisialisasi variabel
$category_name = ''; $filter_tag = ''; $price = ''; $quantity_available = ''; $description = '';
$current_images = [];
$current_types = [];

// Ambil data tiket utama
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

    // Ambil gambar yang sudah ada
    $img_stmt = $conn->prepare("SELECT id, image_url FROM ticket_images WHERE ticket_id = ?");
    $img_stmt->bind_param("i", $ticket_id);
    $img_stmt->execute();
    $current_images = $img_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Ambil TIPE TIKET yang sudah ada
    $types_stmt = $conn->prepare("SELECT id, type_name FROM ticket_types WHERE ticket_id = ?");
    $types_stmt->bind_param("i", $ticket_id);
    $types_stmt->execute();
    $current_types = $types_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
            <h2 class="panel-title">Detail Tiket Utama</h2>
            <div class="form-group">
                <label for="category_name">Nama Tiket</label>
                <input type="text" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category_name); ?>" required>
            </div>
            
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="filter_tag">Tag Filter (explore page)</label>
                    <select id="filter_tag" name="filter_tag">
                        <option value="presale" <?php echo ($filter_tag == 'presale') ? 'selected' : ''; ?>>Presale</option>
                        <option value="day1" <?php echo ($filter_tag == 'day1') ? 'selected' : ''; ?>>Day 1</option>
                        <option value="day2" <?php echo ($filter_tag == 'day2') ? 'selected' : ''; ?>>Day 2</option>
                        <option value="all-access" <?php echo ($filter_tag == 'all-access') ? 'selected' : ''; ?>>All-Access (2 Hari)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">Harga Dasar (contoh: 125000)</label>
                    <input type="number" step="1000" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="quantity_available">Stok (Total)</label>
                <input type="number" id="quantity_available" name="quantity_available" value="<?php echo htmlspecialchars($quantity_available); ?>">
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            
            <button type="submit" name="simpan_tiket" class="btn-standard">Simpan Perubahan Tiket</button>
        </form>

        <?php if ($is_edit_mode): ?>
            
            <div class="admin-form" style="margin-top: 2rem;">
                <h2 class="panel-title">Kelola Tipe Tiket (Regular, VIP, dll)</h2>
                
                <table class="admin-table" style="margin-bottom: 1.5rem;">
                    <thead><tr><th>Tipe yang Terdaftar</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php if (empty($current_types)): ?>
                            <tr><td colspan="2">Belum ada tipe. Tambahkan di bawah.</td></tr>
                        <?php else: ?>
                            <?php foreach ($current_types as $type): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($type['type_name']); ?></td>
                                    <td>
                                        <a href="edit_tiket.php?id=<?php echo $ticket_id; ?>&hapus_tipe=<?php echo $type['id']; ?>" class="btn-delete-small" onclick="return confirm('Hapus tipe ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <form action="edit_tiket.php?id=<?php echo $ticket_id; ?>" method="POST" class="form-inline">
                    <div class="form-group">
                        <label for="type_name">Tambah Tipe Baru:</label>
                        <input type="text" id="type_name" name="type_name" placeholder="Misal: VVIP" required>
                    </div>
                    <button type="submit" name="tambah_tipe" class="btn-standard">Tambah Tipe</button>
                </form>
            </div>

            <div class="admin-form" style="margin-top: 2rem;">
                <h2 class="panel-title">Kelola Gambar Tiket</h2>
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
                
                <form action="upload_gambar_tiket.php?id=<?php echo $ticket_id; ?>" method="POST" enctype="multipart/form-data" class="form-inline">
                    <div class="form-group">
                        <label for="images">Tambah Gambar Baru:</label>
                        <input type="file" id="images" name="images[]" multiple accept="image/*">
                    </div>
                    <button type="submit" class="btn-standard">Upload Gambar</button>
                </form>
            </div>

        <?php endif; ?>
    </div>
</div>
</body>
</html>