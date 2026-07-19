<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: login.php");
    exit;
}

$stmtLap = $conn->query("SELECT jenis_lapangan, MIN(harga_per_jam) as harga_per_jam, COUNT(*) as total_lapangan FROM lapangan GROUP BY jenis_lapangan");
$lapangan = $stmtLap->fetchAll();

$id_pelanggan = $_SESSION['id_pelanggan'];
// Untuk riwayat, kita masih bisa bergabung dengan `lapangan` jika `id_lapangan` tersimpan
$stmtRes = $conn->prepare("SELECT r.*, p.nama, l.nama_lapangan, l.harga_per_jam 
                        FROM reservasi r 
                        JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                        LEFT JOIN lapangan l ON r.id_lapangan = l.id_lapangan 
                        WHERE r.id_pelanggan = ? 
                        ORDER BY r.tanggal DESC, r.jam_mulai ASC");
$stmtRes->execute([$id_pelanggan]);
$reservasi = $stmtRes->fetchAll();

include 'header.php';
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white fw-bold">
                Form Tambah Reservasi
            </div>
            <div class="card-body">
                <form action="reservasi_process.php" method="POST">
                    <input type="hidden" name="action" value="booking_awal">
                    <div class="mb-3">
                        <label class="form-label text-muted">Pilih Lapangan</label>
                        <select name="jenis_lapangan" id="jenis_lapangan" class="form-select" required onchange="updateJumlah()">
                            <option value="">-- Pilih Lapangan --</option>
                            <?php foreach($lapangan as $lap): ?>
                                <option value="<?= htmlspecialchars($lap['jenis_lapangan']) ?>" data-max="<?= $lap['total_lapangan'] ?>">
                                    <?= htmlspecialchars($lap['jenis_lapangan']) ?> (Rp <?= number_format($lap['harga_per_jam'],0,',','.') ?>/jam)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Jumlah Lapangan</label>
                        <select name="jumlah_lapangan" id="jumlah_lapangan" class="form-select" required>
                            <option value="1">1 Lapangan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Tanggal Main</label>
                        <input type="date" name="tanggal" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col">
                            <label class="form-label text-muted">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label text-muted">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="simpan" class="btn btn-danger w-100 fw-bold">Pesan & Lanjut Pembayaran</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="alert alert-warning mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> <strong>Peringatan!</strong> Keterlambatan datang selama 15 menit jika tidak datang wajib menerima konsekuensi booking dibatalkan.
        </div>
        
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                Riwayat Reservasi Saya
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Lapangan</th>
                            <th>Jml</th>
                            <th>Tanggal & Waktu</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($reservasi) == 0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Kamu belum pernah melakukan reservasi.</td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php foreach($reservasi as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_lapangan']) ?></td>
                            <td><?= $row['jumlah_lapangan'] ?></td>
                            <td>
                                <?= date('d M Y', strtotime($row['tanggal'])) ?><br>
                                <small class="text-danger fw-bold"><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?></small>
                            </td>
                            <td>
                                <?= ucfirst($row['metode_pembayaran']) ?><br>
                                <?php if($row['metode_pembayaran'] == 'transfer' && !empty($row['bukti_transfer'])): ?>
                                    <a href="uploads/<?= htmlspecialchars($row['bukti_transfer']) ?>" target="_blank" class="badge bg-primary text-decoration-none">Lihat Bukti</a>
                                <?php elseif($row['metode_pembayaran'] == 'transfer'): ?>
                                    <span class="badge bg-secondary">Belum Upload</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['status'] == 'lunas'): ?>
                                    <span class="badge bg-success">Lunas</span>
                                <?php elseif($row['status'] == 'batal'): ?>
                                    <span class="badge bg-danger">Batal</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['status'] == 'pending'): ?>
                                    <a href="invoice.php?ids=<?= $row['id_reservasi'] ?>" class="btn btn-sm btn-info text-white mb-1"><i class="bi bi-receipt"></i> Bayar</a>
                                    <a href="reservasi_process.php?hapus=<?= $row['id_reservasi'] ?>" onclick="return confirm('Yakin ingin membatalkan?')" class="btn btn-sm btn-outline-danger mb-1"><i class="bi bi-x-circle"></i> Batal</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function updateJumlah() {
    var select = document.getElementById('jenis_lapangan');
    var max = select.options[select.selectedIndex].getAttribute('data-max');
    var jmlSelect = document.getElementById('jumlah_lapangan');
    
    jmlSelect.innerHTML = '';
    if(!max) {
        jmlSelect.innerHTML = '<option value="1">1 Lapangan</option>';
        return;
    }
    
    for(var i=1; i<=max; i++) {
        var opt = document.createElement('option');
        opt.value = i;
        opt.innerHTML = i + ' Lapangan';
        jmlSelect.appendChild(opt);
    }
}
</script>

<?php include 'footer.php'; ?>
