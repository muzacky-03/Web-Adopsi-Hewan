<?php
session_start();
include 'config.php';

$message = '';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Ambil data user
$stmt = $pdo->prepare("SELECT nama, email, no_telepon, alamat, tanggal_daftar, foto_profil FROM users WHERE id_user = ?");
$stmt->execute([$_SESSION['id_user']]);
$user = $stmt->fetch();

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_telepon = trim($_POST['no_telepon']) ?: null;
    $alamat = trim($_POST['alamat']) ?: null;

    // Validasi dasar
    if (empty($nama) || empty($email)) {
        $message = "Nama dan email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format email tidak valid.";
    } else {
        try {
            $foto_profil = $user['foto_profil'];

            // Cek upload foto
            if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['foto_profil'];
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024;

                if (!in_array($file['type'], $allowed_types)) {
                    $message = "Hanya file gambar (JPG, PNG, GIF) yang diperbolehkan.";
                } elseif ($file['size'] > $max_size) {
                    $message = "Ukuran file maksimal 5MB.";
                } else {
                    // Buat folder uploads
                    if (!is_dir('uploads')) {
                        mkdir('uploads', 0777, true);
                    }

                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'profil_' . $_SESSION['id_user'] . '_' . time() . '.' . $ext;
                    $filepath = 'uploads/' . $filename;

                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        // Hapus foto lama
                        if ($user['foto_profil'] && file_exists($user['foto_profil'])) {
                            @unlink($user['foto_profil']);
                        }
                        $foto_profil = $filepath;
                    } else {
                        $message = "Gagal menyimpan file.";
                    }
                }
            }

            // Update ke database
            $stmt = $pdo->prepare("UPDATE users SET nama = ?, email = ?, no_telepon = ?, alamat = ?, foto_profil = ? WHERE id_user = ?");
            $stmt->execute([$nama, $email, $no_telepon, $alamat, $foto_profil, $_SESSION['id_user']]);
            $_SESSION['nama'] = $nama;

            $message = "Profil berhasil diperbarui!";
            $user['foto_profil'] = $foto_profil;
            $user['nama'] = $nama;
            $user['email'] = $email;
            $user['no_telepon'] = $no_telepon;
            $user['alamat'] = $alamat;
        } catch (Exception $e) {
            $message = "Gagal menyimpan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profile - Adoptify</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #333;
        }

        .profile-container {
            max-width: 700px;
            margin: 60px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .hero-profile {
            background: linear-gradient(135deg, #a8e6cf, #c2e9fb);
            text-align: center;
            padding: 50px 20px;
            position: relative;
        }

        .hero-profile h1 {
            font-size: 2.5em;
            color: #4a00e0;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .hero-profile p {
            font-size: 1.2em;
            color: #555;
            opacity: 0.9;
        }

        .profile-body {
            padding: 40px;
        }

        .avatar-box {
            text-align: center;
            margin-bottom: 30px;
        }

        .avatar {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #4a00e0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .upload-label {
            display: inline-block;
            margin-top: 10px;
            font-size: 0.9em;
            color: #4a00e0;
            cursor: pointer;
            text-decoration: underline;
        }

        .profile-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .profile-form label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        .profile-form input,
        .profile-form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
        }

        .profile-form input:focus,
        .profile-form textarea:focus {
            outline: none;
            border-color: #4a00e0;
            box-shadow: 0 0 0 3px rgba(74, 0, 224, 0.1);
        }

        .btn {
            background: #4a00e0;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 50px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }

        .btn:hover {
            background: #3a00b0;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(74, 0, 224, 0.3);
        }

        .logout-btn {
            margin-top: 30px; /* Jarak besar dari elemen atas */
            background: #dc3545;
            border-radius: 50px;
            padding: 14px;
            font-size: 1.1em;
            width: 100%;
            display: inline-flexbox;
            font-weight: 600;
}

        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
            text-align: center;
            font-size: 0.95em;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
            text-align: center;
            font-size: 0.95em;
        }

        .admin-note {
            margin: 25px 0;
            padding: 15px;
            background: #fff3cd;
            color: #856404;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            font-size: 0.95em;
        }
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">ADOPTIFY</div>
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="catalog.php">CATALOG</a></li>
                <li><a href="rehome.php">REHOME</a></li>
                <li><a href="history.php">HISTORY</a></li>
                <li><a href="profile.php">PROFILE</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/pengajuan.php" style="background:#4a00e0; color:white; padding:5px 10px; border-radius:5px; font-weight:500;">PENGAJUAN</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <div class="profile-container">
        <div class="hero-profile">
            <h1>PROFILE</h1>
            <p>Manage your personal information and preferences.</p>
        </div>

        <!-- Ganti bagian ini di profile.php -->
<div class="profile-body">
    <?php if ($message): ?>
        <div class="<?= strpos($message, 'berhasil') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Avatar -->
    <div class="avatar-box">
        <img src="<?= htmlspecialchars($user['foto_profil'] ?: 'https://placehold.co/120x120/a8e6cf/4a00e0?text=👤') ?>" 
             alt="Profile Picture" 
             class="avatar">
        <br>
        <label for="foto_profil" class="upload-label">Change Photo</label>
    </div>

    <!-- Form Edit -->
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="foto_profil" id="foto_profil" accept="image/*" style="display:none;">

        <div class="profile-form">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>No Telepon</label>
            <input type="text" name="no_telepon" value="<?= htmlspecialchars($user['no_telepon'] ?? '') ?>">

            <label>Alamat</label>
            <textarea name="alamat" rows="3"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn">Update Profile</button>
    </form>

    <!-- Admin Note -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="admin-note">
            ⚠ Anda login sebagai admin. Anda tidak dapat mengadopsi kucing.
        </div>
    <?php endif; ?>

    <!-- Logout Button - DIPISAHKAN DARI FORM -->
    <div style="margin-top: 30px; text-align: center;">
        <a href="logout.php" class="btn logout-btn">Logout</a>
    </div>
</div>

    <footer>
        <p>&copy; 2025 Adoptify. All rights reserved.</p>
        <p><a href="index.php">Come on, let's adopt us!</a></p>
    </footer>

    <script>
        document.querySelector('.upload-label').addEventListener('click', function() {
            document.getElementById('foto_profil').click();
        });

        document.getElementById('foto_profil').addEventListener('change', function(e) {
            if (e.target.files[0]) {
                document.querySelector('.avatar').src = URL.createObjectURL(e.target.files[0]);
            }
        });
    </script>
</body>
</html>