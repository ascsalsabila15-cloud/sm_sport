<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan'])) {
    header("Location: login.php");
    exit;
}

// admin
if($_SESSION['role'] == 'admin') {
    
    $stmtPending = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE status='pending'");
    $pending = $stmtPending->fetch()['total'];

    $stmtLunas = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE status='lunas' AND tanggal = CURDATE()");
    $lunas_hari_ini = $stmtLunas->fetch()['total'];
}

include 'header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Dashboard <?= ucfirst($_SESSION['role']) ?></h2>
        <p class="text-muted">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama']) ?>.</p>
    </div>
</div>

<div class="row">
    <!-- tampilan admin -->
    <?php if($_SESSION['role'] == 'admin'): ?>
        <div class="col-md-6 mb-4">
            <div class="card bg-warning text-dark shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-hourglass-split"></i> Reservasi Pending</h5>
                    <h2 class="display-4 fw-bold"><?= $pending ?></h2>
                    <p class="card-text">Menunggu konfirmasi pembayaran</p>
                    <a href="reservasi.php" class="btn btn-dark btn-sm">Lihat Detail &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card bg-success text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-calendar-check"></i> Reservasi Selesai (Hari Ini)</h5>
                    <h2 class="display-4 fw-bold"><?= $lunas_hari_ini ?></h2>
                    <p class="card-text">Pelanggan yang sudah lunas dan main hari ini</p>
                    <a href="reservasi.php" class="btn btn-light btn-sm text-success">Lihat Semua &rarr;</a>
                </div>
            </div>
        </div>
    <!-- tampilan pelanggan -->
    <?php else: ?>
        <div class="col-md-6 mb-4">
            <div class="card bg-utama text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-calendar-plus"></i> Buat Reservasi Baru</h5>
                    <p class="card-text">Cek ketersediaan lapangan dan booking jadwal main kamu sekarang.</p>
                    <a href="reservasi.php" class="btn btn-light text-utama fw-bold">Pesan Sekarang &rarr;</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card bg-white text-dark shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title text-utama"><i class="bi bi-clock-history"></i> Riwayat Reservasi</h5>
                    <p class="card-text">Lihat status booking, upload bukti pembayaran, atau batalkan jadwal.</p>
                    <a href="reservasi.php" class="btn btn-outline-utama fw-bold">Lihat Riwayat &rarr;</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
