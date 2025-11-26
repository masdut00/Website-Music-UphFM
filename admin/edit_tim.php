<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = ($id > 0);
$page_title = $is_edit ? 'Edit Tim' : 'Tambah Tim';

$name = ''; $role = ''; $current_image = '';

if ($is_edit) {
    $stmt = $conn->prepare("SELECT * FROM teams WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    if ($data) {
        $name = $data['name'];
        $role = $data['role'];
        $current_image = $data['image_url'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $new_image = $current_image;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . rand(100,999) . '.' . $ext;
        // Pastikan folder ../assets/images/team/ sudah dibuat
        if (move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/team/' . $filename)) {
            $new_image = $filename;
            if ($is_edit && $current_image && file_exists('../assets/images/team/'.$current_image)) unlink('../assets/images/team/'.$current_image);
        }
    }

    if ($is_edit) {
        $stmt = $conn->prepare("UPDATE teams SET name=?, role=?, image_url=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $role, $new_image, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO teams (name, role, image_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $role, $new_image);
    }
    
    if ($stmt->execute()) {
        header("Location: kelola_tim.php");
        exit();
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
        <div class="admin-header"><h1 class="page-main-title"><?php echo $page_title; ?></h1></div>
        <div class="card-admin">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="form-group">
                    <label>Jabatan (Role)</label>
                    <input type="text" name="role" class="form-control" value="<?php echo htmlspecialchars($role); ?>" placeholder="Contoh: Festival Director" required>
                </div>
                <div class="form-group">
                    <label>Foto Profil</label>
                    <?php if($current_image): ?><br><img src="../assets/images/team/<?php echo $current_image; ?>" width="100"><?php endif; ?>
                    <input type="file" name="image" class="form-control">
                </div>
                <button type="submit" class="btn-save">Simpan</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>