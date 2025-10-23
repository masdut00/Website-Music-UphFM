<?php
require_once '../includes/db.php';

// Proteksi Halaman: Wajib login
if (!isset($_SESSION['user_id'])) {
    // DIUBAH: Path absolut untuk redirect
    header("Location: /upfm_web/auth/login.php");
    exit();
}

$page_title = 'Akun Saya';
require_once '../includes/header.php';

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// BAGIAN 1: HANYA MENANGANI LOGIKA SAAT FORM GANTI PASSWORD DI-SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    // (Logika ganti password Anda sudah benar dan tidak perlu diubah)
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($result && password_verify($current_password, $result['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_password_hash, $user_id);
                $update_stmt->execute();
                $update_stmt->close();
                $message = 'Password berhasil diperbarui!';
                $message_type = 'success';
            } else {
                $message = 'Password baru minimal harus 6 karakter.';
                $message_type = 'error';
            }
        } else {
            $message = 'Konfirmasi password baru tidak cocok.';
            $message_type = 'error';
        }
    } else {
        $message = 'Password saat ini yang Anda masukkan salah.';
        $message_type = 'error';
    }
}

// BAGIAN 2: SELALU AMBIL DATA INI SETIAP KALI HALAMAN DIBUKA
$history_stmt = $conn->prepare(
    "SELECT t.category_name, p.purchase_date FROM ticket_purchases p JOIN tickets t ON p.ticket_id = t.id WHERE p.user_id = ? ORDER BY p.purchase_date DESC LIMIT 5"
);
$history_stmt->bind_param("i", $user_id);
$history_stmt->execute();
$purchase_history = $history_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$history_stmt->close();

$user_stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();
?>

<div class="container page-container">
    <h1 class="page-main-title">Akun Saya</h1>
    <p class="page-subtitle">Kelola informasi profil dan keamanan akun Anda di sini.</p>

    <div class="account-wrapper">
        <div class="account-panel">
            <h2 class="panel-title">Profil Saya</h2>
            <div class="profile-info">
                <div class="info-item">
                    <label>Nama Lengkap</label>
                    <p><?php echo htmlspecialchars($user['full_name']); ?></p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
        </div>

        <div class="account-panel">
            <h2 class="panel-title">Riwayat Pembelian Terakhir</h2>
            <?php if (!empty($purchase_history)): ?>
                <ul class="purchase-history-list">
                    <?php foreach ($purchase_history as $item): ?>
                        <li>
                            <span class="item-name"><?php echo htmlspecialchars($item['category_name']); ?></span>
                            <span class="item-date"><?php echo date('d M Y', strtotime($item['purchase_date'])); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a href="/upfm_web/account/my_tickets.php" class="btn-standard" style="width: 100%; text-align: center; margin-top: 1rem;">Lihat Semua Tiket Saya</a>
            <?php else: ?>
                <p class="panel-description">Anda belum pernah melakukan pembelian tiket.</p>
                <a href="/upfm_web/explore.php" class="btn-standard">Beli Tiket Sekarang</a>
            <?php endif; ?>
        </div>
        
        <div class="account-panel">
            <h2 class="panel-title">Keamanan</h2>
            <p class="panel-description">Ubah password Anda secara berkala untuk menjaga keamanan akun.</p>

            <?php if ($message): ?>
                <div class="alert <?php echo $message_type; ?>">
                    <p><?php echo $message; ?></p>
                </div>
            <?php endif; ?>

            <form action="my_account.php" method="POST">
                <div class="form-group">
                    <label for="current_password">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn-standard">Perbarui Password</button>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>