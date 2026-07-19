<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$stmtRes = $conn->query("SELECT r.*, p.nama, p.no_hp, l.nama_lapangan 
                        FROM reservasi r 
                        JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                        JOIN lapangan l ON r.id_lapangan = l.id_lapangan 
                        ORDER BY r.tanggal DESC, r.jam_mulai ASC");
$reservasi = $stmtRes->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Semua Reservasi - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-theme">
    <div class="sidebar">
        <h2 style="text-align:center; color:white; margin-bottom:30px;">Admin Panel</h2>
        <a href="dashboard_admin.php" style="color:white; display:block; margin-bottom:15px;">Dashboard</a>
        <a href="reservasi_admin.php" style="color:#adb5bd; display:block; margin-bottom:15px; font-weight:bold;">Semua Reservasi</a>

        <a href="logout.php" class="btn btn-danger" style="display:block; margin-top:50px;">Logout</a>
    </div>
    <div class="main-content admin-main">
        <div class="navbar-admin">
            <h2>Data Seluruh Reservasi Masuk</h2>
        </div>
        <div class="content">
            <table class="table-data">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pemesan</th>
                        <th>No HP</th>
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($reservasi) == 0): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">Belum ada data reservasi</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach($reservasi as $row): ?>
                    <tr>
                        <td>#<?= $row['id_reservasi'] ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['no_hp']) ?></td>
                        <td><?= htmlspecialchars($row['nama_lapangan']) ?></td>
                        <td><?= $row['tanggal'] ?></td>
                        <td><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?></td>
                        <td>
                            <?= ucfirst($row['metode_pembayaran'] ?? 'langsung') ?>
                            <?php if(($row['metode_pembayaran'] ?? '') == 'transfer' && !empty($row['bukti_transfer'])): ?>
                                <br><small><a href="uploads/<?= htmlspecialchars($row['bukti_transfer']) ?>" target="_blank" style="color:blue;">Lihat Bukti</a></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="reservasi_process.php?hapus=<?= $row['id_reservasi'] ?>" onclick="return confirm('Hapus/Batalkan reservasi ini?')" class="btn btn-danger">Batalkan</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
