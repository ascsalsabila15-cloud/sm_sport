<?php
session_start();
if(!isset($_SESSION['id_pelanggan']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Pelanggan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="pelanggan-theme">
    <div class="navbar">
        <div style="display: flex; align-items: center;">
            <img src="images/logo.png" alt="SM Sport Logo" class="logo-small">
            <h2 style="margin: 0; margin-left: 5px;">SM Sport Center</h2>
        </div>
        <div>
            <span>Halo, <b><?= htmlspecialchars($_SESSION['nama']) ?></b></span>
            <a href="logout.php" class="btn btn-danger" style="margin-left: 15px;">Logout</a>
        </div>
    </div>
    <div class="content">
        <div class="welcome-banner">
            <h3>Ingin main futsal atau badminton hari ini?</h3>
            <p>Pesan lapangan kamu sekarang juga sebelum kehabisan jadwal!</p>
            <br>
            <a href="reservasi_pelanggan.php" class="btn btn-primary">Pesan Lapangan Sekarang</a>
        </div>
    </div>
</body>
</html>
