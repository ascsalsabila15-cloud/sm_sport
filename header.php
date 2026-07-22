<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SM Sport Center - Reservasi Online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Nunito Sans', sans-serif; background-color: #f8f9fa; }
        .bg-utama { background-color: #276F27 !important; }
        .text-utama { color: #276F27 !important; }
        .header-sticky { box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .nav-link.active { font-weight: bold; border-bottom: 2px solid #276F27 !important; color: #276F27 !important; }
        .logo-img { height: 50px; max-width: 120px; object-fit: contain; }
        
        /* Penggantian Tombol */
        .btn-utama { background-color: #276F27 !important; border-color: #276F27 !important; color: white !important; }
        .btn-utama:hover, .btn-utama:focus, .btn-utama:active { background-color: #1e551e !important; border-color: #1e551e !important; color: white !important; }
        .btn-outline-utama { color: #276F27 !important; border-color: #276F27 !important; }
        .btn-outline-utama:hover, .btn-outline-utama:focus, .btn-outline-utama:active { background-color: #276F27 !important; color: white !important; }
        .border-utama { border-color: #276F27 !important; }
        /* Print Styles */
        @media print {
            body { background-color: white !important; }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .card { break-inside: avoid; border: 1px solid #ddd !important; }
            .container { max-width: 100% !important; padding: 0 !important; }
        }
    </style>
</head>
<body>
    <!-- Bar Teratas -->
    <header class="container-fluid p-0 d-print-none">
        <div class="bg-utama d-none d-sm-block" style="height: 40px;">
            <div class="container d-flex flex-nowrap justify-content-between align-items-center h-100 py-2">
                <ul class="navbar-nav flex-row flex-wrap">
                    <li class="nav-item col-6 col-md-auto mx-2 text-center text-white" style="font-size:14px;">
                        <i class="bi bi-clock"></i> Jam Operasional: 06:00 - 23:00
                    </li>
                    <li class="nav-item col-6 col-md-auto mx-2 text-center text-white" style="font-size:14px;">
                        <i class="bi bi-telephone"></i> 081210214016
                    </li>
                    <li class="nav-item col-6 col-md-auto mx-2 text-center text-white" style="font-size:14px;">
                        <i class="bi bi-geo-alt"></i> Jl. Cimpaeun Kota Depok
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Navigasi -->
    <div class="container-fluid p-0 sticky-top bg-white header-sticky d-print-none">
        <nav class="navbar navbar-expand-lg navbar-light container py-0" style="height: 70px;">
            <a class="navbar-brand m-0" href="index.php">
                <img src="images/logo.png" alt="sm-sport-logo" class="logo-img">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse ms-4" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-utama fw-bold text-uppercase" href="index.php">
                            <i class="bi bi-trophy"></i> Olahraga
                        </a>
                    </li>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-utama fw-bold" href="dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-utama fw-bold" href="reservasi.php">Reservasi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-utama fw-bold" href="lapangan.php">Lapangan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-utama fw-bold" href="pelanggan.php">Pelanggan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-utama fw-bold" href="laporan.php">Laporan</a>
                        </li>
                    <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'pelanggan'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-utama fw-bold" href="dashboard.php">Dashboard Saya</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-utama fw-bold" href="reservasi.php">Riwayat Booking</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center">
                    <?php if(isset($_SESSION['nama'])): ?>
                        <span class="me-3 fw-bold text-dark">Halo, <?= htmlspecialchars($_SESSION['nama']) ?></span>
                        <a href="login.php?action=logout" class="btn btn-sm btn-outline-utama shadow-none">Keluar</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-sm btn-outline-utama mx-2 shadow-none">Masuk</a>
                        <a href="register.php" class="btn btn-sm btn-utama shadow-none">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>
    
    <!-- Konten Utama -->
    <div class="container my-4 min-vh-100">
