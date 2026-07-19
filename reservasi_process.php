<?php
// Wajib start session untuk mengakses data user yang sedang login (seperti Role dan ID)
session_start();
require 'koneksi.php';

// Fungsi bantuan untuk mengembalikan halaman ke dashboard yang sesuai
function redirect_back() {
    if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        header("Location: reservasi_admin.php");
    } else {
        header("Location: reservasi_pelanggan.php");
    }
    exit;
}

// MENYIMPAN RESERVASI BARU
if(isset($_POST['simpan'])) {
    // Ambil ID pemesan langsung dari session (bukan dari input form agar lebih aman & tidak bisa dipalsukan)
    $id_pelanggan = $_SESSION['id_pelanggan'];
    
    $id_lapangan  = $_POST['id_lapangan'];
    $tanggal      = $_POST['tanggal'];
    $jam_mulai    = $_POST['jam_mulai'];
    $jam_selesai  = $_POST['jam_selesai'];
    $metode_pembayaran = $_POST['metode_pembayaran'] ?? 'langsung';
    $bukti_transfer = null;

    if ($metode_pembayaran == 'transfer' && isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] == 0) {
        $upload_dir = 'uploads/';
        $filename = time() . '_' . basename($_FILES['bukti_transfer']['name']);
        if (move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $upload_dir . $filename)) {
            $bukti_transfer = $filename;
        }
    }

    // VALIDASI 1: Jam main harus masuk akal secara matematika (Selesai > Mulai)
    if($jam_selesai <= $jam_mulai) {
        echo "<script>alert('Waktu selesai harus lebih besar dari waktu mulai!'); window.history.back();</script>";
        exit;
    }

    // VALIDASI 2 (SANGAT PENTING): Cek apakah jadwal lapangan tabrakan
    // Kita mencari di database tabel reservasi, apakah di lapangan dan tanggal yang sama ada rentang jam yang bersinggungan
    $cek = $conn->prepare("SELECT * FROM reservasi 
                           WHERE id_lapangan = ? AND tanggal = ? 
                           AND (
                               (jam_mulai < ? AND jam_selesai > ?) OR
                               (jam_mulai < ? AND jam_selesai > ?) OR
                               (jam_mulai >= ? AND jam_selesai <= ?)
                           )");
    // Masukkan nilai parameter berurutan ke tanda tanya (?) di atas
    $cek->execute([$id_lapangan, $tanggal, $jam_selesai, $jam_mulai, $jam_selesai, $jam_mulai, $jam_mulai, $jam_selesai]);
    
    // Jika rowCount > 0, artinya lapangan sudah ada yang booking pada jam irisan tersebut
    if($cek->rowCount() > 0) {
        echo "<script>alert('Gagal! Jadwal bentrok dengan reservasi lain di jam tersebut.'); window.history.back();</script>";
    } else {
        // Jika jadwal kosong (aman), jalankan proses SIMPAN (INSERT) ke database
        $insert = $conn->prepare("INSERT INTO reservasi (id_pelanggan, id_lapangan, tanggal, jam_mulai, jam_selesai, metode_pembayaran, bukti_transfer) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$id_pelanggan, $id_lapangan, $tanggal, $jam_mulai, $jam_selesai, $metode_pembayaran, $bukti_transfer]);
        
        echo "<script>alert('Hore! Reservasi berhasil disimpan.');</script>";
        echo "<script>window.location='" . ($_SESSION['role'] == 'admin' ? "reservasi_admin.php" : "reservasi_pelanggan.php") . "';</script>";
    }
}

// MENGHAPUS RESERVASI
if(isset($_GET['hapus'])) {
    // Menangkap parameter 'hapus' dari URL (metode GET) yang berisi ID reservasi
    $id = $_GET['hapus'];
    // Eksekusi perintah penghapusan dari tabel
    $hapus = $conn->prepare("DELETE FROM reservasi WHERE id_reservasi = ?");
    $hapus->execute([$id]);
    
    // Kembali ke halaman sebelumnya
    redirect_back();
}
?>
