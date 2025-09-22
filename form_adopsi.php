<!-- <?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_kucing = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM kucing WHERE id_kucing = ? AND status = 'Tersedia'");
$stmt->execute([$id_kucing]);
$kucing = $stmt->fetch();

if (!$kucing) {
    die("<div class='alert alert-danger text-center'>Kucing tidak tersedia untuk adopsi.</div>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_SESSION['user_id'];
    $stmt = $pdo->prepare("INSERT INTO pengajuan_adopsi (id_user_pemohon, id_kucing) VALUES (?, ?)");
    $stmt->execute([$id_user, $id_kucing]);
    echo "
    <div class='alert alert-success text-center mt-5'>
        <i class='fas fa-check-circle fa-2x'></i><br>
        <strong>Pengajuan berhasil dikirim!</strong><br>
        Cek status di profil Anda.
    </div>";
    header("refresh:3;url=profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Ajukan Adopsi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="assets/css/style.css"/>
    <style>
        .adoption-form {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .cat-preview {
            text-align: center;
            margin-bottom: 20px;
        }
        .cat-preview img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #FF6B6B;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a href="index.php" class="navbar-brand">ADOPTIFY</a>
        </div>
    </nav>

    <div class="adoption-form">
        <div class="cat-preview">
            <img src="<?= $kucing['foto'] ?>" alt="<?= $kucing['nama_kucing'] ?>">
            <h4 class="mt-3">Ajukan Adopsi: <strong><?= $kucing['nama_kucing'] ?></strong></h4>
        </div>

        <form method="POST">
            <p class="text-center text-muted">
                Kami akan menghubungi Anda untuk proses selanjutnya. Pastikan data kontak Anda lengkap di profil.
            </p>
            <button type="submit" class="btn btn-primary w-100 btn-lg">
                <i class="fas fa-paper-plane"></i> Kirim Pengajuan
            </button>
            <a href="catalog.php" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
        </form>
    </div>
</body>
</html> -->