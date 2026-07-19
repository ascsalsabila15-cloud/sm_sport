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

include 'header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center py-3">
        <span>Kelola Seluruh Reservasi Masuk</span>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Pemesan</th>
                    <th>Lapangan</th>
                    <th>Waktu Main</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi Admin</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($reservasi) == 0): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data reservasi</td>
                </tr>
                <?php endif; ?>
                
                <?php foreach($reservasi as $row): 
                    $waktu_main = strtotime($row['tanggal'] . ' ' . $row['jam_mulai']);
                    $sekarang = time();
                    $telat_30_menit = ($sekarang > ($waktu_main + 1800)); // 1800 detik = 30 menit
                ?>
                <tr>
                    <td>#<?= $row['id_reservasi'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($row['nama']) ?></strong><br>
                        <small class="text-muted"><i class="bi bi-whatsapp"></i> <?= htmlspecialchars($row['no_hp']) ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($row['nama_lapangan']) ?><br>
                        <small class="badge bg-secondary"><?= $row['jumlah_lapangan'] ?> Lapangan</small>
                    </td>
                    <td>
                        <?= date('d M Y', strtotime($row['tanggal'])) ?><br>
                        <small class="text-danger fw-bold"><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?></small>
                    </td>
                    <td>
                        <?= ucfirst($row['metode_pembayaran']) ?><br>
                        <?php if($row['metode_pembayaran'] == 'transfer' && !empty($row['bukti_transfer'])): ?>
                            <a href="uploads/<?= htmlspecialchars($row['bukti_transfer']) ?>" target="_blank" class="badge bg-primary text-decoration-none"><i class="bi bi-image"></i> Lihat Bukti</a>
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
                            <a href="reservasi_process.php?admin_lunas=<?= $row['id_reservasi'] ?>" class="btn btn-sm btn-success mb-1 w-100" onclick="return confirm('Konfirmasi Lunas?')"><i class="bi bi-check-lg"></i> Lunas</a>
                            
                            <?php if($row['metode_pembayaran'] == 'cash' && $telat_30_menit): ?>
                                <a href="reservasi_process.php?admin_batal=<?= $row['id_reservasi'] ?>" class="btn btn-sm btn-danger mb-1 w-100" onclick="return confirm('Pelanggan telat 30 menit. Yakin ingin membatalkan booking ini?')"><i class="bi bi-x-circle"></i> Batal (No-Show)</a>
                            <?php else: ?>
                                <a href="reservasi_process.php?admin_batal=<?= $row['id_reservasi'] ?>" class="btn btn-sm btn-outline-danger mb-1 w-100" onclick="return confirm('Yakin ingin membatalkan reservasi ini?')"><i class="bi bi-trash"></i> Batalkan</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
