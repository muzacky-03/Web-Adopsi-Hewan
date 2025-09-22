<?php
session_start();

// Cek apakah user login dan sebagai admin
if (!isset($_SESSION['id_user']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak. <a href='../login.php'>Login sebagai admin</a>");
}

include '../config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin: Pengajuan Adopsi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-container {
            max-width: 1000px;
            margin: 60px auto;
            padding: 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .admin-header {
            text-align: center;
            margin-bottom: 30px;
            color: #4a00e0;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .admin-table th, .admin-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .admin-table th {
            background: #4a00e0;
            color: white;
        }

        .btn-approve {
            background: #28a745;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
        }

        .status-menunggu {
            color: #ffc107;
            font-weight: 600;
        }

        .status-disetujui {
            color: #28a745;
            font-weight: 600;
        }

        .status-ditolak {
            color: #dc3545;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">ADOPTIFY</div>
            <ul>
                <li><a href="../index.php">HOME</a></li>
                <li><a href="../catalog.php">CATALOG</a></li>
                <li><a href="../rehome.php">REHOME</a></li>
                <li><a href="../history.php">HISTORY</a></li>
                <li><a href="../profile.php">PROFILE</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="admin/pengajuan.php" style="background:#4a00e0; color:white; padding:5px 10px; border-radius:5px;">PENGAJUAN</a></li>
            <?php endif; ?>
            </ul>
        </div>
    </header>

    <div class="admin-container">
        <h2 class="admin-header">📋 Pengajuan Adopsi</h2>
        <a href="../index.php" class="btn" style="margin-bottom:20px;">← Kembali</a>

        <table class="admin-table">
            <tr>
                <th>Pemohon</th>
                <th>Kucing</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php
            $stmt = $pdo->query("
                SELECT 
                    p.id_pengajuan, 
                    p.status_pengajuan, 
                    p.tanggal_pengajuan,
                    u.nama AS nama_pemohon,
                    k.nama_kucing,
                    k.jenis
                FROM pengajuan_adopsi p
                JOIN users u ON p.id_user_pemohon = u.id_user
                JOIN kucing k ON p.id_kucing = k.id_kucing
                ORDER BY p.tanggal_pengajuan DESC
            ");

            while ($p = $stmt->fetch()) {
                $statusClass = '';
                if ($p['status_pengajuan'] == 'Menunggu') $statusClass = 'status-menunggu';
                elseif ($p['status_pengajuan'] == 'Disetujui') $statusClass = 'status-disetujui';
                else $statusClass = 'status-ditolak';

                echo "<tr>
                    <td>{$p['nama_pemohon']}</td>
                    <td>{$p['nama_kucing']} ({$p['jenis']})</td>
                    <td>" . date('d M Y', strtotime($p['tanggal_pengajuan'])) . "</td>
                    <td class='$statusClass'>{$p['status_pengajuan']}</td>
                    <td>";

                if ($p['status_pengajuan'] == 'Menunggu') {
                    echo "
                        <a href='?setujui={$p['id_pengajuan']}' class='btn-approve'>Setujui</a>
                        <a href='?tolak={$p['id_pengajuan']}' class='btn-reject'>Tolak</a>
                    ";
                } else {
                    echo "Selesai";
                }

                echo "</td></tr>";
            }

            // Setujui pengajuan
            if (isset($_GET['setujui'])) {
                $id = $_GET['setujui'];
                $pdo->beginTransaction();

                try {
                    // Ambil data pengajuan
                    $stmt = $pdo->query("SELECT id_kucing, id_user_pemohon FROM pengajuan_adopsi WHERE id_pengajuan = $id");
                    $data = $stmt->fetch();

                    // Update status pengajuan
                    $pdo->exec("UPDATE pengajuan_adopsi SET status_pengajuan = 'Disetujui' WHERE id_pengajuan = $id");

                    // Ubah status kucing
                    $pdo->exec("UPDATE kucing SET status = 'Diadopsi' WHERE id_kucing = {$data['id_kucing']}");

                    // Tambah ke riwayat_adopsi
                    $pdo->exec("INSERT INTO riwayat_adopsi (id_kucing, id_pengadopsi, id_pemilik_awal, tanggal_adopsi) 
                                VALUES ({$data['id_kucing']}, {$data['id_user_pemohon']}, {$data['id_user_pemohon']}, NOW())");

                    $pdo->commit();
                } catch (Exception $e) {
                    $pdo->rollback();
                }

                header("Refresh:0");
            }

            // Tolak pengajuan
            if (isset($_GET['tolak'])) {
                $id = $_GET['tolak'];
                $pdo->exec("UPDATE pengajuan_adopsi SET status_pengajuan = 'Ditolak' WHERE id_pengajuan = $id");
                header("Refresh:0");
            }
            ?>
        </table>
    </div>

    <footer>
        <p>&copy; 2025 Adoptify. All rights reserved.</p>
    </footer>
</body>
</html>