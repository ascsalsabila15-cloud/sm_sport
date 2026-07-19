<?php
require 'koneksi.php';
$stmt = $conn->query('SELECT * FROM reservasi LIMIT 1');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
