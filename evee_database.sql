-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2025 at 08:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `evee_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id_article` varchar(5) NOT NULL,
  `id_user` varchar(5) DEFAULT NULL,
  `judul` varchar(50) DEFAULT NULL,
  `link` text DEFAULT NULL,
  `fase_siklus` enum('menstruasi','folikular','ovulasi','luteal') DEFAULT NULL,
  `id_fase` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id_article`, `id_user`, `judul`, `link`, `fase_siklus`, `id_fase`) VALUES
('A001', 'U001', 'Tips Menstruasi Nyaman', 'https://youtu.be/xvFZjo5PgG0?si=Ra0tzirmdKXUkzXn', 'menstruasi', 'F001'),
('A002', 'U002', 'Nutrisi Saat Ovulasi', 'https://youtu.be/xvFZjo5PgG0?si=Ra0tzirmdKXUkzXn', 'ovulasi', 'F003'),
('A003', 'U003', 'Olahraga Ringan Saat Folikular', 'https://youtu.be/xvFZjo5PgG0?si=Ra0tzirmdKXUkzXn', 'folikular', 'F002');

-- --------------------------------------------------------

--
-- Table structure for table `detail_article`
--

CREATE TABLE `detail_article` (
  `id_detail` varchar(5) NOT NULL,
  `id_user` varchar(5) DEFAULT NULL,
  `id_article` varchar(5) DEFAULT NULL,
  `tanggal_baca` datetime DEFAULT NULL,
  `disimpan` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_article`
--

INSERT INTO `detail_article` (`id_detail`, `id_user`, `id_article`, `tanggal_baca`, `disimpan`) VALUES
('DA001', 'U001', 'A001', '2025-11-10 00:23:33', 1),
('DA002', 'U002', 'A002', '2025-11-10 00:23:33', 0),
('DA003', 'U003', 'A003', '2025-11-10 00:23:33', 1);

-- --------------------------------------------------------

--
-- Table structure for table `detail_mood`
--

