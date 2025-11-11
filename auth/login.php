<?php
require_once '../includes/db.php';
$page_title = 'Login';

if (isset($_SESSION['user_id'])) {
    header("Location: /upfm_web/index.php");
    exit();
}
//test
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi!";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: /upfm_web/admin/dashboard.php");
                } else {
                    header("Location: /upfm_web/index.php");
                }
                exit();
            } else {
                $error = "Email atau password salah.";
            }
        } else {
            $error = "Email atau password salah.";
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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="login-page-wrapper">
    <div class="login-image-panel"></div>
    <div class="login-form-panel">
        <div class="form-container-modern">
            <div class="form-header">
                <a href="/upfm_web/index.php"><img src="/upfm_web/assets/images/logo.png" alt="UpFM Logo" class="form-logo"></a>
                <h2>Selamat Datang Kembali!</h2>
                <p>Login untuk mengakses tiket dan info eksklusif Anda.</p>
            </div>

            <?php 
            if (!empty($error)) {
                echo '<div class="alert error"><p>' . htmlspecialchars($error) . '</p></div>';
            } elseif (isset($_SESSION['error_message'])) {
                echo '<div class="alert error"><p>' . htmlspecialchars($_SESSION['error_message']) . '</p></div>';
                unset($_SESSION['error_message']);
            }
            ?>
            
            <form action="login.php" method="POST">
                <div class="form-group-modern">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="contoh@email.com" required>
                </div>
                <div class="form-group-modern">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-modern">Login</button>
            </form>
            <p class="auth-link-modern">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </div>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>