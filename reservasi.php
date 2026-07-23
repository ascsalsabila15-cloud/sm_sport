<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['id_pelanggan'])) {
    header("Location: login.php");
    exit;
}

// admin
if($_SESSION['role'] == 'admin') {
    $where = [];
    $params = [];

    if(!empty($_GET['search'])) {
        $where[] = "p.nama LIKE ?";
        $params[] = "%" . $_GET['search'] . "%";
    }
    if(!empty($_GET['lapangan'])) {
        $where[] = "l.jenis_lapangan = ?";
        $params[] = $_GET['lapangan'];
    }
    if(!empty($_GET['bulan'])) {
        $where[] = "MONTH(r.tanggal) = ?";
        $params[] = $_GET['bulan'];
    }
    if(!empty($_GET['tanggal'])) {
        $where[] = "r.tanggal = ?";
        $params[] = $_GET['tanggal'];
    }

    $whereClause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

    $stmtRes = $conn->prepare("SELECT 
                                GROUP_CONCAT(r.id_reservasi) as id_reservasi_group,
                                l.jenis_lapangan as nama_lapangan, 
                                SUM(r.jumlah_lapangan) as jumlah_lapangan,
                                r.tanggal, 
                                r.jam_mulai, 
                                r.jam_selesai, 
                                r.metode_pembayaran, 
                                MAX(r.bukti_transfer) as bukti_transfer, 
                                r.status,
                                p.nama,
                                p.no_hp
                            FROM reservasi r 
                            JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                            JOIN lapangan l ON r.id_lapangan = l.id_lapangan 
                            $whereClause
                            GROUP BY 
                                l.jenis_lapangan, 
                                r.tanggal, 
                                r.jam_mulai, 
                                r.jam_selesai, 
                                r.metode_pembayaran, 
                                r.status,
                                p.nama,
                                p.no_hp
                            ORDER BY r.tanggal DESC, r.jam_mulai ASC");
    $stmtRes->execute($params);
    $reservasi_admin = $stmtRes->fetchAll();

    $stmtLap = $conn->query("SELECT DISTINCT jenis_lapangan FROM lapangan");
    $lapangan_list = $stmtLap->fetchAll();
} 

// pelanggan
else {
    $stmtLap = $conn->query("SELECT jenis_lapangan, MIN(harga_per_jam) as harga_per_jam, COUNT(*) as total_lapangan FROM lapangan GROUP BY jenis_lapangan");
    $lapangan = $stmtLap->fetchAll();

    $id_pelanggan = $_SESSION['id_pelanggan'];
    $stmtRes = $conn->prepare("SELECT 
                                GROUP_CONCAT(r.id_reservasi) as id_reservasi_group,
                                l.jenis_lapangan as nama_lapangan, 
                                SUM(r.jumlah_lapangan) as jumlah_lapangan,
                                r.tanggal, 
                                r.jam_mulai, 
                                r.jam_selesai, 
                                r.metode_pembayaran, 
                                MAX(r.bukti_transfer) as bukti_transfer, 
                                r.status
                            FROM reservasi r 
                            JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                            LEFT JOIN lapangan l ON r.id_lapangan = l.id_lapangan 
                            WHERE r.id_pelanggan = ? 
                            GROUP BY 
                                l.jenis_lapangan, 
                                r.tanggal, 
                                r.jam_mulai, 
                                r.jam_selesai, 
                                r.metode_pembayaran, 
                                r.status
                            ORDER BY r.tanggal DESC, r.jam_mulai ASC");
    $stmtRes->execute([$id_pelanggan]);
    $reservasi_pelanggan = $stmtRes->fetchAll();
}

include 'header.php';
?>

<!-- tampilan admin -->
<?php if($_SESSION['role'] == 'admin'): ?>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-light">
            <form method="GET" action="reservasi.php" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Cari Nama</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama Pelanggan" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter Lapangan</label>
                    <select name="lapangan" class="form-select">
                        <option value="">Semua Lapangan</option>
                        <?php foreach($lapangan_list as $lap): ?>
                            <option value="<?= htmlspecialchars($lap['jenis_lapangan']) ?>" <?= (isset($_GET['lapangan']) && $_GET['lapangan'] == $lap['jenis_lapangan']) ? 'selected' : '' ?>><?= htmlspecialchars($lap['jenis_lapangan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        <?php for($i=1; $i<=12; $i++): ?>
                            <option value="<?= $i ?>" <?= (isset($_GET['bulan']) && $_GET['bulan'] == $i) ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$i,10)) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Spesifik</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($_GET['tanggal'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-utama w-100"><i class="bi bi-search"></i> Cari</button>
                </div>
            </form>
        </div>
    </div>

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
                    <?php if(count($reservasi_admin) == 0): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data reservasi</td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach($reservasi_admin as $row): 
                        $waktu_main = strtotime($row['tanggal'] . ' ' . $row['jam_mulai']);
                        $sekarang = time();
                        $telat_30_menit = ($sekarang > ($waktu_main + 1800)); // 1800 detik = 30 menit
                    ?>
                    <tr>
                        <td>#<?= $row['id_reservasi_group'] ?></td>
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
                            <small class="text-utama fw-bold"><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?></small>
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
                                <span class="badge bg-utama">Batal</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'pending'): ?>
                                <a href="reservasi_process.php?admin_lunas=<?= $row['id_reservasi_group'] ?>" class="btn btn-sm btn-success mb-1 w-100" onclick="return confirm('Konfirmasi Lunas?')"><i class="bi bi-check-lg"></i> Lunas</a>
                                
                                <?php if($row['metode_pembayaran'] == 'cash' && $telat_30_menit): ?>
                                    <a href="reservasi_process.php?admin_batal=<?= $row['id_reservasi_group'] ?>" class="btn btn-sm btn-utama mb-1 w-100" onclick="return confirm('Pelanggan telat 30 menit. Yakin ingin membatalkan booking ini?')"><i class="bi bi-x-circle"></i> Batal (No-Show)</a>
                                <?php else: ?>
                                    <a href="reservasi_process.php?admin_batal=<?= $row['id_reservasi_group'] ?>" class="btn btn-sm btn-outline-utama mb-1 w-100" onclick="return confirm('Yakin ingin membatalkan reservasi ini?')"><i class="bi bi-trash"></i> Batalkan</a>
                                <?php endif; ?>
                            <?php elseif($row['status'] == 'lunas'): 
                                $wa_number = preg_replace('/[^0-9]/', '', $row['no_hp']);
                                if(substr($wa_number, 0, 1) == '0') $wa_number = '62' . substr($wa_number, 1);
                                $wa_msg = urlencode("Halo " . $row['nama'] . ", reservasi Anda di SM Sport Center untuk " . $row['nama_lapangan'] . " pada " . date('d M Y', strtotime($row['tanggal'])) . " jam " . substr($row['jam_mulai'],0,5) . " sudah dikonfirmasi LUNAS. Harap datang tepat waktu ya. Terima kasih!");
                            ?>
                                <a href="https://wa.me/<?= $wa_number ?>?text=<?= $wa_msg ?>" target="_blank" class="btn btn-sm btn-success w-100"><i class="bi bi-whatsapp"></i> Konfirmasi Lunas</a>
                            
                            <?php elseif($row['status'] == 'batal'): 
                                $wa_number = preg_replace('/[^0-9]/', '', $row['no_hp']);
                                if(substr($wa_number, 0, 1) == '0') $wa_number = '62' . substr($wa_number, 1);
                                $wa_msg = urlencode("Mohon maaf " . $row['nama'] . ", reservasi Anda di SM Sport Center untuk " . $row['nama_lapangan'] . " pada " . date('d M Y', strtotime($row['tanggal'])) . " jam " . substr($row['jam_mulai'],0,5) . " telah DIBATALKAN (karena telat / permintaan). Silakan lakukan reservasi ulang jika berkenan. Terima kasih.");
                            ?>
                                <a href="https://wa.me/<?= $wa_number ?>?text=<?= $wa_msg ?>" target="_blank" class="btn btn-sm btn-danger w-100"><i class="bi bi-whatsapp"></i> Konfirmasi Batal</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<!-- tampilan pelanggan -->
<?php else: ?>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-utama text-white fw-bold">
                    Form Tambah Reservasi
                </div>
                <div class="card-body">
                    <form action="reservasi_process.php" method="POST">
                        <input type="hidden" name="action" value="booking_awal">
                        <div class="mb-3">
                            <label class="form-label text-muted">Pilih Lapangan</label>
                            <select name="jenis_lapangan" id="jenis_lapangan" class="form-select" required onchange="updateJumlah(); cekJadwal();">
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
                            <input type="date" name="tanggal" id="tanggal" class="form-control" required min="<?= date('Y-m-d') ?>" onchange="cekJadwal()">
                        </div>
                        
                        <div id="info_jadwal" class="mb-3" style="display:none;">
                            <label class="form-label text-muted"><i class="bi bi-info-circle"></i> Info Jadwal Terisi:</label>
                            <ul id="list_jadwal" class="list-group list-group-sm text-utama" style="font-size:0.9rem;">
                            </ul>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col">
                                <label class="form-label text-muted">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control" required min="06:00" max="23:00" step="3600">
                            </div>
                            <div class="col">
                                <label class="form-label text-muted">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control" required min="06:00" max="23:00" step="3600">
                            </div>
                        </div>
                        
                        <button type="submit" name="simpan" class="btn btn-utama w-100 fw-bold">Pesan & Lanjut Pembayaran</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="alert alert-warning mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <strong>Peringatan!</strong> Untuk Pembayaran Cash : Keterlambatan datang maximal 15 menit dari jadwal, jika tidak datang wajib menerima konsekuensi booking dibatalkan oleh admin.
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
                            <?php if(count($reservasi_pelanggan) == 0): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Kamu belum pernah melakukan reservasi.</td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php foreach($reservasi_pelanggan as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_lapangan']) ?></td>
                                <td><?= $row['jumlah_lapangan'] ?></td>
                                <td>
                                    <?= date('d M Y', strtotime($row['tanggal'])) ?><br>
                                    <small class="text-utama fw-bold"><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?></small>
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
                                        <span class="badge bg-utama">Batal</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['status'] == 'pending'): ?>
                                        <a href="invoice.php?ids=<?= $row['id_reservasi_group'] ?>" class="btn btn-sm btn-info text-white mb-1"><i class="bi bi-receipt"></i> Bayar</a>
                                        <a href="reservasi_process.php?batal_banyak=<?= $row['id_reservasi_group'] ?>" onclick="return confirm('Yakin ingin membatalkan?')" class="btn btn-sm btn-outline-utama mb-1"><i class="bi bi-x-circle"></i> Batal</a>
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

    function cekJadwal() {
        var tanggal = document.getElementById('tanggal').value;
        var jenis_lapangan = document.getElementById('jenis_lapangan').value;
        
        if(tanggal && jenis_lapangan) {
            fetch('booking_jam.php?tanggal=' + tanggal + '&jenis_lapangan=' + jenis_lapangan)
                .then(response => response.json())
                .then(data => {
                    var infoDiv = document.getElementById('info_jadwal');
                    var listUl = document.getElementById('list_jadwal');
                    listUl.innerHTML = '';
                    
                    if(data.booked && data.booked.length > 0) {
                        infoDiv.style.display = 'block';
                        data.booked.forEach(function(item) {
                            var li = document.createElement('li');
                            li.className = 'list-group-item py-1';
                            li.innerHTML = `<strong>${item.jam_mulai} - ${item.jam_selesai}</strong> (Terisi ${item.terisi} dari ${data.total_lapangan} Lapangan)`;
                            listUl.appendChild(li);
                        });
                    } else {
                        infoDiv.style.display = 'block';
                        var li = document.createElement('li');
                        li.className = 'list-group-item py-1 text-success';
                        li.innerHTML = '<i class="bi bi-check-circle"></i> Semua jam masih kosong (tersedia).';
                        listUl.appendChild(li);
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        } else {
            document.getElementById('info_jadwal').style.display = 'none';
        }
    }
    </script>
<?php endif; ?>

<?php include 'footer.php'; ?>
