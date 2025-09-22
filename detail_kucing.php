<!-- <?php
include 'config/db.php';
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM kucing WHERE id_kucing = ?");
$stmt->execute([$id]);
$kucing = $stmt->fetch();

if (!$kucing) {
    die("Kucing tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $kucing['nama_kucing'] ?> - Adoptify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="assets/css/style.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a href="index.php" class="navbar-brand">ADOPTIFY</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row g-5 align-items-center">
            <div class="col-md-6">
                <img src="<?= $kucing['foto'] ?>" class="img-fluid rounded shadow" alt="<?= $kucing['nama_kucing'] ?>" style="width: 100%; height: 400px; object-fit: cover;">
            </div>
            <div class="col-md-6">
                <h1 class="mb-3" style="color: var(--primary);"><?= $kucing['nama_kucing'] ?></h1>
                <ul class="list-unstyled">
                    <li><strong><i class="fas fa-tag"></i> Jenis:</strong> <?= $kucing['jenis'] ?></li>
                    <li><strong><i class="fas fa-birthday-cake"></i> Umur:</strong> <?= $kucing['umur'] ?> tahun</li>
                    <li><strong><i class="fas fa-venus-mars"></i> Kelamin:</strong> <?= $kucing['kelamin'] ?></li>
                    <li><strong><i class="fas fa-heart"></i> Status:</strong> 
                        <span class="badge bg-success"><?= $kucing['status'] ?></span>
                    </li>
                </ul>
                <div class="border-start border-4 border-primary ps-3 mb-4">
                    <p><em>"<?= $kucing['deskripsi'] ?>"</em></p>
                </div>

                <?php if ($kucing['status'] == 'Tersedia'): ?>
                    <a href="form_adopsi.php?id=<?= $kucing['id_kucing'] ?>" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-hand-holding-heart"></i> AJUKAN ADOPSI
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary w-100" disabled>
                        <i class="fas fa-check-circle"></i> Sudah Diadopsi
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> -->