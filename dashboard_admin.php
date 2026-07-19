<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Hitung total reservasi pending
$stmtPending = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE status='pending'");
$pending = $stmtPending->fetch()['total'];

// Hitung total reservasi lunas hari ini
$stmtLunas = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE status='lunas' AND tanggal = CURDATE()");
$lunas_hari_ini = $stmtLunas->fetch()['total'];

include 'header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Dashboard Admin</h2>
        <p class="text-muted">Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?>.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card bg-warning text-dark shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-hourglass-split"></i> Reservasi Pending</h5>
                <h2 class="display-4 fw-bold"><?= $pending ?></h2>
                <p class="card-text">Menunggu konfirmasi pembayaran</p>
                <a href="reservasi_admin.php" class="btn btn-dark btn-sm">Lihat Detail &rarr;</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card bg-success text-white shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-calendar-check"></i> Reservasi Selesai (Hari Ini)</h5>
                <h2 class="display-4 fw-bold"><?= $lunas_hari_ini ?></h2>
                <p class="card-text">Pelanggan yang sudah lunas dan main hari ini</p>
                <a href="reservasi_admin.php" class="btn btn-light btn-sm text-success">Lihat Semua &rarr;</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
