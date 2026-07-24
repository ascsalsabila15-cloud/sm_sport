<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Mengambil total pendapatan bulan ini (hanya yang statusnya lunas)
$stmtBulanIni = $conn->query("
    SELECT IFNULL(SUM(((TIME_TO_SEC(r.jam_selesai) - TIME_TO_SEC(r.jam_mulai))/3600) * l.harga_per_jam), 0) as total 
    FROM reservasi r 
    JOIN lapangan l ON r.id_lapangan = l.id_lapangan 
    WHERE r.status = 'lunas' 
    AND DATE_FORMAT(r.tanggal, '%Y-%m') = DATE_FORMAT(CURRENT_DATE(), '%Y-%m')
");
$pendapatan_bulan_ini = $stmtBulanIni->fetch()['total'];

// Mengambil total pendapatan bulan sebelumnya untuk perbandingan
$stmtBulanKemarin = $conn->query("
    SELECT IFNULL(SUM(((TIME_TO_SEC(r.jam_selesai) - TIME_TO_SEC(r.jam_mulai))/3600) * l.harga_per_jam), 0) as total 
    FROM reservasi r 
    JOIN lapangan l ON r.id_lapangan = l.id_lapangan 
    WHERE r.status = 'lunas' 
    AND DATE_FORMAT(r.tanggal, '%Y-%m') = DATE_FORMAT(CURRENT_DATE() - INTERVAL 1 MONTH, '%Y-%m')
");
$pendapatan_bulan_kemarin = $stmtBulanKemarin->fetch()['total'];

// Filter Tanggal
$where = "r.status = 'lunas'";
$params = [];

if(!empty($_GET['tanggal_awal'])) {
    $where .= " AND r.tanggal >= ?";
    $params[] = $_GET['tanggal_awal'];
}
if(!empty($_GET['tanggal_akhir'])) {
    $where .= " AND r.tanggal <= ?";
    $params[] = $_GET['tanggal_akhir'];
}

// Mengambil detail riwayat transaksi lunas untuk ditampilkan di tabel
$stmtTrans = $conn->prepare("
    SELECT r.id_reservasi, p.nama, l.nama_lapangan, r.tanggal, r.jam_mulai, r.jam_selesai, l.harga_per_jam, (((TIME_TO_SEC(r.jam_selesai) - TIME_TO_SEC(r.jam_mulai))/3600) * l.harga_per_jam) as total_bayar, r.waktu_booking
    FROM reservasi r
    JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan
    JOIN lapangan l ON r.id_lapangan = l.id_lapangan
    WHERE $where
    ORDER BY r.tanggal DESC, r.jam_mulai DESC
");
$stmtTrans->execute($params);
$transaksi = $stmtTrans->fetchAll();

$grand_total = 0;

include 'header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold"><i class="bi bi-graph-up-arrow"></i> Laporan Keuangan</h2>
        <p class="text-muted">Ringkasan pendapatan dari reservasi yang sudah lunas.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card bg-success text-white shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title">Pendapatan Bulan Ini</h5>
                <h2 class="display-5 fw-bold">Rp <?= number_format($pendapatan_bulan_ini, 0, ',', '.') ?></h2>
                <p class="card-text mb-0"><?= date('F Y') ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card bg-secondary text-white shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title">Pendapatan Bulan Kemarin</h5>
                <h2 class="display-5 fw-bold">Rp <?= number_format($pendapatan_bulan_kemarin, 0, ',', '.') ?></h2>
                <p class="card-text mb-0"><?= date('F Y', strtotime('-1 month')) ?></p>
            </div>
        </div>
    </div>
</div>

<form method="GET" action="laporan.php" class="row g-3 mb-4 d-print-none bg-white p-3 shadow-sm rounded border-0 mx-0">
    <div class="col-md-4">
        <label class="form-label fw-bold">Dari Tanggal</label>
        <input type="date" name="tanggal_awal" class="form-control" value="<?= htmlspecialchars($_GET['tanggal_awal'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold">Sampai Tanggal</label>
        <input type="date" name="tanggal_akhir" class="form-control" value="<?= htmlspecialchars($_GET['tanggal_akhir'] ?? '') ?>">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-utama me-2 w-50"><i class="bi bi-filter"></i> Filter</button>
        <a href="laporan.php" class="btn btn-outline-secondary w-50">Reset</a>
    </div>
</form>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
        <span>Riwayat Transaksi (Status Lunas)</span>
        <button class="btn btn-sm btn-outline-success d-print-none" onclick="window.print()"><i class="bi bi-printer"></i> Cetak Laporan</button>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Waktu Pembayaran</th>
                    <th>Pelanggan</th>
                    <th>Lapangan</th>
                    <th>Tanggal Main</th>
                    <th>Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($transaksi) == 0): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi lunas</td>
                </tr>
                <?php endif; ?>
                
                <?php 
                $no = 1;
                foreach($transaksi as $row): 
                    $grand_total += $row['total_bayar'];
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d M Y H:i', strtotime($row['waktu_booking'])) ?></td>
                    <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                    <td>
                        <?= htmlspecialchars($row['nama_lapangan']) ?><br>
                        <small class="text-muted">1 Lapangan</small>
                    </td>
                    <td>
                        <?= date('d M Y', strtotime($row['tanggal'])) ?><br>
                        <small class="text-utama"><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?></small>
                    </td>
                    <td class="fw-bold text-success">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="5" class="text-end fw-bold align-middle fs-5">TOTAL KESELURUHAN</td>
                    <td class="fw-bold text-success fs-4">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="row mt-5 d-none d-print-block">
    <div class="col-12 text-end pe-5" style="page-break-inside: avoid;">
        <p class="mb-5">Depok, <?= date('d F Y') ?><br>Mengetahui,</p>
        <p class="fw-bold mt-5 pt-4">Admin SM Sport</p>
    </div>
</div>

<?php include 'footer.php'; ?>
