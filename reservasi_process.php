<?php
session_start();
require 'koneksi.php';

function redirect_back() {
    header("Location: reservasi.php");
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'booking_awal') {
    $id_pelanggan = $_SESSION['id_pelanggan'];
    $jenis_lapangan  = $_POST['jenis_lapangan'];
    $jumlah_lapangan = (int)$_POST['jumlah_lapangan'];
    $tanggal      = $_POST['tanggal'];
    $jam_mulai    = $_POST['jam_mulai'];
    $jam_selesai  = $_POST['jam_selesai'];

    if($jam_selesai <= $jam_mulai) {
        echo "<script>alert('Waktu selesai harus lebih besar dari waktu mulai!'); window.history.back();</script>";
        exit;
    }

    // validasi jam
    date_default_timezone_set('Asia/Jakarta');
    if ($tanggal == date('Y-m-d') && $jam_mulai < date('H:i')) {
        echo "<script>alert('Jam pesanan sudah lewat. Silakan pilih jam yang tersedia!'); window.history.back();</script>";
        exit;
    }

    $stmtLap = $conn->prepare("SELECT id_lapangan FROM lapangan WHERE jenis_lapangan = ?");
    $stmtLap->execute([$jenis_lapangan]);
    $all_lapangan = $stmtLap->fetchAll(PDO::FETCH_COLUMN);
    $total_lapangan_tersedia = count($all_lapangan);

    // cek bentrok
    $booked = [];
    foreach ($all_lapangan as $id_lap) {

        $cek = $conn->prepare("SELECT id_reservasi FROM reservasi 
                               WHERE id_lapangan = ? AND tanggal = ? AND status != 'batal'
                               AND (
                                   (jam_mulai < ? AND jam_selesai > ?) OR
                                   (jam_mulai < ? AND jam_selesai > ?) OR
                                   (jam_mulai >= ? AND jam_selesai <= ?)
                               )");
        $cek->execute([$id_lap, $tanggal, $jam_selesai, $jam_mulai, $jam_selesai, $jam_mulai, $jam_mulai, $jam_selesai]);
        if ($cek->rowCount() > 0) {
            $booked[] = $id_lap;
        }
    }

    $available = array_diff($all_lapangan, $booked);

    if (count($available) < $jumlah_lapangan) {
        echo "<script>alert('Gagal! Kapasitas lapangan penuh pada jam tersebut. Sisa tersedia: " . count($available) . " lapangan.'); window.history.back();</script>";
    } else {
        $available = array_values($available); 
        $inserted_ids = [];

        for ($i = 0; $i < $jumlah_lapangan; $i++) {
            $id_lap = $available[$i];
            $insert = $conn->prepare("INSERT INTO reservasi (id_pelanggan, id_lapangan, tanggal, jam_mulai, jam_selesai, metode_pembayaran, status) VALUES (?, ?, ?, ?, ?, 'cash', 'pending')");
            $insert->execute([$id_pelanggan, $id_lap, $tanggal, $jam_mulai, $jam_selesai]);
            $inserted_ids[] = $conn->lastInsertId();
        }

        $ids_string = implode(',', $inserted_ids);

        header("Location: invoice.php?ids=" . $ids_string);
        exit;
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'bayar_invoice') {
    $ids_string = $_POST['ids_reservasi'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $bukti_transfer = null;

    if (!preg_match('/^[0-9,]+$/', $ids_string)) {
        die("Invalid IDs");
    }

    if ($metode_pembayaran == 'transfer' && isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $file_name = $_FILES['bukti_transfer']['name'];
        $file_size = $_FILES['bukti_transfer']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validasi Ekstensi File
        if (!in_array($file_ext, $allowed_extensions)) {
            echo "<script>alert('Gagal! Ekstensi file tidak diizinkan. Harap upload gambar (JPG/PNG) atau PDF.'); window.history.back();</script>";
            exit;
        }

        // Validasi Ukuran File (Maksimal 2MB)
        if ($file_size > 2097152) { // 2 * 1024 * 1024 bytes
            echo "<script>alert('Gagal! Ukuran file terlalu besar. Maksimal 2MB.'); window.history.back();</script>";
            exit;
        }

        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . basename($file_name);
        if (move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $upload_dir . $filename)) {
            $bukti_transfer = $filename;
        }
    }

    $ids = explode(',', $ids_string);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $params = array_merge([$metode_pembayaran, $bukti_transfer], $ids, [$_SESSION['id_pelanggan']]);

    $update = $conn->prepare("UPDATE reservasi SET metode_pembayaran = ?, bukti_transfer = ? WHERE id_reservasi IN ($placeholders) AND id_pelanggan = ?");
    $update->execute($params);
    
    echo "<script>alert('Berhasil! Pembayaran berhasil diproses.');</script>";
    echo "<script>window.location='reservasi.php';</script>";
    exit;
}

if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $hapus = $conn->prepare("UPDATE reservasi SET status = 'batal' WHERE id_reservasi = ? AND id_pelanggan = ?");
    $hapus->execute([$id, $_SESSION['id_pelanggan']]);
    redirect_back();
}

if(isset($_GET['batal_banyak'])) {
    $ids_string = $_GET['batal_banyak'];
    if (preg_match('/^[0-9,]+$/', $ids_string)) {
        $ids = explode(',', $ids_string);
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $params = array_merge($ids, [$_SESSION['id_pelanggan']]);
        $hapus = $conn->prepare("UPDATE reservasi SET status = 'batal' WHERE id_reservasi IN ($placeholders) AND id_pelanggan = ?");
        $hapus->execute($params);
    }
    redirect_back();
}

if(isset($_GET['admin_batal'])) {
    if($_SESSION['role'] == 'admin') {
        $ids_string = $_GET['admin_batal'];
        if (preg_match('/^[0-9,]+$/', $ids_string)) {
            $ids = explode(',', $ids_string);
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $hapus = $conn->prepare("UPDATE reservasi SET status = 'batal' WHERE id_reservasi IN ($placeholders)");
            $hapus->execute($ids);
        }
        echo "<script>alert('Booking berhasil dibatalkan oleh Admin.'); window.location='reservasi.php';</script>";
    }
}

if(isset($_GET['admin_lunas'])) {
    if($_SESSION['role'] == 'admin') {
        $ids_string = $_GET['admin_lunas'];
        if (preg_match('/^[0-9,]+$/', $ids_string)) {
            $ids = explode(',', $ids_string);
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $lunas = $conn->prepare("UPDATE reservasi SET status = 'lunas' WHERE id_reservasi IN ($placeholders)");
            $lunas->execute($ids);
        }
        echo "<script>alert('Booking berhasil di-set Lunas.'); window.location='reservasi.php';</script>";
    }
}
?>
