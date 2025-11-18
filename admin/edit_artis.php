<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Tambah Artis';
$artist_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($artist_id > 0);
$message = '';
$message_type = '';

$name = ''; $genre = ''; $description = ''; $is_headliner = 0; $current_image = '';

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
    }
    $stmt->close();
}

//simpan data (create / update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];
    $is_headliner = isset($_POST['is_headliner']) ? 1 : 0;
    $new_image_name = $current_image;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/artists/";
        $image_name = basename($_FILES["image"]["name"]);
        $new_image_name = time() . '_' . $image_name;
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
            // Query update
            $sql = "UPDATE artists SET name = ?, genre = ?, description = ?, is_headliner = ?, image_url = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssisi", $name, $genre, $description, $is_headliner, $new_image_name, $artist_id);
        } else {
            // Query insert
            $sql = "INSERT INTO artists (name, genre, description, is_headliner, image_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssis", $name, $genre, $description, $is_headliner, $new_image_name);
        }
        
        if ($stmt->execute()) {
            // Berhasil, redirect kembali ke halaman kelola
            header("Location: kelola_artis.php");
            exit();
        } else {
            $message = 'Gagal menyimpan data: ' . $stmt->error;
            $message_type = 'error';
        }
        $stmt->close();
    }
}

require_once '../includes/header.php';
?>

<div class="container page-container">
    <h1 class="page-main-title"><?php echo $page_title; ?></h1>
    <a href="kelola_artis.php" class="btn-back">← Kembali ke Daftar Artis</a>

    <?php if ($message): ?>
        <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
    <?php endif; ?>

    <form class="admin-form" action="edit_artis.php?id=<?php echo $artist_id; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Nama Artis</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($genre); ?>">
        </div>
        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        <div class="form-group form-group-check">
            <input type="checkbox" id="is_headliner" name="is_headliner" value="1" <?php echo $is_headliner ? 'checked' : ''; ?>>
            <label for="is_headliner">Jadikan Headliner?</label>
        </div>
        <div class="form-group">
            <label for="image">Foto Artis</label>
            <?php if ($current_image): ?>
                <img src="/upfm_web/assets/images/artists/<?php echo $current_image; ?>" alt="Foto saat ini" class="table-thumbnail" style="margin-bottom: 10px;">
                <p>Ganti foto (opsional):</p>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        
        <button type="submit" class="btn-standard">Simpan Perubahan</button>
    </form>
</div>

<?php
require_once '../includes/footer.php';
?>