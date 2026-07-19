<?php
// Menyimpan detail server database
$host = "localhost";
$user = "root"; // Default username XAMPP/Laragon
$pass = "";     // Default password biasanya kosong
$db   = "sm_sport";

try {
    // Mencoba membuka koneksi ke MySQL menggunakan PDO
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    // Mengatur agar error di database ditampilkan sebagai Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Jika gagal connect, program berhenti dan pesan error ditampilkan
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
