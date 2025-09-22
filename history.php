<?php session_start(); include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>History - Adoptify</title>
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
    <section class="hero" style="background: linear-gradient(135deg, #c2e9fb, #a8e6cf); text-align: center; flex-direction: column; gap: 20px;">
        <h1>HISTORY</h1>
        <p>Track your adoption journey and submissions.</p>
        <img src="https://placehold.co/200x150/4a00e0/ffffff?text=🐱" alt="History Cat" style="align-self: center; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
    </section>

    <!-- History Container -->
    <div class="history-container">
        <h2>Your Complete History</h2>

        <?php if (!isset($_SESSION['id_user'])): ?>
            <div class="history-item">
                <p>Please <a href="login.php" style="color:#4a00e0; font-weight:600;">log in</a> to view your history.</p>
            </div>
        <?php else: ?>

            <!-- 1. Cats You Adopted -->
 

            <!-- 2. Cats You Submitted for Adoption -->
            <h3 style="color:#4a00e0; margin:30px 0 15px;">📤 Cats You Submitted for Adoption</h3>
            <?php
            $stmt = $pdo->prepare("
                SELECT nama_kucing, jenis, foto, tanggal_ditambahkan, status
                FROM kucing
                WHERE id_pemilik = ?
                ORDER BY tanggal_ditambahkan DESC
            ");
            $stmt->execute([$_SESSION['id_user']]);
            $submitted = $stmt->fetchAll();

            if ($submitted):
                foreach ($submitted as $s):
                    $statusColor = $s['status'] == 'Tersedia' ? '#28a745' : 
                                   ($s['status'] == 'Diadopsi' ? '#007bff' : '#ffc107');
            ?>
                    <div class="history-item">
                        <div style="display:flex; gap:15px; align-items:center;">
                            <img src="<?= htmlspecialchars($s['foto'] ?: 'https://placehold.co/80x80/a8e6cf/4a00e0?text=🐱') ?>" 
                                 alt="<?= htmlspecialchars($s['nama_kucing']) ?>" 
                                 style="width:80px; height:80px; object-fit:cover; border-radius:10px;">
                            <div>
                                <h4><?= htmlspecialchars($s['nama_kucing']) ?> (<?= htmlspecialchars($s['jenis']) ?>)</h4>
                                <p><strong>Status:</strong> 
                                   <span style="color:<?= $statusColor ?>; font-weight:600; text-transform:uppercase;"><?= $s['status'] ?></span>
                                </p>
                                <p><strong>Submitted on:</strong> <?= date('d M Y', strtotime($s['tanggal_ditambahkan'])) ?></p>
                            </div>
                        </div>
                    </div>
            <?php
                endforeach;
            else:
                echo "<p style='color:#666; text-align:center; font-style:italic;'>You haven't submitted any cat for adoption yet.</p>";
            endif;
            ?>

            <!-- 3. Adoption Applications -->
            <h3 style="color:#4a00e0; margin:30px 0 15px;">⏳ Adoption Applications</h3>
            <?php
            $stmt = $pdo->prepare("
                SELECT k.nama_kucing, k.jenis, k.foto, p.status_pengajuan, p.tanggal_pengajuan
                FROM pengajuan_adopsi p
                JOIN kucing k ON p.id_kucing = k.id_kucing
                WHERE p.id_user_pemohon = ?
            ");
            $stmt->execute([$_SESSION['id_user']]);
            $pengajuan = $stmt->fetchAll();

            if ($pengajuan):
                foreach ($pengajuan as $p):
                    $statusColor = $p['status_pengajuan'] == 'Disetujui' ? '#28a745' : 
                                   ($p['status_pengajuan'] == 'Ditolak' ? '#dc3545' : '#ffc107');
            ?>
                    <div class="history-item">
                        <div style="display:flex; gap:15px; align-items:center;">
                            <img src="<?= htmlspecialchars($p['foto'] ?: 'https://placehold.co/80x80/a8e6cf/4a00e0?text=🐱') ?>" 
                                 alt="<?= htmlspecialchars($p['nama_kucing']) ?>" 
                                 style="width:80px; height:80px; object-fit:cover; border-radius:10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <div>
                                <h4><?= htmlspecialchars($p['nama_kucing']) ?> (<?= htmlspecialchars($p['jenis']) ?>)</h4>
                                <p><strong>Status:</strong> 
                                   <span style="color:<?= $statusColor ?>; font-weight:600; text-transform:uppercase;"><?= $p['status_pengajuan'] ?></span>
                                </p>
                                <p><strong>Applied on:</strong> <?= date('d M Y', strtotime($p['tanggal_pengajuan'])) ?></p>
                            </div>
                        </div>
                    </div>
            <?php
                endforeach;
            else:
                echo "<p style='color:#666; text-align:center; font-style:italic;'>No adoption applications submitted.</p>";
            endif;
            ?>

        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 Adoptify. All rights reserved.</p>
        <p><a href="index.php">Come on, let's adopt us!</a></p>
    </footer>
</body>
</html>