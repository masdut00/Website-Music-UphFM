<?php
require_once '../includes/db.php';
$page_title = 'Login';

if (isset($_SESSION['user_id'])) {
    header("Location: /upfm_web/index.php");
    exit();
}

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
    
    <style>
        /* Panel Kiri Hitam untuk Animasi */
        .login-image-panel {
            background-color: #000; /* Ganti gambar dengan warna hitam */
            position: relative;
            overflow: hidden;
        }
        /* Canvas Penuh di Panel Kiri */
        #login-canvas {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
        }
        /* Teks overlay di atas animasi (Opsional) */
        .login-overlay-text {
            position: absolute;
            bottom: 50px; left: 50px;
            color: white;
            z-index: 10;
            font-family: sans-serif;
        }
        .login-overlay-text h1 { font-size: 3rem; margin: 0; font-weight: 800; color: #ff6347; }
        .login-overlay-text p { font-size: 1.2rem; opacity: 0.8; margin-top: 10px; }
    </style>
</head>
<body>

<div class="login-page-wrapper">
    
    <div class="login-image-panel" id="animPanel">
        <canvas id="login-canvas"></canvas>
        <div class="login-overlay-text">
            <h1>UPFM 2025</h1>
            <p>Experience the music.<br>Feel the beat.</p>
        </div>
    </div>

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

<script>
    const canvas = document.getElementById('login-canvas');
    const ctx = canvas.getContext('2d');
    const panel = document.getElementById('animPanel');

    let width, height;
    let particles = [];

    // Konfigurasi Partikel
    const particleCount = 80;
    const connectionDistance = 120;
    const mouseDistance = 150;

    // Posisi Mouse
    let mouse = { x: null, y: null };

    // Resize Canvas
    function resize() {
        width = canvas.width = panel.offsetWidth;
        height = canvas.height = panel.offsetHeight;
    }

    // Class Particle
    class Particle {
        constructor() {
            this.x = Math.random() * width;
            this.y = Math.random() * height;
            this.vx = (Math.random() - 0.5) * 1.5; // Kecepatan X
            this.vy = (Math.random() - 0.5) * 1.5; // Kecepatan Y
            this.size = Math.random() * 2 + 1;
            this.color = '#ff6347'; // Warna Oranye
        }

        update() {
            this.x += this.vx;
            this.y += this.vy;

            // Pantulan dinding
            if (this.x < 0 || this.x > width) this.vx *= -1;
            if (this.y < 0 || this.y > height) this.vy *= -1;

            // Interaksi Mouse
            let dx = mouse.x - this.x;
            let dy = mouse.y - this.y;
            let distance = Math.sqrt(dx*dx + dy*dy);

            if (distance < mouseDistance) {
                const forceDirectionX = dx / distance;
                const forceDirectionY = dy / distance;
                const force = (mouseDistance - distance) / mouseDistance;
                const directionX = forceDirectionX * force * 3; // Kekuatan tolak
                const directionY = forceDirectionY * force * 3;

                this.x -= directionX;
                this.y -= directionY;
            }
        }

        draw() {
            ctx.fillStyle = this.color;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function init() {
        resize();
        particles = [];
        for (let i = 0; i < particleCount; i++) {
            particles.push(new Particle());
        }
    }

    function animate() {
        ctx.clearRect(0, 0, width, height);
        
        for (let i = 0; i < particles.length; i++) {
            particles[i].update();
            particles[i].draw();

            // Gambar Garis Penghubung
            for (let j = i; j < particles.length; j++) {
                let dx = particles[i].x - particles[j].x;
                let dy = particles[i].y - particles[j].y;
                let distance = Math.sqrt(dx*dx + dy*dy);

                if (distance < connectionDistance) {
                    ctx.beginPath();
                    // Warna garis memudar berdasarkan jarak
                    ctx.strokeStyle = `rgba(255, 99, 71, ${1 - distance/connectionDistance})`;
                    ctx.lineWidth = 1;
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                }
            }
        }
        requestAnimationFrame(animate);
    }

    // Event Listeners
    window.addEventListener('resize', () => {
        resize();
        init();
    });

    panel.addEventListener('mousemove', (e) => {
        // Hitung posisi mouse relatif terhadap canvas (bukan layar)
        const rect = canvas.getBoundingClientRect();
        mouse.x = e.clientX - rect.left;
        mouse.y = e.clientY - rect.top;
    });

    panel.addEventListener('mouseleave', () => {
        mouse.x = null;
        mouse.y = null;
    });

    init();
    animate();
</script>

</body>
</html>