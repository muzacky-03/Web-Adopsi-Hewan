<?php
session_start();
include 'config.php';

$id = $_GET['id'] ?? null;

// Ambil data kucing
$stmt = $pdo->prepare("SELECT * FROM kucing WHERE id_kucing = ?");
$stmt->execute([$id]);
$kucing = $stmt->fetch();

if (!$kucing) {
    die("Kucing tidak ditemukan.");
}

// Cek apakah user bisa edit
$canEdit = false;
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $canEdit = true;
} elseif (isset($_SESSION['id_user']) && $_SESSION['id_user'] == $kucing['id_pemilik']) {
    $canEdit = true;
}

if (!$canEdit) {
    die("Akses ditolak.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kucing = trim($_POST['nama_kucing']);
    $jenis = trim($_POST['jenis']);
    $umur = (int)$_POST['umur'];
    $kelamin = $_POST['kelamin'];
    $berat = floatval($_POST['berat']);
    $deskripsi = trim($_POST['deskripsi']);
    $sudah_steril = isset($_POST['sudah_steril']) ? 1 : 0;
    $sudah_vaksin = isset($_POST['sudah_vaksin']) ? 1 : 0;

    if (empty($nama_kucing) || empty($jenis) || $umur < 1 || empty($kelamin) || $berat <= 0 || empty($deskripsi)) {
        $message = "Semua field wajib diisi.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE kucing SET 
                nama_kucing = ?, jenis = ?, umur = ?, kelamin = ?, berat = ?, 
                deskripsi = ?, sudah_steril = ?, sudah_vaksin = ? 
                WHERE id_kucing = ?");
            $stmt->execute([
                $nama_kucing, $jenis, $umur, $kelamin, $berat, $deskripsi, $sudah_steril, $sudah_vaksin, $id
            ]);
            $message = "Data kucing berhasil diperbarui!";
        } catch (Exception $e) {
            $message = "Gagal: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Kucing - Adoptify</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>...</header>

    <div class="rehome-form" style="max-width:500px; margin:60px auto;">
        <h2>Edit Kucing</h2>
        <?php if ($message): ?>
            <p style="color:green; text-align:center;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Nama Kucing</label>
            <input type="text" name="nama_kucing" value="<?= htmlspecialchars($kucing['nama_kucing']) ?>" required>

            <label>Jenis</label>
            <input type="text" name="jenis" value="<?= htmlspecialchars($kucing['jenis']) ?>" required>

            <label>Umur (bulan)</label>
            <input type="number" name="umur" value="<?= $kucing['umur'] ?>" required>

            <label>Kelamin</label>
            <select name="kelamin" required>
                <option value="Jantan" <?= $kucing['kelamin'] == 'Jantan' ? 'selected' : '' ?>>Jantan</option>
                <option value="Betina" <?= $kucing['kelamin'] == 'Betina' ? 'selected' : '' ?>>Betina</option>
            </select>

            <label>Berat (kg)</label>
            <input type="number" step="0.1" name="berat" value="<?= $kucing['berat'] ?>" required>

            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4"><?= htmlspecialchars($kucing['deskripsi']) ?></textarea>

            <div style="margin:15px 0;">
                <label><input type="checkbox" name="sudah_steril" <?= $kucing['sudah_steril'] ? 'checked' : '' ?>> Sudah steril</label><br>
                <label><input type="checkbox" name="sudah_vaksin" <?= $kucing['sudah_vaksin'] ? 'checked' : '' ?>> Sudah vaksin</label>
            </div>

            <button type="submit" class="btn">Update Kucing</button>
        </form>
    </div>
</body>
</html>