CREATE TABLE `detail_mood` (
  `id_dtl_mood` varchar(5) NOT NULL,
  `id_user` varchar(5) DEFAULT NULL,
  `id_mood` varchar(5) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_mood`
--

INSERT INTO `detail_mood` (`id_dtl_mood`, `id_user`, `id_mood`, `tanggal`, `waktu`) VALUES
('DM001', 'U001', 'M001', '2025-10-02', '08:00:00'),
('DM002', 'U002', 'M002', '2025-10-03', '09:30:00'),
('DM003', 'U003', 'M003', '2025-10-04', '10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `detail_period`
--

CREATE TABLE `detail_period` (
  `id_detail` varchar(5) NOT NULL,
  `id_period` varchar(5) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `gejala` varchar(50) DEFAULT NULL,
  `valume_darah` enum('ringan','sedang','berat','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_period`
--

INSERT INTO `detail_period` (`id_detail`, `id_period`, `tanggal`, `gejala`, `valume_darah`) VALUES
('DP001', 'P001', '2025-10-02', 'Kram ringan', 'ringan'),
('DP002', 'P001', '2025-10-04', 'Mood swing', 'sedang'),
('DP003', 'P002', '2025-10-06', 'Lelah', 'berat');

-- --------------------------------------------------------

--
-- Table structure for table `detail_user`
--

CREATE TABLE `detail_user` (
  `id_dtl_user` varchar(5) NOT NULL,
  `id_user` varchar(5) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_user`
--

INSERT INTO `detail_user` (`id_dtl_user`, `id_user`, `role`) VALUES
('DU001', 'U001', 'admin'),
('DU002', 'U002', 'user'),
('DU003', 'U003', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `fase_siklus`
--

CREATE TABLE `fase_siklus` (
  `id_fase` varchar(5) NOT NULL,
  `nama_fase` enum('menstruasi','folikular','ovulasi','luteal') DEFAULT NULL,
  `jangka_waktu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fase_siklus`
--

INSERT INTO `fase_siklus` (`id_fase`, `nama_fase`, `jangka_waktu`) VALUES
('F001', 'menstruasi', 5),
('F002', 'folikular', 9),
('F003', 'ovulasi', 3),
('F004', 'luteal', 12);

-- --------------------------------------------------------

--
-- Table structure for table `mood`
--

CREATE TABLE `mood` (
  `id_mood` varchar(5) NOT NULL,
  `id_user` varchar(5) DEFAULT NULL,
  `nama_mood` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mood`
--

INSERT INTO `mood` (`id_mood`, `id_user`, `nama_mood`) VALUES
('M001', 'U001', 'Senang'),
('M002', 'U002', 'Lelah'),
('M003', 'U003', 'Sedih');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id_notif` varchar(5) NOT NULL,
  `id_user` varchar(5) DEFAULT NULL,
  `tipe_notifikasi` enum('Menstruasi','kegiatan','lainnya') DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `status` enum('Terkirim','Belum terkirim') DEFAULT NULL,
  `waktu` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id_notif`, `id_user`, `tipe_notifikasi`, `pesan`, `status`, `waktu`) VALUES
('N001', 'U001', 'Menstruasi', 'Hari pertama menstruasi tercatat.', 'Terkirim', '08:00:00'),
('N002', 'U002', 'kegiatan', 'Jangan lupa olahraga ringan hari ini.', 'Belum terkirim', '09:00:00'),
('N003', 'U003', 'lainnya', 'Periksa fase siklus kamu sekarang.', 'Terkirim', '10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `period_record`
--

CREATE TABLE `period_record` (
  `id_period` varchar(5) NOT NULL,
  `id_user` varchar(5) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `panjang_periode` int(11) NOT NULL,
  `panjang_siklus` int(11) NOT NULL,
  `catatan` varchar(20) DEFAULT NULL,
  `id_fase` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `period_record`
--

INSERT INTO `period_record` (`id_period`, `id_user`, `tanggal_mulai`, `tanggal_selesai`, `panjang_periode`, `panjang_siklus`, `catatan`, `id_fase`) VALUES
('P001', 'U001', '2025-10-01', '2025-10-06', 5, 28, 'Periode lancar', 'F001'),
('P002', 'U002', '2025-10-03', '2025-10-08', 5, 30, 'Sedikit nyeri perut', 'F001'),
('P003', 'U003', '2025-10-05', '2025-10-09', 4, 27, 'Normal', 'F001');

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `id_reminder` varchar(5) NOT NULL,
  `id_user` varchar(5) DEFAULT NULL,
  `judul` varchar(25) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam` time DEFAULT NULL,
  `kategori` enum('Kuliah','Kerja','Pribadi','Lainnya') DEFAULT NULL,
  `prioritas` enum('Penting','Sedang','Rendah') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reminders`
--

INSERT INTO `reminders` (`id_reminder`, `id_user`, `judul`, `tanggal`, `jam`, `kategori`, `prioritas`) VALUES
('R001', 'U001', 'Minum air putih', '2025-10-02', '08:00:00', 'Pribadi', 'Penting'),
('R002', 'U002', 'Kelas pagi', '2025-10-03', '07:30:00', 'Kuliah', 'Sedang'),
('R003', 'U003', 'Istirahat cukup', '2025-10-04', '22:00:00', 'Pribadi', 'Rendah');

-- --------------------------------------------------------

--
-- Table structure for table `selfcare_recom`
--

CREATE TABLE `selfcare_recom` (
  `id_recom` varchar(5) NOT NULL,
  `id_user` varchar(5) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `kondisi` varchar(25) DEFAULT NULL,
  `rekomendasi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `selfcare_recom`
--

INSERT INTO `selfcare_recom` (`id_recom`, `id_user`, `tanggal`, `kondisi`, `rekomendasi`) VALUES
('SR001', 'U001', '2025-10-02', 'Kram ringan', 'Mandi air hangat dan minum teh jahe.'),
('SR002', 'U002', '2025-10-03', 'Lelah', 'Tidur lebih awal dan hindari kopi.'),
('SR003', 'U003', '2025-10-04', 'Mood swing', 'Meditasi dan dengarkan musik tenang.');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` varchar(5) NOT NULL,
  `nama` varchar(15) NOT NULL,
  `email` varchar(30) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `rerata_siklus` varchar(10) NOT NULL,
  `last_login` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `tanggal_lahir`, `rerata_siklus`, `last_login`) VALUES
('U001', 'Intan', 'intan@gmail.com', '2003-05-11', '28', '2025-11-10 00:23:14'),
('U002', 'Sari', 'sari@mail.com', '2002-08-09', '30', '2025-11-10 00:23:14'),
('U003', 'Alya', 'alya@mail.com', '2001-11-23', '27', '2025-11-10 00:23:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id_article`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_fase` (`id_fase`);

--
-- Indexes for table `detail_article`
--
ALTER TABLE `detail_article`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_article` (`id_article`);

--
-- Indexes for table `detail_mood`
--
ALTER TABLE `detail_mood`
  ADD PRIMARY KEY (`id_dtl_mood`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_mood` (`id_mood`);

--
-- Indexes for table `detail_period`
--
ALTER TABLE `detail_period`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_period` (`id_period`);

--
-- Indexes for table `detail_user`
--
ALTER TABLE `detail_user`
  ADD PRIMARY KEY (`id_dtl_user`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `fase_siklus`
--
ALTER TABLE `fase_siklus`
  ADD PRIMARY KEY (`id_fase`);

--
-- Indexes for table `mood`
--
ALTER TABLE `mood`
  ADD PRIMARY KEY (`id_mood`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id_notif`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `period_record`
--
ALTER TABLE `period_record`
  ADD PRIMARY KEY (`id_period`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_fase` (`id_fase`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id_reminder`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `selfcare_recom`
--
ALTER TABLE `selfcare_recom`
  ADD PRIMARY KEY (`id_recom`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`id_fase`) REFERENCES `fase_siklus` (`id_fase`);

--
-- Constraints for table `detail_article`
--
ALTER TABLE `detail_article`
  ADD CONSTRAINT `detail_article_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `detail_article_ibfk_2` FOREIGN KEY (`id_article`) REFERENCES `articles` (`id_article`);

--
-- Constraints for table `detail_mood`
--
ALTER TABLE `detail_mood`
  ADD CONSTRAINT `detail_mood_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `detail_mood_ibfk_2` FOREIGN KEY (`id_mood`) REFERENCES `mood` (`id_mood`);

--
-- Constraints for table `detail_period`
--
ALTER TABLE `detail_period`
  ADD CONSTRAINT `detail_period_ibfk_1` FOREIGN KEY (`id_period`) REFERENCES `period_record` (`id_period`);

--
-- Constraints for table `detail_user`
--
ALTER TABLE `detail_user`
  ADD CONSTRAINT `detail_user_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `mood`
--
ALTER TABLE `mood`
  ADD CONSTRAINT `mood_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `period_record`
--
ALTER TABLE `period_record`
  ADD CONSTRAINT `period_record_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `period_record_ibfk_2` FOREIGN KEY (`id_fase`) REFERENCES `fase_siklus` (`id_fase`);

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `selfcare_recom`
--
ALTER TABLE `selfcare_recom`
  ADD CONSTRAINT `selfcare_recom_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
