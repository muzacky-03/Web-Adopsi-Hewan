<?php
session_start();
include 'config.php';

$id = $_GET['id'];

// Ambil data kucing
$stmt = $pdo->prepare("SELECT * FROM kucing WHERE id_kucing = ?");
$stmt->execute([$id]);
$kucing = $stmt->fetch();

if (!$kucing) {
    die("Kucing tidak ditemukan.");
}

// Cek izin
$canDelete = false;
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $canDelete = true;
} elseif (isset($_SESSION['id_user']) && $_SESSION['id_user'] == $kucing['id_pemilik']) {
    $canDelete = true;
}

if (!$canDelete) {
    die("Akses ditolak.");
}

// Hapus
$pdo->prepare("DELETE FROM kucing WHERE id_kucing = ?")->execute([$id]);

// Redirect
header("Location: catalog.php");
exit;
?>