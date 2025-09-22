<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    die("<h3 style='text-align:center; color:#dc3545; margin:50px;'>❌ Admin tidak dapat mengajukan adopsi kucing.</h3>");
}

$kucing_id = $_GET['id'] ?? null;

if (!$kucing_id) {
    die("Kucing tidak ditemukan.");
}

// Ambil data kucing
$stmt = $pdo->prepare("SELECT * FROM kucing WHERE id_kucing = ? AND status = 'Tersedia'");
$stmt->execute([$kucing_id]);
$kucing = $stmt->fetch();

if (!$kucing) {
    die("Kucing tidak tersedia untuk diadopsi.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user_pemohon = $_SESSION['id_user'];
    $id_kucing = $kucing['id_kucing'];

    try {
        $stmt = $pdo->prepare("INSERT INTO pengajuan_adopsi (id_user_pemohon, id_kucing) VALUES (?, ?)");
        $stmt->execute([$id_user_pemohon, $id_kucing]);
        $message = "Pengajuan adopsi berhasil dikirim! Silakan cek di History.";
    } catch (Exception $e) {
        $message = "Gagal mengirim pengajuan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ajukan Adopsi - Adoptify</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 60px auto;
            padding: 30px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            color: #4a00e0;
            text-align: center;
            margin-bottom: 20px;
        }

        .kucing-preview {
            display: flex;
            gap: 15px;
            align-items: center;
            background: #f0f7ff;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .kucing-preview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
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
            </ul>
        </div>
    </header>

    <div class="form-container">
        <h2>Ajukan Adopsi</h2>

        <?php if ($message): ?>
            <p style="color: green; text-align: center;"><?= $message ?></p>
        <?php else: ?>
            <div class="kucing-preview">
                <img src="<?= $kucing['foto'] ?: 'https://placehold.co/80x80' ?>" alt="<?= $kucing['nama_kucing'] ?>">
                <div>
                    <h4><?= htmlspecialchars($kucing['nama_kucing']) ?></h4>
                    <p><?= $kucing['jenis'] ?> | <?= $kucing['umur'] ?> bulan</p>
                </div>
            </div>

            <p>Anda akan mengajukan adopsi kucing ini. Tim kami akan memverifikasi dan memberi kabar melalui notifikasi.</p>

            <form method="POST">
                <button type="submit" class="btn">Ajukan Sekarang</button>
            </form>
        <?php endif; ?>

        <p style="text-align: center; margin-top: 20px;">
            <a href="catalog.php" style="color: #4a00e0;">← Kembali ke Catalog</a>
        </p>
    </div>

    <footer>
        <p>&copy; 2025 Adoptify. All rights reserved.</p>
    </footer>
</body>
</html>