<?php
// Memulai session agar kita bisa menyimpan data login (seperti nama & role)
session_start();

// Memanggil file koneksi agar bisa mengeksekusi query ke database
require 'koneksi.php';

// Mengecek apakah tombol dengan name 'login' sudah ditekan
if(isset($_POST['login'])) {
    // Menangkap data yang diisi dari form login
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Mencari data pelanggan di database yang email dan passwordnya cocok
    $stmt = $conn->prepare("SELECT * FROM pelanggan WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();

    // Jika data ditemukan (user valid)
    if($user) {
        // Menyimpan data ke session agar sistem ingat siapa yang sedang login
        $_SESSION['id_pelanggan'] = $user['id_pelanggan'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        
        // Mengarahkan halaman (Redirect) sesuai tipe akun (role)
        if($user['role'] == 'admin') {
            header("Location: dashboard_admin.php");
        } else {
            header("Location: dashboard_pelanggan.php");
        }
        exit;
    } else {
        // Jika data tidak ditemukan, munculkan popup pesan error
        echo "<script>alert('Opps, Email atau password salah!'); window.location='login.php';</script>";
    }
}
?>
