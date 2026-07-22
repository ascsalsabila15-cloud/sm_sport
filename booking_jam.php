<?php
session_start();
require 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_GET['tanggal']) || !isset($_GET['jenis_lapangan'])) {
    echo json_encode([]);
    exit;
}

$tanggal = $_GET['tanggal'];
$jenis_lapangan = $_GET['jenis_lapangan'];

$stmtLap = $conn->prepare("SELECT COUNT(*) as total_lapangan FROM lapangan WHERE jenis_lapangan = ?");
$stmtLap->execute([$jenis_lapangan]);
$lap_data = $stmtLap->fetch();
$total_lap = $lap_data['total_lapangan'] ?? 0;

$stmtRes = $conn->prepare("
    SELECT r.jam_mulai, r.jam_selesai, COUNT(r.id_reservasi) as terisi
    FROM reservasi r
    JOIN lapangan l ON r.id_lapangan = l.id_lapangan
    WHERE r.tanggal = ? AND l.jenis_lapangan = ? AND r.status != 'batal'
    GROUP BY r.jam_mulai, r.jam_selesai
    ORDER BY r.jam_mulai ASC
");
$stmtRes->execute([$tanggal, $jenis_lapangan]);
$booked = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

$result = [];
foreach ($booked as $b) {
    $sisa = $total_lap - $b['terisi'];
    $result[] = [
        'jam_mulai' => substr($b['jam_mulai'], 0, 5),
        'jam_selesai' => substr($b['jam_selesai'], 0, 5),
        'terisi' => $b['terisi'],
        'sisa' => $sisa
    ];
}

echo json_encode(['total_lapangan' => $total_lap, 'booked' => $result]);
?>
