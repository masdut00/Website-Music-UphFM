<?php
require_once 'includes/db.php';
$page_title = 'Ikut Serta';
require_once 'includes/header.php';

$message = '';
$message_type = '';

// Cek apakah user login, untuk dihubungkan ke relasi user_id
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// --- LOGIKA FORM 1: PENDAFTARAN VOLUNTEER ---
if (isset($_POST['daftar_volunteer'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $reason = $_POST['reason_to_join'];

    $sql = "INSERT INTO volunteers (full_name, email, phone_number, reason_to_join, user_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $full_name, $email, $phone, $reason, $user_id);
    
    if ($stmt->execute()) {
        $message = 'Pendaftaran volunteer berhasil! Kami akan segera menghubungi Anda.';
        $message_type = 'success';
    } else {
        $message = 'Terjadi kesalahan. Coba lagi.';
        $message_type = 'error';
    }
}

// --- LOGIKA FORM 2: PENDAFTARAN TENANT ---
if (isset($_POST['daftar_tenant'])) {
    $brand_name = $_POST['brand_name'];
    $contact_person = $_POST['contact_person'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $booth_type = $_POST['booth_type'];

    $sql = "INSERT INTO tenants (brand_name, contact_person, email, phone_number, booth_type, user_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $brand_name, $contact_person, $email, $phone, $booth_type, $user_id);
    
    if ($stmt->execute()) {
        $message = 'Pendaftaran tenant berhasil! Tim kami akan meninjau proposal Anda.';
        $message_type = 'success';
    } else {
        $message = 'Terjadi kesalahan. Coba lagi.';
        $message_type = 'error';
    }
}
?>

<div class="container page-container">
    <h1 class="page-main-title">Get Involved</h1>
    <p class="page-subtitle">Jadilah bagian dari gerakan ini. Daftar sebagai relawan atau buka booth tenant.</p>

    <?php if ($message): ?>
        <div class="alert <?php echo $message_type; ?>"><p><?php echo $message; ?></p></div>
    <?php endif; ?>

    <div class="form-wrapper-grid">
        <div class="form-panel">
            <h2 class="panel-title">Daftar Volunteer</h2>
            <p class="panel-description">Dapatkan pengalaman di balik layar, teman baru, dan akses ke festival.</p>
            <form action="get_involved.php" method="POST" class="admin-form">
                <input type="hidden" name="daftar_volunteer" value="1">
                <div class="form-group">
                    <label for="vol_name">Nama Lengkap</label>
                    <input type="text" id="vol_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="vol_email">Email</label>
                    <input type="email" id="vol_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="vol_phone">Nomor Telepon</label>
                    <input type="tel" id="vol_phone" name="phone_number" required>
                </div>
                <div class="form-group">
                    <label for="vol_reason">Alasan Ingin Bergabung</label>
                    <textarea id="vol_reason" name="reason_to_join" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn-standard">Kirim Pendaftaran Volunteer</button>
            </form>
        </div>

        <div class="form-panel">
            <h2 class="panel-title">Daftar Tenant</h2>
            <p class="panel-description">Punya brand F&B, fashion, atau komunitas yang keren? Bergabunglah dengan kami.</p>
            <form action="get_involved.php" method="POST" class="admin-form">
                <input type="hidden" name="daftar_tenant" value="1">
                <div class="form-group">
                    <label for="ten_brand">Nama Brand/Tenant</label>
                    <input type="text" id="ten_brand" name="brand_name" required>
                </div>
                <div class="form-group">
                    <label for="ten_contact">Nama Narahubung</label>
                    <input type="text" id="ten_contact" name="contact_person" required>
                </div>
                <div class="form-group">
                    <label for="ten_email">Email</label>
                    <input type="email" id="ten_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="ten_phone">Nomor Telepon</label>
                    <input type="tel" id="ten_phone" name="phone_number" required>
                </div>
                <div class="form-group">
                    <label for="ten_type">Jenis Booth (F&B, Fashion, Komunitas, dll.)</label>
                    <input type="text" id="ten_type" name="booth_type" required>
                </div>
                <button type="submit" class="btn-standard">Kirim Pendaftaran Tenant</button>
            </form>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>