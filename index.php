<?php
session_start();
include 'config.php';

// Ambil 4 kucing terbaru
$stmt = $pdo->prepare("SELECT k.*, u.nama as pemilik_nama FROM kucing k 
                       LEFT JOIN users u ON k.id_pemilik = u.id_user
                       WHERE k.status = 'Tersedia' 
                       ORDER BY k.tanggal_ditambahkan DESC LIMIT 4");
$stmt->execute();
$kittens = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Adoptify - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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

    <!-- Hero Section -->
    <section class="hero" style="background: linear-gradient(135deg, #a8e6cf, #c2e9fb);">
        <div>
            <h1>LET'S FIND YOUR NEW FRIEND!</h1>
            <p>Find your perfect companion with our adorable cats waiting to be adopted.</p>
            <a href="catalog.php" class="btn">Explore Now</a>
        </div>
        <img src="https://placehold.co/400x300/4a00e0/ffffff?text=🐱" alt="Kucing Lucu">
    </section>

    <!-- Recently Added Cats -->
    <div class="catalog">
        <h2>Recently Added Cats</h2>
        <div class="kucing-grid">
            <?php foreach ($kittens as $k): ?>
                <div class="kucing-card">
                    <img src="<?= htmlspecialchars($k['foto'] ?: 'https://placehold.co/300x200/a8e6cf/4a00e0?text=🐱') ?>" 
                         alt="<?= htmlspecialchars($k['nama_kucing']) ?>">
                    <div class="kucing-info">
                        <h3><?= htmlspecialchars($k['nama_kucing']) ?></h3>
                        <p><strong>Type:</strong> <?= htmlspecialchars($k['jenis']) ?></p>
                        <p><strong>Age:</strong> <?= $k['umur'] ?> months</p>
                        
                        <!-- Hanya tampilkan tombol jika login -->
                        <?php if (isset($_SESSION['id_user'])): ?>
                            <?php 
                            $canEdit = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') || 
                                       ($_SESSION['id_user'] == $k['id_pemilik']);
                            ?>
                            <?php if ($canEdit): ?>
                                <div style="margin-top:10px; display:flex; gap:10px;">
                                    <a href="edit_kucing.php?id=<?= $k['id_kucing'] ?>" class="btn" style="padding:6px 10px; font-size:0.9em;">Edit</a>
                                    <a href="delete_kucing.php?id=<?= $k['id_kucing'] ?>" 
                                       class="btn" 
                                       style="background:#dc3545; padding:6px 10px; font-size:0.9em;"
                                       onclick="return confirm('Yakin hapus kucing ini?')">Hapus</a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.php" class="btn">Login to Adopt</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Adoptify. All rights reserved.</p>
        <p><a href="index.php">Come on, let's adopt us!</a></p>
    </footer>
</body>
</html>