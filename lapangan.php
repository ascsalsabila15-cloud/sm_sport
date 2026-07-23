<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Memproses Form CRUD (Create, Update, Delete)
if(isset($_POST['action'])) {
    if($_POST['action'] == 'tambah') {
        $nama = $_POST['nama_lapangan'];
        $jenis = $_POST['jenis_lapangan'];
        $harga = (int)$_POST['harga_per_jam'];
        $stmt = $conn->prepare("INSERT INTO lapangan (nama_lapangan, jenis_lapangan, harga_per_jam) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $jenis, $harga]);
        header("Location: lapangan.php");
        exit;
    }
    
    if($_POST['action'] == 'edit') {
        $id = $_POST['id_lapangan'];
        $nama = $_POST['nama_lapangan'];
        $jenis = $_POST['jenis_lapangan'];
        $harga = (int)$_POST['harga_per_jam'];
        $stmt = $conn->prepare("UPDATE lapangan SET nama_lapangan=?, jenis_lapangan=?, harga_per_jam=? WHERE id_lapangan=?");
        $stmt->execute([$nama, $jenis, $harga, $id]);
        header("Location: lapangan.php");
        exit;
    }
}

if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    try {
        $stmt = $conn->prepare("DELETE FROM lapangan WHERE id_lapangan=?");
        $stmt->execute([$id]);
        header("Location: lapangan.php");
        exit;
    } catch (PDOException $e) {
        // Cek apakah error karena constraint relasi (Code 23000)
        if ($e->getCode() == '23000') {
            echo "<script>alert('Gagal! Lapangan tidak bisa dihapus karena sudah memiliki riwayat pemesanan/reservasi.'); window.location='lapangan.php';</script>";
            exit;
        } else {
            echo "<script>alert('Terjadi kesalahan database: " . $e->getMessage() . "'); window.location='lapangan.php';</script>";
            exit;
        }
    }
}

// Mengambil seluruh data lapangan untuk ditampilkan
$stmt = $conn->query("SELECT * FROM lapangan ORDER BY id_lapangan DESC");
$lapangan = $stmt->fetchAll();

include 'header.php';
?>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold"><i class="bi bi-grid"></i> Kelola Lapangan</h2>
        <button class="btn btn-utama" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-circle"></i> Tambah Lapangan
        </button>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nama Lapangan</th>
                    <th>Jenis Lapangan</th>
                    <th>Harga Per Jam</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($lapangan) == 0): ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data lapangan</td></tr>
                <?php endif; ?>
                
                <?php foreach($lapangan as $row): ?>
                <tr>
                    <td><?= $row['id_lapangan'] ?></td>
                    <td><strong><?= htmlspecialchars($row['nama_lapangan']) ?></strong></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['jenis_lapangan']) ?></span></td>
                    <td>Rp <?= number_format($row['harga_per_jam'], 0, ',', '.') ?></td>
                    <td>
                        <button class="btn btn-sm btn-info text-white mb-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_lapangan'] ?>"><i class="bi bi-pencil-square"></i> Edit</button>
                        <a href="lapangan.php?hapus=<?= $row['id_lapangan'] ?>" class="btn btn-sm btn-outline-utama mb-1" onclick="return confirm('Yakin ingin menghapus lapangan ini?')"><i class="bi bi-trash"></i> Hapus</a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $row['id_lapangan'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="lapangan.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Lapangan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id_lapangan" value="<?= $row['id_lapangan'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Lapangan</label>
                                        <input type="text" name="nama_lapangan" class="form-control" required value="<?= htmlspecialchars($row['nama_lapangan']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jenis Lapangan</label>
                                        <input type="text" name="jenis_lapangan" class="form-control" required value="<?= htmlspecialchars($row['jenis_lapangan']) ?>" placeholder="Misal: Futsal, Badminton">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Harga Per Jam</label>
                                        <input type="number" name="harga_per_jam" class="form-control" required value="<?= $row['harga_per_jam'] ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-utama">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="lapangan.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Lapangan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="tambah">
                    <div class="mb-3">
                        <label class="form-label">Nama Lapangan</label>
                        <input type="text" name="nama_lapangan" class="form-control" required placeholder="Misal: Lapangan 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Lapangan</label>
                        <input type="text" name="jenis_lapangan" class="form-control" required placeholder="Misal: Futsal, Badminton">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Per Jam</label>
                        <input type="number" name="harga_per_jam" class="form-control" required placeholder="Misal: 100000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-utama">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
