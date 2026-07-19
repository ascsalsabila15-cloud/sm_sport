<?php
// Mengecek apakah pengunjung sudah punya hak akses (sudah login) sebagai 'pelanggan'
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan']) || $_SESSION['role'] != 'pelanggan') {
    // Jika belum login atau bukan pelanggan, tendang paksa kembali ke halaman login (index)
    header("Location: login.php");
    exit;
}

// MENGAMBIL DATA REFERENSI: Ambil semua daftar lapangan dari database untuk pilihan di dalam Form (Select Dropdown)
$stmtLap = $conn->query("SELECT * FROM lapangan");
$lapangan = $stmtLap->fetchAll(); // fetchAll berfungsi untuk mengambil semua hasil baris ke dalam tipe Array

// MENGAMBIL DATA RIWAYAT: Ambil data riwayat reservasi yang HANYA milik si pelanggan yang sedang login
$id_pelanggan = $_SESSION['id_pelanggan'];

// Menggunakan perintah SQL JOIN untuk menggabungkan tabel Reservasi, Pelanggan, dan Lapangan agar namanya terbaca jelas
$stmtRes = $conn->prepare("SELECT r.*, p.nama, l.nama_lapangan 
                        FROM reservasi r 
                        JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                        JOIN lapangan l ON r.id_lapangan = l.id_lapangan 
                        WHERE r.id_pelanggan = ? 
                        ORDER BY r.tanggal DESC, r.jam_mulai ASC");
$stmtRes->execute([$id_pelanggan]);
$reservasi = $stmtRes->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Reservasi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="pelanggan-theme">
    <div class="navbar">
        <h2>SM Sport Center</h2>
        <a href="dashboard_pelanggan.php" style="color: white; text-decoration: none;">&larr; Kembali ke Dashboard</a>
    </div>

    <div class="content">
        <h3>Form Tambah Reservasi</h3>
        <!-- Awal dari form reservasi. Jika tombol simpan ditekan, data terkirim ke reservasi_process.php -->
        <form action="reservasi_process.php" method="POST" class="form-group" enctype="multipart/form-data">
            <div style="margin-bottom: 10px;">
                <label>Pilih Lapangan:</label>
                <select name="id_lapangan" required>
                    <option value="">-- Pilih Lapangan --</option>
                    <!-- Melakukan Looping (Perulangan PHP) untuk mencetak pilihan lapangan secara otomatis dari Database -->
                    <?php foreach($lapangan as $lap): ?>
                        <option value="<?= $lap['id_lapangan'] ?>"><?= $lap['nama_lapangan'] ?> (Rp <?= number_format($lap['harga_per_jam'],0,',','.') ?>/jam)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 10px;">
                <!-- Inputan Date untuk mengisi Tanggal -->
                <label>Tanggal Main:</label>
                <input type="date" name="tanggal" required>
            </div>
            
            <div style="margin-bottom: 10px;">
                <!-- Inputan Time untuk mengisi Jam -->
                <label>Jam Mulai (contoh: 18:00):</label>
                <input type="time" name="jam_mulai" required>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label>Jam Selesai (contoh: 20:00):</label>
                <input type="time" name="jam_selesai" required>
            </div>
            
            <div style="margin-bottom: 10px;">
                <label>Metode Pembayaran:</label>
                <select name="metode_pembayaran" id="metode_pembayaran" onchange="toggleBuktiTransfer()" required>
                    <option value="langsung">Bayar Secara Langsung</option>
                    <option value="transfer">Transfer Bank</option>
                </select>
            </div>
            
            <div id="bukti_transfer_div" style="margin-bottom: 15px; display: none;">
                <label>Unggah Bukti Transfer (Gambar):</label>
                <input type="file" name="bukti_transfer" accept="image/*">
                <small style="color: #666; display:block; margin-top:5px;">Harap transfer ke Rekening BCA: 1234567890 a.n SM Sport Center.</small>
            </div>
            
            <button type="submit" name="simpan" class="btn btn-primary">Simpan Jadwal</button>
        </form>

        <hr style="margin: 30px 0;">

        <h3>Riwayat Reservasi Saya</h3>
        <!-- Tabel HTML untuk menampilkan riwayat pemesanan -->
        <table class="table-data">
            <thead>
                <tr>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Logika Kondisi (IF): Mengecek apakah array reservasi kosong atau tidak -->
                <?php if(count($reservasi) == 0): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">Kamu belum pernah melakukan reservasi.</td>
                </tr>
                <?php endif; ?>
                
                <!-- Looping FOREACH: Mencetak isi array tabel data satu persatu menjadi baris <tr> -->
                <?php foreach($reservasi as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_lapangan']) ?></td>
                    <td><?= $row['tanggal'] ?></td>
                    <td><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?></td>
                    <td>
                        <?= ucfirst($row['metode_pembayaran'] ?? 'langsung') ?>
                        <?php if(($row['metode_pembayaran'] ?? '') == 'transfer' && !empty($row['bukti_transfer'])): ?>
                            <br><small><a href="uploads/<?= htmlspecialchars($row['bukti_transfer']) ?>" target="_blank" style="color:blue;">Lihat Bukti</a></small>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge"><?= htmlspecialchars($row['status']) ?></span></td>
                    <td>
                        <!-- Navigasi hyperlink hapus yang membawa Parameter Get (ID) ke reservasi_process.php -->
                        <a href="reservasi_process.php?hapus=<?= $row['id_reservasi'] ?>" onclick="return confirm('Yakin ingin membatalkan?')" class="btn btn-danger">Batal</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleBuktiTransfer() {
            var metode = document.getElementById('metode_pembayaran').value;
            var divBukti = document.getElementById('bukti_transfer_div');
            if (metode === 'transfer') {
                divBukti.style.display = 'block';
            } else {
                divBukti.style.display = 'none';
            }
        }
    </script>
</body>
</html>
