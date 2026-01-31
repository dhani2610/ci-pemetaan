-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 31, 2026 at 06:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pj5`
--

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(2, '2022-03-01-015241', 'App\\Database\\Migrations\\Createuser', 'default', 'App', 1767148537, 1),
(3, '2025-12-31-023359', 'App\\Database\\Migrations\\CreateUserTables', 'default', 'App', 1767148537, 1),
(6, '2022-01-05-130904', 'App\\Database\\Migrations\\Gawe', 'default', 'App', 1767158383, 2),
(7, '2025-12-31-023419', 'App\\Database\\Migrations\\CreateOutletTable', 'default', 'App', 1767158383, 2),
(8, '2025-12-31-023433', 'App\\Database\\Migrations\\CreateDetailsTables', 'default', 'App', 1767158410, 3);

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `id_admin` int(11) UNSIGNED NOT NULL,
  `nama` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`id_admin`, `nama`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', '$2y$10$jDgn.EkAPK26FQ.gVU.lXeESM86qGxZ749qMaWc4LE5i/qzcBsFV2', '2025-12-31 09:36:48', '2025-12-31 09:36:48');

-- --------------------------------------------------------

--
-- Table structure for table `tb_operasional`
--

CREATE TABLE `tb_operasional` (
  `id_operasional` int(11) UNSIGNED NOT NULL,
  `id_outlet` int(11) UNSIGNED NOT NULL,
  `nama_hari` varchar(20) NOT NULL,
  `jam_buka` time NOT NULL,
  `jam_tutup` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tb_operasional`
--

