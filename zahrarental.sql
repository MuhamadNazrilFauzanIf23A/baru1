-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 27, 2024 at 07:50 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zahrarental`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `email_or_phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `role` enum('admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email_or_phone`, `password`, `created_at`, `role`) VALUES
(1, 'admin15@gmail.com', '$2y$10$d1mF/OmqdVH6x/E9TypCEuizImP7HQFYhVxE.v.M5uQ2ZPPko28sK', '2024-12-06 17:56:34', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `detail_mobil`
--

CREATE TABLE `detail_mobil` (
  `id` int NOT NULL,
  `id_mobil` int DEFAULT NULL,
  `deskripsi` text,
  `spesifikasi` text,
  `stok` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_mobil`
--

INSERT INTO `detail_mobil` (`id`, `id_mobil`, `deskripsi`, `spesifikasi`, `stok`) VALUES
(2, 1, 'Mobil Kuat, cocok untuk penggunaan tahunan', 'Manual, Bensin, Kapasitas 2 orang', 2),
(3, 2, 'Mobil menengah, cocok untuk penggunaan harian', 'Manual, Bensin, Kapasitas 2 orang', 1),
(5, 7, 'siap menjelajah', '8 orang', 1),
(6, 5, 'cepat seperti cheetah 12', '2 orang', 2),
(7, 8, 'seperti mobil balap', '2 orang saja', 0),
(8, 11, 'gatawu', '4 orang', 2);

-- --------------------------------------------------------

--
-- Table structure for table `list_mobil`
--

CREATE TABLE `list_mobil` (
  `id` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `harga` varchar(255) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `harga_per6jam` int DEFAULT '0',
  `harga_per12jam` int DEFAULT '0',
  `harga_per24jam` int DEFAULT '0',
  `is_premium` enum('ya','tidak') DEFAULT 'tidak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `list_mobil`
--

INSERT INTO `list_mobil` (`id`, `nama`, `harga`, `gambar`, `harga_per6jam`, `harga_per12jam`, `harga_per24jam`, `is_premium`) VALUES
(1, 'Toyota Avanza', '350000', 'Avanza.jpeg', 350000, 500000, 1000000, 'tidak'),
(2, 'Daihatsu Ayla', '250000', 'Ayla.jpeg', 250000, 400000, 1000000, 'ya'),
(5, 'Tesla Model 3', '1200000', 'Tesla.jpeg', 700000, 1400000, 2800000, 'ya'),
(7, 'Fortuner', '200000', 'fortuner.jpg', 200000, 350000, 900000, 'tidak'),
(8, 'Sedan', '300000', 'sedan.jpg', 300000, 550000, 120000, 'ya'),
(11, 'Xenia', '300000', 'xenia.jpeg', 300000, 550000, 1000000, 'ya');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `verification_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `verification_code`, `expires_at`, `created_at`) VALUES
(12, 7, '102450', '2024-12-19 14:51:11', '2024-12-19 07:41:11');

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `id_mobil` int NOT NULL,
  `tanggal_pemesanan` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `waktu_pengambilan` time NOT NULL,
  `masa_sewa` int NOT NULL,
  `paket_sewa` varchar(50) NOT NULL,
  `harga` int NOT NULL,
  `sopir` enum('iya','tidak') DEFAULT 'tidak',
  `file_unggahan` varchar(255) NOT NULL,
  `status` enum('pending','disetujui','ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pemesanan`
--

INSERT INTO `pemesanan` (`id`, `user_id`, `id_mobil`, `tanggal_pemesanan`, `tanggal_mulai`, `tanggal_selesai`, `waktu_pengambilan`, `masa_sewa`, `paket_sewa`, `harga`, `sopir`, `file_unggahan`, `status`) VALUES
(16, 10, 1, '2024-12-25 13:22:18', '2024-12-25', '2024-12-27', '00:00:00', 2, '12', 1300000, 'iya', '21599.jpg', 'disetujui'),
(19, 7, 5, '2024-12-26 03:00:55', '2024-12-28', '2024-12-30', '00:12:00', 2, '6', 1600000, 'iya', 'halamanadmin.png', 'disetujui'),
(21, 7, 1, '2024-12-27 06:36:25', '2027-12-12', '2027-12-15', '00:00:00', 3, '6', 1350000, 'iya', 'opp (1).jpg', 'disetujui'),
(22, 7, 1, '2024-12-27 06:41:01', '2024-12-27', '2024-12-29', '20:00:00', 2, '12', 1000000, 'tidak', 'oo.jpg', 'disetujui');

-- --------------------------------------------------------

--
-- Table structure for table `profil_pengguna`
--

CREATE TABLE `profil_pengguna` (
  `id` int NOT NULL,
  `nama_lengkap` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Nama pengguna',
  `tempat_tinggal` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `foto_profil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'profiledefault.png',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `profil_pengguna`
--

INSERT INTO `profil_pengguna` (`id`, `nama_lengkap`, `tempat_tinggal`, `no_hp`, `foto_profil`, `created_at`, `update_at`, `user_id`) VALUES
(12, 'Nazril S', 'Indonesia', '081287819067', '1735125457_Guts.jpg', '2024-12-25 10:59:00', '2024-12-25 11:17:37', 9),
(14, 'Nazril f', 'Inggris', '085797177445', '1735129734_21618.png', '2024-12-25 12:27:12', '2024-12-26 02:58:09', 10),
(15, 'Zulfan', 'Kp.Krajan, Rt 5/Rw 2, jalan soedirman', '081287819067', '1735272521_xenia.jpeg', '2024-12-26 03:00:19', '2024-12-27 07:22:30', 7),
(17, 'muhamad nazril fauzan', 'Kp.Krajan, Rt 5/Rw 2', '081287819067', '1735189828_21599.jpg', '2024-12-26 04:53:35', '2024-12-26 05:10:28', 11),
(23, 'faika dilla herlianda', 'wanayasa', '081287819067', '1735238414_21618.png', '2024-12-26 18:14:42', '2024-12-26 18:40:14', 12);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email_or_phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email_or_phone`, `password`, `created_at`, `role`) VALUES
(7, 'ghianpratama646@gmail.com', '$2y$10$bQ7By8KovKF7qrfFOs25Fuxs4d0L/Tia8R.64ODfsWQk8PxJqd.SC', '2024-12-11 11:04:49', 'user'),
(9, '081287819067', '$2y$10$3vnpCOJPcJgNMI6OsoK2tOALyaOy7ENQDwZlcGWvfemZ92a0PUNui', '2024-12-14 04:29:16', 'user'),
(10, 'mnazrilf15@gmail.com', '$2y$10$auE4fPs8ao9rqzRDeA8f/eVXueTjdw8diwZnf9PMkAHlQItZRrtbC', '2024-12-25 12:18:36', 'user'),
(11, '081287819066', '$2y$10$1Niy7UXz99P6R0MVmbBxGuvLRS8jcozA8VCYRvD878HVWavBVQpyy', '2024-12-25 14:22:48', 'user'),
(12, 'FAIKADILLAH@gmail.com', '$2y$10$pJ3aSYM9DRFiXeFIGFb8Y.Mf71zcIXCbk.V8C.sDKjl3EJKYwe8TC', '2024-12-26 06:46:23', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `detail_mobil`
--
ALTER TABLE `detail_mobil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mobil` (`id_mobil`);

--
-- Indexes for table `list_mobil`
--
ALTER TABLE `list_mobil`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `id_mobil` (`id_mobil`);

--
-- Indexes for table `profil_pengguna`
--
ALTER TABLE `profil_pengguna`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `detail_mobil`
--
ALTER TABLE `detail_mobil`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `list_mobil`
--
ALTER TABLE `list_mobil`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `profil_pengguna`
--
ALTER TABLE `profil_pengguna`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_mobil`
--
ALTER TABLE `detail_mobil`
  ADD CONSTRAINT `detail_mobil_ibfk_1` FOREIGN KEY (`id_mobil`) REFERENCES `list_mobil` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`id_mobil`) REFERENCES `list_mobil` (`id`);

--
-- Constraints for table `profil_pengguna`
--
ALTER TABLE `profil_pengguna`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
