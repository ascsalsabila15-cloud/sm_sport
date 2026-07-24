<?php

require 'koneksi.php';

if(isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'pelanggan'; 

    // Validasi ketersediaan email di database
    $cek = $conn->prepare("SELECT * FROM pelanggan WHERE email = ?");
    $cek->execute([$email]);
    if($cek->rowCount() > 0) {
        echo "<script>alert('Email sudah digunakan! Silakan gunakan email lain.'); window.location='register.php';</script>";
        exit;
    }

    // Insert data pengguna baru menggunakan Prepared Statement
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert = $conn->prepare("INSERT INTO pelanggan (nama, email, no_hp, password, role) VALUES (?, ?, ?, ?, ?)");
    if($insert->execute([$nama, $email, $no_hp, $hashed_password, $role])) {
        echo "<script>alert('Pendaftaran berhasil! Silakan login dengan akun barumu.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan, pendaftaran gagal!'); window.location='register.php';</script>";
    }
}
?>
