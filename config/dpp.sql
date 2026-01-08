-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2026 at 07:30 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dpp`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `no_identitas` varchar(20) NOT NULL,
  `nama_penitip` varchar(100) NOT NULL,
  `no_telepon` varchar(15) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `petugas_id` int(11) NOT NULL,
  `status` enum('Tersedia','Diambil') NOT NULL DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `kode_barang`, `no_identitas`, `nama_penitip`, `no_telepon`, `nama_barang`, `tanggal_masuk`, `jam_masuk`, `petugas_id`, `status`) VALUES
(10, 'V8WJULO0', '8138721387', 'Aldo Nelferdian Herman', '0894332243229', 'Ganja', '2026-01-05', '14:49:00', 1, 'Tersedia'),
(11, 'IMCKB766', '8138721387', 'Fanu', '0894332243213', 'Tas Hitam', '2026-01-08', '12:28:00', 1, 'Tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `pengambilan`
--

CREATE TABLE `pengambilan` (
  `id` int(11) NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `nama_pengambil` varchar(100) NOT NULL,
  `tanggal_pengambilan` date NOT NULL,
  `jam_pengambilan` time NOT NULL,
  `petugas_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int(11) NOT NULL,
  `nama_petugas` varchar(100) NOT NULL,
  `email_petugas` varchar(100) NOT NULL,
  `password_petugas` varchar(255) NOT NULL,
  `nomor_telepon` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `nama_petugas`, `email_petugas`, `password_petugas`, `nomor_telepon`) VALUES
(1, 'Pojan', 'admin@admin.com', '$2y$10$9xcQq4gastTgEbdsubPcU.KIvpoGwfLVW.dalj8JGl95F5SlVOOhi', '081234567890');

-- --------------------------------------------------------

--
-- Table structure for table `profil_petugas`
--

CREATE TABLE `profil_petugas` (
  `id_petugas` int(11) NOT NULL,
  `alamat` text DEFAULT NULL,
  `foto_profile` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pengambilan`
--

CREATE TABLE `riwayat_pengambilan` (
  `id` int(11) NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `nama_pengambil` varchar(100) NOT NULL,
  `nama_penitip` varchar(100) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `petugas_id` int(11) NOT NULL,
  `status` enum('Diambil','Batal','Menunggu','Proses') NOT NULL DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_barang` (`kode_barang`),
  ADD KEY `petugas_id` (`petugas_id`);

--
-- Indexes for table `pengambilan`
--
ALTER TABLE `pengambilan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode_barang` (`kode_barang`),
  ADD KEY `petugas_id` (`petugas_id`);

--
-- Indexes for table `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id_petugas`),
  ADD UNIQUE KEY `email_petugas` (`email_petugas`);

--
-- Indexes for table `profil_petugas`
--
ALTER TABLE `profil_petugas`
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indexes for table `riwayat_pengambilan`
--
ALTER TABLE `riwayat_pengambilan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode_barang` (`kode_barang`),
  ADD KEY `petugas_id` (`petugas_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pengambilan`
--
ALTER TABLE `pengambilan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `riwayat_pengambilan`
--
ALTER TABLE `riwayat_pengambilan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`petugas_id`) REFERENCES `petugas` (`id_petugas`);

--
-- Constraints for table `pengambilan`
--
ALTER TABLE `pengambilan`
  ADD CONSTRAINT `pengambilan_ibfk_1` FOREIGN KEY (`kode_barang`) REFERENCES `barang` (`kode_barang`),
  ADD CONSTRAINT `pengambilan_ibfk_2` FOREIGN KEY (`petugas_id`) REFERENCES `petugas` (`id_petugas`);

--
-- Constraints for table `profil_petugas`
--
ALTER TABLE `profil_petugas`
  ADD CONSTRAINT `profil_petugas_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`);

--
-- Constraints for table `riwayat_pengambilan`
--
ALTER TABLE `riwayat_pengambilan`
  ADD CONSTRAINT `riwayat_pengambilan_ibfk_1` FOREIGN KEY (`kode_barang`) REFERENCES `barang` (`kode_barang`),
  ADD CONSTRAINT `riwayat_pengambilan_ibfk_2` FOREIGN KEY (`petugas_id`) REFERENCES `petugas` (`id_petugas`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
