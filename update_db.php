<?php
require 'koneksi.php';

try {
    $conn->exec("ALTER TABLE reservasi ADD COLUMN jumlah_lapangan INT DEFAULT 1");
    echo "Added jumlah_lapangan.\n";
} catch (Exception $e) {
    echo "jumlah_lapangan already exists or error: " . $e->getMessage() . "\n";
}

try {
    $conn->exec("ALTER TABLE reservasi ADD COLUMN waktu_booking DATETIME DEFAULT CURRENT_TIMESTAMP");
    echo "Added waktu_booking.\n";
} catch (Exception $e) {
    echo "waktu_booking already exists or error: " . $e->getMessage() . "\n";
}
?>
