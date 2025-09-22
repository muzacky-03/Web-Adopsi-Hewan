<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
include '../../config/db.php';

// Pastikan hanya admin yang bisa akses (opsional: tambah pengecekan role)
// Hapus kucing
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = (int)$_GET['hapus']; // Pastikan ID adalah angka
    try {
        $stmt = $pdo->prepare("DELETE FROM kucing WHERE id_kucing = ?");
        $stmt->execute([$id]);
        // Redirect untuk hindari refresh = hapus ulang
        header("Location: kucing.php?msg=deleted");
        exit;
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// Tambah/Edit kucing
if ($_POST['aksi'] ?? '' == 'simpan') {
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $umur = $_POST['umur'];
    $kelamin = $_POST['kelamin'];
    $deskripsi = $_POST['deskripsi'];
    $foto = 'assets/images/cat-placeholder.jpg'; // Bisa diganti dengan upload
    $id_pemilik = $_SESSION['user_id'];

    if (!empty($_POST['id'])) {
        // Update
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("UPDATE kucing SET nama_kucing=?, jenis=?, umur=?, kelamin=?, deskripsi=? WHERE id_kucing=?");
        $stmt->execute([$nama, $jenis, $umur, $kelamin, $deskripsi, $id]);
        header("Location: kucing.php?msg=updated");
    } else {
        // Insert baru
        $stmt = $pdo->prepare("INSERT INTO kucing (nama_kucing, jenis, umur, kelamin, deskripsi, foto, id_pemilik) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $jenis, $umur, $kelamin, $deskripsi, $foto, $id_pemilik]);
        header("Location: kucing.php?msg=added");
    }
    exit;
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM kucing WHERE id_kucing = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if (!$edit_data) {
        header("Location: kucing.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin: Kelola Kucing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        body { background: #f8f9fa; }
        .btn-paw { display: inline-flex; align-items: center; gap: 6px; }
        .alert { border-radius: 10px; }
    </style>
</head>
<body>
    <div class="container my-5">
        <h2><i class="fas fa-cat"></i> Kelola Kucing</h2>
        <a href="dashboard.php" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">Data berhasil diperbarui!</div>
        <?php endif; ?>

        <!-- Form Tambah/Edit -->
        <form method="POST" class="mb-5 p-4 bg-white rounded shadow">
            <input type="hidden" name="aksi" value="simpan">
            <input type="hidden" name="id" value="<?= $edit_data['id_kucing'] ?? '' ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label><i class="fas fa-cat"></i> Nama Kucing</label>
                    <input type="text" name="nama" class="form-control" 
                           value="<?= htmlspecialchars($edit_data['nama_kucing'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label><i class="fas fa-paw"></i> Jenis</label>
                    <input type="text" name="jenis" class="form-control"
                           value="<?= htmlspecialchars($edit_data['jenis'] ?? '') ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label><i class="fas fa-birthday-cake"></i> Umur (tahun)</label>
                    <input type="number" name="umur" class="form-control"
                           value="<?= htmlspecialchars($edit_data['umur'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label><i class="fas fa-venus-mars"></i> Kelamin</label>
                    <select name="kelamin" class="form-control" required>
                        <option value="Jantan" <?= ($edit_data['kelamin'] ?? '') == 'Jantan' ? 'selected' : '' ?>>Jantan</option>
                        <option value="Betina" <?= ($edit_data['kelamin'] ?? '') == 'Betina' ? 'selected' : '' ?>>Betina</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label><i class="fas fa-comment"></i> Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($edit_data['deskripsi'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-paw">
                <i class="fas fa-save"></i> Simpan
            </button>
        </form>

        <!-- Daftar Kucing -->
        <h3><i class="fas fa-list"></i> Daftar Kucing</h3>
        <table class="table table-bordered table-hover bg-white">
            <tr class="table-light">
                <th>Nama</th>
                <th>Jenis</th>
                <th>Umur</th>
                <th>Kelamin</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php
            $stmt = $pdo->query("SELECT * FROM kucing ORDER BY tanggal_ditambahkan DESC");
            while ($k = $stmt->fetch()): ?>
                <tr>
                    <td><?= htmlspecialchars($k['nama_kucing']) ?></td>
                    <td><?= htmlspecialchars($k['jenis']) ?></td>
                    <td><?= htmlspecialchars($k['umur']) ?></td>
                    <td><?= htmlspecialchars($k['kelamin']) ?></td>
                    <td>
                        <span class="badge bg-success"><?= htmlspecialchars($k['status']) ?></span>
                    </td>
                    <td>
                        <a href="?edit=<?= $k['id_kucing'] ?>" class="btn btn-sm btn-warning btn-paw">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="?hapus=<?= $k['id_kucing'] ?>" 
                           class="btn btn-sm btn-danger btn-paw" 
                           onclick="return confirm('Yakin ingin menghapus kucing ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>