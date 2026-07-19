<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: login.php");
    exit;
}

include 'header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Dashboard Pelanggan</h2>
        <p class="text-muted">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama']) ?>.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card bg-danger text-white shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-calendar-plus"></i> Buat Reservasi Baru</h5>
                <p class="card-text">Cek ketersediaan lapangan dan booking jadwal main kamu sekarang.</p>
                <a href="reservasi_pelanggan.php" class="btn btn-light text-danger fw-bold">Pesan Sekarang &rarr;</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card bg-white text-dark shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title text-danger"><i class="bi bi-clock-history"></i> Riwayat Reservasi</h5>
                <p class="card-text">Lihat status booking, upload bukti pembayaran, atau batalkan jadwal.</p>
                <a href="reservasi_pelanggan.php" class="btn btn-outline-danger fw-bold">Lihat Riwayat &rarr;</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
