<?php
session_start();
include 'config.php';

$message = '';

// Query kucing tersedia
try {
    $stmt = $pdo->prepare("SELECT * FROM kucing WHERE status = 'Tersedia'");
    $stmt->execute();
} catch (Exception $e) {
    $message = "Gagal memuat data kucing.";
    $stmt = false;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catalog - Adoptify</title>
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
            <a href="#catalog" class="btn">Explore Now</a>
        </div>
        <img src="https://placehold.co/400x300/4a00e0/ffffff?text=🐱" alt="Kucing Lucu">
        <div class="wave"></div>
    </section>

    <!-- Recently Added Cats -->
    <div class="catalog" id="catalog">
        <h2>Recently Added Cats</h2>
        <div class="kucing-grid">
            <?php
            try {
                $recent = $pdo->prepare("SELECT k.*, id_pemilik FROM kucing k WHERE status = 'Tersedia' ORDER BY tanggal_ditambahkan DESC LIMIT 4");
                $recent->execute();
                $kittens = $recent->fetchAll();

                if ($kittens):
                    foreach ($kittens as $k):
            ?>
                        <div class="kucing-card">
                            <img src="<?= htmlspecialchars($k['foto'] ?: 'https://placehold.co/300x200/a8e6cf/4a00e0?text=🐱') ?>" 
                                 alt="<?= htmlspecialchars($k['nama_kucing']) ?>">
                            <div class="kucing-info">
                                <h3><?= htmlspecialchars($k['nama_kucing']) ?></h3>
                                <p><strong>Type:</strong> <?= htmlspecialchars($k['jenis']) ?></p>
                                <p><strong>Age:</strong> <?= $k['umur'] ?> months</p>

                                <!-- Tombol Adopt Me -->
                                <?php if (isset($_SESSION['id_user']) && $_SESSION['role'] !== 'admin'): ?>
                                    <a href="ajukan-adopsi.php?id=<?= $k['id_kucing'] ?>" class="btn">Adopt Me</a>
                                <?php elseif (!isset($_SESSION['id_user'])): ?>
                                    <a href="login.php" class="btn">Login to Adopt</a>
                                <?php else: ?>
                                    <button class="btn" disabled>Admin cannot adopt</button>
                                <?php endif; ?>

                                <!-- Tombol Edit & Hapus (Hanya untuk Pemilik atau Admin) -->
                                <?php if (isset($_SESSION['id_user'])): ?>
                                    <?php 
                                    $canEdit = false;
                                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                                        $canEdit = true; // Admin bisa edit semua
                                    } elseif ($_SESSION['id_user'] == $k['id_pemilik']) {
                                        $canEdit = true; // Pemilik bisa edit
                                    }

                                    if ($canEdit): ?>
                                        <div style="margin-top:10px; display:flex; gap:10px;">
                                            <a href="edit_kucing.php?id=<?= $k['id_kucing'] ?>" 
                                               class="btn" 
                                               style="padding:6px 10px; font-size:0.9em; background:#ffc107;">Edit</a>
                                            <a href="delete_kucing.php?id=<?= $k['id_kucing'] ?>" 
                                               class="btn" 
                                               style="padding:6px 10px; font-size:0.9em; background:#dc3545;"
                                               onclick="return confirm('Yakin hapus kucing ini?')">Hapus</a>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
            <?php
                    endforeach;
                else:
                    echo "<p style='color:#666; text-align:center; width:100%;'>No cats available at the moment.</p>";
                endif;
            } catch (Exception $e) {
                echo "<p style='color:red; text-align:center;'>Error loading cats.</p>";
            }
            ?>
        </div>
    </div>

    <!-- All Available Cats -->
    <div class="catalog">
        <h2>Available Cats for Adoption</h2>
        <?php if ($message): ?>
            <p style="color:red; text-align:center;"><?= htmlspecialchars($message) ?></p>
        <?php elseif (!$stmt || $stmt->rowCount() == 0): ?>
            <p style="color:#666; text-align:center;">No cats available at the moment.</p>
        <?php else: ?>
            <div class="kucing-grid">
                <?php while ($kucing = $stmt->fetch()): ?>
                    <div class="kucing-card">
                        <img src="<?= htmlspecialchars($kucing['foto'] ?: 'https://placehold.co/300x200/a8e6cf/4a00e0?text=🐱') ?>" 
                             alt="<?= htmlspecialchars($kucing['nama_kucing']) ?>">
                        <div class="kucing-info">
                            <h3><?= htmlspecialchars($kucing['nama_kucing']) ?></h3>
                            <p><strong>Type:</strong> <?= htmlspecialchars($kucing['jenis']) ?></p>
                            <p><strong>Age:</strong> <?= $kucing['umur'] ?> months</p>

                            <!-- Tombol Adopt Me -->
                            <?php if (isset($_SESSION['id_user']) && $_SESSION['role'] !== 'admin'): ?>
                                <a href="ajukan-adopsi.php?id=<?= $kucing['id_kucing'] ?>" class="btn">Adopt Me</a>
                            <?php elseif (!isset($_SESSION['id_user'])): ?>
                                <a href="login.php" class="btn">Login to Adopt</a>
                            <?php else: ?>
                                <button class="btn" disabled>Admin cannot adopt</button>
                            <?php endif; ?>

                            <!-- Tombol Edit & Hapus -->
                            <?php if (isset($_SESSION['id_user'])): ?>
                                <?php 
                                $canEdit = false;
                                if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                                    $canEdit = true;
                                } elseif ($_SESSION['id_user'] == $kucing['id_pemilik']) {
                                    $canEdit = true;
                                }

                                if ($canEdit): ?>
                                    <div style="margin-top:10px; display:flex; gap:10px;">
                                        <a href="edit_kucing.php?id=<?= $kucing['id_kucing'] ?>" 
                                           class="btn" 
                                           style="padding:6px 10px; font-size:0.9em; background:#ffc107;">Edit</a>
                                        <a href="delete_kucing.php?id=<?= $kucing['id_kucing'] ?>" 
                                           class="btn" 
                                           style="padding:6px 10px; font-size:0.9em; background:#dc3545;"
                                           onclick="return confirm('Yakin hapus kucing ini?')">Hapus</a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 Adoptify. All rights reserved.</p>
    </footer>
</body>
</html>