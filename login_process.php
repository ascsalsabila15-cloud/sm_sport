<?php
session_start();
require 'koneksi.php';

if(isset($_POST['login'])) {
    // Menangkap data yang diisi dari form login
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Mencari data pelanggan di database yang email dan passwordnya cocok
    $stmt = $conn->prepare("SELECT * FROM pelanggan WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();

    // User Valid
    if($user) {
        // Menyimpan data ke session agar sistem ingat siapa yang sedang login
        $_SESSION['id_pelanggan'] = $user['id_pelanggan'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] == 'admin') {
            header("Location: dashboard_admin.php");
        } else {
            header("Location: dashboard_pelanggan.php");
        }
        exit;
    } else {
        echo "<script>alert('Opps, Email atau password salah!'); window.location='login.php';</script>";
    }
}
?>
