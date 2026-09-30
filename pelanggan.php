<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    $cek = $conn->prepare("SELECT email FROM pelanggan WHERE email = ?");
    $cek->execute([$email]);
    if($cek->rowCount() > 0) {
        echo "<script>alert('Gagal! Email sudah digunakan oleh pengguna lain.'); window.location='pelanggan.php';</script>";
        exit;
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO pelanggan (nama, email, no_hp, role, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nama, $email, $no_hp, $role, $hashed_password]);
    echo "<script>alert('Pengguna baru berhasil ditambahkan!'); window.location='pelanggan.php';</script>";
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = $_POST['id_'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $role = $_POST['role'];
    
    if(!empty($_POST['password'])) {
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE pelanggan SET nama=?, email=?, no_hp=?, role=?, password=? WHERE id_pelanggan=?");
        $stmt->execute([$nama, $email, $no_hp, $role, $hashed_password, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE pelanggan SET nama=?, email=?, no_hp=?, role=? WHERE id_pelanggan=?");
        $stmt->execute([$nama, $email, $no_hp, $role, $id]);
    }
    header("Location: pelanggan.php");
    exit;
}

if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    if($id != $_SESSION['id_pelanggan']) {
        try {
            $stmt = $conn->prepare("DELETE FROM pelanggan WHERE id_pelanggan=?");
            $stmt->execute([$id]);
            echo "<script>alert('Berhasil dihapus!'); window.location='pelanggan.php';</script>";
        } catch(PDOException $e) {
            echo "<script>alert('Gagal! Pelanggan ini tidak bisa dihapus karena sudah memiliki riwayat transaksi/reservasi.'); window.location='pelanggan.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal! Anda tidak bisa menghapus akun Anda sendiri saat sedang login.'); window.location='pelanggan.php';</script>";
    }
    exit;
}

$stmt = $conn->query("SELECT * FROM pelanggan ORDER BY role ASC, id_pelanggan DESC");
$pelanggan = $stmt->fetchAll();

include 'header.php';
?>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold"><i class="bi bi-people"></i> Kelola Pelanggan</h2>
        <button class="btn btn-utama" data-bs-toggle="modal" data-bs-target="#tambahModal"><i class="bi bi-plus-circle"></i> Tambah Pelanggan</button>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($pelanggan as $row): ?>
                <tr>
                    <td><?= $row['id_pelanggan'] ?></td>
                    <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                    <td>
                        <?php if($row['role'] == 'admin'): ?>
                            <span class="badge bg-utama">Admin</span>
                        <?php else: ?>
                            <span class="badge bg-success">Pelanggan</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($row['id_pelanggan'] != $_SESSION['id_pelanggan']): ?>
                            <button class="btn btn-sm btn-info text-white mb-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_pelanggan'] ?>"><i class="bi bi-pencil-square"></i> Edit</button>
                            <a href="pelanggan.php?hapus=<?= $row['id_pelanggan'] ?>" class="btn btn-sm btn-outline-utama mb-1" onclick="return confirm('Yakin ingin menghapus pengguna ini? Semua data reservasi pengguna ini akan ikut terhapus.')"><i class="bi bi-trash"></i> Hapus</a>
                        <?php else: ?>
                            <span class="text-muted fst-italic">Akun Anda</span>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $row['id_pelanggan'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="pelanggan.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Pelanggan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id_pelanggan" value="<?= $row['id_pelanggan'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Nama</label>
                                        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($row['nama']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($row['email']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">No HP</label>
                                        <input type="tel" name="no_hp" class="form-control" required maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="<?= htmlspecialchars($row['no_hp']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role" class="form-select" required>
                                            <option value="pelanggan" <?= $row['role'] == 'pelanggan' ? 'selected' : '' ?>>Pelanggan</option>
                                            <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password Baru (Kosongkan jika tidak ingin diubah)</label>
                                        <input type="password" name="password" class="form-control">
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
            <form action="pelanggan.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pelanggan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="tambah">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No HP</label>
                        <input type="tel" name="no_hp" class="form-control" required maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="pelanggan">Pelanggan</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
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
