-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 24, 2026 at 11:56 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

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
-- Table structure for table `master_region`
--

CREATE TABLE `master_region` (
  `id` bigint UNSIGNED NOT NULL,
  `kd_region_sap` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kd_region` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `masa_persiapan` int DEFAULT NULL,
  `kd_provinsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lon` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kota` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facsimile` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_surat` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `master_region`
--

INSERT INTO `master_region` (`id`, `kd_region_sap`, `kd_region`, `nama_region`, `masa_persiapan`, `kd_provinsi`, `lat`, `lon`, `manager`, `jabatan`, `kota`, `alamat`, `telepon`, `facsimile`, `email`, `kode_surat`, `created_at`, `updated_at`) VALUES
(1, 'SUL2', '01', 'UP SULAWESI 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Bonto Ramba No.9, Mannuruki, Kec. Tamalate, Kota Makassar, Sulawesi Selatan 90223', NULL, NULL, NULL, NULL, '2023-10-05 01:00:50', '2023-10-05 01:00:50'),
(2, 'SUL1', '02', 'UP SULAWESI 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Tikala Ares No.32, Dikrama, guntur, Kec. Tikala, Kota Manado, Sulawesi Utara 95123', NULL, NULL, NULL, NULL, '2023-10-05 01:00:50', '2023-10-05 01:00:50'),
(3, 'KAL1', '03', 'UP KALIMANTAN 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Parit H. Husin II, Bangka Belitung Darat, Kec. Pontianak Tenggara, Kota Pontianak, Kalimantan Barat 78116', NULL, NULL, NULL, NULL, '2023-10-05 01:00:50', '2023-10-05 01:00:50'),
(4, 'KAL2', '04', 'UP KALIMANTAN 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Pangeran Hidayatullah No.22, Loktabat Utara, Kec. Banjarbaru Utara, Kota Banjar Baru, Kalimantan Selatan 70714', NULL, NULL, NULL, NULL, '2023-10-05 01:00:50', '2023-10-05 01:00:50'),
(5, 'KAL3', '05', 'UP KALIMANTAN 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. RE Martadinata, Gunungsari Ilir, Kec. Balikpapan Tengah, Kota Balikpapan, Kalimantan Timur 76113', NULL, NULL, NULL, NULL, '2023-10-05 01:00:50', '2023-10-05 01:00:50'),
(6, 'NUSA', '07', 'UP NUSA TENGGARA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Bung Karno No. 26, Mataram Timur, Mataram, Kota Mataram, Nusa Tenggara Barat, 83127', NULL, NULL, NULL, NULL, '2023-10-05 01:00:50', '2023-10-05 01:00:50'),
(7, 'PAPA', '08', 'UP PAPUA', 15, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl.Perum Jaya asri, Entrop, Distrik Jayapura Selatan, Kota Jayapura, Papua 99223', NULL, NULL, NULL, NULL, '2023-10-05 01:00:50', '2023-11-06 04:02:49'),
(8, 'PUST', '09', 'KANTOR PUSAT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Letjen Zaini Azhar Maulani, Gn. Bahagia, Kecamatan Balikpapan Selatan, Kota Balikpapan, Kalimantan Timur 76114', NULL, NULL, NULL, NULL, '2023-10-05 01:00:50', '2023-10-05 01:00:50'),
(453, 'MAMA', '10', 'UP MALUKU', 15, NULL, NULL, NULL, NULL, NULL, NULL, 'Said Perintah No.53, Kel Ahusen, Kec. Sirimau, Kota Ambon, Maluku 97126', NULL, NULL, NULL, NULL, '2024-06-06 07:47:08', '2024-09-20 05:22:12'),
(454, 'MALU', '11', 'UP MALUKU UTARA', 15, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Bandara Sultan Babullah, Kelurahan Tabam, Kec. Kota Ternate Utara, Prov. Maluku Utara 97728', NULL, NULL, NULL, NULL, '2025-05-10 11:50:04', '2025-05-10 11:50:04'),
(688, 'ASMU', '13', 'UP PEMBANGKIT ACEH DAN SUMATERA BAGIAN UTARA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-28 03:41:37', '2026-03-28 03:41:37'),
(689, 'BSSJ', '14', 'UP PEMBANGKIT BANGKA BELITUNG, SUMATERA SELATAN, DAN JAWA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-28 03:41:37', '2026-03-28 03:41:37'),
(690, 'RKPR', '15', 'UP PEMBANGKIT RIAU DAN KEPULAUAN RIAU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-28 03:41:37', '2026-03-28 03:41:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `master_region`
--
ALTER TABLE `master_region`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `master_region_kd_region_unique` (`kd_region`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `master_region`
--
ALTER TABLE `master_region`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=691;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
