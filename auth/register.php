<?php
require_once '../includes/db.php';
$page_title = 'Registrasi';

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validasi
    if (empty($full_name) || empty($email) || empty($password)) {
        $errors[] = "Semua kolom wajib diisi!";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid!";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password minimal harus 6 karakter.";
    }

    // Cek email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email sudah terdaftar. Silakan gunakan email lain.";
    }
    $stmt->close();

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'pengunjung')");
        $stmt->bind_param("sss", $full_name, $email, $password_hash);
        
        if ($stmt->execute()) {
            $success_message = "Registrasi berhasil! Silakan <a href='login.php'>login di sini</a>.";
        } else {
            $errors[] = "Terjadi kesalahan. Gagal mendaftar.";
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
    <title><?php echo $page_title . ' - UpFM'; ?></title>
    <link rel="stylesheet" href="/upfm_web/assets/css/style.css">
</head>
<body>

<div class="login-page-wrapper">
    <div class="login-image-panel">
        </div>

    <div class="login-form-panel">
        <div class="form-container-modern">
            <div class="form-header">
                <a href="index.php"><img src="assets/images/logo.png" alt="UpFM Logo" class="form-logo"></a>
                <h2>Buat Akun Baru</h2>
                <p>Daftar sekarang untuk menjadi bagian dari festival musik paling seru.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert success">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php else: ?>
                <form action="register.php" method="POST">
                    <div class="form-group-modern">
                        <label for="full_name">Nama Lengkap</label>
                        <input type="text" id="full_name" name="full_name" placeholder="Nama Anda" required>
                    </div>
                    <div class="form-group-modern">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="contoh@email.com" required>
                    </div>
                    <div class="form-group-modern">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                    </div>
                    <button type="submit" class="btn-modern">Daftar Akun</button>
                </form>
                <p class="auth-link-modern">Sudah punya akun? <a href="login.php">Login di sini</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>