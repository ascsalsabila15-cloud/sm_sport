<?php
// Menyambung ke database
require 'koneksi.php';

// Pendaftaran
if(isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'pelanggan'; 

    // Cek Email apakah sudah terdaftar
    $cek = $conn->prepare("SELECT * FROM pelanggan WHERE email = ?");
    $cek->execute([$email]);
    if($cek->rowCount() > 0) {
        echo "<script>alert('Email sudah digunakan! Silakan gunakan email lain.'); window.location='register.php';</script>";
        exit;
    }

    // Menyimpan Data
    $insert = $conn->prepare("INSERT INTO pelanggan (nama, email, no_hp, password, role) VALUES (?, ?, ?, ?, ?)");
    if($insert->execute([$nama, $email, $no_hp, $password, $role])) {
        echo "<script>alert('Pendaftaran berhasil! Silakan login dengan akun barumu.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan, pendaftaran gagal!'); window.location='register.php';</script>";
    }
}
?>
