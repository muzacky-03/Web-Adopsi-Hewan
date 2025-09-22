<!-- <?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard - Adoptify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="../../assets/css/style.css"/>
    <style>
        .admin-card {
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: 0.3s;
            text-align: center;
        }
        .admin-card:hover {
            transform: translateY(-10px);
        }
        .admin-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a href="../index.php" class="navbar-brand">ADOPTIFY</a>
            <span class="text-light">Admin Panel</span>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="text-center mb-5" style="color: var(--primary);">
            <i class="fas fa-tachometer-alt"></i> Admin Dashboard
        </h2>

        <div class="row g-4">
            <div class="col-md-6">
                <a href="kucing.php" class="text-decoration-none">
                    <div class="admin-card p-5 bg-white">
                        <div class="admin-icon text-primary">
                            <i class="fas fa-cat"></i>
                        </div>
                        <h5>Kelola Kucing</h5>
                        <p class="text-muted">Tambah, edit, hapus data kucing</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="pengajuan.php" class="text-decoration-none">
                    <div class="admin-card p-5 bg-white">
                        <div class="admin-icon text-success">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <h5>Kelola Pengajuan</h5>
                        <p class="text-muted">Verifikasi atau tolak adopsi</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="../logout.php" class="btn btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</body>
</html> -->