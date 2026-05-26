-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Waktu pembuatan: 21 Bulan Mei 2026 pada 05.21
-- Versi server: 5.7.39
-- Versi PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eproc`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_bidang`
--

CREATE TABLE `master_bidang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kd_bidang` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_bidang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_available_for_up` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Apakah bidang ini tersedia untuk user UP',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_bidang`
--

INSERT INTO `master_bidang` (`id`, `kd_bidang`, `nama_bidang`, `is_available_for_up`, `created_at`, `updated_at`) VALUES
(1, '01', 'PEMBANGKITAN', 1, '2023-09-26 08:15:40', '2023-09-26 08:15:40'),
(2, '02', 'DISTRIBUSI', 1, '2023-09-26 08:15:40', '2023-09-26 08:15:40'),
(3, '03', 'PELAYANAN PELANGGAN', 1, '2023-09-26 08:15:40', '2023-09-26 08:15:40'),
(4, '04', 'TRANSMISI DAN GARDU INDUK', 1, '2023-09-26 08:15:40', '2023-09-26 08:15:40'),
(5, '05', 'SDM & UMUM', 1, '2023-09-26 08:15:40', '2023-09-26 08:15:40'),
(6, '06', 'KEUANGAN & ANGGARAN', 1, '2023-09-26 08:15:40', '2023-09-26 08:15:40'),
(7, '07', 'SEKPER', 0, '2023-09-26 08:15:40', '2024-01-17 15:11:30'),
(8, '08', 'PERENCANAAN & PENGEMBANGAN USAHA', 0, '2023-09-26 08:15:40', '2023-09-26 08:15:40'),
(9, '09', 'K3LH', 1, '2023-09-26 08:15:40', '2023-09-26 08:15:40'),
(10, '10', 'SPI', 0, '2023-09-26 08:15:40', '2023-09-26 08:15:40'),
(11, '11', 'BEYOND KWH', 1, '2023-12-21 01:43:29', '2024-01-17 15:11:44'),
(12, '12', 'OPERASI', 1, '2024-09-03 02:34:57', '2024-09-03 02:34:57');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `master_bidang`
--
ALTER TABLE `master_bidang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `master_bidang_kd_bidang_unique` (`kd_bidang`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `master_bidang`
--
ALTER TABLE `master_bidang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
