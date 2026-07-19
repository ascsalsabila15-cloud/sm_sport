<?php
// Memanggil file koneksi untuk menyambung ke database
require 'koneksi.php';

// Mengecek apakah form pendaftaran sudah disubmit
if(isset($_POST['register'])) {
    // Menangkap semua inputan dari elemen form
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'pelanggan'; // Memaksa akun baru menjadi 'pelanggan' otomatis

    // VALIDASI: Mengecek apakah email ini sudah pernah didaftarkan sebelumnya
    $cek = $conn->prepare("SELECT * FROM pelanggan WHERE email = ?");
    $cek->execute([$email]);
    if($cek->rowCount() > 0) {
        // Jika rowCount > 0, berarti email ada. Kembalikan ke halaman register
        echo "<script>alert('Email sudah digunakan! Silakan gunakan email lain.'); window.location='register.php';</script>";
        exit; // Stop eksekusi program di sini agar tidak dilanjutkan
    }

    // MENYIMPAN DATA (INSERT): Memasukkan data baru ke tabel pelanggan
    $insert = $conn->prepare("INSERT INTO pelanggan (nama, email, no_hp, password, role) VALUES (?, ?, ?, ?, ?)");
    if($insert->execute([$nama, $email, $no_hp, $password, $role])) {
        // Jika query sukses, beri tahu user dan kembalikan ke form login
        echo "<script>alert('Pendaftaran berhasil! Silakan login dengan akun barumu.'); window.location='login.php';</script>";
    } else {
        // Jika query insert gagal karena suatu hal
        echo "<script>alert('Terjadi kesalahan, pendaftaran gagal!'); window.location='register.php';</script>";
    }
}
?>
