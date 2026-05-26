-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Waktu pembuatan: 21 Bulan Mei 2026 pada 05.20
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
-- Struktur dari tabel `master_sub_bidang`
--

CREATE TABLE `master_sub_bidang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `master_bidang_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_sub_bidang`
--

INSERT INTO `master_sub_bidang` (`id`, `nama`, `master_bidang_id`, `created_at`, `updated_at`) VALUES
(1, 'Fastra', 11, NULL, NULL),
(2, 'Fastra IML', 11, NULL, NULL),
(3, 'O&M GI', 11, NULL, NULL),
(4, 'O&M Transmisi', 11, NULL, NULL),
(5, 'O&M GI dan Transmisi', 11, NULL, NULL),
(6, 'O&M Distribusi', 11, NULL, NULL),
(7, 'O&M Instalasi Kelistrikan', 11, NULL, NULL),
(8, 'O&M Pembangkit', 11, NULL, NULL),
(9, 'Sewa Pembangkit', 11, NULL, NULL),
(10, 'Yantek', 2, NULL, NULL),
(11, 'Command Center', 2, NULL, NULL),
(12, 'Scada', 2, NULL, NULL),
(13, 'EPC Distribusi', 2, NULL, NULL),
(14, 'Penyediaan Peralatan Distribusi', 2, NULL, NULL),
(15, 'HCS', 2, NULL, NULL),
(16, 'SPKLU', 2, NULL, NULL),
(17, 'ListriQu', 2, NULL, NULL),
(18, 'Billman', 3, NULL, NULL),
(19, 'Ground Patrol', 4, NULL, NULL),
(20, 'O&M GI', 4, NULL, NULL),
(21, 'Pembangunan GI', 4, NULL, NULL),
(22, 'Pemeliharaan GI', 4, NULL, NULL),
(23, 'Uji Minyak Trafo', 4, NULL, NULL),
(24, 'Penyediaan Peralatan Transmisi', 4, NULL, NULL),
(25, 'EPC Transmisi', 4, NULL, NULL),
(26, 'AMC Transmisi', 4, NULL, NULL),
(27, 'O&M Pembangkit', 1, NULL, NULL),
(28, 'AMC Pembangkit', 1, NULL, NULL),
(29, 'Ancilerry Service', 1, NULL, NULL),
(30, 'Sewa Pembangkit', 1, NULL, NULL),
(31, 'Penyediaan Peralatan Pembangkit', 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `master_sub_bidang`
--
ALTER TABLE `master_sub_bidang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `master_sub_bidang_master_bidang_id_foreign` (`master_bidang_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `master_sub_bidang`
--
ALTER TABLE `master_sub_bidang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `master_sub_bidang`
--
ALTER TABLE `master_sub_bidang`
  ADD CONSTRAINT `master_sub_bidang_master_bidang_id_foreign` FOREIGN KEY (`master_bidang_id`) REFERENCES `master_bidang` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
