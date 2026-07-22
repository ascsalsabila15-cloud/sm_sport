<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sm_sport";

// Konfigurasi PDO untuk keamanan dan penanganan error
try {
    
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
