<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan'])) {
    header("Location: login.php");
    exit;
}

if(!isset($_GET['ids'])) {
    header("Location: reservasi_pelanggan.php");
    exit;
}

$ids_string = $_GET['ids'];
$id_array = explode(',', $ids_string);

// Mengambil detail reservasi berdasarkan ID pertama sebagai acuan data
$id_reservasi_pertama = $id_array[0];

$stmt = $conn->prepare("SELECT r.*, l.nama_lapangan, l.jenis_lapangan, l.harga_per_jam 
                        FROM reservasi r 
                        JOIN lapangan l ON r.id_lapangan = l.id_lapangan 
                        WHERE r.id_reservasi = ? AND r.id_pelanggan = ?");
$stmt->execute([$id_reservasi_pertama, $_SESSION['id_pelanggan']]);
$reservasi = $stmt->fetch();

if(!$reservasi) {
    header("Location: reservasi_pelanggan.php");
    exit;
}

// Menghitung jumlah lapangan yang dipesan dalam satu sesi
$jumlah_lapangan = count($id_array);

// Menghitung durasi bermain (dalam jam) dan total biaya keseluruhan
$jam_mulai = strtotime($reservasi['jam_mulai']);
$jam_selesai = strtotime($reservasi['jam_selesai']);
$durasi_jam = ($jam_selesai - $jam_mulai) / 3600;
$durasi_tampil = round($durasi_jam, 2);
$total_harga = $durasi_jam * $reservasi['harga_per_jam'] * $jumlah_lapangan;

include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 mb-4">
        <div class="card shadow border-0">
            <div class="card-header bg-utama text-white text-center fw-bold py-3">
                <i class="bi bi-receipt"></i> INVOICE PEMBAYARAN
            </div>
            <div class="card-body p-4">
                <h5 class="mb-3 text-center fw-bold">Detail Reservasi</h5>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Layanan</span>
                        <span class="fw-bold"><?= $reservasi['jenis_lapangan'] ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Jumlah Lapangan</span>
                        <span class="fw-bold"><?= $jumlah_lapangan ?> Lapangan</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Tanggal</span>
                        <span class="fw-bold"><?= date('d M Y', strtotime($reservasi['tanggal'])) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Waktu</span>
                        <span class="fw-bold"><?= substr($reservasi['jam_mulai'],0,5) ?> - <?= substr($reservasi['jam_selesai'],0,5) ?> (<?= $durasi_tampil ?> Jam)</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                        <span class="fw-bold text-dark">TOTAL TAGIHAN</span>
                        <span class="fw-bold text-utama fs-5">Rp <?= number_format($total_harga, 0, ',', '.') ?></span>
                    </li>
                </ul>

                <form action="reservasi_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="bayar_invoice">
                    <input type="hidden" name="ids_reservasi" value="<?= htmlspecialchars($ids_string) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Pilih Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" onchange="toggleBuktiTransfer()" required>
                            <option value="">-- Pilih --</option>
                            <option value="cash" <?= $reservasi['metode_pembayaran'] == 'cash' ? 'selected' : '' ?>>Bayar Cash di Tempat</option>
                            <option value="transfer" <?= $reservasi['metode_pembayaran'] == 'transfer' ? 'selected' : '' ?>>Transfer Bank</option>
                        </select>
                    </div>

                    <div id="bukti_transfer_div" class="mb-4" style="display: <?= $reservasi['metode_pembayaran'] == 'transfer' ? 'block' : 'none' ?>;">
                        <div class="alert alert-info py-2">
                            <small><i class="bi bi-info-circle"></i> Silakan transfer ke <strong>BCA 1234567890 a.n SM Sport Center</strong> sejumlah <strong>Rp <?= number_format($total_harga, 0, ',', '.') ?></strong>.</small>
                        </div>
                        <label class="form-label text-muted">Unggah Bukti Transfer (Gambar)</label>
                        <input type="file" name="bukti_transfer" id="bukti_transfer_input" class="form-control" accept="image/*" <?= $reservasi['metode_pembayaran'] == 'transfer' ? 'required' : '' ?>>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-bold py-2"><i class="bi bi-check-circle"></i> Konfirmasi Pembayaran</button>
                    <a href="reservasi_process.php?batal_banyak=<?= htmlspecialchars($ids_string) ?>" onclick="return confirm('Yakin ingin membatalkan pesanan ini?')" class="btn btn-outline-secondary w-100 fw-bold py-2 mt-2">Batal & Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleBuktiTransfer() {
    var metode = document.getElementById('metode_pembayaran').value;
    var divBukti = document.getElementById('bukti_transfer_div');
    var inputBukti = document.getElementById('bukti_transfer_input');
    if (metode === 'transfer') {
        divBukti.style.display = 'block';
        inputBukti.required = true;
    } else {
        divBukti.style.display = 'none';
        inputBukti.required = false;
    }
}
</script>

<?php include 'footer.php'; ?>
