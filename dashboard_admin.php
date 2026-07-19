<?php
session_start();
if(!isset($_SESSION['id_pelanggan']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-theme">
    <div class="sidebar">
        <h2 style="text-align:center; color:white; margin-bottom:30px;">Admin Panel</h2>
        <a href="dashboard_admin.php" style="color:#adb5bd; display:block; margin-bottom:15px; font-weight:bold;">Dashboard</a>
        <a href="reservasi_admin.php" style="color:white; display:block; margin-bottom:15px;">Semua Reservasi</a>

        <a href="logout.php" class="btn btn-danger" style="display:block; margin-top:50px;">Logout</a>
    </div>
    <div class="main-content admin-main">
        <div class="navbar-admin">
            <h2>Selamat datang, Admin <?= htmlspecialchars($_SESSION['nama']) ?>!</h2>
            <!-- #belajar -->
        </div>
        <div class="content">
            <h3>Monitoring Sistem</h3>
            <p>Gunakan menu sidebar untuk mengontrol pemesanan lapangan futsal dan badminton.</p>
            <br>
            <a href="reservasi_admin.php" class="btn btn-primary">Lihat Daftar Reservasi Masuk</a>
        </div>
    </div>
</body>
</html>
