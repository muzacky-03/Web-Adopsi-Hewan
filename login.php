<?php
session_start();
include 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $as_role = $_POST['as_role'] ?? 'user'; // Default: user

    if (empty($email) || empty($password)) {
        $message = "Email dan password wajib diisi.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Simpan session
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                // Cek peran yang diminta vs peran sebenarnya
                if ($as_role === 'admin') {
                    if ($user['role'] === 'admin') {
                        header("Location: admin/pengajuan.php");
                        exit;
                    } else {
                        $message = "Akses ditolak: Anda bukan admin.";
                    }
                } else {
                    // Login sebagai user
                    if ($user['role'] === 'admin') {
                        $message = "❌ Anda adalah admin. Harus login sebagai admin.";
                    } else {
                        header("Location: index.php");
                        exit;
                    }
                }
            } else {
                $message = "Email atau password salah.";
            }
        } catch (Exception $e) {
            $message = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Adoptify</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            background: #f0f7ff;
        }

        .login-illustration {
            flex: 1;
            background: linear-gradient(135deg, #a8e6cf, #c2e9fb);
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 20px 0 0 20px;
            overflow: hidden;
        }

        .login-illustration h1 {
            color: #4a00e0;
            font-size: 2.5em;
            text-align: center;
            padding: 40px;
            font-weight: 700;
        }

        .login-illustration p {
            color: #333;
            font-size: 1.2em;
            text-align: center;
            padding: 0 40px;
            opacity: 0.9;
        }

        .login-form {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: white;
            border-radius: 0 20px 20px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .login-box h2 {
            text-align: center;
            color: #4a00e0;
            font-size: 2em;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
        }

        .btn {
            background: #4a00e0;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn:hover {
            background: #3a00b0;
        }

        .btn-admin {
            background: #dc3545;
        }

        .btn-admin:hover {
            background: #c82333;
        }

        .footer-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.95em;
        }

        .footer-link a {
            color: #4a00e0;
            text-decoration: none;
            font-weight: 600;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Ilustrasi -->
        <div class="login-illustration">
            <div>
                <h1>COME ON, LET'S ADOPT US!</h1>
                <p>Join the family and give love to a cat in need.</p>
            </div>
        </div>

        <!-- Form Login -->
        <div class="login-form">
            <div class="login-box">
                <h2>Login</h2>

                <?php if ($message): ?>
                    <div class="error"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="your@email.com" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>

                    <!-- Tombol Login sebagai User -->
                    <button type="submit" name="as_role" value="user" class="btn">Login as User</button>

                    <!-- Tombol Login sebagai Admin -->
                    <button type="submit" name="as_role" value="admin" class="btn btn-admin">Login as Admin</button>
                </form>

                <div class="footer-link">
                    Don't have an account? <a href="register.php">Register here</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>