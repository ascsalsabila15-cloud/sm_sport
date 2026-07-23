-- Database schema for SM Sport Center
CREATE DATABASE IF NOT EXISTS sm_sport;
USE sm_sport;

-- Table struktur untuk pelanggan
CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `role` enum('admin','pelanggan') DEFAULT 'pelanggan',
  PRIMARY KEY (`id_pelanggan`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data dummy untuk pelanggan
INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `email`, `password`, `no_hp`, `role`) VALUES
(1, 'Administrator', 'admin@smsport.com', 'admin123', '08123456789', 'admin'),
(2, 'Pelanggan Satu', 'pelanggan1@gmail.com', 'pelanggan123', '08987654321', 'pelanggan'),
(3, 'Pelanggan Dua', 'pelanggan2@gmail.com', 'pelanggan123', '08111222333', 'pelanggan');

-- Table struktur untuk lapangan
CREATE TABLE IF NOT EXISTS `lapangan` (
  `id_lapangan` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lapangan` varchar(100) NOT NULL,
  `jenis_lapangan` enum('Futsal','Badminton') NOT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_lapangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data lapangan sesuai kebutuhan (2 futsal, 3 badminton)
INSERT INTO `lapangan` (`id_lapangan`, `nama_lapangan`, `jenis_lapangan`, `harga_per_jam`) VALUES
(1, 'Futsal (Sintetis)', 'Futsal', 100000.00),
(2, 'Badminton (Karpet)', 'Badminton', 50000.00);


-- Table struktur untuk reservasi
CREATE TABLE IF NOT EXISTS `reservasi` (
  `id_reservasi` int(11) NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int(11) NOT NULL,
  `id_lapangan` int(11) NOT NULL,
  `jumlah_lapangan` int(11) NOT NULL DEFAULT 1,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `metode_pembayaran` enum('cash','transfer') NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status` enum('pending','lunas','batal') NOT NULL DEFAULT 'pending',
  `waktu_booking` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_reservasi`),
  KEY `id_pelanggan` (`id_pelanggan`),
  KEY `id_lapangan` (`id_lapangan`),
  CONSTRAINT `fk_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_lapangan` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id_lapangan`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dummy data reservasi
INSERT INTO `reservasi` (`id_reservasi`, `id_pelanggan`, `id_lapangan`, `jumlah_lapangan`, `tanggal`, `jam_mulai`, `jam_selesai`, `metode_pembayaran`, `bukti_transfer`, `status`) VALUES
(1, 2, 1, 1, '2026-08-01', '15:00:00', '17:00:00', 'transfer', 'dummy_bukti.jpg', 'lunas'),
(2, 3, 2, 2, '2026-08-01', '19:00:00', '21:00:00', 'cash', NULL, 'pending');
