<?php
session_start();
if(!isset($_SESSION['id_pelanggan'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - SM Sport Center</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h2>SM Sport Center</h2>
        <div>
            <span>Halo, <b><?= htmlspecialchars($_SESSION['nama']) ?></b> (<?= htmlspecialchars($_SESSION['role']) ?>)</span>
            <a href="logout.php" class="btn btn-danger" style="margin-left: 15px;">Logout</a>
        </div>
    </div>
    <div class="content">
        <h3>Selamat Datang di Sistem Reservasi</h3>
        <p>Gunakan menu di bawah ini untuk mengelola penyewaan lapangan futsal maupun badminton.</p>
        <br>
        <a href="reservasi.php" class="btn btn-primary">Kelola Reservasi</a>
    </div>
</body>
</html>