INSERT INTO `tb_operasional` (`id_operasional`, `id_outlet`, `nama_hari`, `jam_buka`, `jam_tutup`) VALUES
(7, 1, 'Senin', '13:14:00', '16:14:00'),
(8, 1, 'Selasa', '13:14:00', '19:14:00'),
(9, 2, 'Senin', '23:54:00', '14:54:00'),
(10, 3, 'Jumat', '23:30:00', '14:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `tb_outlet`
--

CREATE TABLE `tb_outlet` (
  `id_outlet` int(11) UNSIGNED NOT NULL,
  `id_umkm` int(11) UNSIGNED NOT NULL,
  `id_admin` int(11) UNSIGNED DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `kabupaten` varchar(50) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `kontak` text NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `validasi` varchar(20) NOT NULL,
  `status` enum('Waiting Approval','Approved','Rejected') NOT NULL DEFAULT 'Waiting Approval',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tb_outlet`
--

INSERT INTO `tb_outlet` (`id_outlet`, `id_umkm`, `id_admin`, `nama`, `jenis`, `alamat`, `kabupaten`, `kategori`, `deskripsi`, `kontak`, `longitude`, `latitude`, `foto`, `validasi`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Oleh oleh khas yogya', '', 'Yogyakarta', 'Yogyakarta', 'Pusat Oleh Oleh', 'oleh oleh khas yogya', '089128392198', '110.632324', '-8.042913', '1767161698_f298f82edd1fb06af9a3.png', '', 'Approved', '2025-12-31 13:14:58', '2026-01-31 12:13:25'),
(2, 2, NULL, 'sadasdsadas', '', 'tesadasdsa', 'Yogyakarta', 'Kuliner', 'esdsds', '12312312312', '110.227547', '-7.784345', '1769792125_4c2e91ff119ac3307076.jpeg', '', 'Approved', '2026-01-30 23:55:25', '2026-01-31 12:11:59'),
(3, 1, NULL, 'Labore ut enim ex li', '', 'Quis quis molestiae ', 'Sleman', 'Pusat Oleh Oleh', 'Dolor id beatae dolo', 'Quo exercitation qua', '110.246773', '-7.716308', '1769836531_24ff9f7ba9aa7ca22cdb.jpeg', '', 'Approved', '2026-01-31 12:15:31', '2026-01-31 12:18:19'),
(4, 1, NULL, 'testing outelt baru', '', 'sadasdas', 'Yogyakarta', 'Kuliner', 'testingggg', '12931203109', '110.323677', '-7.886380', '1769836753_d93d346d435414c5e273.jpeg', '', 'Approved', '2026-01-31 12:19:13', '2026-01-31 12:19:48');

-- --------------------------------------------------------

--
-- Table structure for table `tb_produk`
--

CREATE TABLE `tb_produk` (
  `id_produk` int(11) UNSIGNED NOT NULL,
  `id_outlet` int(11) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `harga` bigint(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tb_produk`
--

INSERT INTO `tb_produk` (`id_produk`, `id_outlet`, `nama`, `jenis`, `harga`, `created_at`, `updated_at`) VALUES
(1, 1, 'Produk 1', 'Makanan', 10000, '2025-12-31 13:15:15', '2026-01-30 23:46:15'),
(2, 1, 'Produk 2', 'Minuman', 200000, '2025-12-31 13:15:31', '2025-12-31 13:15:31'),
(3, 1, 'testing', 'Makanan', 10000, '2026-01-30 23:42:01', '2026-01-30 23:42:01'),
(4, 2, 'bakso', 'Makanan', 200000, '2026-01-30 23:55:51', '2026-01-30 23:55:51');

-- --------------------------------------------------------

--
-- Table structure for table `tb_umkm`
--

CREATE TABLE `tb_umkm` (
  `id_umkm` int(11) UNSIGNED NOT NULL,
  `nama` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tb_umkm`
--

INSERT INTO `tb_umkm` (`id_umkm`, `nama`, `username`, `password`, `status`, `created_at`, `updated_at`) VALUES
(1, 'jdeva', 'jdeva', '$2y$10$CbWq2WJQ9/xw3Ko9QmbFxuqEd78zso8tJz8Om5J/IhctB4GWuGL5S', 'Aktif', '2025-12-31 13:13:24', '2026-01-31 12:02:53'),
(2, 'jdeva 2', 'jdeva2', '$2y$10$t/saxkG3q0Bq60UL/Gfv9ukqPEo9pCvcmt2ASQhzq9nNK5byNA89S', 'Aktif', '2025-12-31 13:23:42', '2025-12-31 13:23:42');

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

CREATE TABLE `tb_users` (
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `name_user` varchar(50) NOT NULL,
  `email_user` varchar(50) NOT NULL,
  `password_user` varchar(70) NOT NULL,
  `info_user` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `tb_operasional`
--
ALTER TABLE `tb_operasional`
  ADD PRIMARY KEY (`id_operasional`),
  ADD KEY `tb_operasional_id_outlet_foreign` (`id_outlet`);

--
-- Indexes for table `tb_outlet`
--
ALTER TABLE `tb_outlet`
  ADD PRIMARY KEY (`id_outlet`),
  ADD KEY `tb_outlet_id_umkm_foreign` (`id_umkm`),
  ADD KEY `tb_outlet_id_admin_foreign` (`id_admin`);

--
-- Indexes for table `tb_produk`
--
ALTER TABLE `tb_produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `tb_produk_id_outlet_foreign` (`id_outlet`);

--
-- Indexes for table `tb_umkm`
--
ALTER TABLE `tb_umkm`
  ADD PRIMARY KEY (`id_umkm`);

--
-- Indexes for table `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `id_admin` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_operasional`
--
ALTER TABLE `tb_operasional`
  MODIFY `id_operasional` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_outlet`
--
ALTER TABLE `tb_outlet`
  MODIFY `id_outlet` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_produk`
--
ALTER TABLE `tb_produk`
  MODIFY `id_produk` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_umkm`
--
ALTER TABLE `tb_umkm`
  MODIFY `id_umkm` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id_user` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_operasional`
--
ALTER TABLE `tb_operasional`
  ADD CONSTRAINT `tb_operasional_id_outlet_foreign` FOREIGN KEY (`id_outlet`) REFERENCES `tb_outlet` (`id_outlet`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_outlet`
--
ALTER TABLE `tb_outlet`
  ADD CONSTRAINT `tb_outlet_id_admin_foreign` FOREIGN KEY (`id_admin`) REFERENCES `tb_admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_outlet_id_umkm_foreign` FOREIGN KEY (`id_umkm`) REFERENCES `tb_umkm` (`id_umkm`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_produk`
--
ALTER TABLE `tb_produk`
  ADD CONSTRAINT `tb_produk_id_outlet_foreign` FOREIGN KEY (`id_outlet`) REFERENCES `tb_outlet` (`id_outlet`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
