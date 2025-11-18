<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Tulis Artikel Baru';
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($article_id > 0);
$message = '';
$message_type = '';

$title = ''; $content = ''; $category = ''; $current_image = '';

if ($is_edit_mode) {
    $page_title = 'Edit Artikel';
    $stmt = $conn->prepare("SELECT * FROM journal_articles WHERE id = ?");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $article = $stmt->get_result()->fetch_assoc();
    if ($article) {
        $title = $article['title'];
        $content = $article['content'];
        $category = $article['category'];
        $current_image = $article['image_url'];
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $new_image_name = $current_image;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/articles/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

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
            $sql = "UPDATE journal_articles SET title = ?, content = ?, category = ?, image_url = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $title, $content, $category, $new_image_name, $article_id);
        } else {
            $sql = "INSERT INTO journal_articles (title, content, category, image_url, user_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $title, $content, $category, $new_image_name, $admin_id);
        }
        
        if ($stmt->execute()) {
            header("Location: kelola_artikel.php");
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
        <a href="kelola_artikel.php" class="btn-back">← Kembali ke Daftar Artikel</a>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <form class="admin-form" action="edit_artikel.php?id=<?php echo $article_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Judul Artikel</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category">Kategori</label>
                <select id="category" name="category" required>
                    <option value="" disabled <?php echo empty($category) ? 'selected' : ''; ?>>Pilih Kategori</option>
                    <option value="Artist Highlights" <?php echo ($category == 'Artist Highlights') ? 'selected' : ''; ?>>Artist Highlights</option>
                    <option value="Festival Tips" <?php echo ($category == 'Festival Tips') ? 'selected' : ''; ?>>Festival Tips</option>
                    <option value="Behind the Scenes" <?php echo ($category == 'Behind the Scenes') ? 'selected' : ''; ?>>Behind the Scenes</option>
                    <option value="Update" <?php echo ($category == 'Update') ? 'selected' : ''; ?>>Update</option>
                </select>
            </div>

            <div class="form-group">
                <label for="content">Isi Artikel</label>
                <textarea id="content" name="content" rows="15"><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Gambar Unggulan (Cover)</label>
                <?php if ($current_image): ?>
                    <img src="/upfm_web/assets/images/articles/<?php echo $current_image; ?>" alt="Foto saat ini" class="table-thumbnail" style="margin-bottom: 10px; width: 150px; height: auto;">
                    <p>Ganti gambar (opsional):</p>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            
            <button type="submit" class="btn-standard">Simpan & Publikasikan</button>
        </form>
    </div>
</div>
</body>
</html>