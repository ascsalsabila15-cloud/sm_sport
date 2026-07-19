<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan'])) {
    header("Location: index.php");
    exit;
}

// Ambil data lapangan untuk form tambah
$stmtLap = $conn->query("SELECT * FROM lapangan");
$lapangan = $stmtLap->fetchAll();

// Ambil riwayat reservasi
if($_SESSION['role'] == 'admin') {
    $stmtRes = $conn->query("SELECT r.*, p.nama, l.nama_lapangan 
                            FROM reservasi r 
                            JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                            JOIN lapangan l ON r.id_lapangan = l.id_lapangan 
                            ORDER BY r.tanggal DESC, r.jam_mulai ASC");
} else {
    $id_pelanggan = $_SESSION['id_pelanggan'];
    $stmtRes = $conn->prepare("SELECT r.*, p.nama, l.nama_lapangan 
                            FROM reservasi r 
                            JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                            JOIN lapangan l ON r.id_lapangan = l.id_lapangan 
                            WHERE r.id_pelanggan = ? 
                            ORDER BY r.tanggal DESC, r.jam_mulai ASC");
    $stmtRes->execute([$id_pelanggan]);
}
$reservasi = $stmtRes->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Reservasi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h2>SM Sport Center</h2>
        <a href="dashboard.php" style="color: white; text-decoration: none;">&larr; Kembali ke Dashboard</a>
    </div>

    <div class="content">
        <h3>Form Tambah Reservasi #belajar</h3>
        <form action="reservasi_process.php" method="POST" class="form-group">
            <div style="margin-bottom: 10px;">
                <label>Pilih Lapangan:</label>
                <select name="id_lapangan" required>
                    <option value="">-- Pilih Lapangan --</option>
                    <?php foreach($lapangan as $lap): ?>
                        <option value="<?= $lap['id_lapangan'] ?>"><?= $lap['nama_lapangan'] ?> (Rp <?= number_format($lap['harga_per_jam'],0,',','.') ?>/jam)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 10px;">
                <label>Tanggal Main:</label>
                <input type="date" name="tanggal" required>
            </div>
            
            <div style="margin-bottom: 10px;">
                <label>Jam Mulai (contoh: 18:00):</label>
                <input type="time" name="jam_mulai" required>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label>Jam Selesai (contoh: 20:00):</label>
                <input type="time" name="jam_selesai" required>
            </div>
            
            <button type="submit" name="simpan" class="btn btn-primary">Simpan Jadwal</button>
        </form>

        <hr style="margin: 30px 0;">

        <h3>Daftar Reservasi Anda</h3>
        <table class="table-data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pemesan</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($reservasi) == 0): ?>
                <tr>
                    <td colspan="7" style="text-align:center;">Belum ada data reservasi</td>
                </tr>
                <?php endif; ?>
                
                <?php $no=1; foreach($reservasi as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['nama_lapangan']) ?></td>
                    <td><?= $row['tanggal'] ?></td>
                    <td><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?></td>
                    <td><span class="badge"><?= htmlspecialchars($row['status']) ?></span></td>
                    <td>
                        <a href="reservasi_process.php?hapus=<?= $row['id_reservasi'] ?>" onclick="return confirm('Apakah kamu yakin mau menghapus data ini?')" class="btn btn-danger">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
