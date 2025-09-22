<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Adoptify</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-register-container">
        <div class="login-illustration">
            <div>
                <h1>LET'S FIND YOUR NEW FRIEND!</h1>
                <p>Join the family and give love to a cat in need.</p>
            </div>
        </div>

        <div class="login-form">
            <div class="login-box">
                <h2>REGISTER</h2>

                <?php
                $message = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $nama = trim($_POST['nama']);
                    $email = trim($_POST['email']);
                    $password = $_POST['password'] ?? '';
                    $no_telepon = $_POST['no_telepon'] ?? null;
                    $alamat = $_POST['alamat'] ?? null;

                    // 1. Cek field wajib
                    if (empty($nama) || empty($email) || empty($password)) {
                        $message = "Semua field wajib diisi.";
                    }
                    // 2. Cek panjang password
                    elseif (strlen($password) < 6) {
                        $message = "Password minimal 6 karakter.";
                    }
                    else {
                        try {
                            $stmt = $pdo->prepare("SELECT id_user FROM users WHERE email = ?");
                            $stmt->execute([$email]);
                            if ($stmt->fetch()) {
                                $message = "Email sudah terdaftar. <a href='login.php'>Login</a>";
                            } else {
                                $hashed = password_hash($password, PASSWORD_DEFAULT);
                                $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, no_telepon, alamat) VALUES (?, ?, ?, ?, ?)");
                                $stmt->execute([$nama, $email, $hashed, $no_telepon, $alamat]);
                                $message = "Berhasil daftar! <a href='login.php'>Login sekarang</a>";
                            }
                        } catch (Exception $e) {
                            $message = "Error: " . $e->getMessage();
                        }
                    }
                }
                ?>

                <?php if ($message): ?>
                    <p style="color:green; text-align:center; margin-bottom:15px;">
                        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" placeholder="Nama Anda" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>No Telepon</label>
                        <input type="text" name="no_telepon" placeholder="08123456789" value="<?= htmlspecialchars($_POST['no_telepon'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="alamat" placeholder="Jl. Kucing No. 123" value="<?= htmlspecialchars($_POST['alamat'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn">REGISTER</button>
                </form>

                <div class="footer-link">
                    Sudah punya akun? <a href="login.php">Login di sini</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>