<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = ($id > 0);
$page_title = $is_edit ? 'Edit FAQ' : 'Tambah FAQ Baru';
$message = '';
$message_type = '';

// Default Values
$question = '';
$answer = '';
$category = 'General'; // Default

// --- 1. AMBIL KATEGORI YANG SUDAH ADA (Untuk Datalist) ---
$existing_categories = $conn->query("SELECT DISTINCT category FROM faq WHERE category != '' ORDER BY category ASC")->fetch_all(MYSQLI_ASSOC);

// --- 2. AMBIL DATA JIKA EDIT ---
if ($is_edit) {
    $stmt = $conn->prepare("SELECT * FROM faq WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    
    if ($data) {
        $question = $data['question'];
        $answer = $data['answer'];
        $category = $data['category'];
    } else {
        header("Location: kelola_faq.php");
        exit();
    }
}

// --- 3. PROSES SIMPAN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $category = $_POST['category']; // Bisa pilih atau ketik baru

    if (empty($question) || empty($answer)) {
        $message = "Pertanyaan dan Jawaban wajib diisi.";
        $message_type = "error";
    } else {
        if ($is_edit) {
            $stmt = $conn->prepare("UPDATE faq SET question=?, answer=?, category=? WHERE id=?");
            $stmt->bind_param("sssi", $question, $answer, $category, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO faq (question, answer, category) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $question, $answer, $category);
        }

        if ($stmt->execute()) {
            $_SESSION['flash_message'] = 'Data FAQ berhasil disimpan!';
            $_SESSION['flash_type'] = 'success';
            header("Location: kelola_faq.php");
            exit();
        } else {
            $message = "Gagal menyimpan: " . $stmt->error;
            $message_type = "error";
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
            <a href="kelola_faq.php" class="btn-back">← Kembali</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-error" style="background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="card-admin" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <form action="" method="POST">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Kategori Pertanyaan</label>
                    <input type="text" 
                           name="category" 
                           class="form-control" 
                           list="cat_list" 
                           value="<?php echo htmlspecialchars($category); ?>" 
                           placeholder="Pilih kategori atau ketik baru..." 
                           required 
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    
                    <datalist id="cat_list">
                        <option value="General">
                        <option value="Tiket">
                        <option value="Venue & Lokasi">
                        <option value="Barang Bawaan">
                        <?php foreach ($existing_categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['category']); ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <small style="color:#666;">Contoh: "Tiket", "Pembayaran", "Lokasi". Ketik baru untuk membuat kategori baru.</small>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Pertanyaan (Question)</label>
                    <input type="text" name="question" class="form-control" value="<?php echo htmlspecialchars($question); ?>" required placeholder="Contoh: Bagaimana cara menukar tiket?" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Jawaban (Answer)</label>
                    <textarea name="answer" rows="5" class="form-control" required placeholder="Tulis jawaban lengkap di sini..." style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-family: inherit;"><?php echo htmlspecialchars($answer); ?></textarea>
                </div>

                <button type="submit" class="btn-save" style="background: #007bff; color: white; border: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; cursor: pointer;">Simpan FAQ</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>