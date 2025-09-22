<?php
session_start();
include 'config.php';

$message = '';
$user_name = '';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Ambil nama user dari database
try {
    $stmt = $pdo->prepare("SELECT nama FROM users WHERE id_user = ?");
    $stmt->execute([$_SESSION['id_user']]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "User tidak ditemukan.";
        header("refresh:3;url=login.php");
        exit;
    }
    $user_name = $user['nama'];
} catch (Exception $e) {
    $message = "Error sistem: " . $e->getMessage();
}

// Proses form saat disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kucing = trim($_POST['nama_kucing']);
    $jenis = trim($_POST['jenis']);
    $umur = (int)$_POST['umur'];
    $kelamin = $_POST['kelamin'];
    $berat = floatval($_POST['berat']);
    $deskripsi = trim($_POST['deskripsi']);
    $sudah_steril = isset($_POST['sudah_steril']) ? 1 : 0;
    $sudah_vaksin = isset($_POST['sudah_vaksin']) ? 1 : 0;
    $persetujuan = isset($_POST['persetujuan']) ? 1 : 0;

    // Validasi input
    if (empty($nama_kucing) || empty($jenis) || $umur < 1 || empty($kelamin) || $berat <= 0 || empty($deskripsi)) {
        $message = "Semua field wajib diisi dengan benar.";
    } elseif (!in_array($kelamin, ['Jantan', 'Betina'])) {
        $message = "Kelamin harus Jantan atau Betina.";
    } elseif (!$persetujuan) {
        $message = "Anda harus menyetujui pernyataan.";
    } else {
        // Upload file
        $file = $_FILES['foto'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $message = "Error upload file.";
        } elseif (!in_array($file['type'], $allowed_types)) {
            $message = "Hanya file gambar (JPG, PNG, GIF) yang diperbolehkan.";
        } elseif ($file['size'] > $max_size) {
            $message = "Ukuran file maksimal 5MB.";
        } else {
            // Buat folder uploads jika belum ada
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . uniqid() . '.' . $ext;
            $filepath = 'uploads/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO kucing (
                        nama_kucing, jenis, umur, kelamin, deskripsi, foto, id_pemilik, status
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Tersedia')");

                    $stmt->execute([
                        $nama_kucing, $jenis, $umur, $kelamin, $deskripsi, $filepath, $_SESSION['id_user']
                    ]);

                    $message = "Kucing berhasil diserahkan untuk diadopsi!";
                    $_POST = []; // Reset form
                } catch (Exception $e) {
                    $message = "Gagal menyimpan ke database: " . $e->getMessage();
                }
            } else {
                $message = "Gagal menyimpan file. Pastikan folder 'uploads' bisa ditulis.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rehome - Adoptify</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .rehome-form .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 0.95em;
            border: 1px solid #f5c6cb;
        }

        .rehome-form .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 0.95em;
            border: 1px solid #c3e6cb;
        }

        .rehome-form input,
        .rehome-form textarea,
        .rehome-form select {
            transition: all 0.3s ease;
        }

        .rehome-form input:focus,
        .rehome-form textarea:focus {
            border-color: #4a00e0;
            box-shadow: 0 0 0 3px rgba(74, 0, 224, 0.1);
        }

        .rehome-form input::placeholder {
            color: #aaa;
        }

        .hero img {
            width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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

    <section class="hero" style="background: linear-gradient(135deg, #e0c3fc, #a8e6cf); text-align: center; flex-direction: column; gap: 20px;">
        <h1>REHOME</h1>
        <p>Need to rehome your pet? We're here to help.</p>
        <img src="https://placehold.co/200x150/4a00e0/ffffff?text=🐱" alt="Rehome Cat" style="align-self: center;">
    </section>

    <div class="rehome-form">
        <h2>Submit Your Pet for Adoption</h2>

        <?php if ($message): ?>
            <div class="<?= strpos($message, 'berhasil') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Nama Pemilik</label>
            <input type="text" value="<?= htmlspecialchars($user_name) ?>" readonly style="background: #f0f0f0; color: #333; font-weight: 500;">

            <label>Nama Kucing</label>
            <input type="text" name="nama_kucing" placeholder="e.g. Mimi" value="<?= htmlspecialchars($_POST['nama_kucing'] ?? '') ?>" required>

            <label>Jenis</label>
            <input type="text" name="jenis" placeholder="e.g. Persia" value="<?= htmlspecialchars($_POST['jenis'] ?? '') ?>" required>

            <label>Umur (bulan)</label>
            <input type="number" name="umur" min="1" value="<?= $_POST['umur'] ?? '' ?>" required>

            <label>Kelamin</label>
            <select name="kelamin" required>
                <option value="">Pilih kelamin</option>
                <option value="Jantan" <?= (($_POST['kelamin'] ?? '') == 'Jantan') ? 'selected' : '' ?>>Jantan</option>
                <option value="Betina" <?= (($_POST['kelamin'] ?? '') == 'Betina') ? 'selected' : '' ?>>Betina</option>
            </select>

            <label>Berat Badan (kg)</label>
            <input type="number" step="0.1" name="berat" placeholder="e.g. 3.5" value="<?= $_POST['berat'] ?? '' ?>" required>

            <label>Foto</label>
            <input type="file" name="foto" accept="image/*" required>

            <label>Deskripsi (sifat, kebiasaan, dll)</label>
            <textarea name="deskripsi" rows="4" placeholder="e.g. Suka bermain, ramah, suka tidur di sofa."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>

            <div style="margin: 15px 0; font-size: 0.95em;">
                <label><input type="checkbox" name="sudah_steril" <?= isset($_POST['sudah_steril']) ? 'checked' : '' ?>> Sudah steril</label><br>
                <label><input type="checkbox" name="sudah_vaksin" <?= isset($_POST['sudah_vaksin']) ? 'checked' : '' ?>> Sudah vaksin</label><br>
                <label><input type="checkbox" name="persetujuan" required <?= isset($_POST['persetujuan']) ? 'checked' : '' ?>> Saya menyetujui bahwa informasi yang saya berikan benar dan siap untuk proses adopsi.</label>
            </div>

            <button type="submit" class="btn">Submit for Adoption</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 Adoptify. All rights reserved.</p>
        <p><a href="index.php">Come on, let's adopt us!</a></p>
    </footer>
</body>
</html>