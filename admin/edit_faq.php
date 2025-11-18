<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$page_title = 'Tambah FAQ';
$faq_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = ($faq_id > 0);
$message = '';
$message_type = '';

$question = ''; $answer = ''; $category = 'General';

if ($is_edit_mode) {
    $page_title = 'Edit FAQ';
    $stmt = $conn->prepare("SELECT * FROM faq WHERE id = ?");
    $stmt->bind_param("i", $faq_id);
    $stmt->execute();
    $faq = $stmt->get_result()->fetch_assoc();
    if ($faq) {
        $question = $faq['question'];
        $answer = $faq['answer'];
        $category = $faq['category'];
    }
    $stmt->close();
}

// Logika Simpan Data (Create / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $category = $_POST['category'];

    if ($is_edit_mode) {
        $sql = "UPDATE faq SET question = ?, answer = ?, category = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $question, $answer, $category, $faq_id);
    } else {
        // Kita masukkan ID admin yang login ke relasi 'added_by_user_id'
        $sql = "INSERT INTO faq (question, answer, category, added_by_user_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $question, $answer, $category, $admin_id);
    }
    
    if ($stmt->execute()) {
        header("Location: kelola_faq.php");
        exit();
    } else {
        $message = 'Gagal menyimpan data: ' . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
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
        <a href="kelola_faq.php" class="btn-back">← Kembali ke Daftar FAQ</a>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <form class="admin-form" action="edit_faq.php?id=<?php echo $faq_id; ?>" method="POST">
            <div class="form-group">
                <label for="question">Pertanyaan</label>
                <input type="text" id="question" name="question" value="<?php echo htmlspecialchars($question); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category">Kategori (Contoh: General, Tiket, Venue)</label>
                <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($category); ?>">
            </div>

            <div class="form-group">
                <label for="answer">Jawaban</label>
                <textarea id="answer" name="answer" rows="8"><?php echo htmlspecialchars($answer); ?></textarea>
            </div>
            
            <button type="submit" class="btn-standard">Simpan FAQ</button>
        </form>
    </div>
</div>
</body>
</html>