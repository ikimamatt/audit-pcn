-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 22 Bulan Mei 2026 pada 08.15
-- Versi server: 8.0.30
-- Versi PHP: 8.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `audit`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `email_notification_logs`
--

CREATE TABLE `email_notification_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `penutup_lha_rekomendasi_id` bigint UNSIGNED NOT NULL,
  `master_user_id` bigint UNSIGNED NOT NULL,
  `trigger_type` enum('manual','scheduled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `sent_by` bigint UNSIGNED DEFAULT NULL,
  `status` enum('sent','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `entry_meeting`
--

CREATE TABLE `entry_meeting` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `actual_meeting_date` date DEFAULT NULL,
  `auditee_id` bigint UNSIGNED NOT NULL,
  `program_kerja_audit_id` bigint UNSIGNED DEFAULT NULL,
  `file_undangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_absensi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_approval` enum('pending','approved_level1','approved','rejected_level1','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_level1` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level1` timestamp NULL DEFAULT NULL,
  `rejected_by_level1` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level1` timestamp NULL DEFAULT NULL,
  `rejection_reason_level1` text COLLATE utf8mb4_unicode_ci,
  `approved_by_level2` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level2` timestamp NULL DEFAULT NULL,
  `rejected_by_level2` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level2` timestamp NULL DEFAULT NULL,
  `rejection_reason_level2` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `entry_meeting`
--

INSERT INTO `entry_meeting` (`id`, `tanggal`, `actual_meeting_date`, `auditee_id`, `program_kerja_audit_id`, `file_undangan`, `file_absensi`, `status_approval`, `rejection_reason`, `approved_by`, `approved_at`, `approved_by_level1`, `approved_at_level1`, `rejected_by_level1`, `rejected_at_level1`, `rejection_reason_level1`, `approved_by_level2`, `approved_at_level2`, `rejected_by_level2`, `rejected_at_level2`, `rejection_reason_level2`, `created_at`, `updated_at`) VALUES
(1, '2024-07-07', '2024-07-10', 1, 1, 'entry_meeting/undangan_1.pdf', 'entry_meeting/absensi_1.pdf', 'approved', NULL, 1, '2026-05-19 22:48:12', 1, '2026-05-19 22:48:09', NULL, NULL, NULL, 1, '2026-05-19 22:48:12', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, '2024-08-06', '2024-08-09', 2, 2, 'entry_meeting/undangan_2.pdf', 'entry_meeting/absensi_2.pdf', 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, '2024-09-05', '2024-09-06', 3, 3, 'entry_meeting/undangan_3.pdf', 'entry_meeting/absensi_3.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, '2024-10-05', '2024-10-07', 4, 4, 'entry_meeting/undangan_4.pdf', 'entry_meeting/absensi_4.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, '2024-11-04', '2024-11-06', 5, 5, 'entry_meeting/undangan_5.pdf', 'entry_meeting/absensi_5.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, '2024-12-04', '2024-12-04', 1, 6, 'entry_meeting/undangan_6.pdf', 'entry_meeting/absensi_6.pdf', 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, '2025-01-03', '2025-01-05', 2, 7, 'entry_meeting/undangan_7.pdf', 'entry_meeting/absensi_7.pdf', 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, '2025-02-02', '2025-02-03', 3, 8, 'entry_meeting/undangan_8.pdf', 'entry_meeting/absensi_8.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, '2026-03-17', '2026-03-19', 2, 9, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, '2026-04-11', '2026-04-16', 6, 10, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, '2026-02-13', '2026-02-18', 2, 11, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, '2026-03-28', '2026-03-29', 5, 14, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, '2026-04-07', '2026-04-10', 9, 16, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, '2026-02-14', '2026-02-18', 8, 18, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, '2026-02-15', '2026-02-16', 3, 19, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, '2026-05-07', '2026-05-05', 7, 20, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, '2026-02-21', '2026-02-23', 7, 23, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, '2026-03-20', '2026-03-21', 7, 26, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(19, '2026-04-27', '2026-05-02', 1, 27, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(20, '2026-05-09', '2026-05-14', 3, 28, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(21, '2026-03-10', '2026-03-08', 5, 30, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(22, '2026-02-19', '2026-02-24', 1, 31, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(23, '2026-04-24', '2026-04-24', 6, 32, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(24, '2026-04-11', '2026-04-12', 4, 33, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(25, '2026-03-23', '2026-03-24', 5, 38, 'dummy_undangan.pdf', 'dummy_absensi.pdf', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `exit_meeting_uploads`
--

CREATE TABLE `exit_meeting_uploads` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal_exit_meeting` date NOT NULL,
  `auditee_id` bigint UNSIGNED NOT NULL,
  `file_undangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_absensi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_approval_undangan` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by_undangan` bigint UNSIGNED DEFAULT NULL,
  `approved_at_undangan` timestamp NULL DEFAULT NULL,
  `status_approval_absensi` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by_absensi` bigint UNSIGNED DEFAULT NULL,
  `approved_at_absensi` timestamp NULL DEFAULT NULL,
  `approve` tinyint(1) NOT NULL DEFAULT '0',
  `approve_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_pkpt_audits`
--

CREATE TABLE `jadwal_pkpt_audits` (
  `id` bigint UNSIGNED NOT NULL,
  `auditee_id` bigint UNSIGNED NOT NULL,
  `jenis_audit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_auditor` int NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_approval` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jadwal_pkpt_audits`
--

INSERT INTO `jadwal_pkpt_audits` (`id`, `auditee_id`, `jenis_audit`, `jumlah_auditor`, `tanggal_mulai`, `tanggal_selesai`, `status_approval`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Operasional', 3, '2024-07-01', '2024-07-10', 'pending', NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:54:56'),
(2, 1, 'Khusus', 2, '2024-08-01', '2024-08-05', 'approved', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17', '2026-05-19 22:55:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lha_lhk_uploads`
--

CREATE TABLE `lha_lhk_uploads` (
  `id` bigint UNSIGNED NOT NULL,
  `pelaporan_hasil_audit_id` bigint UNSIGNED NOT NULL,
  `file_lha_lhk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_approval` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approve` tinyint(1) NOT NULL DEFAULT '0',
  `approve_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_akses_user`
--

CREATE TABLE `master_akses_user` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_akses` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_akses_user`
--

INSERT INTO `master_akses_user` (`id`, `nama_akses`, `created_at`, `updated_at`) VALUES
(1, 'KSPI', NULL, NULL),
(2, 'ASMAN SPI', NULL, NULL),
(3, 'AUDITOR', NULL, NULL),
(4, 'AUDITEE', NULL, NULL),
(5, 'SUPER ADMIN', NULL, NULL),
(6, 'VIEW BOD', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_area`
--

CREATE TABLE `master_area` (
  `id` int NOT NULL,
  `api_id` int DEFAULT NULL,
  `kd_region` varchar(50) DEFAULT NULL,
  `kd_area` varchar(50) DEFAULT NULL,
  `nama_area` varchar(255) DEFAULT NULL,
  `manager` varchar(255) DEFAULT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `alamat` text,
  `telepon` varchar(50) DEFAULT NULL,
  `facsimile` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `kode_surat` varchar(50) DEFAULT NULL,
  `lat` varchar(50) DEFAULT NULL,
  `lon` varchar(50) DEFAULT NULL,
  `base_region` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `master_area`
--

INSERT INTO `master_area` (`id`, `api_id`, `kd_region`, `kd_area`, `nama_area`, `manager`, `jabatan`, `kota`, `alamat`, `telepon`, `facsimile`, `email`, `kode_surat`, `lat`, `lon`, `base_region`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 16, '03', '02', 'AREA SANGGAU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(2, 17, '03', '03', 'AREA PONTIANAK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(3, 18, '03', '04', 'AREA KETAPANG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(4, 1, '01', '05', 'AREA KENDARI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(5, 2, '01', '06', 'AREA MAKASSAR, BULUKUMBA & WATAMPONE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(6, 7, '02', '07', 'AREA GORONTALO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(7, 21, '04', '08', 'AREA BANJARMASIN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(8, 22, '04', '10', 'AREA BARABAI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(9, 23, '04', '12', 'AREA KUALA KAPUAS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(10, 24, '04', '13', 'AREA PALANGKARAYA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(11, 36, '07', '14', 'AREA NTT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(12, 37, '07', '15', 'AREA NTB', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(13, 27, '05', '16', 'AREA BALIKPAPAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(14, 28, '05', '17', 'AREA BERAU & TARAKAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(15, 29, '05', '18', 'AREA SAMARINDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(16, 43, '10', '19', 'AREA AMBON', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(17, 44, '10', '20', 'AREA MASOHI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(18, 45, '08', '21', 'AREA PAPUA & PAPUA BARAT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(19, 25, '04', '22', 'UPM BANGKANAI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(20, 3, '01', '23', 'KANTOR REGION SUL2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(21, 26, '04', '24', 'KANTOR REGION KAL2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(22, 19, '03', '25', 'KANTOR REGION KAL1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(23, 30, '05', '26', 'KANTOR REGION KAL3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(24, 38, '07', '27', 'KANTOR REGION NUSRA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(25, 8, '02', '28', 'AREA PALU & LUWUK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(26, 9, '02', '30', 'AREA MANADO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(27, 10, '02', '31', 'AREA TAHUNA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(28, 11, '02', '32', 'AREA TOLI - TOLI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(29, 46, '11', '33', 'AREA TERNATE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(30, 47, '11', '34', 'AREA SOFIFI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(31, 48, '11', '35', 'AREA TOBELO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(32, 49, '10', '36', 'AREA TUAL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(33, 50, '10', '37', 'AREA SAUMLAKI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(34, 51, '10', '38', 'AREA MALUKU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(35, 20, '03', '39', 'SITE SINGKAWANG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(36, 31, '05', '40', 'SITE BONTANG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(37, 32, '05', '41', 'SITE MELAK & KOTABANGUN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(38, 33, '05', '42', 'SITE TARAKAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(39, 12, '02', '43', 'SITE TOLI-TOLI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(40, 13, '02', '44', 'SITE AMC TELAGA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(41, 4, '01', '45', 'UPP PUNAGAYA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(42, 39, '07', '46', 'SITE SUMBAWA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(43, 40, '07', '47', 'SITE BIMA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(44, 41, '07', '48', 'UPP TALIWANG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(45, 52, '08', '49', 'SITE BIAK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(46, 53, '08', '50', 'SITE TIMIKA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(47, 54, '08', '51', 'SITE NABIRE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(48, 55, '08', '52', 'SITE SORONG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(49, 56, '08', '53', 'SITE MANOKWARI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(50, 62, '09', '54', 'KANTOR PUSAT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(51, 57, '08', '55', 'SITE MERAUKE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(52, 58, '08', '56', 'SITE WAMENA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(53, 59, '08', '57', 'SITE JAYAPURA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(54, 60, '11', '58', 'AREA MALUKU UTARA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(55, 34, '05', '59', 'AREA MALINAU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(56, 14, '02', '60', 'AREA MINAHASA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(57, 5, '01', '61', 'SITE WATAMPONE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(58, 6, '01', '62', 'SITE BARRU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(59, 42, '07', '63', 'SITE MATARAM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(60, 61, '08', '64', 'KANTOR REGION PAPA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(61, 35, '05', '65', 'AREA KALTARA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(62, 15, '02', '66', 'KANTOR REGION SUL1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(63, 63, '05', '67', 'SITE BERAU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(64, 64, '01', '68', 'AREA MAMUJU & PALOPO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(65, 508, '10', '69', 'KANTOR REGION MAMA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(66, 423, '02', '70', 'AREA LAHENDONG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(67, 509, '02', '71', 'AREA PALU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(68, 510, '02', '72', 'AREA LUWUK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(69, 511, '01', '73', 'AREA PAREPARE & PINRANG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(70, 512, '04', '74', 'AREA PANGKALAN BUN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(71, 513, '03', '75', 'AREA MEMPAWAH', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(72, 514, '05', '76', 'UL NUSANTARA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(73, 515, '02', '77', 'AREA KOTAMOBAGU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(74, 516, '11', '78', 'KANTOR REGION MALU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(75, 526, '15', '79', 'UL Pembangkit Tersebar Riau 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(76, 527, '15', '80', 'UL Pembangkit Tersebar Riau 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(77, 528, '15', '81', 'UL Pembangkit Tersebar Riau 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(78, 529, '15', '82', 'UL Pembangkit Tersebar Riau 4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(79, 517, '13', '83', 'UL Pembangkit Tersebar Banda Aceh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(80, 518, '13', '84', 'UL Pembangkit Tersebar meulaboh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(81, 519, '13', '85', 'UL Pembangkit Tersebar Langsa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(82, 520, '13', '86', 'UL Pembangkit Tersebar Subulussalam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(83, 521, '13', '87', 'UL Pembangkit Tersebar Sumut dan Sumbar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(84, 522, '14', '88', 'UL Pembangkit Tersebar Bangka', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(85, 523, '14', '89', 'UL Pembangkit Tersebar Belitung', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(86, 524, '14', '90', 'UL Pembangkit Tersebar Lampung,Sumsel,Jambi dan Bengkulu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(87, 525, '14', '91', 'UL Pembangkit Tersebar Jawa Timur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(88, 530, '13', '92', 'KANTOR UP PEMBANGKIT ACEH DAN SUMATERA BAGIAN UTARA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(89, 531, '14', '93', 'KANTOR UP PEMBANGKIT BANGKA BELITUNG, SUMATERA SELATAN, DAN JAWA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL),
(90, 532, '15', '94', 'KANTOR UP PEMBANGKIT RIAU DAN KEPULAUAN RIAU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:16', '2026-05-19 19:41:16', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_auditee`
--

CREATE TABLE `master_auditee` (
  `id` bigint UNSIGNED NOT NULL,
  `direktorat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `divisi_cabang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `divisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_auditee`
--

INSERT INTO `master_auditee` (`id`, `direktorat`, `divisi_cabang`, `divisi`) VALUES
(1, NULL, NULL, 'PEMBANGKITAN'),
(2, NULL, NULL, 'DISTRIBUSI'),
(3, NULL, NULL, 'PELAYANAN PELANGGAN'),
(4, NULL, NULL, 'TRANSMISI DAN GARDU INDUK'),
(5, NULL, NULL, 'SDM & UMUM'),
(6, NULL, NULL, 'KEUANGAN & ANGGARAN'),
(7, NULL, NULL, 'SEKPER'),
(8, NULL, NULL, 'PERENCANAAN & PENGEMBANGAN USAHA'),
(9, NULL, NULL, 'K3LH'),
(10, NULL, NULL, 'SPI'),
(11, NULL, NULL, 'BEYOND KWH'),
(12, '', NULL, 'OPERASI');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_jenis_audit`
--

CREATE TABLE `master_jenis_audit` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_jenis_audit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_jenis_audit`
--

INSERT INTO `master_jenis_audit` (`id`, `nama_jenis_audit`, `kode`, `created_at`, `updated_at`) VALUES
(1, 'Audit Operasional', 'SPI.01.02', '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(2, 'Audit Khusus', 'SPI.01.03', '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(3, 'Konsultasi', 'SPI.01.04', '2026-05-19 22:21:16', '2026-05-19 22:21:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_kode_aoi`
--

CREATE TABLE `master_kode_aoi` (
  `id` bigint UNSIGNED NOT NULL,
  `indikator_pengawasan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_area_of_improvement` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_area_of_improvement` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_kode_aoi`
--

INSERT INTO `master_kode_aoi` (`id`, `indikator_pengawasan`, `kode_area_of_improvement`, `deskripsi_area_of_improvement`, `created_at`, `updated_at`) VALUES
(1, 'KEPATUHAN', '01.01', 'Pelanggaran terhadap peraturan perundang-undangan yang berlaku', NULL, NULL),
(2, 'KEPATUHAN', '01.02', 'Pelanggaran terhadap prosedur dan tata kerja yang ditetapkan', NULL, NULL),
(3, 'KEPATUHAN', '01.03', 'Penyimpangan dari ketentuan pelaksanaan anggaran', NULL, NULL),
(4, 'KEANDALAN & KEAKURATAN INFORMASI / LAPORAN', '02.01', 'Keandalan & keakuratan administrasi, informasi / laporan keuangan dan non keuangan', NULL, NULL),
(5, 'KEANDALAN & KEAKURATAN INFORMASI / LAPORAN', '02.02', 'Keandalan & keakuratan informasi / laporan tata usaha langganan', NULL, NULL),
(6, 'PENGAMANAN ASSET', '03.01', 'Pengamanan Asset', NULL, NULL),
(7, 'PEMANFAATAN SUMBER DAYA YANG EKONOMIS EFEKTIF DAN EFISIEN', '04.01', 'Pemanfaatan sumber daya manusia', NULL, NULL),
(8, 'PEMANFAATAN SUMBER DAYA YANG EKONOMIS EFEKTIF DAN EFISIEN', '04.02', 'Pemanfaatan sumber daya material dan peralatan', NULL, NULL),
(9, 'PEMANFAATAN SUMBER DAYA YANG EKONOMIS EFEKTIF DAN EFISIEN', '04.03', 'Pemanfaatan sumber daya uang', NULL, NULL),
(10, 'PENCAPAIAN TUJUAN SASARAN PROGRAM ATAU OPERASI', '05.01', 'Pencapaian tujuan dan sasaran program atau operasi', NULL, NULL),
(11, 'KASUS YANG MERUGIKAN PERUSAHAAN ATAU NEGARA', '05.02', 'Kasus yang merugikan negara dan atau perusahaan', NULL, NULL),
(12, 'KASUS YANG MERUGIKAN PERUSAHAAN ATAU NEGARA', '05.03', 'Kewajiban penyetoran kepada negara dan atau perusahaan', NULL, NULL),
(13, 'TEMUAN BERULANG', '07.01', 'Temuan berulang terkait Kepatuhan', NULL, NULL),
(14, 'TEMUAN BERULANG', '07.02', 'Temuan berulang terkait Keandalan & Keakuratan Informasi/Laporan', NULL, NULL),
(15, 'TEMUAN BERULANG', '07.03', 'Temuan berulang terkait Pengamanan Asset', NULL, NULL),
(16, 'TEMUAN BERULANG', '07.04', 'Temuan berulang terkait Pemanfaatan Sumber Daya yang Ekonomis Efektif dan Efisien', NULL, NULL),
(17, 'TEMUAN BERULANG', '07.05', 'Temuan berulang terkait Pencapaian Tujuan Sasaran Program atau Operasi', NULL, NULL),
(18, 'TEMUAN BERULANG', '07.06', 'Temuan berulang terkait Kasus yang Merugikan Perusahaan atau Negara', NULL, NULL),
(19, 'KEPATUHAN', '01.01', 'Pelanggaran terhadap peraturan perundang-undangan yang berlaku', NULL, NULL),
(20, 'KEPATUHAN', '01.02', 'Pelanggaran terhadap prosedur dan tata kerja yang ditetapkan', NULL, NULL),
(21, 'KEPATUHAN', '01.03', 'Penyimpangan dari ketentuan pelaksanaan anggaran', NULL, NULL),
(22, 'KEANDALAN & KEAKURATAN INFORMASI / LAPORAN', '02.01', 'Keandalan & keakuratan administrasi, informasi / laporan keuangan dan non keuangan', NULL, NULL),
(23, 'KEANDALAN & KEAKURATAN INFORMASI / LAPORAN', '02.02', 'Keandalan & keakuratan informasi / laporan tata usaha langganan', NULL, NULL),
(24, 'PENGAMANAN ASSET', '03.01', 'Pengamanan Asset', NULL, NULL),
(25, 'PEMANFAATAN SUMBER DAYA YANG EKONOMIS EFEKTIF DAN EFISIEN', '04.01', 'Pemanfaatan sumber daya manusia', NULL, NULL),
(26, 'PEMANFAATAN SUMBER DAYA YANG EKONOMIS EFEKTIF DAN EFISIEN', '04.02', 'Pemanfaatan sumber daya material dan peralatan', NULL, NULL),
(27, 'PEMANFAATAN SUMBER DAYA YANG EKONOMIS EFEKTIF DAN EFISIEN', '04.03', 'Pemanfaatan sumber daya uang', NULL, NULL),
(28, 'PENCAPAIAN TUJUAN SASARAN PROGRAM ATAU OPERASI', '05.01', 'Pencapaian tujuan dan sasaran program atau operasi', NULL, NULL),
(29, 'KASUS YANG MERUGIKAN PERUSAHAAN ATAU NEGARA', '05.02', 'Kasus yang merugikan negara dan atau perusahaan', NULL, NULL),
(30, 'KASUS YANG MERUGIKAN PERUSAHAAN ATAU NEGARA', '05.03', 'Kewajiban penyetoran kepada negara dan atau perusahaan', NULL, NULL),
(31, 'TEMUAN BERULANG', '07.01', 'Temuan berulang terkait Kepatuhan', NULL, NULL),
(32, 'TEMUAN BERULANG', '07.02', 'Temuan berulang terkait Keandalan & Keakuratan Informasi/Laporan', NULL, NULL),
(33, 'TEMUAN BERULANG', '07.03', 'Temuan berulang terkait Pengamanan Asset', NULL, NULL),
(34, 'TEMUAN BERULANG', '07.04', 'Temuan berulang terkait Pemanfaatan Sumber Daya yang Ekonomis Efektif dan Efisien', NULL, NULL),
(35, 'TEMUAN BERULANG', '07.05', 'Temuan berulang terkait Pencapaian Tujuan Sasaran Program atau Operasi', NULL, NULL),
(36, 'TEMUAN BERULANG', '07.06', 'Temuan berulang terkait Kasus yang Merugikan Perusahaan atau Negara', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_kode_risk`
--

CREATE TABLE `master_kode_risk` (
  `id` bigint UNSIGNED NOT NULL,
  `kelompok_risiko` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_risiko` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelompok_risiko_detail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_risiko` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_kode_risk`
--

INSERT INTO `master_kode_risk` (`id`, `kelompok_risiko`, `kode_risiko`, `kelompok_risiko_detail`, `deskripsi_risiko`, `created_at`, `updated_at`) VALUES
(1, 'STRATEGIS', 'S.1.1', 'Regulasi Pemerintah', 'Risiko Tarif Listrik', NULL, NULL),
(2, 'STRATEGIS', 'S.1.2', 'Regulasi Pemerintah', 'Risiko Subsidi Listrik', NULL, NULL),
(3, 'STRATEGIS', 'S.1.3', 'Regulasi Pemerintah', 'Risiko Regulasi Daerah', NULL, NULL),
(4, 'STRATEGIS', 'S.2.1', 'Reputasi', 'Risiko Reputasi', NULL, NULL),
(5, 'STRATEGIS', 'S.3.1', 'Organisasi Korporat', 'Risiko Perubahan Organisasi Korporat', NULL, NULL),
(6, 'STRATEGIS', 'S.4.1', 'Portofolio Bisnis', 'Risiko Anak Perusahaan', NULL, NULL),
(7, 'STRATEGIS', 'S.4.2', 'Portofolio Bisnis', 'Risiko Kerjasama Strategis', NULL, NULL),
(8, 'STRATEGIS', 'S.5.1', 'Business Continuity', 'Risiko Business Continuity Management', NULL, NULL),
(9, 'FINANSIAL', 'F.1.1', 'Ekonomi Makro', 'Risiko Perubahan Kurs', NULL, NULL),
(10, 'FINANSIAL', 'F.1.2', 'Ekonomi Makro', 'Risiko Perubahan Inflasi', NULL, NULL),
(11, 'FINANSIAL', 'F.2.1', 'Harga Energi Primer', 'Risiko Harga Batubara', NULL, NULL),
(12, 'FINANSIAL', 'F.2.2', 'Harga Energi Primer', 'Risiko Harga Gas', NULL, NULL),
(13, 'FINANSIAL', 'F.2.3', 'Harga Energi Primer', 'Risiko Harga BBM', NULL, NULL),
(14, 'FINANSIAL', 'F.2.4', 'Harga Energi Primer', 'Risiko Harga Panas Bumi', NULL, NULL),
(15, 'FINANSIAL', 'F.2.5', 'Harga Energi Primer', 'Risiko Harga Energi Primer Lainnya', NULL, NULL),
(16, 'FINANSIAL', 'F.3.1', 'Likuiditas', 'Risiko Tunggakan', NULL, NULL),
(17, 'FINANSIAL', 'F.4.1', 'Pinjaman', 'Risiko Covenant', NULL, NULL),
(18, 'FINANSIAL', 'F.4.2', 'Pinjaman', 'Risiko Suku Bunga', NULL, NULL),
(19, 'FINANSIAL', 'F.4.3', 'Pinjaman', 'Risiko Debt Repayment', NULL, NULL),
(20, 'FINANSIAL', 'F.5.1', 'Pendapatan', 'Risiko Pendapatan Penjualan', NULL, NULL),
(21, 'FINANSIAL', 'F.5.2', 'Pendapatan', 'Risiko Pendapatan Lain-lain', NULL, NULL),
(22, 'FINANSIAL', 'F.6.1', 'Akunting', 'Risiko Akunting & Pelaporan', NULL, NULL),
(23, 'FINANSIAL', 'F.6.2', 'Akunting', 'Risiko Kontrol Internal', NULL, NULL),
(24, 'FINANSIAL', 'F.7.1', 'Pajak', 'Risiko Pajak', NULL, NULL),
(25, 'OPERASIONAL', 'O.1.1', 'Energi Primer', 'Risiko Kontinuitas Pasokan Batubara', NULL, NULL),
(26, 'OPERASIONAL', 'O.1.2', 'Energi Primer', 'Risiko Kualitas Batubara', NULL, NULL),
(27, 'OPERASIONAL', 'O.1.3', 'Energi Primer', 'Risiko Kontinuitas Pasokan Gas', NULL, NULL),
(28, 'OPERASIONAL', 'O.1.4', 'Energi Primer', 'Risiko Kontinuitas Pasokan BBM', NULL, NULL),
(29, 'OPERASIONAL', 'O.1.5', 'Energi Primer', 'Risiko Bauran Energi (Felmix)', NULL, NULL),
(30, 'OPERASIONAL', 'O.2.1', 'SDM', 'Risiko Kompetensi SDM', NULL, NULL),
(31, 'OPERASIONAL', 'O.2.2', 'SDM', 'Risiko Jumlah SDM', NULL, NULL),
(32, 'OPERASIONAL', 'O.2.3', 'SDM', 'Risiko Keselamatan Kerja', NULL, NULL),
(33, 'OPERASIONAL', 'O.2.4', 'SDM', 'Risiko Kesejahteraan Pekerja', NULL, NULL),
(34, 'OPERASIONAL', 'O.2.5', 'SDM', 'Risiko Outsourcing', NULL, NULL),
(35, 'OPERASIONAL', 'O.3.1', 'Sistem Tenaga Listrik', 'Risiko Cadangan Daya Listrik', NULL, NULL),
(36, 'OPERASIONAL', 'O.3.2', 'Sistem Tenaga Listrik', 'Risiko Take or Pay', NULL, NULL),
(37, 'OPERASIONAL', 'O.3.3', 'Sistem Tenaga Listrik', 'Risiko Optimalisasi Operasi Sistem Tenaga Listrik', NULL, NULL),
(38, 'OPERASIONAL', 'O.4.1', 'Pembangkitan', 'Risiko Ketersediaan Pembangkitan', NULL, NULL),
(39, 'OPERASIONAL', 'O.4.2', 'Pembangkitan', 'Risiko Keandalan Pembangkitan', NULL, NULL),
(40, 'OPERASIONAL', 'O.4.3', 'Pembangkitan', 'Risiko Derating Pembangkitan', NULL, NULL),
(41, 'OPERASIONAL', 'O.4.4', 'Pembangkitan', 'Risiko Efisiensi Pembangkitan', NULL, NULL),
(42, 'OPERASIONAL', 'O.4.5', 'Pembangkitan', 'Risiko IPP', NULL, NULL),
(43, 'OPERASIONAL', 'O.5.1', 'Penyaluran', 'Risiko Ketersediaan Penyaluran', NULL, NULL),
(44, 'OPERASIONAL', 'O.5.2', 'Penyaluran', 'Risiko Keandalan Penyaluran', NULL, NULL),
(45, 'OPERASIONAL', 'O.6.1', 'Distribusi', 'Risiko Ketersediaan Jaringan Distribusi', NULL, NULL),
(46, 'OPERASIONAL', 'O.6.2', 'Distribusi', 'Risiko Keandalan Jaringan Distribusi', NULL, NULL),
(47, 'OPERASIONAL', 'O.6.3', 'Distribusi', 'Risiko Pertumbuhan Konsumsi Energi Listrik', NULL, NULL),
(48, 'OPERASIONAL', 'O.7.1', 'Pelayanan Pelanggan', 'Risiko GCG Penyambungan Baru / Tambah Daya', NULL, NULL),
(49, 'OPERASIONAL', 'O.7.2', 'Pelayanan Pelanggan', 'Risiko GCG Pembacaan Meter', NULL, NULL),
(50, 'OPERASIONAL', 'O.7.3', 'Pelayanan Pelanggan', 'Risiko GCG Pelayanan Gangguan', NULL, NULL),
(51, 'OPERASIONAL', 'O.7.4', 'Pelayanan Pelanggan', 'Risiko Keterbatasan Suplai', NULL, NULL),
(52, 'OPERASIONAL', 'O.7.5', 'Pelayanan Pelanggan', 'Risiko Ekspektasi Pelanggan', NULL, NULL),
(53, 'OPERASIONAL', 'O.7.6', 'Pelayanan Pelanggan', 'Risiko Kualitas Layanan', NULL, NULL),
(54, 'OPERASIONAL', 'O.8.1', 'Teknologi', 'Risiko Obsolete Teknologi', NULL, NULL),
(55, 'OPERASIONAL', 'O.8.2', 'Teknologi', 'Risiko Security Teknologi', NULL, NULL),
(56, 'OPERASIONAL', 'O.9.1', 'Bencana Alam', 'Risiko Bencana Lokal', NULL, NULL),
(57, 'OPERASIONAL', 'O.9.2', 'Bencana Alam', 'Risiko Bencana Nasional (Force Majeur)', NULL, NULL),
(58, 'OPERASIONAL', 'O.9.3', 'Bencana Alam', 'Risiko Terorisme / Terrorisme', NULL, NULL),
(59, 'PROYEK', 'P.1.1', 'Perencanaan & Desain', 'Risiko Kelayakan Proyek', NULL, NULL),
(60, 'PROYEK', 'P.1.2', 'Perencanaan & Desain', 'Risiko Desain Proyek', NULL, NULL),
(61, 'PROYEK', 'P.2.1', 'Pendanaan Proyek', 'Risiko Sumber Dana', NULL, NULL),
(62, 'PROYEK', 'P.2.2', 'Pendanaan Proyek', 'Risiko Financial Closing', NULL, NULL),
(63, 'PROYEK', 'P.2.3', 'Pendanaan Proyek', 'Risiko Disbursement', NULL, NULL),
(64, 'PROYEK', 'P.3.1', 'Pengadaan Proyek', 'Risiko Nilai Proyek (HPS)', NULL, NULL),
(65, 'PROYEK', 'P.3.2', 'Pengadaan Proyek', 'Risiko Kualitas Kontraktor', NULL, NULL),
(66, 'PROYEK', 'P.3.3', 'Pengadaan Proyek', 'Risiko Gagal Lelang', NULL, NULL),
(67, 'PROYEK', 'P.4.1', 'Konstruksi', 'Risiko Waktu Penyelesaian Proyek', NULL, NULL),
(68, 'PROYEK', 'P.4.2', 'Konstruksi', 'Risiko Kualitas Material / Jasa', NULL, NULL),
(69, 'PROYEK', 'P.4.3', 'Konstruksi', 'Risiko Pembayaran Termin Proyek', NULL, NULL),
(70, 'PROYEK', 'P.5.1', 'Risiko Pasca Konstruksi', 'Risiko Serah Terima Proyek', NULL, NULL),
(71, 'PROYEK', 'P.5.2', 'Risiko Pasca Konstruksi', 'Risiko Performance Pasca Proyek', NULL, NULL),
(72, 'PROYEK', 'P.5.3', 'Risiko Pasca Konstruksi', 'Risiko Garansi Hasil Pekerjaan', NULL, NULL),
(73, 'KEPATUHAN', 'K.1.1', 'Aspek Legal', 'Risiko Kerjasama Pihak Ketiga', NULL, NULL),
(74, 'KEPATUHAN', 'K.1.2', 'Aspek Legal', 'Risiko Hak Atas Kekayaan Intelektual (HAKI)', NULL, NULL),
(75, 'KEPATUHAN', 'K.1.3', 'Aspek Legal', 'Risiko Tuntutan Hukum', NULL, NULL),
(76, 'KEPATUHAN', 'K.1.4', 'Aspek Legal', 'Risiko Perijinan', NULL, NULL),
(77, 'KEPATUHAN', 'K.1.5', 'Aspek Legal', 'Risiko Pembebasan Tanah', NULL, NULL),
(78, 'KEPATUHAN', 'K.2.1', 'Etika & Kecurangan (Fraud)', 'Risiko Etika', NULL, NULL),
(79, 'KEPATUHAN', 'K.2.2', 'Etika & Kecurangan (Fraud)', 'Risiko Kecurangan / Korupsi', NULL, NULL),
(80, 'KEPATUHAN', 'K.3.1', 'Lingkungan', 'Risiko Dampak Lingkungan', NULL, NULL),
(81, 'KEPATUHAN', 'K.3.2', 'Lingkungan', 'Risiko Sosial / Politik / Budaya', NULL, NULL),
(82, 'STRATEGIS', 'S.1.1', 'Regulasi Pemerintah', 'Risiko Tarif Listrik', NULL, NULL),
(83, 'STRATEGIS', 'S.1.2', 'Regulasi Pemerintah', 'Risiko Subsidi Listrik', NULL, NULL),
(84, 'STRATEGIS', 'S.1.3', 'Regulasi Pemerintah', 'Risiko Regulasi Daerah', NULL, NULL),
(85, 'STRATEGIS', 'S.2.1', 'Reputasi', 'Risiko Reputasi', NULL, NULL),
(86, 'STRATEGIS', 'S.3.1', 'Organisasi Korporat', 'Risiko Perubahan Organisasi Korporat', NULL, NULL),
(87, 'STRATEGIS', 'S.4.1', 'Portofolio Bisnis', 'Risiko Anak Perusahaan', NULL, NULL),
(88, 'STRATEGIS', 'S.4.2', 'Portofolio Bisnis', 'Risiko Kerjasama Strategis', NULL, NULL),
(89, 'STRATEGIS', 'S.5.1', 'Business Continuity', 'Risiko Business Continuity Management', NULL, NULL),
(90, 'FINANSIAL', 'F.1.1', 'Ekonomi Makro', 'Risiko Perubahan Kurs', NULL, NULL),
(91, 'FINANSIAL', 'F.1.2', 'Ekonomi Makro', 'Risiko Perubahan Inflasi', NULL, NULL),
(92, 'FINANSIAL', 'F.2.1', 'Harga Energi Primer', 'Risiko Harga Batubara', NULL, NULL),
(93, 'FINANSIAL', 'F.2.2', 'Harga Energi Primer', 'Risiko Harga Gas', NULL, NULL),
(94, 'FINANSIAL', 'F.2.3', 'Harga Energi Primer', 'Risiko Harga BBM', NULL, NULL),
(95, 'FINANSIAL', 'F.2.4', 'Harga Energi Primer', 'Risiko Harga Panas Bumi', NULL, NULL),
(96, 'FINANSIAL', 'F.2.5', 'Harga Energi Primer', 'Risiko Harga Energi Primer Lainnya', NULL, NULL),
(97, 'FINANSIAL', 'F.3.1', 'Likuiditas', 'Risiko Tunggakan', NULL, NULL),
(98, 'FINANSIAL', 'F.4.1', 'Pinjaman', 'Risiko Covenant', NULL, NULL),
(99, 'FINANSIAL', 'F.4.2', 'Pinjaman', 'Risiko Suku Bunga', NULL, NULL),
(100, 'FINANSIAL', 'F.4.3', 'Pinjaman', 'Risiko Debt Repayment', NULL, NULL),
(101, 'FINANSIAL', 'F.5.1', 'Pendapatan', 'Risiko Pendapatan Penjualan', NULL, NULL),
(102, 'FINANSIAL', 'F.5.2', 'Pendapatan', 'Risiko Pendapatan Lain-lain', NULL, NULL),
(103, 'FINANSIAL', 'F.6.1', 'Akunting', 'Risiko Akunting & Pelaporan', NULL, NULL),
(104, 'FINANSIAL', 'F.6.2', 'Akunting', 'Risiko Kontrol Internal', NULL, NULL),
(105, 'FINANSIAL', 'F.7.1', 'Pajak', 'Risiko Pajak', NULL, NULL),
(106, 'OPERASIONAL', 'O.1.1', 'Energi Primer', 'Risiko Kontinuitas Pasokan Batubara', NULL, NULL),
(107, 'OPERASIONAL', 'O.1.2', 'Energi Primer', 'Risiko Kualitas Batubara', NULL, NULL),
(108, 'OPERASIONAL', 'O.1.3', 'Energi Primer', 'Risiko Kontinuitas Pasokan Gas', NULL, NULL),
(109, 'OPERASIONAL', 'O.1.4', 'Energi Primer', 'Risiko Kontinuitas Pasokan BBM', NULL, NULL),
(110, 'OPERASIONAL', 'O.1.5', 'Energi Primer', 'Risiko Bauran Energi (Felmix)', NULL, NULL),
(111, 'OPERASIONAL', 'O.2.1', 'SDM', 'Risiko Kompetensi SDM', NULL, NULL),
(112, 'OPERASIONAL', 'O.2.2', 'SDM', 'Risiko Jumlah SDM', NULL, NULL),
(113, 'OPERASIONAL', 'O.2.3', 'SDM', 'Risiko Keselamatan Kerja', NULL, NULL),
(114, 'OPERASIONAL', 'O.2.4', 'SDM', 'Risiko Kesejahteraan Pekerja', NULL, NULL),
(115, 'OPERASIONAL', 'O.2.5', 'SDM', 'Risiko Outsourcing', NULL, NULL),
(116, 'OPERASIONAL', 'O.3.1', 'Sistem Tenaga Listrik', 'Risiko Cadangan Daya Listrik', NULL, NULL),
(117, 'OPERASIONAL', 'O.3.2', 'Sistem Tenaga Listrik', 'Risiko Take or Pay', NULL, NULL),
(118, 'OPERASIONAL', 'O.3.3', 'Sistem Tenaga Listrik', 'Risiko Optimalisasi Operasi Sistem Tenaga Listrik', NULL, NULL),
(119, 'OPERASIONAL', 'O.4.1', 'Pembangkitan', 'Risiko Ketersediaan Pembangkitan', NULL, NULL),
(120, 'OPERASIONAL', 'O.4.2', 'Pembangkitan', 'Risiko Keandalan Pembangkitan', NULL, NULL),
(121, 'OPERASIONAL', 'O.4.3', 'Pembangkitan', 'Risiko Derating Pembangkitan', NULL, NULL),
(122, 'OPERASIONAL', 'O.4.4', 'Pembangkitan', 'Risiko Efisiensi Pembangkitan', NULL, NULL),
(123, 'OPERASIONAL', 'O.4.5', 'Pembangkitan', 'Risiko IPP', NULL, NULL),
(124, 'OPERASIONAL', 'O.5.1', 'Penyaluran', 'Risiko Ketersediaan Penyaluran', NULL, NULL),
(125, 'OPERASIONAL', 'O.5.2', 'Penyaluran', 'Risiko Keandalan Penyaluran', NULL, NULL),
(126, 'OPERASIONAL', 'O.6.1', 'Distribusi', 'Risiko Ketersediaan Jaringan Distribusi', NULL, NULL),
(127, 'OPERASIONAL', 'O.6.2', 'Distribusi', 'Risiko Keandalan Jaringan Distribusi', NULL, NULL),
(128, 'OPERASIONAL', 'O.6.3', 'Distribusi', 'Risiko Pertumbuhan Konsumsi Energi Listrik', NULL, NULL),
(129, 'OPERASIONAL', 'O.7.1', 'Pelayanan Pelanggan', 'Risiko GCG Penyambungan Baru / Tambah Daya', NULL, NULL),
(130, 'OPERASIONAL', 'O.7.2', 'Pelayanan Pelanggan', 'Risiko GCG Pembacaan Meter', NULL, NULL),
(131, 'OPERASIONAL', 'O.7.3', 'Pelayanan Pelanggan', 'Risiko GCG Pelayanan Gangguan', NULL, NULL),
(132, 'OPERASIONAL', 'O.7.4', 'Pelayanan Pelanggan', 'Risiko Keterbatasan Suplai', NULL, NULL),
(133, 'OPERASIONAL', 'O.7.5', 'Pelayanan Pelanggan', 'Risiko Ekspektasi Pelanggan', NULL, NULL),
(134, 'OPERASIONAL', 'O.7.6', 'Pelayanan Pelanggan', 'Risiko Kualitas Layanan', NULL, NULL),
(135, 'OPERASIONAL', 'O.8.1', 'Teknologi', 'Risiko Obsolete Teknologi', NULL, NULL),
(136, 'OPERASIONAL', 'O.8.2', 'Teknologi', 'Risiko Security Teknologi', NULL, NULL),
(137, 'OPERASIONAL', 'O.9.1', 'Bencana Alam', 'Risiko Bencana Lokal', NULL, NULL),
(138, 'OPERASIONAL', 'O.9.2', 'Bencana Alam', 'Risiko Bencana Nasional (Force Majeur)', NULL, NULL),
(139, 'OPERASIONAL', 'O.9.3', 'Bencana Alam', 'Risiko Terorisme / Terrorisme', NULL, NULL),
(140, 'PROYEK', 'P.1.1', 'Perencanaan & Desain', 'Risiko Kelayakan Proyek', NULL, NULL),
(141, 'PROYEK', 'P.1.2', 'Perencanaan & Desain', 'Risiko Desain Proyek', NULL, NULL),
(142, 'PROYEK', 'P.2.1', 'Pendanaan Proyek', 'Risiko Sumber Dana', NULL, NULL),
(143, 'PROYEK', 'P.2.2', 'Pendanaan Proyek', 'Risiko Financial Closing', NULL, NULL),
(144, 'PROYEK', 'P.2.3', 'Pendanaan Proyek', 'Risiko Disbursement', NULL, NULL),
(145, 'PROYEK', 'P.3.1', 'Pengadaan Proyek', 'Risiko Nilai Proyek (HPS)', NULL, NULL),
(146, 'PROYEK', 'P.3.2', 'Pengadaan Proyek', 'Risiko Kualitas Kontraktor', NULL, NULL),
(147, 'PROYEK', 'P.3.3', 'Pengadaan Proyek', 'Risiko Gagal Lelang', NULL, NULL),
(148, 'PROYEK', 'P.4.1', 'Konstruksi', 'Risiko Waktu Penyelesaian Proyek', NULL, NULL),
(149, 'PROYEK', 'P.4.2', 'Konstruksi', 'Risiko Kualitas Material / Jasa', NULL, NULL),
(150, 'PROYEK', 'P.4.3', 'Konstruksi', 'Risiko Pembayaran Termin Proyek', NULL, NULL),
(151, 'PROYEK', 'P.5.1', 'Risiko Pasca Konstruksi', 'Risiko Serah Terima Proyek', NULL, NULL),
(152, 'PROYEK', 'P.5.2', 'Risiko Pasca Konstruksi', 'Risiko Performance Pasca Proyek', NULL, NULL),
(153, 'PROYEK', 'P.5.3', 'Risiko Pasca Konstruksi', 'Risiko Garansi Hasil Pekerjaan', NULL, NULL),
(154, 'KEPATUHAN', 'K.1.1', 'Aspek Legal', 'Risiko Kerjasama Pihak Ketiga', NULL, NULL),
(155, 'KEPATUHAN', 'K.1.2', 'Aspek Legal', 'Risiko Hak Atas Kekayaan Intelektual (HAKI)', NULL, NULL),
(156, 'KEPATUHAN', 'K.1.3', 'Aspek Legal', 'Risiko Tuntutan Hukum', NULL, NULL),
(157, 'KEPATUHAN', 'K.1.4', 'Aspek Legal', 'Risiko Perijinan', NULL, NULL),
(158, 'KEPATUHAN', 'K.1.5', 'Aspek Legal', 'Risiko Pembebasan Tanah', NULL, NULL),
(159, 'KEPATUHAN', 'K.2.1', 'Etika & Kecurangan (Fraud)', 'Risiko Etika', NULL, NULL),
(160, 'KEPATUHAN', 'K.2.2', 'Etika & Kecurangan (Fraud)', 'Risiko Kecurangan / Korupsi', NULL, NULL),
(161, 'KEPATUHAN', 'K.3.1', 'Lingkungan', 'Risiko Dampak Lingkungan', NULL, NULL),
(162, 'KEPATUHAN', 'K.3.2', 'Lingkungan', 'Risiko Sosial / Politik / Budaya', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_region`
--

CREATE TABLE `master_region` (
  `id` int NOT NULL,
  `api_id` int DEFAULT NULL,
  `kd_region_sap` varchar(50) DEFAULT NULL,
  `kd_region` varchar(50) DEFAULT NULL,
  `nama_region` varchar(255) DEFAULT NULL,
  `masa_persiapan` int DEFAULT NULL,
  `kd_provinsi` varchar(50) DEFAULT NULL,
  `lat` varchar(50) DEFAULT NULL,
  `lon` varchar(50) DEFAULT NULL,
  `manager` varchar(255) DEFAULT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `alamat` text,
  `telepon` varchar(50) DEFAULT NULL,
  `facsimile` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `kode_surat` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `master_region`
--

INSERT INTO `master_region` (`id`, `api_id`, `kd_region_sap`, `kd_region`, `nama_region`, `masa_persiapan`, `kd_provinsi`, `lat`, `lon`, `manager`, `jabatan`, `kota`, `alamat`, `telepon`, `facsimile`, `email`, `kode_surat`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'SUL2', '01', 'UP SULAWESI 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Bonto Ramba No.9, Mannuruki, Kec. Tamalate, Kota Makassar, Sulawesi Selatan 90223', NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(2, 2, 'SUL1', '02', 'UP SULAWESI 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Tikala Ares No.32, Dikrama, guntur, Kec. Tikala, Kota Manado, Sulawesi Utara 95123', NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(3, 3, 'KAL1', '03', 'UP KALIMANTAN 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Parit H. Husin II, Bangka Belitung Darat, Kec. Pontianak Tenggara, Kota Pontianak, Kalimantan Barat 78116', NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(4, 4, 'KAL2', '04', 'UP KALIMANTAN 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Pangeran Hidayatullah No.22, Loktabat Utara, Kec. Banjarbaru Utara, Kota Banjar Baru, Kalimantan Selatan 70714', NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(5, 5, 'KAL3', '05', 'UP KALIMANTAN 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. RE Martadinata, Gunungsari Ilir, Kec. Balikpapan Tengah, Kota Balikpapan, Kalimantan Timur 76113', NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(6, 6, 'NUSA', '07', 'UP NUSA TENGGARA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Bung Karno No. 26, Mataram Timur, Mataram, Kota Mataram, Nusa Tenggara Barat, 83127', NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(7, 7, 'PAPA', '08', 'UP PAPUA', 15, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl.Perum Jaya asri, Entrop, Distrik Jayapura Selatan, Kota Jayapura, Papua 99223', NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(8, 8, 'PUST', '09', 'KANTOR PUSAT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Letjen Zaini Azhar Maulani, Gn. Bahagia, Kecamatan Balikpapan Selatan, Kota Balikpapan, Kalimantan Timur 76114', NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(9, 453, 'MAMA', '10', 'UP MALUKU', 15, NULL, NULL, NULL, NULL, NULL, NULL, 'Said Perintah No.53, Kel Ahusen, Kec. Sirimau, Kota Ambon, Maluku 97126', NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(10, 454, 'MALU', '11', 'UP MALUKU UTARA', 15, NULL, NULL, NULL, NULL, NULL, NULL, 'Jl. Bandara Sultan Babullah, Kelurahan Tabam, Kec. Kota Ternate Utara, Prov. Maluku Utara 97728', NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(11, 688, 'ASMU', '13', 'UP PEMBANGKIT ACEH DAN SUMATERA BAGIAN UTARA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(12, 689, 'BSSJ', '14', 'UP PEMBANGKIT BANGKA BELITUNG, SUMATERA SELATAN, DAN JAWA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL),
(13, 690, 'RKPR', '15', 'UP PEMBANGKIT RIAU DAN KEPULAUAN RIAU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 19:41:14', '2026-05-19 19:41:14', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_unit`
--

CREATE TABLE `master_unit` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_unit` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_user`
--

CREATE TABLE `master_user` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telpon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `master_auditee_id` bigint UNSIGNED NOT NULL,
  `master_area_id` int NOT NULL,
  `master_akses_user_id` bigint UNSIGNED NOT NULL DEFAULT '2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_user`
--

INSERT INTO `master_user` (`id`, `nama`, `username`, `nip`, `email`, `no_telpon`, `jabatan`, `password`, `master_auditee_id`, `master_area_id`, `master_akses_user_id`, `created_at`, `updated_at`) VALUES
(1, 'System Administrator', 'superadmin', 'SUPERADMIN001', 'superadmin@pcn.co.id', '000000000000', 'System Administrator', '$2y$12$nDSJx9ZGmQhUR2JkqyCNB.heX1zP8pciO5v4OHOwE9UrDvdpUsBLW', 1, 1, 5, '2026-05-19 22:21:11', '2026-05-19 22:21:11'),
(2, 'DINAR AFIDAH PRAVITA PUTRI', 'dinar.afidah', '01253007PST', 'dinar.afidah@pcn.co.id', '081234567001', 'JUNIOR OFFICER AUDITOR', '$2y$12$qaxGvEeMCawTfX7i6mTUPu8TSK7hsagXTgh9vG0U/2GOZohn1U/OW', 1, 1, 3, '2026-05-19 22:21:11', '2026-05-19 22:21:11'),
(3, 'ASMAN SPI', 'asman.spi', '85012345SPI', 'asman.spi@pcn.co.id', '081234567002', 'ASISTEN MANAGER SPI', '$2y$12$wGwYVEKTdptLxDJAdg1M6OGpyVfhXVV8ZTgx041vRT1joJM2NMdMS', 1, 1, 2, '2026-05-19 22:21:12', '2026-05-19 22:21:12'),
(4, 'AGIL FRASSETYO', 'agil.frassetyo', '84091962', 'agil.frassetyo@pcn.co.id', '081234567003', 'KEPALA SATUAN PENGAWAS INTERNAL', '$2y$12$wt0iZVacNDpd1gWDydmTv.Uav2OE//nuVI2KaoSj21XqxIa8cPoES', 1, 1, 1, '2026-05-19 22:21:12', '2026-05-19 22:21:12'),
(5, 'DEWI SATYA NINGSIH', 'dewi.satya', '8010035TRK', 'dewi.satya@pcn.co.id', '081234567004', 'SUPERVISOR AKUNTANSI', '$2y$12$t62ESb3eM5P.iY2uxAxbK.bLKXEcjzB2hvOfbJXPnRP5eH98oq0FO', 2, 1, 4, '2026-05-19 22:21:12', '2026-05-19 22:21:12'),
(6, 'ANDI RIPANSYAH', 'andi.ripansyah', '7610036TRK', 'andi.ripansyah@pcn.co.id', '081234567005', 'ASMAN KEUANGAN & ANGGARAN', '$2y$12$mLnUfgNG8UIeLyrwxPyWmeTOoAcgSqWfmdV8RrOhPMulMKOvYKX/C', 2, 1, 4, '2026-05-19 22:21:12', '2026-05-19 22:21:12'),
(7, 'YUSUF SAEFUDIN', 'yusuf.saefudin', '7510005TRK', 'yusuf.saefudin@pcn.co.id', '081234567006', 'MANAGER KEUANGAN', '$2y$12$51AMH7Rju7RvTPP44GkZCOMkqmZHzgxyHPJG7oSOb81UMcqHg7dlO', 2, 1, 4, '2026-05-19 22:21:12', '2026-05-19 22:21:12'),
(8, 'BUDI MULYONO', 'budi.mulyono', '8207283REN', 'budi.mulyono@pcn.co.id', '081234567007', 'ASMAN PERENCANAAN DAN PENGEMBANGAN USAHA', '$2y$12$tH37KuBttbfvQmmnsuBMvuV6zWPBofQjsUztUmGc1R6uGbKy6scBq', 3, 1, 4, '2026-05-19 22:21:13', '2026-05-19 22:21:13'),
(9, 'RIZKA ABDULLAH', 'rizka.abdullah', '8507284REN', 'rizka.abdullah@pcn.co.id', '081234567008', 'MANAGER PERENCANAAN DAN PENGEMBANGAN USAHA', '$2y$12$VvsQX.NipJMpDD4OQTLhs.6KS0Pxg0F1BJCwflt/pRTSok1AEDVF6', 3, 1, 4, '2026-05-19 22:21:13', '2026-05-19 22:21:13'),
(10, 'FATAHUDDIN YOGI AMIBOWO', 'fatahuddin.yogi.renus', '7905004BREN', 'fatahuddin.yogi.renus@pcn.co.id', '081234567009', 'DIREKTUR OPERASI & PENGEMBANGAN USAHA', '$2y$12$2ZkAkQXRNqW6HInQgjY12uqlEKZBImCiL9RJQE2.3lhIPg1F.Uahq', 3, 1, 4, '2026-05-19 22:21:13', '2026-05-19 22:21:13'),
(11, 'WAHYU KURNIAWAN', 'wahyu.kurniawan', '6724001OPS', 'wahyu.kurniawan@pcn.co.id', '081234567010', 'SUPERVISOR LOGISTIK', '$2y$12$vVMOY8JZhujgZyaJ/IgqTuTwBDuBVaQsquHFkZPJJWgqG/PRSLrA.', 4, 1, 4, '2026-05-19 22:21:13', '2026-05-19 22:21:13'),
(12, 'ROESMIN', 'roesmin', '6824002OPS', 'roesmin@pcn.co.id', '081234567011', 'ASMAN OPHARDUNG', '$2y$12$XLVIhXGZq/6GK66ntNXyPeGLS2xSryKuQvbXQPZvWua3IluFEoR3S', 4, 1, 4, '2026-05-19 22:21:14', '2026-05-19 22:21:14'),
(13, 'FATAHUDDIN YOGI AMIBOWO', 'fatahuddin.yogi.ops', '7905004BOPS', 'fatahuddin.yogi.ops@pcn.co.id', '081234567012', 'DIREKTUR OPERASI & PENGEMBANGAN USAHA', '$2y$12$3WHsCHW8YQpys8.05uYg6eSvP8dZT8xCZUHGBpTy/14PQZGg.NEDy', 4, 1, 4, '2026-05-19 22:21:14', '2026-05-19 22:21:14'),
(14, 'PRASETIO NINGSIH', 'prasetio.ningsih', '6924001HC', 'prasetio.ningsih@pcn.co.id', '081234567013', 'SPV. PELAYANAN HUMAN CAPITAL', '$2y$12$hmH4uMHZBGP0Fc/zYJtn3uA5.4f.6vw0Ta4nE6JQA1Mro0SJGvIja', 5, 1, 4, '2026-05-19 22:21:14', '2026-05-19 22:21:14'),
(15, 'EMAN SLAMET WIDODO', 'eman.slamet', '6924002HC', 'eman.slamet@pcn.co.id', '081234567014', 'ASMAN HUMAN CAPITAL', '$2y$12$pHuDu1UxgCtSxZf7HQjq9eA0KF3blWfLni4Uzxy93HBPuTIJ50nLS', 5, 1, 4, '2026-05-19 22:21:14', '2026-05-19 22:21:14'),
(16, 'YAINUS SHOLEH', 'yainus.sholeh', '6924003HC', 'yainus.sholeh@pcn.co.id', '081234567015', 'MANAGER HUMAN CAPITAL DAN ADMINISTRASI UMUM', '$2y$12$9AP1FP7kiVZ2WuWLFscx0uR3gH58U9Hjb.wLKYdlK8XgpW6fWPmTi', 5, 1, 4, '2026-05-19 22:21:14', '2026-05-19 22:21:14'),
(17, 'NURUL AZISAH', 'nurul.azisah', '7208027SEK', 'nurul.azisah@pcn.co.id', '081234567016', 'JUNIOR OFFICER KOMUNIKASI DAN TATA KELOLA', '$2y$12$4rz69hdrLAAlf04XZQi8VeTgOgkvz6y5HLBI7U.6qI5XRcaHYSqKC', 6, 1, 4, '2026-05-19 22:21:15', '2026-05-19 22:21:15'),
(18, 'ROMY HARYADI', 'romy.haryadi', '7208028SEK', 'romy.haryadi@pcn.co.id', '081234567017', 'ASMAN HUKUM DAN TATA KELOLA', '$2y$12$oKIVupVf8EjmZJNOZgJd1ubOaCQmmIbevvIvAc29MlPl3/vVVW0Ke', 6, 1, 4, '2026-05-19 22:21:15', '2026-05-19 22:21:15'),
(19, 'IRAWAN HERNANDA', 'irawan.hernanda.sekper', '76020041SEK', 'irawan.hernanda.sekper@pcn.co.id', '081234567018', 'DIREKTUR UTAMA', '$2y$12$s3vZ9prS3jMc8zJDFJvWxOIjaY3B5QrK7r3fxBvhlydDWm8.tYXnm', 6, 1, 4, '2026-05-19 22:21:15', '2026-05-19 22:21:15'),
(20, 'IRAWAN HERNANDA', 'irawan.hernanda', '76020041BOD', 'irawan.hernanda@pcn.co.id', '081234567019', 'DIREKTUR UTAMA', '$2y$12$XYa6Ew58BUDkzqdLI4VwueBM50fTAPZFr497xK8KIw7jy4i11Kti.', 7, 1, 6, '2026-05-19 22:21:15', '2026-05-19 22:21:15'),
(21, 'ANDRY APRIAWAN', 'andry.apriawan', '7705003BOD', 'andry.apriawan@pcn.co.id', '081234567020', 'DIREKTUR KEUANGAN DAN ADMINISTRASI', '$2y$12$boJEqAg1k5OsBgCbGlvVNO6TqCcm0Y52W19NgfV7Hh/ujzqjFe.Si', 7, 1, 6, '2026-05-19 22:21:15', '2026-05-19 22:21:15'),
(22, 'FATAHUDDIN YOGI AMIBOWO', 'fatahuddin.yogi', '7905004BOD', 'fatahuddin.yogi@pcn.co.id', '081234567021', 'DIREKTUR OPERASI & PENGEMBANGAN USAHA', '$2y$12$y2CBVWeiZvlEcrNTrLwEfu8Usl6O0A/UZPVIk0CnpEZBRC7YA7Wa6', 7, 1, 6, '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(23, 'IRVAN SANJAYA', 'irvan.sanjaya', '90000001ADM', 'irvan.sanjaya@pcn.co.id', '081234567022', 'ASMAN IT', '$2y$12$K1.iNVG0A/aNy3vnSz5H7OcDNuvWvdohXUU1McvJehp/XN90Z.wWi', 8, 1, 5, '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(24, 'OKTO INDRA LESMANA', 'okto.indra', '8013084KAL', 'okto.indra@pcn.co.id', '081234567023', 'JUNIOR OFFICER OPERASI CABANG/SITE', '$2y$12$zqj2A2mnuv2LW.ll8kxw7urVpFOeqjgghmMAG84WyJDgd7JVsTgwW', 9, 1, 4, '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(25, 'JOKO SUTRISNO', 'joko.sutrisno', '8013085KAL', 'joko.sutrisno@pcn.co.id', '081234567024', 'SUPERVISOR OPERASI', '$2y$12$1zTpyhY0rCQxCWUfzfqwdut.kT88jz/Zt5ppy1wxh.RZw/nmejeYa', 9, 1, 4, '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(26, 'DONY BAYUMAR', 'dony.bayumar', '8013086KAL', 'dony.bayumar@pcn.co.id', '081234567025', 'MANAGER CABANG KALTIMRA', '$2y$12$UakybjwnHaoqjpfWVCzsOeGEhre5qy5dwXM3j9c7sDmGODZjfUlPq', 9, 1, 4, '2026-05-19 22:21:16', '2026-05-19 22:21:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_05_18_000000_create_master_kode_aoi_table', 1),
(5, '2024_05_18_000001_create_master_kode_risk_table', 1),
(6, '2024_05_18_000002_create_master_auditee_table', 1),
(7, '2024_05_18_000003_create_master_akses_user_table', 1),
(8, '2024_05_18_000004_create_master_user_table', 1),
(9, '2024_05_18_000005_create_perencanaan_audit_table', 1),
(10, '2025_07_12_020746_create_program_kerja_audit_table', 1),
(11, '2025_07_12_020838_create_pka_risk_based_audit_table', 1),
(12, '2025_07_12_020848_create_pka_milestone_table', 1),
(13, '2025_07_12_020901_create_pka_dokumen_table', 1),
(14, '2025_07_12_042235_create_jadwal_pkpt_audits_table', 1),
(15, '2025_07_12_084055_create_walkthrough_audit_table', 1),
(16, '2025_07_12_090940_create_tod_bpm_audit_table', 1),
(17, '2025_07_12_091017_create_tod_bpm_evaluasi_table', 1),
(18, '2025_07_12_091100_create_toe_audit_table', 1),
(19, '2025_07_12_091101_create_toe_evaluasi_table', 1),
(20, '2025_07_12_091200_create_entry_meeting_table', 1),
(21, '2025_07_12_170645_create_pelaporan_hasil_audit_table', 1),
(22, '2025_07_12_170835_create_pelaporan_temuan_table', 1),
(23, '2025_07_12_184130_create_pelaporan_isi_lha_table', 1),
(24, '2025_07_13_000000_create_penutup_lha_rekomendasi_table', 1),
(25, '2025_07_13_010000_create_exit_meeting_uploads_table', 1),
(26, '2025_07_13_010100_create_lha_lhk_uploads_table', 1),
(27, '2025_07_13_010200_create_nota_dinas_uploads_table', 1),
(28, '2025_07_14_000000_create_penutup_lha_tindak_lanjut_table', 1),
(29, '2025_07_14_082002_add_approve_to_exit_meeting_uploads_table', 1),
(30, '2025_07_14_082619_add_approve_to_lha_lhk_uploads_table', 1),
(31, '2025_07_15_030805_create_realisasi_audits_table', 1),
(32, '2025_07_15_040000_add_approval_fields_to_realisasi_audits_table', 1),
(33, '2025_08_04_024051_add_master_akses_user_id_to_master_user_table', 1),
(34, '2025_08_05_043557_add_program_kerja_audit_id_to_entry_meeting_table', 1),
(35, '2025_08_05_063146_add_plan_actual_check_to_walkthrough_audit_table', 1),
(36, '2025_08_05_070236_add_rejection_reason_to_walkthrough_audit_table', 1),
(37, '2025_08_05_072545_add_rejection_reason_to_entry_meeting_table', 1),
(38, '2025_08_05_080509_add_rejection_reason_to_tod_bpm_audit_table', 1),
(39, '2025_08_05_080657_add_rejection_reason_to_toe_audit_table', 1),
(40, '2025_08_06_164856_add_alasan_reject_to_pelaporan_isi_lha_table', 1),
(41, '2025_08_06_164913_add_alasan_reject_to_pelaporan_hasil_audit_table', 1),
(42, '2025_08_06_182525_create_monitoring_tindak_lanjut_table', 1),
(43, '2025_08_06_231145_add_direktorat_and_divisi_cabang_to_master_auditee_table', 1),
(44, '2025_08_06_233249_add_file_undangan_and_file_absensi_to_realisasi_audit_table', 1),
(45, '2025_08_12_055741_add_nomor_urut_and_tahun_to_pelaporan_hasil_audit_table', 1),
(46, '2025_08_12_064249_add_nomor_urut_iss_to_pelaporan_temuan_table', 1),
(47, '2025_08_12_071307_modify_nomor_iss_field_in_pelaporan_hasil_audit_table', 1),
(48, '2025_08_20_065109_restructure_pelaporan_for_multiple_iss', 1),
(49, '2025_08_21_004102_restructure_pelaporan_hasil_audit_for_multiple_iss', 1),
(50, '2025_08_21_012028_add_missing_fields_to_pelaporan_temuan_table', 1),
(51, '2025_08_21_020410_consolidate_root_cause_fields_in_pelaporan_temuan', 1),
(52, '2025_08_22_032435_add_missing_fields_to_pelaporan_temuan_table_v2', 1),
(53, '2025_08_22_032729_update_penutup_lha_rekomendasi_foreign_key', 1),
(54, '2025_08_23_052953_add_alasan_reject_to_penutup_lha_rekomendasi_table', 1),
(55, '2025_08_23_054143_fix_status_tindak_lanjut_enum_values', 1),
(56, '2025_08_23_054719_ensure_username_unique_in_master_user_table', 1),
(57, '2025_11_23_121012_add_file_bpm_to_walkthrough_audit_table', 1),
(58, '2025_11_23_123733_add_resiko_kontrol_kka_tod_to_tod_bpm_audit_table', 1),
(59, '2025_11_23_131944_add_pemilihan_sampel_audit_file_kka_toe_resiko_kontrol_to_toe_audit_table', 1),
(60, '2025_11_23_133645_add_timestamps_to_master_user_table', 1),
(61, '2025_11_23_134952_create_penutup_lha_rekomendasi_pic_table', 1),
(62, '2025_12_05_131958_create_master_jenis_audit_table', 1),
(63, '2025_12_05_132005_add_jenis_audit_id_to_perencanaan_audit_table', 1),
(64, '2025_12_12_033940_add_new_akses_user_to_master_akses_user_table', 1),
(65, '2025_12_12_053253_add_approval_level_fields_to_entry_meeting_table', 1),
(66, '2025_12_12_053307_add_approval_level_fields_to_walkthrough_audit_table', 1),
(67, '2025_12_12_053319_add_approval_level_fields_to_tod_bpm_audit_table', 1),
(68, '2025_12_12_053332_add_approval_level_fields_to_toe_audit_table', 1),
(69, '2025_12_12_053344_add_approval_level_fields_to_realisasi_audits_table', 1),
(70, '2025_12_12_062920_add_approval_level_fields_to_pelaporan_hasil_audit_table', 1),
(71, '2025_12_12_171409_add_email_phone_jabatan_to_master_user_table', 1),
(72, '2025_12_12_172701_add_approval_level_fields_to_penutup_lha_rekomendasi_table', 1),
(73, '2025_12_13_040457_add_auditee_akses_to_master_akses_user_table', 1),
(74, '2025_12_13_120000_add_jenis_audit_id_to_pelaporan_hasil_audit_table', 1),
(75, '2025_12_13_120001_modify_kode_spi_to_string_in_pelaporan_hasil_audit_table', 1),
(76, '2025_12_13_120002_remove_po_audit_konsul_from_pelaporan_hasil_audit_table', 1),
(77, '2025_12_16_072448_update_akses_user_for_create_page', 1),
(78, '2025_12_18_062421_add_pic_type_to_penutup_lha_rekomendasi_pic_table', 1),
(79, '2026_05_12_031458_create_master_unit_table', 1),
(80, '2026_05_12_032632_add_unit_to_master_user_table', 1),
(81, '2026_05_12_033219_change_master_unit_id_required_in_master_user', 1),
(82, '2026_05_14_100000_create_pka_proses_bisnis_table', 1),
(83, '2026_05_14_100001_create_pka_risiko_table', 1),
(84, '2026_05_14_100002_create_pka_kontrol_table', 1),
(85, '2026_05_16_073646_create_tod_bpm_risiko_table', 1),
(86, '2026_05_16_073647_create_tod_bpm_kontrol_table', 1),
(87, '2026_05_16_073647_create_toe_risiko_table', 1),
(88, '2026_05_16_073648_create_toe_kontrol_table', 1),
(89, '2026_05_16_073852_make_pengendalian_eksisting_nullable_in_toe_audit', 1),
(90, '2026_05_18_000001_add_approval_level_fields_to_remaining_tables', 1),
(91, '2026_05_18_000001_add_last_notified_at_to_penutup_lha_rekomendasi_table', 1),
(92, '2026_05_18_000002_create_email_notification_logs_table', 1),
(93, '2026_05_18_104611_add_approval_level_fields_to_program_kerja_audit_table', 1),
(94, '2026_05_20_140000_add_master_area_id_to_master_user_table', 2),
(95, '2026_05_20_140100_change_unit_id_to_area_id_in_perencanaan_audit_table', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `monitoring_tindak_lanjut`
--

CREATE TABLE `monitoring_tindak_lanjut` (
  `id` bigint UNSIGNED NOT NULL,
  `objek_pemeriksaan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aoi_count` int NOT NULL DEFAULT '0',
  `rekomendasi_count` int NOT NULL DEFAULT '0',
  `tindak_lanjut_target` int NOT NULL DEFAULT '0',
  `tindak_lanjut_real` int NOT NULL DEFAULT '0',
  `sisa_target` int NOT NULL DEFAULT '0',
  `sisa_real` int NOT NULL DEFAULT '0',
  `bulan_jan_target` int NOT NULL DEFAULT '0',
  `bulan_jan_real` int NOT NULL DEFAULT '0',
  `bulan_feb_target` int NOT NULL DEFAULT '0',
  `bulan_feb_real` int NOT NULL DEFAULT '0',
  `bulan_mar_target` int NOT NULL DEFAULT '0',
  `bulan_mar_real` int NOT NULL DEFAULT '0',
  `bulan_apr_target` int NOT NULL DEFAULT '0',
  `bulan_apr_real` int NOT NULL DEFAULT '0',
  `bulan_mei_target` int NOT NULL DEFAULT '0',
  `bulan_mei_real` int NOT NULL DEFAULT '0',
  `bulan_jun_target` int NOT NULL DEFAULT '0',
  `bulan_jun_real` int NOT NULL DEFAULT '0',
  `bulan_jul_target` int NOT NULL DEFAULT '0',
  `bulan_jul_real` int NOT NULL DEFAULT '0',
  `bulan_ags_target` int NOT NULL DEFAULT '0',
  `bulan_ags_real` int NOT NULL DEFAULT '0',
  `bulan_sep_target` int NOT NULL DEFAULT '0',
  `bulan_sep_real` int NOT NULL DEFAULT '0',
  `bulan_okt_target` int NOT NULL DEFAULT '0',
  `bulan_okt_real` int NOT NULL DEFAULT '0',
  `is_category` tinyint(1) NOT NULL DEFAULT '0',
  `is_total` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `nota_dinas_uploads`
--

CREATE TABLE `nota_dinas_uploads` (
  `id` bigint UNSIGNED NOT NULL,
  `pelaporan_hasil_audit_id` bigint UNSIGNED NOT NULL,
  `file_nota_dinas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tujuan_nota_dinas` enum('dirut','dekom','auditee') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelaporan_hasil_audit`
--

CREATE TABLE `pelaporan_hasil_audit` (
  `id` bigint UNSIGNED NOT NULL,
  `nomor_urut` int UNSIGNED NOT NULL COMMENT 'Nomor urut untuk generate nomor LHA/LHK',
  `tahun` year NOT NULL COMMENT 'Tahun untuk generate nomor LHA/LHK',
  `perencanaan_audit_id` bigint UNSIGNED NOT NULL,
  `nomor_lha_lhk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_lha_lhk` enum('LHA','LHK') COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_spi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_audit_id` bigint UNSIGNED DEFAULT NULL,
  `nomor_iss` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_temuan` text COLLATE utf8mb4_unicode_ci,
  `kode_aoi_id` bigint UNSIGNED DEFAULT NULL,
  `kode_risk_id` bigint UNSIGNED DEFAULT NULL,
  `permasalahan` text COLLATE utf8mb4_unicode_ci,
  `penyebab_people` text COLLATE utf8mb4_unicode_ci,
  `penyebab_process` text COLLATE utf8mb4_unicode_ci,
  `penyebab_policy` text COLLATE utf8mb4_unicode_ci,
  `penyebab_system` text COLLATE utf8mb4_unicode_ci,
  `penyebab_eksternal` text COLLATE utf8mb4_unicode_ci,
  `kriteria` text COLLATE utf8mb4_unicode_ci,
  `dampak_terjadi` text COLLATE utf8mb4_unicode_ci,
  `dampak_potensi` text COLLATE utf8mb4_unicode_ci,
  `signifikan` enum('Tinggi','Medium','Rendah') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approval` enum('pending','approved_level1','approved','rejected_level1','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `alasan_reject` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_level1` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level1` timestamp NULL DEFAULT NULL,
  `rejected_by_level1` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level1` timestamp NULL DEFAULT NULL,
  `rejection_reason_level1` text COLLATE utf8mb4_unicode_ci,
  `approved_by_level2` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level2` timestamp NULL DEFAULT NULL,
  `rejected_by_level2` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level2` timestamp NULL DEFAULT NULL,
  `rejection_reason_level2` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pelaporan_hasil_audit`
--

INSERT INTO `pelaporan_hasil_audit` (`id`, `nomor_urut`, `tahun`, `perencanaan_audit_id`, `nomor_lha_lhk`, `jenis_lha_lhk`, `kode_spi`, `jenis_audit_id`, `nomor_iss`, `hasil_temuan`, `kode_aoi_id`, `kode_risk_id`, `permasalahan`, `penyebab_people`, `penyebab_process`, `penyebab_policy`, `penyebab_system`, `penyebab_eksternal`, `kriteria`, `dampak_terjadi`, `dampak_potensi`, `signifikan`, `status_approval`, `alasan_reject`, `approved_by`, `approved_at`, `approved_by_level1`, `approved_at_level1`, `rejected_by_level1`, `rejected_at_level1`, `rejection_reason_level1`, `approved_by_level2`, `approved_at_level2`, `rejected_by_level2`, `rejected_at_level2`, `rejection_reason_level2`, `created_at`, `updated_at`) VALUES
(1, 1, '2024', 1, '001.LHA/PO/SPI.01.02/SPI.PCN/2024', 'LHA', 'SPI.01.02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 2, '2024', 1, '002.LHK/KONSUL/SPI.01.03/SPI.PCN/2024', 'LHK', 'SPI.01.03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, 1, '2026-05-21 22:45:10', 1, '2026-05-21 22:45:04', NULL, NULL, NULL, 1, '2026-05-21 22:45:10', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 3, '2024', 1, '003.LHA/PO/SPI.01.04/SPI.PCN/2024', 'LHA', 'SPI.01.04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 1, '2026', 9, 'LHK/DUMMY/9994', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 2, '2026', 10, 'LHK/DUMMY/8549', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 3, '2026', 11, 'LHK/DUMMY/8585', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 6, '2026', 14, 'LHK/DUMMY/7270', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 8, '2026', 16, 'LHK/DUMMY/5099', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 10, '2026', 18, 'LHK/DUMMY/8812', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 11, '2026', 19, 'LHK/DUMMY/4484', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 12, '2026', 20, 'LHK/DUMMY/9262', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 15, '2026', 23, 'LHK/DUMMY/2478', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 18, '2026', 26, 'LHK/DUMMY/3151', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(14, 19, '2026', 27, 'LHK/DUMMY/9471', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(15, 20, '2026', 28, 'LHK/DUMMY/6552', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(16, 22, '2026', 30, 'LHK/DUMMY/5664', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(17, 23, '2026', 31, 'LHK/DUMMY/9493', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(18, 24, '2026', 32, 'LHK/DUMMY/5824', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(19, 25, '2026', 33, 'LHK/DUMMY/5659', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(20, 30, '2026', 38, 'LHK/DUMMY/8502', 'LHK', 'SPI', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelaporan_isi_lha`
--

CREATE TABLE `pelaporan_isi_lha` (
  `id` bigint UNSIGNED NOT NULL,
  `pelaporan_hasil_audit_id` bigint UNSIGNED NOT NULL,
  `nomor_iss` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permasalahan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `penyebab` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kriteria` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `dampak_terjadi` text COLLATE utf8mb4_unicode_ci,
  `dampak_potensi` text COLLATE utf8mb4_unicode_ci,
  `signifikansi` enum('Tinggi','Medium','Rendah') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Medium',
  `status_approval` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `alasan_reject` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelaporan_temuan`
--

CREATE TABLE `pelaporan_temuan` (
  `id` bigint UNSIGNED NOT NULL,
  `nomor_urut_iss` int UNSIGNED NOT NULL COMMENT 'Nomor urut untuk generate nomor ISS',
  `pelaporan_hasil_audit_id` bigint UNSIGNED NOT NULL,
  `hasil_temuan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `permasalahan` text COLLATE utf8mb4_unicode_ci,
  `penyebab` text COLLATE utf8mb4_unicode_ci,
  `kriteria` text COLLATE utf8mb4_unicode_ci,
  `dampak_terjadi` text COLLATE utf8mb4_unicode_ci,
  `dampak_potensi` text COLLATE utf8mb4_unicode_ci,
  `signifikan` enum('Tinggi','Medium','Rendah') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_aoi_id` bigint UNSIGNED NOT NULL,
  `kode_risk_id` bigint UNSIGNED NOT NULL,
  `nomor_iss` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` year NOT NULL,
  `status_approval` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `alasan_reject` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pelaporan_temuan`
--

INSERT INTO `pelaporan_temuan` (`id`, `nomor_urut_iss`, `pelaporan_hasil_audit_id`, `hasil_temuan`, `permasalahan`, `penyebab`, `kriteria`, `dampak_terjadi`, `dampak_potensi`, `signifikan`, `kode_aoi_id`, `kode_risk_id`, `nomor_iss`, `tahun`, `status_approval`, `approved_by`, `approved_at`, `alasan_reject`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Dokumentasi transaksi keuangan tidak lengkap dan tidak sesuai dengan standar akuntansi yang berlaku. Beberapa transaksi tidak memiliki bukti pendukung yang memadai.', 'Kurangnya pemahaman karyawan terhadap SOP yang berlaku.', 'Kurangnya pemahaman karyawan terhadap SOP yang berlaku. Proses workflow yang tidak terstruktur dengan baik. Kebijakan yang belum jelas dan tidak terkomunikasikan dengan baik. Sistem informasi yang belum terintegrasi dengan optimal. Perubahan regulasi yang belum diadaptasi dengan cepat.', 'Sesuai dengan standar pengendalian internal yang berlaku.', 'Terjadi kesalahan dalam pencatatan transaksi keuangan.', 'Potensi kerugian finansial dan reputasi perusahaan.', 'Tinggi', 1, 1, 'ISS.001/PO PCN/SPI.01.02/01/01/2024', '2024', 'approved', 1, '2026-05-19 22:21:17', NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 2, 1, 'Proses approval transaksi keuangan tidak dilakukan sesuai dengan hierarki yang telah ditetapkan. Beberapa transaksi dengan nilai besar tidak mendapat approval dari level manajemen yang sesuai.', 'Proses approval transaksi keuangan tidak sesuai hierarki.', 'Kurangnya pemahaman terhadap kebijakan approval. Proses workflow approval yang tidak terstruktur. Kebijakan approval yang belum jelas. Sistem approval yang belum otomatis. Perubahan regulasi approval yang belum diimplementasi.', 'Sesuai dengan standar pengendalian internal yang berlaku.', 'Transaksi besar tidak mendapat approval yang sesuai.', 'Potensi fraud dan kerugian finansial.', 'Tinggi', 2, 2, 'ISS.001/PO PCN/SPI.01.02/01/02/2024', '2024', 'approved', 1, '2026-05-19 22:21:17', NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 3, 2, 'Sistem pengendalian risiko operasional belum terintegrasi dengan baik. Identifikasi dan penilaian risiko tidak dilakukan secara sistematis dan berkelanjutan.', 'Sistem pengendalian risiko operasional belum optimal.', 'Kurangnya kompetensi dalam manajemen risiko. Proses identifikasi risiko yang tidak sistematis. Kebijakan manajemen risiko yang belum komprehensif. Sistem monitoring risiko yang belum real-time. Dinamika lingkungan bisnis yang cepat berubah.', 'Sesuai dengan framework manajemen risiko yang diakui.', 'Beberapa risiko operasional tidak terdeteksi tepat waktu.', 'Potensi gangguan operasional dan kerugian bisnis.', 'Medium', 3, 3, 'ISS.002/PO PCN/SPI.01.03/01/01/2024', '2024', 'approved', 1, '2026-05-21 22:45:10', NULL, '2026-05-19 22:21:17', '2026-05-21 22:45:10'),
(4, 4, 2, 'Monitoring dan pelaporan risiko tidak dilakukan secara real-time. Informasi risiko tidak tersedia secara tepat waktu untuk pengambilan keputusan manajemen.', 'Monitoring dan pelaporan risiko tidak real-time.', 'Kurangnya awareness terhadap pentingnya monitoring risiko. Proses monitoring yang tidak terstruktur. Kebijakan monitoring yang belum jelas. Sistem monitoring yang belum otomatis. Tekanan bisnis yang mengharuskan keputusan cepat.', 'Sesuai dengan framework manajemen risiko yang diakui.', 'Informasi risiko tidak tersedia tepat waktu.', 'Potensi keputusan yang tidak optimal.', 'Medium', 4, 4, 'ISS.002/PO PCN/SPI.01.03/01/02/2024', '2024', 'approved', 1, '2026-05-21 22:45:10', NULL, '2026-05-19 22:21:17', '2026-05-21 22:45:10'),
(5, 5, 3, 'Kepatuhan terhadap regulasi sektor keuangan belum optimal. Beberapa ketentuan regulator tidak diimplementasikan dengan baik dalam proses bisnis.', 'Kepatuhan terhadap regulasi sektor keuangan belum optimal.', 'Kesadaran kepatuhan yang masih rendah di beberapa unit. Proses monitoring kepatuhan yang tidak terstruktur. Kebijakan kepatuhan yang belum terintegrasi dengan baik. Sistem pelaporan kepatuhan yang belum otomatis. Perubahan regulasi yang sering terjadi.', 'Sesuai dengan standar kepatuhan yang berlaku.', 'Beberapa pelanggaran regulasi tidak terdeteksi.', 'Potensi sanksi regulator dan kerugian reputasi.', 'Tinggi', 5, 5, 'ISS.003/PO PCN/SPI.01.04/01/01/2024', '2024', 'approved', 1, '2026-05-19 22:21:17', NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 6, 3, 'Sistem pelaporan kepatuhan tidak terintegrasi dan tidak menyediakan informasi yang komprehensif. Pelaporan kepada regulator sering terlambat dan tidak akurat.', 'Sistem pelaporan kepatuhan tidak terintegrasi.', 'Kurangnya koordinasi antar unit dalam pelaporan. Proses pelaporan yang tidak terstruktur. Kebijakan pelaporan yang belum jelas. Sistem pelaporan yang belum otomatis. Deadline regulator yang ketat.', 'Sesuai dengan standar kepatuhan yang berlaku.', 'Pelaporan kepada regulator terlambat dan tidak akurat.', 'Potensi sanksi regulator dan kerugian reputasi.', 'Tinggi', 1, 1, 'ISS.003/PO PCN/SPI.01.04/01/02/2024', '2024', 'approved', 1, '2026-05-19 22:21:17', NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 1, 4, 'Dummy Temuan 0 untuk Audit 9', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 69, 'ISS/DUMMY/4183', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 2, 4, 'Dummy Temuan 1 untuk Audit 9', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 56, 'ISS/DUMMY/1842', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 1, 5, 'Dummy Temuan 0 untuk Audit 10', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 21, 'ISS/DUMMY/4973', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 2, 5, 'Dummy Temuan 1 untuk Audit 10', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 76, 'ISS/DUMMY/9537', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 3, 5, 'Dummy Temuan 2 untuk Audit 10', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 32, 'ISS/DUMMY/7089', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 4, 5, 'Dummy Temuan 3 untuk Audit 10', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 30, 'ISS/DUMMY/8515', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 5, 5, 'Dummy Temuan 4 untuk Audit 10', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 19, 'ISS/DUMMY/2769', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 6, 5, 'Dummy Temuan 5 untuk Audit 10', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 5, 'ISS/DUMMY/9686', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 1, 6, 'Dummy Temuan 0 untuk Audit 11', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 46, 'ISS/DUMMY/2903', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 2, 6, 'Dummy Temuan 1 untuk Audit 11', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 45, 'ISS/DUMMY/3459', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 1, 7, 'Dummy Temuan 0 untuk Audit 14', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 20, 'ISS/DUMMY/4998', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 2, 7, 'Dummy Temuan 1 untuk Audit 14', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 3, 'ISS/DUMMY/7004', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 3, 7, 'Dummy Temuan 2 untuk Audit 14', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 58, 'ISS/DUMMY/8243', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 4, 7, 'Dummy Temuan 3 untuk Audit 14', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 17, 'ISS/DUMMY/9519', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 5, 7, 'Dummy Temuan 4 untuk Audit 14', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 21, 'ISS/DUMMY/8797', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 1, 8, 'Dummy Temuan 0 untuk Audit 16', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 21, 'ISS/DUMMY/8633', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 2, 8, 'Dummy Temuan 1 untuk Audit 16', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 19, 'ISS/DUMMY/6466', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 3, 8, 'Dummy Temuan 2 untuk Audit 16', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 58, 'ISS/DUMMY/4226', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(25, 4, 8, 'Dummy Temuan 3 untuk Audit 16', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 54, 'ISS/DUMMY/5611', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(26, 5, 8, 'Dummy Temuan 4 untuk Audit 16', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 48, 'ISS/DUMMY/1424', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(27, 6, 8, 'Dummy Temuan 5 untuk Audit 16', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 45, 'ISS/DUMMY/2338', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(28, 1, 9, 'Dummy Temuan 0 untuk Audit 18', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 41, 'ISS/DUMMY/4411', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(29, 2, 9, 'Dummy Temuan 1 untuk Audit 18', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 23, 'ISS/DUMMY/5495', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(30, 3, 9, 'Dummy Temuan 2 untuk Audit 18', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 47, 'ISS/DUMMY/4396', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(31, 1, 10, 'Dummy Temuan 0 untuk Audit 19', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 11, 'ISS/DUMMY/1176', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(32, 2, 10, 'Dummy Temuan 1 untuk Audit 19', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 23, 'ISS/DUMMY/9242', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(33, 1, 11, 'Dummy Temuan 0 untuk Audit 20', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 68, 'ISS/DUMMY/7740', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(34, 2, 11, 'Dummy Temuan 1 untuk Audit 20', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 42, 'ISS/DUMMY/8298', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(35, 3, 11, 'Dummy Temuan 2 untuk Audit 20', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 29, 'ISS/DUMMY/1011', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(36, 4, 11, 'Dummy Temuan 3 untuk Audit 20', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 55, 'ISS/DUMMY/8533', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(37, 5, 11, 'Dummy Temuan 4 untuk Audit 20', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 52, 'ISS/DUMMY/2863', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(38, 1, 12, 'Dummy Temuan 0 untuk Audit 23', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 24, 'ISS/DUMMY/2517', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(39, 2, 12, 'Dummy Temuan 1 untuk Audit 23', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 34, 'ISS/DUMMY/3440', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(40, 3, 12, 'Dummy Temuan 2 untuk Audit 23', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 38, 'ISS/DUMMY/5594', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(41, 4, 12, 'Dummy Temuan 3 untuk Audit 23', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 40, 'ISS/DUMMY/9999', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(42, 1, 13, 'Dummy Temuan 0 untuk Audit 26', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 61, 'ISS/DUMMY/4956', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(43, 2, 13, 'Dummy Temuan 1 untuk Audit 26', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 6, 'ISS/DUMMY/9792', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(44, 3, 13, 'Dummy Temuan 2 untuk Audit 26', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 60, 'ISS/DUMMY/8096', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(45, 4, 13, 'Dummy Temuan 3 untuk Audit 26', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 1, 'ISS/DUMMY/9138', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(46, 1, 14, 'Dummy Temuan 0 untuk Audit 27', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 16, 'ISS/DUMMY/6932', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(47, 1, 15, 'Dummy Temuan 0 untuk Audit 28', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 14, 'ISS/DUMMY/7809', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(48, 2, 15, 'Dummy Temuan 1 untuk Audit 28', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 27, 'ISS/DUMMY/9321', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(49, 3, 15, 'Dummy Temuan 2 untuk Audit 28', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 38, 'ISS/DUMMY/8318', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(50, 1, 16, 'Dummy Temuan 0 untuk Audit 30', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 5, 'ISS/DUMMY/1193', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(51, 2, 16, 'Dummy Temuan 1 untuk Audit 30', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 30, 'ISS/DUMMY/7268', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(52, 1, 17, 'Dummy Temuan 0 untuk Audit 31', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 31, 'ISS/DUMMY/6646', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(53, 2, 17, 'Dummy Temuan 1 untuk Audit 31', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 74, 'ISS/DUMMY/2505', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(54, 3, 17, 'Dummy Temuan 2 untuk Audit 31', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 55, 'ISS/DUMMY/6397', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(55, 1, 18, 'Dummy Temuan 0 untuk Audit 32', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 60, 'ISS/DUMMY/5919', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(56, 2, 18, 'Dummy Temuan 1 untuk Audit 32', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 41, 'ISS/DUMMY/8634', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(57, 3, 18, 'Dummy Temuan 2 untuk Audit 32', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 1, 'ISS/DUMMY/5292', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(58, 4, 18, 'Dummy Temuan 3 untuk Audit 32', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 17, 'ISS/DUMMY/8767', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(59, 5, 18, 'Dummy Temuan 4 untuk Audit 32', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 56, 'ISS/DUMMY/4745', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(60, 1, 19, 'Dummy Temuan 0 untuk Audit 33', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 9, 'ISS/DUMMY/6834', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(61, 2, 19, 'Dummy Temuan 1 untuk Audit 33', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 65, 'ISS/DUMMY/4345', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(62, 3, 19, 'Dummy Temuan 2 untuk Audit 33', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 36, 'ISS/DUMMY/7352', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(63, 4, 19, 'Dummy Temuan 3 untuk Audit 33', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 32, 'ISS/DUMMY/6542', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(64, 1, 20, 'Dummy Temuan 0 untuk Audit 38', 'Dummy Permasalahan', 'Dummy Penyebab', 'Dummy Kriteria', 'Dummy Dampak Terjadi', 'Dummy Dampak Potensi', 'Tinggi', 1, 49, 'ISS/DUMMY/8435', '2026', 'approved', NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penutup_lha_rekomendasi`
--

CREATE TABLE `penutup_lha_rekomendasi` (
  `id` bigint UNSIGNED NOT NULL,
  `pelaporan_isi_lha_id` bigint UNSIGNED NOT NULL,
  `rekomendasi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rencana_aksi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `eviden_rekomendasi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pic_rekomendasi` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_waktu` date NOT NULL,
  `real_waktu` date DEFAULT NULL,
  `komentar` text COLLATE utf8mb4_unicode_ci,
  `file_eviden` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_tindak_lanjut` enum('open','closed','on_progress') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `last_notified_at` timestamp NULL DEFAULT NULL,
  `status_approval` enum('pending','approved_level1','approved','rejected_level1','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_level1` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level1` timestamp NULL DEFAULT NULL,
  `rejected_by_level1` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level1` timestamp NULL DEFAULT NULL,
  `rejection_reason_level1` text COLLATE utf8mb4_unicode_ci,
  `approved_by_level2` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level2` timestamp NULL DEFAULT NULL,
  `rejected_by_level2` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level2` timestamp NULL DEFAULT NULL,
  `rejection_reason_level2` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `alasan_reject` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `penutup_lha_rekomendasi`
--

INSERT INTO `penutup_lha_rekomendasi` (`id`, `pelaporan_isi_lha_id`, `rekomendasi`, `rencana_aksi`, `eviden_rekomendasi`, `pic_rekomendasi`, `target_waktu`, `real_waktu`, `komentar`, `file_eviden`, `status_tindak_lanjut`, `last_notified_at`, `status_approval`, `approved_by`, `approved_at`, `approved_by_level1`, `approved_at_level1`, `rejected_by_level1`, `rejected_at_level1`, `rejection_reason_level1`, `approved_by_level2`, `approved_at_level2`, `rejected_by_level2`, `rejected_at_level2`, `rejection_reason_level2`, `created_at`, `updated_at`, `alasan_reject`) VALUES
(1, 7, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-21', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(2, 8, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-29', '2026-05-09', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(3, 9, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-11', '2026-06-12', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(4, 10, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-25', '2026-04-30', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(5, 11, 'Dummy Rekomendasi 2', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-25', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(6, 12, 'Dummy Rekomendasi 3', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-14', '2026-06-24', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(7, 13, 'Dummy Rekomendasi 4', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-05', '2026-04-12', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(8, 14, 'Dummy Rekomendasi 5', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-01-05', '2026-01-06', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(9, 15, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-03-14', '2026-03-12', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(10, 16, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-03-17', '2026-03-25', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(11, 17, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-13', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(12, 18, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-08', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(13, 19, 'Dummy Rekomendasi 2', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-27', '2026-04-27', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(14, 20, 'Dummy Rekomendasi 3', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-01-11', '2026-01-09', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(15, 21, 'Dummy Rekomendasi 4', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-01-14', '2026-01-16', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(16, 22, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-02-26', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(17, 23, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-09', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(18, 24, 'Dummy Rekomendasi 2', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-03-06', '2026-03-07', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(19, 25, 'Dummy Rekomendasi 3', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-05-11', '2026-05-07', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(20, 26, 'Dummy Rekomendasi 4', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-04', '2026-06-14', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(21, 27, 'Dummy Rekomendasi 5', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-05-16', '2026-05-24', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(22, 28, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-26', '2026-07-05', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(23, 29, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-12', '2026-04-15', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(24, 30, 'Dummy Rekomendasi 2', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-18', '2026-04-13', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(25, 31, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-02-22', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(26, 32, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-01-01', '2026-01-04', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(27, 33, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-05-10', '2026-05-05', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(28, 34, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-03-26', '2026-03-21', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(29, 35, 'Dummy Rekomendasi 2', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-01-18', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(30, 36, 'Dummy Rekomendasi 3', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-03-13', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(31, 37, 'Dummy Rekomendasi 4', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-16', '2026-06-22', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(32, 38, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-02-28', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17', NULL),
(33, 39, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-17', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(34, 40, 'Dummy Rekomendasi 2', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-02', '2026-04-10', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(35, 41, 'Dummy Rekomendasi 3', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-06', '2026-04-04', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(36, 42, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-04', '2026-05-30', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(37, 43, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-05-15', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(38, 44, 'Dummy Rekomendasi 2', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-03-06', '2026-03-09', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(39, 45, 'Dummy Rekomendasi 3', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-27', '2026-06-22', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(40, 46, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-04', '2026-04-07', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(41, 47, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2025-12-23', '2025-12-26', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(42, 48, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-03-17', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(43, 49, 'Dummy Rekomendasi 2', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-23', '2026-04-24', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(44, 50, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-02', '2026-05-31', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(45, 51, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-01-24', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(46, 52, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-04', '2026-04-05', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(47, 53, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-05-08', '2026-05-03', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(48, 54, 'Dummy Rekomendasi 2', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-16', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(49, 55, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-01-10', '2026-01-15', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(50, 56, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-04-24', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(51, 57, 'Dummy Rekomendasi 2', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-02-02', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(52, 58, 'Dummy Rekomendasi 3', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-02-28', '2026-03-02', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(53, 59, 'Dummy Rekomendasi 4', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-11', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(54, 60, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-02-22', '2026-02-19', NULL, NULL, 'closed', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(55, 61, 'Dummy Rekomendasi 1', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-03-07', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(56, 62, 'Dummy Rekomendasi 2', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-06', '2026-06-02', NULL, NULL, 'on_progress', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(57, 63, 'Dummy Rekomendasi 3', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-06-16', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(58, 64, 'Dummy Rekomendasi 0', 'Dummy Rencana Aksi', 'dummy.pdf', '[\"Finance\"]', '2026-03-20', NULL, NULL, NULL, 'open', NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18', NULL),
(59, 2, 'Perbaikan proses approval keuangan agar sesuai dengan hierarki yang elah ditetapkan sebelumnya, agar tetap sesuai dengan level manajemennya', 'memperbaiki sistem yang sudah ada agar sesuai dengan rencana sistem', 'file UAT fitur approval di sistem transaksi keuangan', 'BUSINESS CONTACT: FATAHUDDIN YOGI AMIBOWO - PELAYANAN PELANGGAN | APPROVAL 1 SPI: IRAWAN HERNANDA - SEKPER | APPROVAL 2 SPI: NURUL AZISAH - KEUANGAN & ANGGARAN', '2026-07-24', NULL, NULL, NULL, 'open', NULL, 'approved', 1, '2026-05-20 23:04:37', 1, '2026-05-20 23:04:35', NULL, NULL, NULL, 1, '2026-05-20 23:04:37', NULL, NULL, NULL, '2026-05-20 23:04:32', '2026-05-20 23:04:32', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `penutup_lha_rekomendasi_pic`
--

CREATE TABLE `penutup_lha_rekomendasi_pic` (
  `id` bigint UNSIGNED NOT NULL,
  `penutup_lha_rekomendasi_id` bigint UNSIGNED NOT NULL,
  `master_user_id` bigint UNSIGNED NOT NULL,
  `pic_type` enum('business_contact','approval_1_spi','approval_2_spi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'business_contact',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `penutup_lha_rekomendasi_pic`
--

INSERT INTO `penutup_lha_rekomendasi_pic` (`id`, `penutup_lha_rekomendasi_id`, `master_user_id`, `pic_type`, `created_at`, `updated_at`) VALUES
(1, 59, 10, 'business_contact', '2026-05-20 23:04:32', '2026-05-20 23:04:32'),
(2, 59, 20, 'approval_1_spi', '2026-05-20 23:04:32', '2026-05-20 23:04:32'),
(3, 59, 17, 'approval_2_spi', '2026-05-20 23:04:32', '2026-05-20 23:04:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penutup_lha_tindak_lanjut`
--

CREATE TABLE `penutup_lha_tindak_lanjut` (
  `id` bigint UNSIGNED NOT NULL,
  `penutup_lha_rekomendasi_id` bigint UNSIGNED NOT NULL,
  `real_waktu` date DEFAULT NULL,
  `komentar` text COLLATE utf8mb4_unicode_ci,
  `file_eviden` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_tindak_lanjut` enum('open','closed','on_progress') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `penutup_lha_tindak_lanjut`
--

INSERT INTO `penutup_lha_tindak_lanjut` (`id`, `penutup_lha_rekomendasi_id`, `real_waktu`, `komentar`, `file_eviden`, `status_tindak_lanjut`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-05-09', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-05-08 22:21:17', '2026-05-08 22:21:17'),
(2, 3, '2026-06-12', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-06-11 22:21:17', '2026-06-11 22:21:17'),
(3, 4, '2026-04-30', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-04-29 22:21:17', '2026-04-29 22:21:17'),
(4, 6, '2026-06-24', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-06-23 22:21:17', '2026-06-23 22:21:17'),
(5, 7, '2026-04-12', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-04-11 22:21:17', '2026-04-11 22:21:17'),
(6, 8, '2026-01-06', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-01-05 22:21:17', '2026-01-05 22:21:17'),
(7, 9, '2026-03-12', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-03-11 22:21:17', '2026-03-11 22:21:17'),
(8, 10, '2026-03-25', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-03-24 22:21:17', '2026-03-24 22:21:17'),
(9, 13, '2026-04-27', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-04-26 22:21:17', '2026-04-26 22:21:17'),
(10, 14, '2026-01-09', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-01-08 22:21:17', '2026-01-08 22:21:17'),
(11, 15, '2026-01-16', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-01-15 22:21:17', '2026-01-15 22:21:17'),
(12, 18, '2026-03-07', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-03-06 22:21:17', '2026-03-06 22:21:17'),
(13, 19, '2026-05-07', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-05-06 22:21:17', '2026-05-06 22:21:17'),
(14, 20, '2026-06-14', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-06-13 22:21:17', '2026-06-13 22:21:17'),
(15, 21, '2026-05-24', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-05-23 22:21:17', '2026-05-23 22:21:17'),
(16, 22, '2026-07-05', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-07-04 22:21:17', '2026-07-04 22:21:17'),
(17, 23, '2026-04-15', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-04-14 22:21:17', '2026-04-14 22:21:17'),
(18, 24, '2026-04-13', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-04-12 22:21:17', '2026-04-12 22:21:17'),
(19, 26, '2026-01-04', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-01-03 22:21:17', '2026-01-03 22:21:17'),
(20, 27, '2026-05-05', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-05-04 22:21:17', '2026-05-04 22:21:17'),
(21, 28, '2026-03-21', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-03-20 22:21:17', '2026-03-20 22:21:17'),
(22, 31, '2026-06-22', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-06-21 22:21:17', '2026-06-21 22:21:17'),
(23, 34, '2026-04-10', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-04-09 22:21:17', '2026-04-09 22:21:17'),
(24, 35, '2026-04-04', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-04-03 22:21:17', '2026-04-03 22:21:17'),
(25, 36, '2026-05-30', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-05-29 22:21:17', '2026-05-29 22:21:17'),
(26, 38, '2026-03-09', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-03-08 22:21:17', '2026-03-08 22:21:17'),
(27, 39, '2026-06-22', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-06-21 22:21:17', '2026-06-21 22:21:17'),
(28, 40, '2026-04-07', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-04-06 22:21:17', '2026-04-06 22:21:17'),
(29, 41, '2025-12-26', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2025-12-25 22:21:17', '2025-12-25 22:21:17'),
(30, 43, '2026-04-24', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-04-23 22:21:17', '2026-04-23 22:21:17'),
(31, 44, '2026-05-31', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-05-30 22:21:17', '2026-05-30 22:21:17'),
(32, 46, '2026-04-05', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-04-04 22:21:17', '2026-04-04 22:21:17'),
(33, 47, '2026-05-03', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-05-02 22:21:17', '2026-05-02 22:21:17'),
(34, 49, '2026-01-15', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-01-14 22:21:17', '2026-01-14 22:21:17'),
(35, 52, '2026-03-02', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-03-01 22:21:17', '2026-03-01 22:21:17'),
(36, 54, '2026-02-19', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'closed', '2026-02-18 22:21:17', '2026-02-18 22:21:17'),
(37, 56, '2026-06-02', 'Dummy bukti pengerjaan tindak lanjut...', 'dummy_bukti.pdf', 'on_progress', '2026-06-01 22:21:17', '2026-06-01 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `perencanaan_audit`
--

CREATE TABLE `perencanaan_audit` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal_surat_tugas` date NOT NULL,
  `nomor_surat_tugas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_audit_id` bigint UNSIGNED DEFAULT NULL,
  `area_id` int DEFAULT NULL,
  `jenis_audit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `koordinator_id` bigint UNSIGNED DEFAULT NULL,
  `ketua_tim_id` bigint UNSIGNED DEFAULT NULL,
  `auditor` json NOT NULL,
  `auditee_id` bigint UNSIGNED NOT NULL,
  `ruang_lingkup` json NOT NULL,
  `tanggal_audit_mulai` date NOT NULL,
  `tanggal_audit_sampai` date NOT NULL,
  `periode_audit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `perencanaan_audit`
--

INSERT INTO `perencanaan_audit` (`id`, `tanggal_surat_tugas`, `nomor_surat_tugas`, `jenis_audit_id`, `area_id`, `jenis_audit`, `koordinator_id`, `ketua_tim_id`, `auditor`, `auditee_id`, `ruang_lingkup`, `tanggal_audit_mulai`, `tanggal_audit_sampai`, `periode_audit`, `created_at`, `updated_at`) VALUES
(1, '2024-07-01', '001.STG/SPI.01.02/SPI-ND/2026', NULL, 1, 'Audit Operasional', 1, 2, '[\"Auditor 1 - NIP: 123456789\"]', 1, '[\"Sistem Keuangan\", \"Sistem SDM\"]', '2024-07-10', '2024-07-15', 'Januari 2024 s/d Juni 2024', '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(2, '2024-07-02', '002.STG/SPI.01.02/SPI-ND/2026', 1, 2, 'Audit Operasional', 11, 2, '[\"ANDRY APRIAWAN - NIP: 7705003BOD\"]', 2, '[\"Sistem Operasional\", \"Sistem IT\"]', '2024-07-20', '2024-07-25', 'Januari 2024 s/d Juni 2024', '2026-05-19 22:21:16', '2026-05-19 22:48:57'),
(3, '2024-07-03', '001.STG/SPI.01.03/SPI-ND/2026', NULL, 3, 'Audit Khusus', 1, 2, '[\"Auditor 3 - NIP: 456789123\"]', 3, '[\"Investigasi Khusus\", \"Pemeriksaan Khusus\"]', '2024-08-01', '2024-08-05', 'Januari 2024 s/d Desember 2024', '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(4, '2024-07-04', '001.STG/SPI.01.04/SPI-ND/2026', NULL, 4, 'Konsultasi', 1, 2, '[\"Konsultan 1 - NIP: 789123456\"]', 4, '[\"Konsultasi Sistem\", \"Konsultasi Proses\"]', '2024-08-10', '2024-08-15', 'Januari 2024 s/d Desember 2024', '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(5, '2024-07-05', '003.STG/SPI.01.02/SPI-ND/2026', NULL, 5, 'Audit Operasional', 1, 2, '[\"Auditor 4 - NIP: 321654987\", \"Auditor 5 - NIP: 654987321\"]', 5, '[\"Sistem Keamanan\", \"Sistem Monitoring\", \"Sistem Pelaporan\"]', '2024-08-20', '2024-08-30', 'Januari 2024 s/d Juni 2024', '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(6, '2024-07-06', '004.STG/SPI.01.02/SPI-PCN/2026', NULL, 6, 'Audit Kepatuhan', 1, 2, '[\"Auditor 6 - NIP: 147258369\"]', 1, '[\"Kepatuhan Regulasi\", \"Sistem Pengendalian\"]', '2024-09-01', '2024-09-10', 'Januari 2024 s/d Desember 2024', '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(7, '2024-07-07', '005.STG/SPI.01.02/SPI-PCN/2026', NULL, 7, 'Audit Sistem Informasi', 1, 2, '[\"Auditor 7 - NIP: 963852741\", \"Auditor 8 - NIP: 852963741\"]', 2, '[\"Sistem IT\", \"Keamanan Data\", \"Infrastruktur\"]', '2024-09-15', '2024-09-25', 'Januari 2024 s/d Desember 2024', '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(8, '2024-07-08', '006.STG/SPI.01.02/SPI-PCN/2026', NULL, 8, 'Audit Keuangan', 1, 2, '[\"Auditor 9 - NIP: 741852963\"]', 3, '[\"Laporan Keuangan\", \"Sistem Akuntansi\", \"Pengendalian Internal\"]', '2024-10-01', '2024-10-15', 'Januari 2024 s/d Desember 2024', '2026-05-19 22:21:16', '2026-05-19 22:21:16'),
(9, '2026-03-15', 'ST/DUMMY/2026/4971', 1, 1, 'RBA', 1, 1, '[1]', 2, '[\"Finance\"]', '2026-03-17', '2026-03-31', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, '2026-04-09', 'ST/DUMMY/2026/7148', 1, 1, 'RBA', 1, 1, '[1]', 6, '[\"Finance\"]', '2026-04-11', '2026-05-01', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, '2026-02-11', 'ST/DUMMY/2026/3891', 1, 1, 'RBA', 1, 1, '[1]', 2, '[\"Finance\"]', '2026-02-13', '2026-03-06', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, '2026-03-29', 'ST/DUMMY/2026/6926', 1, 1, 'RBA', 1, 1, '[1]', 3, '[\"Finance\"]', '2026-03-31', '2026-04-11', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, '2026-04-19', 'ST/DUMMY/2026/1106', 1, 1, 'RBA', 1, 1, '[1]', 8, '[\"Finance\"]', '2026-04-21', '2026-05-09', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, '2026-03-26', 'ST/DUMMY/2026/5026', 1, 1, 'RBA', 1, 1, '[1]', 5, '[\"Finance\"]', '2026-03-28', '2026-04-04', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, '2026-04-28', 'ST/DUMMY/2026/7252', 1, 1, 'RBA', 1, 1, '[1]', 2, '[\"Finance\"]', '2026-04-30', '2026-05-08', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, '2026-04-05', 'ST/DUMMY/2026/5716', 1, 1, 'RBA', 1, 1, '[1]', 9, '[\"Finance\"]', '2026-04-07', '2026-04-28', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, '2026-04-19', 'ST/DUMMY/2026/1578', 1, 1, 'RBA', 1, 1, '[1]', 8, '[\"Finance\"]', '2026-04-21', '2026-04-30', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, '2026-02-12', 'ST/DUMMY/2026/1170', 1, 1, 'RBA', 1, 1, '[1]', 8, '[\"Finance\"]', '2026-02-14', '2026-02-21', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, '2026-02-13', 'ST/DUMMY/2026/1707', 1, 1, 'RBA', 1, 1, '[1]', 3, '[\"Finance\"]', '2026-02-15', '2026-03-03', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, '2026-05-05', 'ST/DUMMY/2026/7816', 1, 1, 'RBA', 1, 1, '[1]', 7, '[\"Finance\"]', '2026-05-07', '2026-05-22', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, '2026-03-26', 'ST/DUMMY/2026/2631', 1, 1, 'RBA', 1, 1, '[1]', 3, '[\"Finance\"]', '2026-03-28', '2026-04-08', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, '2026-02-12', 'ST/DUMMY/2026/3810', 1, 1, 'RBA', 1, 1, '[1]', 8, '[\"Finance\"]', '2026-02-14', '2026-02-23', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, '2026-02-19', 'ST/DUMMY/2026/2103', 1, 1, 'RBA', 1, 1, '[1]', 7, '[\"Finance\"]', '2026-02-21', '2026-02-28', '2026', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, '2026-03-15', 'ST/DUMMY/2026/8500', 1, 1, 'RBA', 1, 1, '[1]', 4, '[\"Finance\"]', '2026-03-17', '2026-03-26', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(25, '2026-04-24', 'ST/DUMMY/2026/7466', 1, 1, 'RBA', 1, 1, '[1]', 5, '[\"Finance\"]', '2026-04-26', '2026-05-14', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(26, '2026-03-18', 'ST/DUMMY/2026/5545', 1, 1, 'RBA', 1, 1, '[1]', 7, '[\"Finance\"]', '2026-03-20', '2026-04-04', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(27, '2026-04-25', 'ST/DUMMY/2026/4628', 1, 1, 'RBA', 1, 1, '[1]', 1, '[\"Finance\"]', '2026-04-27', '2026-05-04', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(28, '2026-05-07', 'ST/DUMMY/2026/5089', 1, 1, 'RBA', 1, 1, '[1]', 3, '[\"Finance\"]', '2026-05-09', '2026-05-27', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(29, '2026-04-04', 'ST/DUMMY/2026/3949', 1, 1, 'RBA', 1, 1, '[1]', 5, '[\"Finance\"]', '2026-04-06', '2026-04-20', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(30, '2026-03-08', 'ST/DUMMY/2026/2394', 1, 1, 'RBA', 1, 1, '[1]', 5, '[\"Finance\"]', '2026-03-10', '2026-03-28', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(31, '2026-02-17', 'ST/DUMMY/2026/1600', 1, 1, 'RBA', 1, 1, '[1]', 1, '[\"Finance\"]', '2026-02-19', '2026-03-10', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(32, '2026-04-22', 'ST/DUMMY/2026/5570', 1, 1, 'RBA', 1, 1, '[1]', 6, '[\"Finance\"]', '2026-04-24', '2026-05-05', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(33, '2026-04-09', 'ST/DUMMY/2026/4985', 1, 1, 'RBA', 1, 1, '[1]', 4, '[\"Finance\"]', '2026-04-11', '2026-04-21', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(34, '2026-04-11', 'ST/DUMMY/2026/8201', 1, 1, 'RBA', 1, 1, '[1]', 8, '[\"Finance\"]', '2026-04-13', '2026-04-20', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(35, '2026-02-26', 'ST/DUMMY/2026/2402', 1, 1, 'RBA', 1, 1, '[1]', 7, '[\"Finance\"]', '2026-02-28', '2026-03-16', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(36, '2026-02-11', 'ST/DUMMY/2026/7245', 1, 1, 'RBA', 1, 1, '[1]', 1, '[\"Finance\"]', '2026-02-13', '2026-02-23', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(37, '2026-04-01', 'ST/DUMMY/2026/7064', 1, 1, 'RBA', 1, 1, '[1]', 7, '[\"Finance\"]', '2026-04-03', '2026-04-17', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(38, '2026-03-21', 'ST/DUMMY/2026/6108', 1, 1, 'RBA', 1, 1, '[1]', 5, '[\"Finance\"]', '2026-03-23', '2026-04-09', '2026', '2026-05-19 22:21:18', '2026-05-19 22:21:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pka_dokumen`
--

CREATE TABLE `pka_dokumen` (
  `id` bigint UNSIGNED NOT NULL,
  `program_kerja_audit_id` bigint UNSIGNED NOT NULL,
  `nama_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_approval` enum('pending','approved_level1','approved','rejected_level1','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_level1` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level1` timestamp NULL DEFAULT NULL,
  `rejected_by_level1` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level1` timestamp NULL DEFAULT NULL,
  `rejection_reason_level1` text COLLATE utf8mb4_unicode_ci,
  `approved_by_level2` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level2` timestamp NULL DEFAULT NULL,
  `rejected_by_level2` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level2` timestamp NULL DEFAULT NULL,
  `rejection_reason_level2` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pka_dokumen`
--

INSERT INTO `pka_dokumen` (`id`, `program_kerja_audit_id`, `nama_dokumen`, `file_path`, `status_approval`, `approved_by`, `approved_at`, `approved_by_level1`, `approved_at_level1`, `rejected_by_level1`, `rejected_at_level1`, `rejection_reason_level1`, `approved_by_level2`, `approved_at_level2`, `rejected_by_level2`, `rejected_at_level2`, `rejection_reason_level2`, `created_at`, `updated_at`) VALUES
(1, 1, 'Program Kerja Audit 1', 'dokumen/pka_1.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 1, 'Lampiran Dokumen 1', 'dokumen/lampiran_1.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 1, 'Surat Tugas Audit 1', 'dokumen/surat_tugas_1.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 2, 'Program Kerja Audit 2', 'dokumen/pka_2.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 2, 'Lampiran Dokumen 2', 'dokumen/lampiran_2.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 2, 'Surat Tugas Audit 2', 'dokumen/surat_tugas_2.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 3, 'Program Kerja Audit 3', 'dokumen/pka_3.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 3, 'Lampiran Dokumen 3', 'dokumen/lampiran_3.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 3, 'Surat Tugas Audit 3', 'dokumen/surat_tugas_3.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 4, 'Program Kerja Audit 4', 'dokumen/pka_4.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 4, 'Lampiran Dokumen 4', 'dokumen/lampiran_4.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 4, 'Surat Tugas Audit 4', 'dokumen/surat_tugas_4.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 5, 'Program Kerja Audit 5', 'dokumen/pka_5.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 5, 'Lampiran Dokumen 5', 'dokumen/lampiran_5.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 5, 'Surat Tugas Audit 5', 'dokumen/surat_tugas_5.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 6, 'Program Kerja Audit 6', 'dokumen/pka_6.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 6, 'Lampiran Dokumen 6', 'dokumen/lampiran_6.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 6, 'Surat Tugas Audit 6', 'dokumen/surat_tugas_6.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 7, 'Program Kerja Audit 7', 'dokumen/pka_7.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 7, 'Lampiran Dokumen 7', 'dokumen/lampiran_7.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 7, 'Surat Tugas Audit 7', 'dokumen/surat_tugas_7.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 8, 'Program Kerja Audit 8', 'dokumen/pka_8.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 8, 'Lampiran Dokumen 8', 'dokumen/lampiran_8.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 8, 'Surat Tugas Audit 8', 'dokumen/surat_tugas_8.pdf', 'approved', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pka_kontrol`
--

CREATE TABLE `pka_kontrol` (
  `id` bigint UNSIGNED NOT NULL,
  `pka_risiko_id` bigint UNSIGNED NOT NULL,
  `deskripsi_kontrol` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pka_kontrol`
--

INSERT INTO `pka_kontrol` (`id`, `pka_risiko_id`, `deskripsi_kontrol`, `urutan`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sistem monitoring regulasi dan update berkala kepada seluruh unit', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 1, 'Pelatihan kepatuhan regulasi secara berkala untuk staf terkait', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 2, 'Review anggaran oleh komite keuangan sebelum disetujui', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 2, 'Benchmarking dengan data historis dan standar industri', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 3, 'Review proses berkala dan implementasi best practices', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 3, 'Penerapan KPI operasional dengan monitoring mingguan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 4, 'Backup sistem harian dan disaster recovery plan yang teruji', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 4, 'Maintenance preventif terjadwal dan monitoring sistem 24 jam', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 5, 'Prosedur verifikasi kredit sebelum pemberian kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 5, 'Aging schedule piutang dengan eskalasi otomatis ke manajemen', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 6, 'Sistem invoicing otomatis dengan notifikasi jatuh tempo', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 6, 'SOP penagihan dengan batas waktu yang ketat dan terukur', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 7, 'Sistem monitoring regulasi dan update berkala kepada seluruh unit', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 7, 'Pelatihan kepatuhan regulasi secara berkala untuk staf terkait', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 8, 'Review anggaran oleh komite keuangan sebelum disetujui', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 8, 'Benchmarking dengan data historis dan standar industri', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 9, 'Review proses berkala dan implementasi best practices', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 9, 'Penerapan KPI operasional dengan monitoring mingguan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 10, 'Backup sistem harian dan disaster recovery plan yang teruji', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 10, 'Maintenance preventif terjadwal dan monitoring sistem 24 jam', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 11, 'Prosedur verifikasi kredit sebelum pemberian kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 11, 'Aging schedule piutang dengan eskalasi otomatis ke manajemen', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 12, 'Sistem invoicing otomatis dengan notifikasi jatuh tempo', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 12, 'SOP penagihan dengan batas waktu yang ketat dan terukur', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(25, 13, 'Sistem monitoring regulasi dan update berkala kepada seluruh unit', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(26, 13, 'Pelatihan kepatuhan regulasi secara berkala untuk staf terkait', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(27, 14, 'Review anggaran oleh komite keuangan sebelum disetujui', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(28, 14, 'Benchmarking dengan data historis dan standar industri', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(29, 15, 'Review proses berkala dan implementasi best practices', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(30, 15, 'Penerapan KPI operasional dengan monitoring mingguan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(31, 16, 'Backup sistem harian dan disaster recovery plan yang teruji', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(32, 16, 'Maintenance preventif terjadwal dan monitoring sistem 24 jam', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(33, 17, 'Prosedur verifikasi kredit sebelum pemberian kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(34, 17, 'Aging schedule piutang dengan eskalasi otomatis ke manajemen', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(35, 18, 'Sistem invoicing otomatis dengan notifikasi jatuh tempo', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(36, 18, 'SOP penagihan dengan batas waktu yang ketat dan terukur', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(37, 19, 'Sistem monitoring regulasi dan update berkala kepada seluruh unit', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(38, 19, 'Pelatihan kepatuhan regulasi secara berkala untuk staf terkait', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(39, 20, 'Review anggaran oleh komite keuangan sebelum disetujui', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(40, 20, 'Benchmarking dengan data historis dan standar industri', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(41, 21, 'Review proses berkala dan implementasi best practices', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(42, 21, 'Penerapan KPI operasional dengan monitoring mingguan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(43, 22, 'Backup sistem harian dan disaster recovery plan yang teruji', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(44, 22, 'Maintenance preventif terjadwal dan monitoring sistem 24 jam', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(45, 23, 'Prosedur verifikasi kredit sebelum pemberian kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(46, 23, 'Aging schedule piutang dengan eskalasi otomatis ke manajemen', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(47, 24, 'Sistem invoicing otomatis dengan notifikasi jatuh tempo', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(48, 24, 'SOP penagihan dengan batas waktu yang ketat dan terukur', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(49, 25, 'Sistem monitoring regulasi dan update berkala kepada seluruh unit', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(50, 25, 'Pelatihan kepatuhan regulasi secara berkala untuk staf terkait', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(51, 26, 'Review anggaran oleh komite keuangan sebelum disetujui', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(52, 26, 'Benchmarking dengan data historis dan standar industri', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(53, 27, 'Review proses berkala dan implementasi best practices', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(54, 27, 'Penerapan KPI operasional dengan monitoring mingguan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(55, 28, 'Backup sistem harian dan disaster recovery plan yang teruji', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(56, 28, 'Maintenance preventif terjadwal dan monitoring sistem 24 jam', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(57, 29, 'Prosedur verifikasi kredit sebelum pemberian kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(58, 29, 'Aging schedule piutang dengan eskalasi otomatis ke manajemen', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(59, 30, 'Sistem invoicing otomatis dengan notifikasi jatuh tempo', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(60, 30, 'SOP penagihan dengan batas waktu yang ketat dan terukur', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(61, 31, 'Sistem monitoring regulasi dan update berkala kepada seluruh unit', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(62, 31, 'Pelatihan kepatuhan regulasi secara berkala untuk staf terkait', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(63, 32, 'Review anggaran oleh komite keuangan sebelum disetujui', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(64, 32, 'Benchmarking dengan data historis dan standar industri', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(65, 33, 'Review proses berkala dan implementasi best practices', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(66, 33, 'Penerapan KPI operasional dengan monitoring mingguan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(67, 34, 'Backup sistem harian dan disaster recovery plan yang teruji', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(68, 34, 'Maintenance preventif terjadwal dan monitoring sistem 24 jam', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(69, 35, 'Prosedur verifikasi kredit sebelum pemberian kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(70, 35, 'Aging schedule piutang dengan eskalasi otomatis ke manajemen', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(71, 36, 'Sistem invoicing otomatis dengan notifikasi jatuh tempo', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(72, 36, 'SOP penagihan dengan batas waktu yang ketat dan terukur', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(73, 37, 'Sistem monitoring regulasi dan update berkala kepada seluruh unit', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(74, 37, 'Pelatihan kepatuhan regulasi secara berkala untuk staf terkait', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(75, 38, 'Review anggaran oleh komite keuangan sebelum disetujui', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(76, 38, 'Benchmarking dengan data historis dan standar industri', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(77, 39, 'Review proses berkala dan implementasi best practices', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(78, 39, 'Penerapan KPI operasional dengan monitoring mingguan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(79, 40, 'Backup sistem harian dan disaster recovery plan yang teruji', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(80, 40, 'Maintenance preventif terjadwal dan monitoring sistem 24 jam', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(81, 41, 'Prosedur verifikasi kredit sebelum pemberian kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(82, 41, 'Aging schedule piutang dengan eskalasi otomatis ke manajemen', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(83, 42, 'Sistem invoicing otomatis dengan notifikasi jatuh tempo', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(84, 42, 'SOP penagihan dengan batas waktu yang ketat dan terukur', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(85, 43, 'Sistem monitoring regulasi dan update berkala kepada seluruh unit', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(86, 43, 'Pelatihan kepatuhan regulasi secara berkala untuk staf terkait', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(87, 44, 'Review anggaran oleh komite keuangan sebelum disetujui', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(88, 44, 'Benchmarking dengan data historis dan standar industri', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(89, 45, 'Review proses berkala dan implementasi best practices', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(90, 45, 'Penerapan KPI operasional dengan monitoring mingguan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(91, 46, 'Backup sistem harian dan disaster recovery plan yang teruji', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(92, 46, 'Maintenance preventif terjadwal dan monitoring sistem 24 jam', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(93, 47, 'Prosedur verifikasi kredit sebelum pemberian kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(94, 47, 'Aging schedule piutang dengan eskalasi otomatis ke manajemen', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(95, 48, 'Sistem invoicing otomatis dengan notifikasi jatuh tempo', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(96, 48, 'SOP penagihan dengan batas waktu yang ketat dan terukur', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pka_milestone`
--

CREATE TABLE `pka_milestone` (
  `id` bigint UNSIGNED NOT NULL,
  `program_kerja_audit_id` bigint UNSIGNED NOT NULL,
  `nama_milestone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pka_milestone`
--

INSERT INTO `pka_milestone` (`id`, `program_kerja_audit_id`, `nama_milestone`, `tanggal_mulai`, `tanggal_selesai`, `created_at`, `updated_at`) VALUES
(1, 1, 'Surat Permintaan Dokumen kepada Auditee', '2024-07-01', '2024-07-03', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 1, 'Ekspose PKA Internal', '2024-07-04', '2024-07-06', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 1, 'Entry Meeting', '2024-07-07', '2024-07-11', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 1, 'Walkthrough', '2024-07-12', '2024-07-26', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 1, 'TOD', '2024-07-27', '2024-08-15', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 1, 'TOE', '2024-08-16', '2024-08-30', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 1, 'Draf LHA', '2024-08-31', '2024-09-09', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 1, 'Pra Exit Meeting untuk Finalisasi LHA', '2024-09-10', '2024-09-14', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 1, 'Exit Meeting', '2024-09-15', '2024-09-19', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 2, 'Surat Permintaan Dokumen kepada Auditee', '2024-07-31', '2024-08-02', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 2, 'Ekspose PKA Internal', '2024-08-03', '2024-08-05', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 2, 'Entry Meeting', '2024-08-06', '2024-08-10', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 2, 'Walkthrough', '2024-08-11', '2024-08-25', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 2, 'TOD', '2024-08-26', '2024-09-14', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 2, 'TOE', '2024-09-15', '2024-09-29', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 2, 'Draf LHA', '2024-09-30', '2024-10-09', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 2, 'Pra Exit Meeting untuk Finalisasi LHA', '2024-10-10', '2024-10-14', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 2, 'Exit Meeting', '2024-10-15', '2024-10-19', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 3, 'Surat Permintaan Dokumen kepada Auditee', '2024-08-30', '2024-09-01', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 3, 'Ekspose PKA Internal', '2024-09-02', '2024-09-04', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 3, 'Entry Meeting', '2024-09-05', '2024-09-09', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 3, 'Walkthrough', '2024-09-10', '2024-09-24', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 3, 'TOD', '2024-09-25', '2024-10-14', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 3, 'TOE', '2024-10-15', '2024-10-29', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(25, 3, 'Draf LHA', '2024-10-30', '2024-11-08', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(26, 3, 'Pra Exit Meeting untuk Finalisasi LHA', '2024-11-09', '2024-11-13', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(27, 3, 'Exit Meeting', '2024-11-14', '2024-11-18', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(28, 4, 'Surat Permintaan Dokumen kepada Auditee', '2024-09-29', '2024-10-01', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(29, 4, 'Ekspose PKA Internal', '2024-10-02', '2024-10-04', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(30, 4, 'Entry Meeting', '2024-10-05', '2024-10-09', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(31, 4, 'Walkthrough', '2024-10-10', '2024-10-24', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(32, 4, 'TOD', '2024-10-25', '2024-11-13', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(33, 4, 'TOE', '2024-11-14', '2024-11-28', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(34, 4, 'Draf LHA', '2024-11-29', '2024-12-08', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(35, 4, 'Pra Exit Meeting untuk Finalisasi LHA', '2024-12-09', '2024-12-13', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(36, 4, 'Exit Meeting', '2024-12-14', '2024-12-18', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(37, 5, 'Surat Permintaan Dokumen kepada Auditee', '2024-10-29', '2024-10-31', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(38, 5, 'Ekspose PKA Internal', '2024-11-01', '2024-11-03', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(39, 5, 'Entry Meeting', '2024-11-04', '2024-11-08', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(40, 5, 'Walkthrough', '2024-11-09', '2024-11-23', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(41, 5, 'TOD', '2024-11-24', '2024-12-13', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(42, 5, 'TOE', '2024-12-14', '2024-12-28', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(43, 5, 'Draf LHA', '2024-12-29', '2025-01-07', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(44, 5, 'Pra Exit Meeting untuk Finalisasi LHA', '2025-01-08', '2025-01-12', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(45, 5, 'Exit Meeting', '2025-01-13', '2025-01-17', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(46, 6, 'Surat Permintaan Dokumen kepada Auditee', '2024-11-28', '2024-11-30', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(47, 6, 'Ekspose PKA Internal', '2024-12-01', '2024-12-03', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(48, 6, 'Entry Meeting', '2024-12-04', '2024-12-08', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(49, 6, 'Walkthrough', '2024-12-09', '2024-12-23', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(50, 6, 'TOD', '2024-12-24', '2025-01-12', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(51, 6, 'TOE', '2025-01-13', '2025-01-27', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(52, 6, 'Draf LHA', '2025-01-28', '2025-02-06', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(53, 6, 'Pra Exit Meeting untuk Finalisasi LHA', '2025-02-07', '2025-02-11', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(54, 6, 'Exit Meeting', '2025-02-12', '2025-02-16', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(55, 7, 'Surat Permintaan Dokumen kepada Auditee', '2024-12-28', '2024-12-30', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(56, 7, 'Ekspose PKA Internal', '2024-12-31', '2025-01-02', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(57, 7, 'Entry Meeting', '2025-01-03', '2025-01-07', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(58, 7, 'Walkthrough', '2025-01-08', '2025-01-22', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(59, 7, 'TOD', '2025-01-23', '2025-02-11', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(60, 7, 'TOE', '2025-02-12', '2025-02-26', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(61, 7, 'Draf LHA', '2025-02-27', '2025-03-08', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(62, 7, 'Pra Exit Meeting untuk Finalisasi LHA', '2025-03-09', '2025-03-13', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(63, 7, 'Exit Meeting', '2025-03-14', '2025-03-18', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(64, 8, 'Surat Permintaan Dokumen kepada Auditee', '2025-01-27', '2025-01-29', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(65, 8, 'Ekspose PKA Internal', '2025-01-30', '2025-02-01', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(66, 8, 'Entry Meeting', '2025-02-02', '2025-02-06', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(67, 8, 'Walkthrough', '2025-02-07', '2025-02-21', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(68, 8, 'TOD', '2025-02-22', '2025-03-13', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(69, 8, 'TOE', '2025-03-14', '2025-03-28', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(70, 8, 'Draf LHA', '2025-03-29', '2025-04-07', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(71, 8, 'Pra Exit Meeting untuk Finalisasi LHA', '2025-04-08', '2025-04-12', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(72, 8, 'Exit Meeting', '2025-04-13', '2025-04-17', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(73, 9, 'Entry Meeting', '2026-03-17', '2026-03-19', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(74, 9, 'Exit Meeting', '2026-03-29', '2026-03-31', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(75, 10, 'Entry Meeting', '2026-04-11', '2026-04-13', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(76, 10, 'Exit Meeting', '2026-04-29', '2026-05-01', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(77, 11, 'Entry Meeting', '2026-02-13', '2026-02-15', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(78, 11, 'Exit Meeting', '2026-03-04', '2026-03-06', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(79, 12, 'Entry Meeting', '2026-03-31', '2026-04-02', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(80, 12, 'Exit Meeting', '2026-04-09', '2026-04-11', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(81, 13, 'Entry Meeting', '2026-04-21', '2026-04-23', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(82, 13, 'Exit Meeting', '2026-05-07', '2026-05-09', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(83, 14, 'Entry Meeting', '2026-03-28', '2026-03-30', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(84, 14, 'Exit Meeting', '2026-04-02', '2026-04-04', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(85, 15, 'Entry Meeting', '2026-04-30', '2026-05-02', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(86, 15, 'Exit Meeting', '2026-05-06', '2026-05-08', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(87, 16, 'Entry Meeting', '2026-04-07', '2026-04-09', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(88, 16, 'Exit Meeting', '2026-04-26', '2026-04-28', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(89, 17, 'Entry Meeting', '2026-04-21', '2026-04-23', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(90, 17, 'Exit Meeting', '2026-04-28', '2026-04-30', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(91, 18, 'Entry Meeting', '2026-02-14', '2026-02-16', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(92, 18, 'Exit Meeting', '2026-02-19', '2026-02-21', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(93, 19, 'Entry Meeting', '2026-02-15', '2026-02-17', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(94, 19, 'Exit Meeting', '2026-03-01', '2026-03-03', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(95, 20, 'Entry Meeting', '2026-05-07', '2026-05-09', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(96, 20, 'Exit Meeting', '2026-05-20', '2026-05-22', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(97, 21, 'Entry Meeting', '2026-03-28', '2026-03-30', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(98, 21, 'Exit Meeting', '2026-04-06', '2026-04-08', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(99, 22, 'Entry Meeting', '2026-02-14', '2026-02-16', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(100, 22, 'Exit Meeting', '2026-02-21', '2026-02-23', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(101, 23, 'Entry Meeting', '2026-02-21', '2026-02-23', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(102, 23, 'Exit Meeting', '2026-02-26', '2026-02-28', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(103, 24, 'Entry Meeting', '2026-03-17', '2026-03-19', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(104, 24, 'Exit Meeting', '2026-03-24', '2026-03-26', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(105, 25, 'Entry Meeting', '2026-04-26', '2026-04-28', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(106, 25, 'Exit Meeting', '2026-05-12', '2026-05-14', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(107, 26, 'Entry Meeting', '2026-03-20', '2026-03-22', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(108, 26, 'Exit Meeting', '2026-04-02', '2026-04-04', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(109, 27, 'Entry Meeting', '2026-04-27', '2026-04-29', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(110, 27, 'Exit Meeting', '2026-05-02', '2026-05-04', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(111, 28, 'Entry Meeting', '2026-05-09', '2026-05-11', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(112, 28, 'Exit Meeting', '2026-05-25', '2026-05-27', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(113, 29, 'Entry Meeting', '2026-04-06', '2026-04-08', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(114, 29, 'Exit Meeting', '2026-04-18', '2026-04-20', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(115, 30, 'Entry Meeting', '2026-03-10', '2026-03-12', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(116, 30, 'Exit Meeting', '2026-03-26', '2026-03-28', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(117, 31, 'Entry Meeting', '2026-02-19', '2026-02-21', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(118, 31, 'Exit Meeting', '2026-03-08', '2026-03-10', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(119, 32, 'Entry Meeting', '2026-04-24', '2026-04-26', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(120, 32, 'Exit Meeting', '2026-05-03', '2026-05-05', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(121, 33, 'Entry Meeting', '2026-04-11', '2026-04-13', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(122, 33, 'Exit Meeting', '2026-04-19', '2026-04-21', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(123, 34, 'Entry Meeting', '2026-04-13', '2026-04-15', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(124, 34, 'Exit Meeting', '2026-04-18', '2026-04-20', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(125, 35, 'Entry Meeting', '2026-02-28', '2026-03-02', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(126, 35, 'Exit Meeting', '2026-03-14', '2026-03-16', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(127, 36, 'Entry Meeting', '2026-02-13', '2026-02-15', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(128, 36, 'Exit Meeting', '2026-02-21', '2026-02-23', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(129, 37, 'Entry Meeting', '2026-04-03', '2026-04-05', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(130, 37, 'Exit Meeting', '2026-04-15', '2026-04-17', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(131, 38, 'Entry Meeting', '2026-03-23', '2026-03-25', '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(132, 38, 'Exit Meeting', '2026-04-07', '2026-04-09', '2026-05-19 22:21:18', '2026-05-19 22:21:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pka_proses_bisnis`
--

CREATE TABLE `pka_proses_bisnis` (
  `id` bigint UNSIGNED NOT NULL,
  `program_kerja_audit_id` bigint UNSIGNED NOT NULL,
  `nama_proses_bisnis` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pka_proses_bisnis`
--

INSERT INTO `pka_proses_bisnis` (`id`, `program_kerja_audit_id`, `nama_proses_bisnis`, `urutan`, `created_at`, `updated_at`) VALUES
(1, 1, 'Proses Perencanaan Kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 1, 'Proses Pelaksanaan Kontrak', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 1, 'Proses Penagihan Kontrak', 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 2, 'Proses Perencanaan Kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 2, 'Proses Pelaksanaan Kontrak', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 2, 'Proses Penagihan Kontrak', 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 3, 'Proses Perencanaan Kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 3, 'Proses Pelaksanaan Kontrak', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 3, 'Proses Penagihan Kontrak', 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 4, 'Proses Perencanaan Kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 4, 'Proses Pelaksanaan Kontrak', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 4, 'Proses Penagihan Kontrak', 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 5, 'Proses Perencanaan Kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 5, 'Proses Pelaksanaan Kontrak', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 5, 'Proses Penagihan Kontrak', 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 6, 'Proses Perencanaan Kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 6, 'Proses Pelaksanaan Kontrak', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 6, 'Proses Penagihan Kontrak', 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 7, 'Proses Perencanaan Kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 7, 'Proses Pelaksanaan Kontrak', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 7, 'Proses Penagihan Kontrak', 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 8, 'Proses Perencanaan Kontrak', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 8, 'Proses Pelaksanaan Kontrak', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 8, 'Proses Penagihan Kontrak', 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pka_risiko`
--

CREATE TABLE `pka_risiko` (
  `id` bigint UNSIGNED NOT NULL,
  `pka_proses_bisnis_id` bigint UNSIGNED NOT NULL,
  `deskripsi_risiko` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_risiko` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penyebab_risiko` text COLLATE utf8mb4_unicode_ci,
  `dampak_risiko` text COLLATE utf8mb4_unicode_ci,
  `urutan` int UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pka_risiko`
--

INSERT INTO `pka_risiko` (`id`, `pka_proses_bisnis_id`, `deskripsi_risiko`, `level_risiko`, `penyebab_risiko`, `dampak_risiko`, `urutan`, `created_at`, `updated_at`) VALUES
(1, 1, 'Risiko ketidakpatuhan terhadap regulasi', 'Tinggi', 'Perubahan regulasi yang tidak diikuti dengan baik oleh unit kerja', 'Sanksi dari regulator dan kerugian finansial perusahaan', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 1, 'Risiko inefisiensi perencanaan anggaran', 'Sedang', 'Estimasi biaya tidak akurat dan kurangnya data historis', 'Pembengkakan anggaran dan gagal mencapai target keuangan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 2, 'Risiko inefisiensi operasional', 'Rendah', 'Proses bisnis yang tidak optimal dan duplikasi pekerjaan', 'Peningkatan biaya operasional dan penurunan produktivitas', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 2, 'Risiko kegagalan teknologi', 'Tinggi', 'Sistem IT yang tidak handal dan kurangnya pemeliharaan', 'Gangguan layanan dan kehilangan data operasional', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 3, 'Risiko piutang tak tertagih', NULL, 'Lemahnya proses verifikasi kredibilitas pelanggan', 'Kerugian finansial akibat piutang macet', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 3, 'Risiko keterlambatan penagihan', NULL, 'Proses invoicing yang lambat dan tidak terstruktur', 'Cash flow terganggu dan denda keterlambatan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 4, 'Risiko ketidakpatuhan terhadap regulasi', 'Tinggi', 'Perubahan regulasi yang tidak diikuti dengan baik oleh unit kerja', 'Sanksi dari regulator dan kerugian finansial perusahaan', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 4, 'Risiko inefisiensi perencanaan anggaran', 'Sedang', 'Estimasi biaya tidak akurat dan kurangnya data historis', 'Pembengkakan anggaran dan gagal mencapai target keuangan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 5, 'Risiko inefisiensi operasional', 'Rendah', 'Proses bisnis yang tidak optimal dan duplikasi pekerjaan', 'Peningkatan biaya operasional dan penurunan produktivitas', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 5, 'Risiko kegagalan teknologi', 'Tinggi', 'Sistem IT yang tidak handal dan kurangnya pemeliharaan', 'Gangguan layanan dan kehilangan data operasional', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 6, 'Risiko piutang tak tertagih', NULL, 'Lemahnya proses verifikasi kredibilitas pelanggan', 'Kerugian finansial akibat piutang macet', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 6, 'Risiko keterlambatan penagihan', NULL, 'Proses invoicing yang lambat dan tidak terstruktur', 'Cash flow terganggu dan denda keterlambatan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 7, 'Risiko ketidakpatuhan terhadap regulasi', 'Tinggi', 'Perubahan regulasi yang tidak diikuti dengan baik oleh unit kerja', 'Sanksi dari regulator dan kerugian finansial perusahaan', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 7, 'Risiko inefisiensi perencanaan anggaran', 'Sedang', 'Estimasi biaya tidak akurat dan kurangnya data historis', 'Pembengkakan anggaran dan gagal mencapai target keuangan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 8, 'Risiko inefisiensi operasional', 'Rendah', 'Proses bisnis yang tidak optimal dan duplikasi pekerjaan', 'Peningkatan biaya operasional dan penurunan produktivitas', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 8, 'Risiko kegagalan teknologi', 'Tinggi', 'Sistem IT yang tidak handal dan kurangnya pemeliharaan', 'Gangguan layanan dan kehilangan data operasional', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 9, 'Risiko piutang tak tertagih', NULL, 'Lemahnya proses verifikasi kredibilitas pelanggan', 'Kerugian finansial akibat piutang macet', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 9, 'Risiko keterlambatan penagihan', NULL, 'Proses invoicing yang lambat dan tidak terstruktur', 'Cash flow terganggu dan denda keterlambatan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 10, 'Risiko ketidakpatuhan terhadap regulasi', 'Tinggi', 'Perubahan regulasi yang tidak diikuti dengan baik oleh unit kerja', 'Sanksi dari regulator dan kerugian finansial perusahaan', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 10, 'Risiko inefisiensi perencanaan anggaran', 'Sedang', 'Estimasi biaya tidak akurat dan kurangnya data historis', 'Pembengkakan anggaran dan gagal mencapai target keuangan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 11, 'Risiko inefisiensi operasional', 'Rendah', 'Proses bisnis yang tidak optimal dan duplikasi pekerjaan', 'Peningkatan biaya operasional dan penurunan produktivitas', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 11, 'Risiko kegagalan teknologi', 'Tinggi', 'Sistem IT yang tidak handal dan kurangnya pemeliharaan', 'Gangguan layanan dan kehilangan data operasional', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 12, 'Risiko piutang tak tertagih', NULL, 'Lemahnya proses verifikasi kredibilitas pelanggan', 'Kerugian finansial akibat piutang macet', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 12, 'Risiko keterlambatan penagihan', NULL, 'Proses invoicing yang lambat dan tidak terstruktur', 'Cash flow terganggu dan denda keterlambatan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(25, 13, 'Risiko ketidakpatuhan terhadap regulasi', 'Tinggi', 'Perubahan regulasi yang tidak diikuti dengan baik oleh unit kerja', 'Sanksi dari regulator dan kerugian finansial perusahaan', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(26, 13, 'Risiko inefisiensi perencanaan anggaran', 'Sedang', 'Estimasi biaya tidak akurat dan kurangnya data historis', 'Pembengkakan anggaran dan gagal mencapai target keuangan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(27, 14, 'Risiko inefisiensi operasional', 'Rendah', 'Proses bisnis yang tidak optimal dan duplikasi pekerjaan', 'Peningkatan biaya operasional dan penurunan produktivitas', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(28, 14, 'Risiko kegagalan teknologi', 'Tinggi', 'Sistem IT yang tidak handal dan kurangnya pemeliharaan', 'Gangguan layanan dan kehilangan data operasional', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(29, 15, 'Risiko piutang tak tertagih', NULL, 'Lemahnya proses verifikasi kredibilitas pelanggan', 'Kerugian finansial akibat piutang macet', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(30, 15, 'Risiko keterlambatan penagihan', NULL, 'Proses invoicing yang lambat dan tidak terstruktur', 'Cash flow terganggu dan denda keterlambatan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(31, 16, 'Risiko ketidakpatuhan terhadap regulasi', 'Tinggi', 'Perubahan regulasi yang tidak diikuti dengan baik oleh unit kerja', 'Sanksi dari regulator dan kerugian finansial perusahaan', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(32, 16, 'Risiko inefisiensi perencanaan anggaran', 'Sedang', 'Estimasi biaya tidak akurat dan kurangnya data historis', 'Pembengkakan anggaran dan gagal mencapai target keuangan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(33, 17, 'Risiko inefisiensi operasional', 'Rendah', 'Proses bisnis yang tidak optimal dan duplikasi pekerjaan', 'Peningkatan biaya operasional dan penurunan produktivitas', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(34, 17, 'Risiko kegagalan teknologi', 'Tinggi', 'Sistem IT yang tidak handal dan kurangnya pemeliharaan', 'Gangguan layanan dan kehilangan data operasional', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(35, 18, 'Risiko piutang tak tertagih', NULL, 'Lemahnya proses verifikasi kredibilitas pelanggan', 'Kerugian finansial akibat piutang macet', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(36, 18, 'Risiko keterlambatan penagihan', NULL, 'Proses invoicing yang lambat dan tidak terstruktur', 'Cash flow terganggu dan denda keterlambatan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(37, 19, 'Risiko ketidakpatuhan terhadap regulasi', 'Tinggi', 'Perubahan regulasi yang tidak diikuti dengan baik oleh unit kerja', 'Sanksi dari regulator dan kerugian finansial perusahaan', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(38, 19, 'Risiko inefisiensi perencanaan anggaran', 'Sedang', 'Estimasi biaya tidak akurat dan kurangnya data historis', 'Pembengkakan anggaran dan gagal mencapai target keuangan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(39, 20, 'Risiko inefisiensi operasional', 'Rendah', 'Proses bisnis yang tidak optimal dan duplikasi pekerjaan', 'Peningkatan biaya operasional dan penurunan produktivitas', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(40, 20, 'Risiko kegagalan teknologi', 'Tinggi', 'Sistem IT yang tidak handal dan kurangnya pemeliharaan', 'Gangguan layanan dan kehilangan data operasional', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(41, 21, 'Risiko piutang tak tertagih', NULL, 'Lemahnya proses verifikasi kredibilitas pelanggan', 'Kerugian finansial akibat piutang macet', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(42, 21, 'Risiko keterlambatan penagihan', NULL, 'Proses invoicing yang lambat dan tidak terstruktur', 'Cash flow terganggu dan denda keterlambatan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(43, 22, 'Risiko ketidakpatuhan terhadap regulasi', 'Tinggi', 'Perubahan regulasi yang tidak diikuti dengan baik oleh unit kerja', 'Sanksi dari regulator dan kerugian finansial perusahaan', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(44, 22, 'Risiko inefisiensi perencanaan anggaran', 'Sedang', 'Estimasi biaya tidak akurat dan kurangnya data historis', 'Pembengkakan anggaran dan gagal mencapai target keuangan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(45, 23, 'Risiko inefisiensi operasional', 'Rendah', 'Proses bisnis yang tidak optimal dan duplikasi pekerjaan', 'Peningkatan biaya operasional dan penurunan produktivitas', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(46, 23, 'Risiko kegagalan teknologi', 'Tinggi', 'Sistem IT yang tidak handal dan kurangnya pemeliharaan', 'Gangguan layanan dan kehilangan data operasional', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(47, 24, 'Risiko piutang tak tertagih', NULL, 'Lemahnya proses verifikasi kredibilitas pelanggan', 'Kerugian finansial akibat piutang macet', 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(48, 24, 'Risiko keterlambatan penagihan', NULL, 'Proses invoicing yang lambat dan tidak terstruktur', 'Cash flow terganggu dan denda keterlambatan', 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pka_risk_based_audit`
--

CREATE TABLE `pka_risk_based_audit` (
  `id` bigint UNSIGNED NOT NULL,
  `program_kerja_audit_id` bigint UNSIGNED NOT NULL,
  `deskripsi_resiko` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `penyebab_resiko` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `dampak_resiko` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pengendalian_eksisting` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `program_kerja_audit`
--

CREATE TABLE `program_kerja_audit` (
  `id` bigint UNSIGNED NOT NULL,
  `perencanaan_audit_id` bigint UNSIGNED NOT NULL,
  `tanggal_pka` date NOT NULL,
  `no_pka` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul_pka` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proses_bisnis` json DEFAULT NULL,
  `informasi_umum` text COLLATE utf8mb4_unicode_ci,
  `kpi_tidak_tercapai` text COLLATE utf8mb4_unicode_ci,
  `data_awal_dokumen` json DEFAULT NULL,
  `status_approval` enum('pending','approved_level1','approved','rejected_level1','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by_level1` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level1` timestamp NULL DEFAULT NULL,
  `rejected_by_level1` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level1` timestamp NULL DEFAULT NULL,
  `rejection_reason_level1` text COLLATE utf8mb4_unicode_ci,
  `approved_by_level2` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level2` timestamp NULL DEFAULT NULL,
  `rejected_by_level2` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level2` timestamp NULL DEFAULT NULL,
  `rejection_reason_level2` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `program_kerja_audit`
--

INSERT INTO `program_kerja_audit` (`id`, `perencanaan_audit_id`, `tanggal_pka`, `no_pka`, `judul_pka`, `proses_bisnis`, `informasi_umum`, `kpi_tidak_tercapai`, `data_awal_dokumen`, `status_approval`, `approved_by_level1`, `approved_at_level1`, `rejected_by_level1`, `rejected_at_level1`, `rejection_reason_level1`, `approved_by_level2`, `approved_at_level2`, `rejected_by_level2`, `rejected_at_level2`, `rejection_reason_level2`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-07-01', 'PKA-001/2024', 'Audit Kepatuhan dan Operasional 1', NULL, 'Program Kerja Audit untuk Audit Operasional pada Direktorat', 'KPI yang tidak tercapai: Efisiensi operasional, Kepatuhan regulasi, Pengelolaan risiko', '[{\"periode\": \"Q1 2024\", \"nama_dokumen\": \"Laporan keuangan\", \"ruang_lingkup\": \"Seluruh perusahaan\"}, {\"periode\": \"Tahun 2024\", \"nama_dokumen\": \"SOP Operasional\", \"ruang_lingkup\": \"Departemen Operasional\"}]', 'approved', 1, '2026-05-19 22:55:53', NULL, NULL, NULL, 1, '2026-05-19 22:55:55', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 2, '2024-07-01', 'PKA-002/2024', 'Audit Kepatuhan dan Operasional 2', NULL, 'Program Kerja Audit untuk Audit Operasional pada Direktorat', 'KPI yang tidak tercapai: Efisiensi operasional, Kepatuhan regulasi, Pengelolaan risiko', '[{\"periode\": \"Q1 2024\", \"nama_dokumen\": \"Laporan keuangan\", \"ruang_lingkup\": \"Seluruh perusahaan\"}, {\"periode\": \"Tahun 2024\", \"nama_dokumen\": \"SOP Operasional\", \"ruang_lingkup\": \"Departemen Operasional\"}]', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 3, '2024-07-01', 'PKA-003/2024', 'Audit Kepatuhan dan Operasional 3', NULL, 'Program Kerja Audit untuk Audit Khusus pada Direktorat', 'KPI yang tidak tercapai: Efisiensi operasional, Kepatuhan regulasi, Pengelolaan risiko', '[{\"periode\": \"Q1 2024\", \"nama_dokumen\": \"Laporan keuangan\", \"ruang_lingkup\": \"Seluruh perusahaan\"}, {\"periode\": \"Tahun 2024\", \"nama_dokumen\": \"SOP Operasional\", \"ruang_lingkup\": \"Departemen Operasional\"}]', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 4, '2024-07-01', 'PKA-004/2024', 'Audit Kepatuhan dan Operasional 4', NULL, 'Program Kerja Audit untuk Konsultasi pada Direktorat', 'KPI yang tidak tercapai: Efisiensi operasional, Kepatuhan regulasi, Pengelolaan risiko', '[{\"periode\": \"Q1 2024\", \"nama_dokumen\": \"Laporan keuangan\", \"ruang_lingkup\": \"Seluruh perusahaan\"}, {\"periode\": \"Tahun 2024\", \"nama_dokumen\": \"SOP Operasional\", \"ruang_lingkup\": \"Departemen Operasional\"}]', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 5, '2024-07-01', 'PKA-005/2024', 'Audit Kepatuhan dan Operasional 5', NULL, 'Program Kerja Audit untuk Audit Operasional pada Direktorat', 'KPI yang tidak tercapai: Efisiensi operasional, Kepatuhan regulasi, Pengelolaan risiko', '[{\"periode\": \"Q1 2024\", \"nama_dokumen\": \"Laporan keuangan\", \"ruang_lingkup\": \"Seluruh perusahaan\"}, {\"periode\": \"Tahun 2024\", \"nama_dokumen\": \"SOP Operasional\", \"ruang_lingkup\": \"Departemen Operasional\"}]', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 6, '2024-07-01', 'PKA-006/2024', 'Audit Kepatuhan dan Operasional 6', NULL, 'Program Kerja Audit untuk Audit Kepatuhan pada Direktorat', 'KPI yang tidak tercapai: Efisiensi operasional, Kepatuhan regulasi, Pengelolaan risiko', '[{\"periode\": \"Q1 2024\", \"nama_dokumen\": \"Laporan keuangan\", \"ruang_lingkup\": \"Seluruh perusahaan\"}, {\"periode\": \"Tahun 2024\", \"nama_dokumen\": \"SOP Operasional\", \"ruang_lingkup\": \"Departemen Operasional\"}]', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 7, '2024-07-01', 'PKA-007/2024', 'Audit Kepatuhan dan Operasional 7', NULL, 'Program Kerja Audit untuk Audit Sistem Informasi pada Direktorat', 'KPI yang tidak tercapai: Efisiensi operasional, Kepatuhan regulasi, Pengelolaan risiko', '[{\"periode\": \"Q1 2024\", \"nama_dokumen\": \"Laporan keuangan\", \"ruang_lingkup\": \"Seluruh perusahaan\"}, {\"periode\": \"Tahun 2024\", \"nama_dokumen\": \"SOP Operasional\", \"ruang_lingkup\": \"Departemen Operasional\"}]', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 8, '2024-07-01', 'PKA-008/2024', 'Audit Kepatuhan dan Operasional 8', NULL, 'Program Kerja Audit untuk Audit Keuangan pada Direktorat', 'KPI yang tidak tercapai: Efisiensi operasional, Kepatuhan regulasi, Pengelolaan risiko', '[{\"periode\": \"Q1 2024\", \"nama_dokumen\": \"Laporan keuangan\", \"ruang_lingkup\": \"Seluruh perusahaan\"}, {\"periode\": \"Tahun 2024\", \"nama_dokumen\": \"SOP Operasional\", \"ruang_lingkup\": \"Departemen Operasional\"}]', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 9, '2026-03-16', 'PKA/DUMMY/2026/5929', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 10, '2026-04-10', 'PKA/DUMMY/2026/7518', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 11, '2026-02-12', 'PKA/DUMMY/2026/9134', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 12, '2026-03-30', 'PKA/DUMMY/2026/3028', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 13, '2026-04-20', 'PKA/DUMMY/2026/5447', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 14, '2026-03-27', 'PKA/DUMMY/2026/3492', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 15, '2026-04-29', 'PKA/DUMMY/2026/5648', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 16, '2026-04-06', 'PKA/DUMMY/2026/3540', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 17, '2026-04-20', 'PKA/DUMMY/2026/9999', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 18, '2026-02-13', 'PKA/DUMMY/2026/2190', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 19, '2026-02-14', 'PKA/DUMMY/2026/4129', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 20, '2026-05-06', 'PKA/DUMMY/2026/9538', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 21, '2026-03-27', 'PKA/DUMMY/2026/8541', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 22, '2026-02-13', 'PKA/DUMMY/2026/1663', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 23, '2026-02-20', 'PKA/DUMMY/2026/1155', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 24, '2026-03-16', 'PKA/DUMMY/2026/6868', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(25, 25, '2026-04-25', 'PKA/DUMMY/2026/8113', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(26, 26, '2026-03-19', 'PKA/DUMMY/2026/9970', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(27, 27, '2026-04-26', 'PKA/DUMMY/2026/3301', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(28, 28, '2026-05-08', 'PKA/DUMMY/2026/7492', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(29, 29, '2026-04-05', 'PKA/DUMMY/2026/9674', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(30, 30, '2026-03-09', 'PKA/DUMMY/2026/7683', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(31, 31, '2026-02-18', 'PKA/DUMMY/2026/7013', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(32, 32, '2026-04-23', 'PKA/DUMMY/2026/3049', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(33, 33, '2026-04-10', 'PKA/DUMMY/2026/5370', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(34, 34, '2026-04-12', 'PKA/DUMMY/2026/5897', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(35, 35, '2026-02-27', 'PKA/DUMMY/2026/7054', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(36, 36, '2026-02-12', 'PKA/DUMMY/2026/1098', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(37, 37, '2026-04-02', 'PKA/DUMMY/2026/5979', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(38, 38, '2026-03-22', 'PKA/DUMMY/2026/9608', NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `realisasi_audits`
--

CREATE TABLE `realisasi_audits` (
  `id` bigint UNSIGNED NOT NULL,
  `perencanaan_audit_id` bigint UNSIGNED NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('selesai','on progress','belum') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_undangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_absensi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approval` enum('pending','approved_level1','approved','rejected_level1','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_level1` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level1` timestamp NULL DEFAULT NULL,
  `rejected_by_level1` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level1` timestamp NULL DEFAULT NULL,
  `rejection_reason_level1` text COLLATE utf8mb4_unicode_ci,
  `approved_by_level2` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level2` timestamp NULL DEFAULT NULL,
  `rejected_by_level2` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level2` timestamp NULL DEFAULT NULL,
  `rejection_reason_level2` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `realisasi_audits`
--

INSERT INTO `realisasi_audits` (`id`, `perencanaan_audit_id`, `tanggal_mulai`, `tanggal_selesai`, `status`, `file_undangan`, `file_absensi`, `status_approval`, `approved_by`, `approved_at`, `approved_by_level1`, `approved_at_level1`, `rejected_by_level1`, `rejected_at_level1`, `rejection_reason_level1`, `approved_by_level2`, `approved_at_level2`, `rejected_by_level2`, `rejected_at_level2`, `rejection_reason_level2`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-07-10', '2024-07-15', 'selesai', NULL, NULL, 'approved', 1, '2026-05-14 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 2, '2024-07-11', '2024-07-16', 'on progress', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 3, '2024-07-12', '2024-07-17', 'belum', NULL, NULL, 'rejected', 1, '2026-05-17 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Dokumen exit meeting tidak lengkap dan perlu dilengkapi terlebih dahulu.', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 4, '2024-07-13', '2024-07-18', 'selesai', NULL, NULL, 'approved', 1, '2026-05-18 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 5, '2024-07-14', '2024-07-19', 'on progress', NULL, NULL, 'rejected', 1, '2026-05-15 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Dokumentasi exit meeting perlu perbaikan format dan konten.', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 6, '2024-07-15', '2024-07-20', 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 7, '2024-07-16', '2024-07-21', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 8, '2024-07-17', '2024-07-22', 'on progress', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 9, '2026-04-07', '2026-04-09', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 10, NULL, NULL, 'on progress', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 11, NULL, NULL, 'on progress', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 12, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 13, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 14, NULL, NULL, 'on progress', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 15, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 16, NULL, NULL, 'on progress', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 17, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 18, '2026-03-09', '2026-03-11', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 19, '2026-03-05', '2026-03-07', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 20, '2026-05-16', '2026-05-18', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 21, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 22, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 23, NULL, NULL, 'on progress', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 24, NULL, '2026-05-20', 'selesai', NULL, NULL, 'approved', 1, '2026-05-19 22:47:20', 1, '2026-05-19 22:47:15', NULL, NULL, NULL, 1, '2026-05-19 22:47:20', NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:47:20'),
(25, 25, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(26, 26, '2026-04-17', '2026-04-19', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(27, 27, '2026-05-19', '2026-05-21', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(28, 28, '2026-05-22', '2026-05-24', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(29, 29, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(30, 30, NULL, NULL, 'on progress', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(31, 31, NULL, NULL, 'on progress', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(32, 32, '2026-05-16', '2026-05-18', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(33, 33, '2026-05-07', '2026-05-09', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(34, 34, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(35, 35, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(36, 36, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(37, 37, NULL, NULL, 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18'),
(38, 38, '2026-04-08', '2026-04-10', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:18', '2026-05-19 22:21:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tod_bpm_audit`
--

CREATE TABLE `tod_bpm_audit` (
  `id` bigint UNSIGNED NOT NULL,
  `perencanaan_audit_id` bigint UNSIGNED NOT NULL,
  `judul_bpm` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_bpo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `resiko` text COLLATE utf8mb4_unicode_ci,
  `kontrol` text COLLATE utf8mb4_unicode_ci,
  `file_bpm` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_kka_tod` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approval` enum('pending','approved_level1','approved','rejected_level1','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_level1` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level1` timestamp NULL DEFAULT NULL,
  `rejected_by_level1` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level1` timestamp NULL DEFAULT NULL,
  `rejection_reason_level1` text COLLATE utf8mb4_unicode_ci,
  `approved_by_level2` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level2` timestamp NULL DEFAULT NULL,
  `rejected_by_level2` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level2` timestamp NULL DEFAULT NULL,
  `rejection_reason_level2` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tod_bpm_audit`
--

INSERT INTO `tod_bpm_audit` (`id`, `perencanaan_audit_id`, `judul_bpm`, `nama_bpo`, `resiko`, `kontrol`, `file_bpm`, `file_kka_tod`, `status_approval`, `rejection_reason`, `approved_by`, `approved_at`, `approved_by_level1`, `approved_at_level1`, `rejected_by_level1`, `rejected_at_level1`, `rejection_reason_level1`, `approved_by_level2`, `approved_at_level2`, `rejected_by_level2`, `rejected_at_level2`, `rejection_reason_level2`, `created_at`, `updated_at`) VALUES
(1, 1, 'Business Process Mapping - Audit Operasional', 'BPO - Direktorat', NULL, NULL, 'bpm/placeholder_1.pdf', NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 2, 'Business Process Mapping - Audit Operasional', 'BPO - Direktorat', NULL, NULL, 'bpm/placeholder_2.pdf', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 3, 'Business Process Mapping - Audit Khusus', 'BPO - Direktorat', NULL, NULL, 'bpm/placeholder_3.pdf', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 4, 'Business Process Mapping - Konsultasi', 'BPO - Direktorat', NULL, NULL, 'bpm/placeholder_4.pdf', NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 5, 'Business Process Mapping - Audit Operasional', 'BPO - Direktorat', NULL, NULL, 'bpm/placeholder_5.pdf', NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 6, 'Business Process Mapping - Audit Kepatuhan', 'BPO - Direktorat', NULL, NULL, 'bpm/placeholder_6.pdf', NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 7, 'Business Process Mapping - Audit Sistem Informasi', 'BPO - Direktorat', NULL, NULL, 'bpm/placeholder_7.pdf', NULL, 'rejected', 'Judul BPM tidak sesuai dengan scope audit yang direncanakan.', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 8, 'Business Process Mapping - Audit Keuangan', 'BPO - Direktorat', NULL, NULL, 'bpm/placeholder_8.pdf', NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tod_bpm_evaluasi`
--

CREATE TABLE `tod_bpm_evaluasi` (
  `id` bigint UNSIGNED NOT NULL,
  `tod_bpm_audit_id` bigint UNSIGNED NOT NULL,
  `hasil_evaluasi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tod_bpm_evaluasi`
--

INSERT INTO `tod_bpm_evaluasi` (`id`, `tod_bpm_audit_id`, `hasil_evaluasi`, `created_at`, `updated_at`) VALUES
(1, 1, 'Cukup', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 2, 'Cukup', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 3, 'Cukup', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 4, 'Tidak Cukup', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 5, 'Tidak Cukup', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 6, 'Cukup', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 7, 'Cukup', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 8, 'Tidak Cukup', '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tod_bpm_kontrol`
--

CREATE TABLE `tod_bpm_kontrol` (
  `id` bigint UNSIGNED NOT NULL,
  `tod_bpm_audit_id` bigint UNSIGNED NOT NULL,
  `pka_kontrol_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tod_bpm_kontrol`
--

INSERT INTO `tod_bpm_kontrol` (`id`, `tod_bpm_audit_id`, `pka_kontrol_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 1, 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 1, 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 1, 4, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 1, 5, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 1, 6, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 1, 7, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 1, 8, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 1, 9, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 1, 10, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 1, 11, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 1, 12, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 2, 13, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 2, 14, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 2, 15, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 2, 16, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 2, 17, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 2, 18, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 2, 19, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 2, 20, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 2, 21, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 2, 22, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 2, 23, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 2, 24, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(25, 3, 25, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(26, 3, 26, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(27, 3, 27, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(28, 3, 28, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(29, 3, 29, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(30, 3, 30, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(31, 3, 31, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(32, 3, 32, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(33, 3, 33, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(34, 3, 34, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(35, 3, 35, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(36, 3, 36, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(37, 4, 37, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(38, 4, 38, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(39, 4, 39, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(40, 4, 40, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(41, 4, 41, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(42, 4, 42, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(43, 4, 43, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(44, 4, 44, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(45, 4, 45, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(46, 4, 46, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(47, 4, 47, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(48, 4, 48, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(49, 5, 49, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(50, 5, 50, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(51, 5, 51, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(52, 5, 52, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(53, 5, 53, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(54, 5, 54, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(55, 5, 55, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(56, 5, 56, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(57, 5, 57, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(58, 5, 58, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(59, 5, 59, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(60, 5, 60, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(61, 6, 61, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(62, 6, 62, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(63, 6, 63, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(64, 6, 64, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(65, 6, 65, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(66, 6, 66, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(67, 6, 67, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(68, 6, 68, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(69, 6, 69, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(70, 6, 70, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(71, 6, 71, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(72, 6, 72, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(73, 7, 73, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(74, 7, 74, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(75, 7, 75, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(76, 7, 76, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(77, 7, 77, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(78, 7, 78, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(79, 7, 79, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(80, 7, 80, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(81, 7, 81, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(82, 7, 82, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(83, 7, 83, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(84, 7, 84, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(85, 8, 85, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(86, 8, 86, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(87, 8, 87, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(88, 8, 88, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(89, 8, 89, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(90, 8, 90, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(91, 8, 91, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(92, 8, 92, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(93, 8, 93, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(94, 8, 94, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(95, 8, 95, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(96, 8, 96, '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tod_bpm_risiko`
--

CREATE TABLE `tod_bpm_risiko` (
  `id` bigint UNSIGNED NOT NULL,
  `tod_bpm_audit_id` bigint UNSIGNED NOT NULL,
  `pka_risiko_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tod_bpm_risiko`
--

INSERT INTO `tod_bpm_risiko` (`id`, `tod_bpm_audit_id`, `pka_risiko_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 1, 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 1, 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 1, 4, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 1, 5, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 1, 6, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 2, 7, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 2, 8, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 2, 9, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 2, 10, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 2, 11, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 2, 12, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 3, 13, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 3, 14, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 3, 15, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 3, 16, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 3, 17, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 3, 18, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 4, 19, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 4, 20, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 4, 21, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 4, 22, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 4, 23, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 4, 24, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(25, 5, 25, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(26, 5, 26, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(27, 5, 27, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(28, 5, 28, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(29, 5, 29, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(30, 5, 30, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(31, 6, 31, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(32, 6, 32, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(33, 6, 33, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(34, 6, 34, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(35, 6, 35, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(36, 6, 36, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(37, 7, 37, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(38, 7, 38, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(39, 7, 39, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(40, 7, 40, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(41, 7, 41, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(42, 7, 42, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(43, 8, 43, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(44, 8, 44, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(45, 8, 45, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(46, 8, 46, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(47, 8, 47, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(48, 8, 48, '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `toe_audit`
--

CREATE TABLE `toe_audit` (
  `id` bigint UNSIGNED NOT NULL,
  `perencanaan_audit_id` bigint UNSIGNED NOT NULL,
  `judul_bpm` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pengendalian_eksisting` text COLLATE utf8mb4_unicode_ci,
  `pemilihan_sampel_audit` text COLLATE utf8mb4_unicode_ci,
  `resiko` text COLLATE utf8mb4_unicode_ci,
  `kontrol` text COLLATE utf8mb4_unicode_ci,
  `file_kka_toe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approval` enum('pending','approved_level1','approved','rejected_level1','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_level1` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level1` timestamp NULL DEFAULT NULL,
  `rejected_by_level1` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level1` timestamp NULL DEFAULT NULL,
  `rejection_reason_level1` text COLLATE utf8mb4_unicode_ci,
  `approved_by_level2` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level2` timestamp NULL DEFAULT NULL,
  `rejected_by_level2` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level2` timestamp NULL DEFAULT NULL,
  `rejection_reason_level2` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `toe_audit`
--

INSERT INTO `toe_audit` (`id`, `perencanaan_audit_id`, `judul_bpm`, `pengendalian_eksisting`, `pemilihan_sampel_audit`, `resiko`, `kontrol`, `file_kka_toe`, `status_approval`, `rejection_reason`, `approved_by`, `approved_at`, `approved_by_level1`, `approved_at_level1`, `rejected_by_level1`, `rejected_at_level1`, `rejection_reason_level1`, `approved_by_level2`, `approved_at_level2`, `rejected_by_level2`, `rejected_at_level2`, `rejection_reason_level2`, `created_at`, `updated_at`) VALUES
(1, 1, 'Business Process Mapping - Audit Operasional', NULL, 'Sampel audit dipilih berdasarkan risiko tinggi dan materialitas transaksi.', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 4, 'Business Process Mapping - Konsultasi', NULL, 'Sampel audit dipilih berdasarkan risiko tinggi dan materialitas transaksi.', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 5, 'Business Process Mapping - Audit Operasional', NULL, 'Sampel audit dipilih berdasarkan risiko tinggi dan materialitas transaksi.', NULL, NULL, NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 6, 'Business Process Mapping - Audit Kepatuhan', NULL, 'Sampel audit dipilih berdasarkan risiko tinggi dan materialitas transaksi.', NULL, NULL, NULL, 'rejected', 'Pengendalian yang diidentifikasi tidak sesuai dengan standar berlaku.', 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 8, 'Business Process Mapping - Audit Keuangan', NULL, 'Sampel audit dipilih berdasarkan risiko tinggi dan materialitas transaksi.', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `toe_evaluasi`
--

CREATE TABLE `toe_evaluasi` (
  `id` bigint UNSIGNED NOT NULL,
  `toe_audit_id` bigint UNSIGNED NOT NULL,
  `hasil_evaluasi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `toe_evaluasi`
--

INSERT INTO `toe_evaluasi` (`id`, `toe_audit_id`, `hasil_evaluasi`, `created_at`, `updated_at`) VALUES
(1, 1, 'Tidak Efektif', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 2, 'Efektif', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 3, 'Efektif', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 4, 'Efektif Sebagian', '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 5, 'Efektif Sebagian', '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `toe_kontrol`
--

CREATE TABLE `toe_kontrol` (
  `id` bigint UNSIGNED NOT NULL,
  `toe_audit_id` bigint UNSIGNED NOT NULL,
  `pka_kontrol_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `toe_kontrol`
--

INSERT INTO `toe_kontrol` (`id`, `toe_audit_id`, `pka_kontrol_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 1, 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 1, 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 1, 4, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 1, 5, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 1, 6, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 1, 7, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 1, 8, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 1, 9, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 1, 10, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 1, 11, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 1, 12, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 2, 37, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 2, 38, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 2, 39, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 2, 40, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 2, 41, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 2, 42, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 2, 43, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 2, 44, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 2, 45, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 2, 46, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 2, 47, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 2, 48, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(25, 3, 49, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(26, 3, 50, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(27, 3, 51, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(28, 3, 52, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(29, 3, 53, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(30, 3, 54, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(31, 3, 55, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(32, 3, 56, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(33, 3, 57, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(34, 3, 58, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(35, 3, 59, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(36, 3, 60, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(37, 4, 61, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(38, 4, 62, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(39, 4, 63, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(40, 4, 64, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(41, 4, 65, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(42, 4, 66, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(43, 4, 67, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(44, 4, 68, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(45, 4, 69, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(46, 4, 70, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(47, 4, 71, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(48, 4, 72, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(49, 5, 85, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(50, 5, 86, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(51, 5, 87, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(52, 5, 88, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(53, 5, 89, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(54, 5, 90, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(55, 5, 91, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(56, 5, 92, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(57, 5, 93, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(58, 5, 94, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(59, 5, 95, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(60, 5, 96, '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `toe_risiko`
--

CREATE TABLE `toe_risiko` (
  `id` bigint UNSIGNED NOT NULL,
  `toe_audit_id` bigint UNSIGNED NOT NULL,
  `pka_risiko_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `toe_risiko`
--

INSERT INTO `toe_risiko` (`id`, `toe_audit_id`, `pka_risiko_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 1, 2, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 1, 3, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 1, 4, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 1, 5, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 1, 6, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 2, 19, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 2, 20, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(9, 2, 21, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(10, 2, 22, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(11, 2, 23, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(12, 2, 24, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(13, 3, 25, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(14, 3, 26, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(15, 3, 27, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(16, 3, 28, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(17, 3, 29, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(18, 3, 30, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(19, 4, 31, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(20, 4, 32, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(21, 4, 33, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(22, 4, 34, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(23, 4, 35, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(24, 4, 36, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(25, 5, 43, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(26, 5, 44, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(27, 5, 45, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(28, 5, 46, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(29, 5, 47, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(30, 5, 48, '2026-05-19 22:21:17', '2026-05-19 22:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Tapeli', 'demo@user.com', '2026-05-19 22:25:29', '$2y$12$k.Nerdtswkg0zm8Tz0HFHe7HBcjHTn5EqJv5E19GAnyAp0G9/4k0q', 'y5IoU60dHD', '2026-05-19 22:21:11', '2026-05-19 22:25:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `walkthrough_audit`
--

CREATE TABLE `walkthrough_audit` (
  `id` bigint UNSIGNED NOT NULL,
  `perencanaan_audit_id` bigint UNSIGNED NOT NULL,
  `program_kerja_audit_id` bigint UNSIGNED DEFAULT NULL,
  `tanggal_walkthrough` date NOT NULL,
  `planned_walkthrough_date` date DEFAULT NULL,
  `actual_walkthrough_date` date DEFAULT NULL,
  `auditee_nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hasil_walkthrough` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_bpm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approval` enum('pending','approved_level1','approved','rejected_level1','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_level1` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level1` timestamp NULL DEFAULT NULL,
  `rejected_by_level1` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level1` timestamp NULL DEFAULT NULL,
  `rejection_reason_level1` text COLLATE utf8mb4_unicode_ci,
  `approved_by_level2` bigint UNSIGNED DEFAULT NULL,
  `approved_at_level2` timestamp NULL DEFAULT NULL,
  `rejected_by_level2` bigint UNSIGNED DEFAULT NULL,
  `rejected_at_level2` timestamp NULL DEFAULT NULL,
  `rejection_reason_level2` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `walkthrough_audit`
--

INSERT INTO `walkthrough_audit` (`id`, `perencanaan_audit_id`, `program_kerja_audit_id`, `tanggal_walkthrough`, `planned_walkthrough_date`, `actual_walkthrough_date`, `auditee_nama`, `hasil_walkthrough`, `file_bpm`, `status_approval`, `rejection_reason`, `approved_by`, `approved_at`, `approved_by_level1`, `approved_at_level1`, `rejected_by_level1`, `rejected_at_level1`, `rejection_reason_level1`, `approved_by_level2`, `approved_at_level2`, `rejected_by_level2`, `rejected_at_level2`, `rejection_reason_level2`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-07-12', '2024-07-12', '2024-07-12', 'BOD', 'Walkthrough telah dilaksanakan untuk memahami proses operasional. Ditemukan beberapa area yang memerlukan perhatian khusus dalam hal efisiensi dan kepatuhan SOP.', NULL, 'rejected', 'Auditee tidak dapat hadir pada waktu yang ditentukan, walkthrough perlu ditunda.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(2, 2, 2, '2024-08-11', '2024-08-11', '2024-08-11', 'HUMAN CAPITAL', 'Hasil walkthrough menunjukkan bahwa proses operasional berjalan sesuai dengan standar yang ditetapkan. Beberapa rekomendasi perbaikan telah diidentifikasi.', NULL, 'rejected', 'Auditee tidak dapat hadir pada waktu yang ditentukan, walkthrough perlu ditunda.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(3, 3, 3, '1970-01-01', '2024-09-10', '1970-01-01', 'OPERASI', 'Hasil walkthrough audit khusus menunjukkan bahwa area yang diaudit telah memenuhi kriteria yang ditetapkan. Beberapa catatan perbaikan minor telah diidentifikasi.', NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(4, 4, 4, '2024-10-12', '2024-10-10', '2024-10-12', 'SPI', 'Hasil walkthrough konsultasi menunjukkan bahwa proses yang dikonsultasikan berjalan dengan baik. Beberapa rekomendasi optimasi telah diidentifikasi.', NULL, 'rejected', 'Lokasi walkthrough tidak dapat diakses pada waktu yang direncanakan, perlu koordinasi ulang.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(5, 5, 5, '2024-11-10', '2024-11-09', '2024-11-10', 'CABANG KALTIMRA', 'Walkthrough mengungkapkan beberapa ketidaksesuaian dalam implementasi prosedur operasional. Perlu dilakukan perbaikan untuk meningkatkan efektivitas.', NULL, 'approved', NULL, 1, '2026-05-19 22:48:28', 1, '2026-05-19 22:48:24', NULL, NULL, NULL, 1, '2026-05-19 22:48:28', NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(6, 6, 6, '2024-12-09', '2024-12-09', NULL, 'KEUANGAN', 'Hasil walkthrough menunjukkan bahwa proses operasional berjalan sesuai dengan standar yang ditetapkan. Beberapa rekomendasi perbaikan telah diidentifikasi.', NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(7, 7, 7, '2025-01-08', '2025-01-08', NULL, 'CABANG KALTIMRA', 'Hasil walkthrough menunjukkan bahwa proses operasional berjalan sesuai dengan standar yang ditetapkan. Beberapa rekomendasi perbaikan telah diidentifikasi.', NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17'),
(8, 8, 8, '2025-02-08', '2025-02-07', '2025-02-08', 'OPERASI', 'Walkthrough telah dilaksanakan untuk memahami proses operasional. Ditemukan beberapa area yang memerlukan perhatian khusus dalam hal efisiensi dan kepatuhan SOP.', NULL, 'approved', NULL, 1, '2026-05-19 22:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 22:21:17', '2026-05-19 22:21:17');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `email_notification_logs`
--
ALTER TABLE `email_notification_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_notification_logs_penutup_lha_rekomendasi_id_foreign` (`penutup_lha_rekomendasi_id`),
  ADD KEY `email_notification_logs_master_user_id_foreign` (`master_user_id`),
  ADD KEY `email_notification_logs_sent_by_foreign` (`sent_by`);

--
-- Indeks untuk tabel `entry_meeting`
--
ALTER TABLE `entry_meeting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entry_meeting_auditee_id_foreign` (`auditee_id`),
  ADD KEY `entry_meeting_program_kerja_audit_id_foreign` (`program_kerja_audit_id`),
  ADD KEY `entry_meeting_approved_by_level1_foreign` (`approved_by_level1`),
  ADD KEY `entry_meeting_rejected_by_level1_foreign` (`rejected_by_level1`),
  ADD KEY `entry_meeting_approved_by_level2_foreign` (`approved_by_level2`),
  ADD KEY `entry_meeting_rejected_by_level2_foreign` (`rejected_by_level2`);

--
-- Indeks untuk tabel `exit_meeting_uploads`
--
ALTER TABLE `exit_meeting_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exit_meeting_uploads_auditee_id_foreign` (`auditee_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jadwal_pkpt_audits`
--
ALTER TABLE `jadwal_pkpt_audits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jadwal_pkpt_audits_auditee_id_foreign` (`auditee_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `lha_lhk_uploads`
--
ALTER TABLE `lha_lhk_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lha_lhk_uploads_pelaporan_hasil_audit_id_foreign` (`pelaporan_hasil_audit_id`);

--
-- Indeks untuk tabel `master_akses_user`
--
ALTER TABLE `master_akses_user`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_area`
--
ALTER TABLE `master_area`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_auditee`
--
ALTER TABLE `master_auditee`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_jenis_audit`
--
ALTER TABLE `master_jenis_audit`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_kode_aoi`
--
ALTER TABLE `master_kode_aoi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_kode_risk`
--
ALTER TABLE `master_kode_risk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_region`
--
ALTER TABLE `master_region`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_unit`
--
ALTER TABLE `master_unit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `master_unit_kode_unit_unique` (`kode_unit`);

--
-- Indeks untuk tabel `master_user`
--
ALTER TABLE `master_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `master_user_username_unique` (`username`),
  ADD KEY `master_user_master_auditee_id_foreign` (`master_auditee_id`),
  ADD KEY `master_user_master_akses_user_id_foreign` (`master_akses_user_id`),
  ADD KEY `master_user_master_area_id_foreign` (`master_area_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `monitoring_tindak_lanjut`
--
ALTER TABLE `monitoring_tindak_lanjut`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `nota_dinas_uploads`
--
ALTER TABLE `nota_dinas_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nota_dinas_uploads_pelaporan_hasil_audit_id_foreign` (`pelaporan_hasil_audit_id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `pelaporan_hasil_audit`
--
ALTER TABLE `pelaporan_hasil_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelaporan_hasil_audit_perencanaan_audit_id_foreign` (`perencanaan_audit_id`),
  ADD KEY `pelaporan_hasil_audit_kode_aoi_id_foreign` (`kode_aoi_id`),
  ADD KEY `pelaporan_hasil_audit_kode_risk_id_foreign` (`kode_risk_id`),
  ADD KEY `pelaporan_hasil_audit_approved_by_foreign` (`approved_by`),
  ADD KEY `pelaporan_hasil_audit_approved_by_level1_foreign` (`approved_by_level1`),
  ADD KEY `pelaporan_hasil_audit_rejected_by_level1_foreign` (`rejected_by_level1`),
  ADD KEY `pelaporan_hasil_audit_approved_by_level2_foreign` (`approved_by_level2`),
  ADD KEY `pelaporan_hasil_audit_rejected_by_level2_foreign` (`rejected_by_level2`),
  ADD KEY `pelaporan_hasil_audit_jenis_audit_id_foreign` (`jenis_audit_id`);

--
-- Indeks untuk tabel `pelaporan_isi_lha`
--
ALTER TABLE `pelaporan_isi_lha`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelaporan_isi_lha_pelaporan_hasil_audit_id_foreign` (`pelaporan_hasil_audit_id`),
  ADD KEY `pelaporan_isi_lha_approved_by_foreign` (`approved_by`);

--
-- Indeks untuk tabel `pelaporan_temuan`
--
ALTER TABLE `pelaporan_temuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelaporan_temuan_pelaporan_hasil_audit_id_foreign` (`pelaporan_hasil_audit_id`),
  ADD KEY `pelaporan_temuan_kode_aoi_id_foreign` (`kode_aoi_id`),
  ADD KEY `pelaporan_temuan_kode_risk_id_foreign` (`kode_risk_id`),
  ADD KEY `pelaporan_temuan_approved_by_foreign` (`approved_by`);

--
-- Indeks untuk tabel `penutup_lha_rekomendasi`
--
ALTER TABLE `penutup_lha_rekomendasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penutup_lha_rekomendasi_approved_by_foreign` (`approved_by`),
  ADD KEY `penutup_lha_rekomendasi_pelaporan_isi_lha_id_foreign` (`pelaporan_isi_lha_id`),
  ADD KEY `penutup_lha_rekomendasi_approved_by_level1_foreign` (`approved_by_level1`),
  ADD KEY `penutup_lha_rekomendasi_rejected_by_level1_foreign` (`rejected_by_level1`),
  ADD KEY `penutup_lha_rekomendasi_approved_by_level2_foreign` (`approved_by_level2`),
  ADD KEY `penutup_lha_rekomendasi_rejected_by_level2_foreign` (`rejected_by_level2`);

--
-- Indeks untuk tabel `penutup_lha_rekomendasi_pic`
--
ALTER TABLE `penutup_lha_rekomendasi_pic`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `penutup_lha_rekomendasi_pic_unique` (`penutup_lha_rekomendasi_id`,`master_user_id`),
  ADD KEY `penutup_lha_rekomendasi_pic_master_user_id_foreign` (`master_user_id`);

--
-- Indeks untuk tabel `penutup_lha_tindak_lanjut`
--
ALTER TABLE `penutup_lha_tindak_lanjut`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penutup_lha_tindak_lanjut_penutup_lha_rekomendasi_id_foreign` (`penutup_lha_rekomendasi_id`);

--
-- Indeks untuk tabel `perencanaan_audit`
--
ALTER TABLE `perencanaan_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perencanaan_audit_auditee_id_foreign` (`auditee_id`),
  ADD KEY `perencanaan_audit_koordinator_id_foreign` (`koordinator_id`),
  ADD KEY `perencanaan_audit_ketua_tim_id_foreign` (`ketua_tim_id`),
  ADD KEY `perencanaan_audit_jenis_audit_id_foreign` (`jenis_audit_id`),
  ADD KEY `perencanaan_audit_area_id_foreign` (`area_id`);

--
-- Indeks untuk tabel `pka_dokumen`
--
ALTER TABLE `pka_dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pka_dokumen_program_kerja_audit_id_foreign` (`program_kerja_audit_id`),
  ADD KEY `pka_dokumen_approved_by_level1_foreign` (`approved_by_level1`),
  ADD KEY `pka_dokumen_rejected_by_level1_foreign` (`rejected_by_level1`),
  ADD KEY `pka_dokumen_approved_by_level2_foreign` (`approved_by_level2`),
  ADD KEY `pka_dokumen_rejected_by_level2_foreign` (`rejected_by_level2`);

--
-- Indeks untuk tabel `pka_kontrol`
--
ALTER TABLE `pka_kontrol`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pka_kontrol_pka_risiko_id_foreign` (`pka_risiko_id`);

--
-- Indeks untuk tabel `pka_milestone`
--
ALTER TABLE `pka_milestone`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pka_milestone_program_kerja_audit_id_foreign` (`program_kerja_audit_id`);

--
-- Indeks untuk tabel `pka_proses_bisnis`
--
ALTER TABLE `pka_proses_bisnis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pka_proses_bisnis_program_kerja_audit_id_foreign` (`program_kerja_audit_id`);

--
-- Indeks untuk tabel `pka_risiko`
--
ALTER TABLE `pka_risiko`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pka_risiko_pka_proses_bisnis_id_foreign` (`pka_proses_bisnis_id`);

--
-- Indeks untuk tabel `pka_risk_based_audit`
--
ALTER TABLE `pka_risk_based_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pka_risk_based_audit_program_kerja_audit_id_foreign` (`program_kerja_audit_id`);

--
-- Indeks untuk tabel `program_kerja_audit`
--
ALTER TABLE `program_kerja_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_kerja_audit_perencanaan_audit_id_foreign` (`perencanaan_audit_id`),
  ADD KEY `program_kerja_audit_approved_by_level1_foreign` (`approved_by_level1`),
  ADD KEY `program_kerja_audit_rejected_by_level1_foreign` (`rejected_by_level1`),
  ADD KEY `program_kerja_audit_approved_by_level2_foreign` (`approved_by_level2`),
  ADD KEY `program_kerja_audit_rejected_by_level2_foreign` (`rejected_by_level2`);

--
-- Indeks untuk tabel `realisasi_audits`
--
ALTER TABLE `realisasi_audits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `realisasi_audits_perencanaan_audit_id_foreign` (`perencanaan_audit_id`),
  ADD KEY `realisasi_audits_approved_by_foreign` (`approved_by`),
  ADD KEY `realisasi_audits_approved_by_level1_foreign` (`approved_by_level1`),
  ADD KEY `realisasi_audits_rejected_by_level1_foreign` (`rejected_by_level1`),
  ADD KEY `realisasi_audits_approved_by_level2_foreign` (`approved_by_level2`),
  ADD KEY `realisasi_audits_rejected_by_level2_foreign` (`rejected_by_level2`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `tod_bpm_audit`
--
ALTER TABLE `tod_bpm_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tod_bpm_audit_perencanaan_audit_id_foreign` (`perencanaan_audit_id`),
  ADD KEY `tod_bpm_audit_approved_by_level1_foreign` (`approved_by_level1`),
  ADD KEY `tod_bpm_audit_rejected_by_level1_foreign` (`rejected_by_level1`),
  ADD KEY `tod_bpm_audit_approved_by_level2_foreign` (`approved_by_level2`),
  ADD KEY `tod_bpm_audit_rejected_by_level2_foreign` (`rejected_by_level2`);

--
-- Indeks untuk tabel `tod_bpm_evaluasi`
--
ALTER TABLE `tod_bpm_evaluasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tod_bpm_evaluasi_tod_bpm_audit_id_foreign` (`tod_bpm_audit_id`);

--
-- Indeks untuk tabel `tod_bpm_kontrol`
--
ALTER TABLE `tod_bpm_kontrol`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tod_bpm_kontrol_tod_bpm_audit_id_pka_kontrol_id_unique` (`tod_bpm_audit_id`,`pka_kontrol_id`),
  ADD KEY `tod_bpm_kontrol_pka_kontrol_id_foreign` (`pka_kontrol_id`);

--
-- Indeks untuk tabel `tod_bpm_risiko`
--
ALTER TABLE `tod_bpm_risiko`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tod_bpm_risiko_tod_bpm_audit_id_pka_risiko_id_unique` (`tod_bpm_audit_id`,`pka_risiko_id`),
  ADD KEY `tod_bpm_risiko_pka_risiko_id_foreign` (`pka_risiko_id`);

--
-- Indeks untuk tabel `toe_audit`
--
ALTER TABLE `toe_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `toe_audit_perencanaan_audit_id_foreign` (`perencanaan_audit_id`),
  ADD KEY `toe_audit_approved_by_level1_foreign` (`approved_by_level1`),
  ADD KEY `toe_audit_rejected_by_level1_foreign` (`rejected_by_level1`),
  ADD KEY `toe_audit_approved_by_level2_foreign` (`approved_by_level2`),
  ADD KEY `toe_audit_rejected_by_level2_foreign` (`rejected_by_level2`);

--
-- Indeks untuk tabel `toe_evaluasi`
--
ALTER TABLE `toe_evaluasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `toe_evaluasi_toe_audit_id_foreign` (`toe_audit_id`);

--
-- Indeks untuk tabel `toe_kontrol`
--
ALTER TABLE `toe_kontrol`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `toe_kontrol_toe_audit_id_pka_kontrol_id_unique` (`toe_audit_id`,`pka_kontrol_id`),
  ADD KEY `toe_kontrol_pka_kontrol_id_foreign` (`pka_kontrol_id`);

--
-- Indeks untuk tabel `toe_risiko`
--
ALTER TABLE `toe_risiko`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `toe_risiko_toe_audit_id_pka_risiko_id_unique` (`toe_audit_id`,`pka_risiko_id`),
  ADD KEY `toe_risiko_pka_risiko_id_foreign` (`pka_risiko_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indeks untuk tabel `walkthrough_audit`
--
ALTER TABLE `walkthrough_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `walkthrough_audit_perencanaan_audit_id_foreign` (`perencanaan_audit_id`),
  ADD KEY `walkthrough_audit_program_kerja_audit_id_foreign` (`program_kerja_audit_id`),
  ADD KEY `walkthrough_audit_approved_by_level1_foreign` (`approved_by_level1`),
  ADD KEY `walkthrough_audit_rejected_by_level1_foreign` (`rejected_by_level1`),
  ADD KEY `walkthrough_audit_approved_by_level2_foreign` (`approved_by_level2`),
  ADD KEY `walkthrough_audit_rejected_by_level2_foreign` (`rejected_by_level2`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `email_notification_logs`
--
ALTER TABLE `email_notification_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `entry_meeting`
--
ALTER TABLE `entry_meeting`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `exit_meeting_uploads`
--
ALTER TABLE `exit_meeting_uploads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal_pkpt_audits`
--
ALTER TABLE `jadwal_pkpt_audits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `lha_lhk_uploads`
--
ALTER TABLE `lha_lhk_uploads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `master_akses_user`
--
ALTER TABLE `master_akses_user`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `master_auditee`
--
ALTER TABLE `master_auditee`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `master_jenis_audit`
--
ALTER TABLE `master_jenis_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `master_kode_aoi`
--
ALTER TABLE `master_kode_aoi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT untuk tabel `master_kode_risk`
--
ALTER TABLE `master_kode_risk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT untuk tabel `master_unit`
--
ALTER TABLE `master_unit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `master_user`
--
ALTER TABLE `master_user`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT untuk tabel `monitoring_tindak_lanjut`
--
ALTER TABLE `monitoring_tindak_lanjut`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `nota_dinas_uploads`
--
ALTER TABLE `nota_dinas_uploads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pelaporan_hasil_audit`
--
ALTER TABLE `pelaporan_hasil_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `pelaporan_isi_lha`
--
ALTER TABLE `pelaporan_isi_lha`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pelaporan_temuan`
--
ALTER TABLE `pelaporan_temuan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT untuk tabel `penutup_lha_rekomendasi`
--
ALTER TABLE `penutup_lha_rekomendasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `penutup_lha_rekomendasi_pic`
--
ALTER TABLE `penutup_lha_rekomendasi_pic`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `penutup_lha_tindak_lanjut`
--
ALTER TABLE `penutup_lha_tindak_lanjut`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `perencanaan_audit`
--
ALTER TABLE `perencanaan_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `pka_dokumen`
--
ALTER TABLE `pka_dokumen`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `pka_kontrol`
--
ALTER TABLE `pka_kontrol`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT untuk tabel `pka_milestone`
--
ALTER TABLE `pka_milestone`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT untuk tabel `pka_proses_bisnis`
--
ALTER TABLE `pka_proses_bisnis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `pka_risiko`
--
ALTER TABLE `pka_risiko`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `pka_risk_based_audit`
--
ALTER TABLE `pka_risk_based_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `program_kerja_audit`
--
ALTER TABLE `program_kerja_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `realisasi_audits`
--
ALTER TABLE `realisasi_audits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `tod_bpm_audit`
--
ALTER TABLE `tod_bpm_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tod_bpm_evaluasi`
--
ALTER TABLE `tod_bpm_evaluasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tod_bpm_kontrol`
--
ALTER TABLE `tod_bpm_kontrol`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT untuk tabel `tod_bpm_risiko`
--
ALTER TABLE `tod_bpm_risiko`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `toe_audit`
--
ALTER TABLE `toe_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `toe_evaluasi`
--
ALTER TABLE `toe_evaluasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `toe_kontrol`
--
ALTER TABLE `toe_kontrol`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT untuk tabel `toe_risiko`
--
ALTER TABLE `toe_risiko`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `walkthrough_audit`
--
ALTER TABLE `walkthrough_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `email_notification_logs`
--
ALTER TABLE `email_notification_logs`
  ADD CONSTRAINT `email_notification_logs_master_user_id_foreign` FOREIGN KEY (`master_user_id`) REFERENCES `master_user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `email_notification_logs_penutup_lha_rekomendasi_id_foreign` FOREIGN KEY (`penutup_lha_rekomendasi_id`) REFERENCES `penutup_lha_rekomendasi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `email_notification_logs_sent_by_foreign` FOREIGN KEY (`sent_by`) REFERENCES `master_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `entry_meeting`
--
ALTER TABLE `entry_meeting`
  ADD CONSTRAINT `entry_meeting_approved_by_level1_foreign` FOREIGN KEY (`approved_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `entry_meeting_approved_by_level2_foreign` FOREIGN KEY (`approved_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `entry_meeting_auditee_id_foreign` FOREIGN KEY (`auditee_id`) REFERENCES `master_auditee` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `entry_meeting_program_kerja_audit_id_foreign` FOREIGN KEY (`program_kerja_audit_id`) REFERENCES `program_kerja_audit` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `entry_meeting_rejected_by_level1_foreign` FOREIGN KEY (`rejected_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `entry_meeting_rejected_by_level2_foreign` FOREIGN KEY (`rejected_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `exit_meeting_uploads`
--
ALTER TABLE `exit_meeting_uploads`
  ADD CONSTRAINT `exit_meeting_uploads_auditee_id_foreign` FOREIGN KEY (`auditee_id`) REFERENCES `master_auditee` (`id`);

--
-- Ketidakleluasaan untuk tabel `jadwal_pkpt_audits`
--
ALTER TABLE `jadwal_pkpt_audits`
  ADD CONSTRAINT `jadwal_pkpt_audits_auditee_id_foreign` FOREIGN KEY (`auditee_id`) REFERENCES `master_auditee` (`id`) ON DELETE RESTRICT;

--
-- Ketidakleluasaan untuk tabel `lha_lhk_uploads`
--
ALTER TABLE `lha_lhk_uploads`
  ADD CONSTRAINT `lha_lhk_uploads_pelaporan_hasil_audit_id_foreign` FOREIGN KEY (`pelaporan_hasil_audit_id`) REFERENCES `pelaporan_hasil_audit` (`id`);

--
-- Ketidakleluasaan untuk tabel `master_user`
--
ALTER TABLE `master_user`
  ADD CONSTRAINT `master_user_master_akses_user_id_foreign` FOREIGN KEY (`master_akses_user_id`) REFERENCES `master_akses_user` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `master_user_master_area_id_foreign` FOREIGN KEY (`master_area_id`) REFERENCES `master_area` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `master_user_master_auditee_id_foreign` FOREIGN KEY (`master_auditee_id`) REFERENCES `master_auditee` (`id`) ON DELETE RESTRICT;

--
-- Ketidakleluasaan untuk tabel `nota_dinas_uploads`
--
ALTER TABLE `nota_dinas_uploads`
  ADD CONSTRAINT `nota_dinas_uploads_pelaporan_hasil_audit_id_foreign` FOREIGN KEY (`pelaporan_hasil_audit_id`) REFERENCES `pelaporan_hasil_audit` (`id`);

--
-- Ketidakleluasaan untuk tabel `pelaporan_hasil_audit`
--
ALTER TABLE `pelaporan_hasil_audit`
  ADD CONSTRAINT `pelaporan_hasil_audit_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pelaporan_hasil_audit_approved_by_level1_foreign` FOREIGN KEY (`approved_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pelaporan_hasil_audit_approved_by_level2_foreign` FOREIGN KEY (`approved_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pelaporan_hasil_audit_jenis_audit_id_foreign` FOREIGN KEY (`jenis_audit_id`) REFERENCES `master_jenis_audit` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pelaporan_hasil_audit_kode_aoi_id_foreign` FOREIGN KEY (`kode_aoi_id`) REFERENCES `master_kode_aoi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pelaporan_hasil_audit_kode_risk_id_foreign` FOREIGN KEY (`kode_risk_id`) REFERENCES `master_kode_risk` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pelaporan_hasil_audit_perencanaan_audit_id_foreign` FOREIGN KEY (`perencanaan_audit_id`) REFERENCES `perencanaan_audit` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `pelaporan_hasil_audit_rejected_by_level1_foreign` FOREIGN KEY (`rejected_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pelaporan_hasil_audit_rejected_by_level2_foreign` FOREIGN KEY (`rejected_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `pelaporan_isi_lha`
--
ALTER TABLE `pelaporan_isi_lha`
  ADD CONSTRAINT `pelaporan_isi_lha_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pelaporan_isi_lha_pelaporan_hasil_audit_id_foreign` FOREIGN KEY (`pelaporan_hasil_audit_id`) REFERENCES `pelaporan_hasil_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pelaporan_temuan`
--
ALTER TABLE `pelaporan_temuan`
  ADD CONSTRAINT `pelaporan_temuan_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pelaporan_temuan_kode_aoi_id_foreign` FOREIGN KEY (`kode_aoi_id`) REFERENCES `master_kode_aoi` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `pelaporan_temuan_kode_risk_id_foreign` FOREIGN KEY (`kode_risk_id`) REFERENCES `master_kode_risk` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `pelaporan_temuan_pelaporan_hasil_audit_id_foreign` FOREIGN KEY (`pelaporan_hasil_audit_id`) REFERENCES `pelaporan_hasil_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penutup_lha_rekomendasi`
--
ALTER TABLE `penutup_lha_rekomendasi`
  ADD CONSTRAINT `penutup_lha_rekomendasi_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penutup_lha_rekomendasi_approved_by_level1_foreign` FOREIGN KEY (`approved_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penutup_lha_rekomendasi_approved_by_level2_foreign` FOREIGN KEY (`approved_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penutup_lha_rekomendasi_pelaporan_isi_lha_id_foreign` FOREIGN KEY (`pelaporan_isi_lha_id`) REFERENCES `pelaporan_temuan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penutup_lha_rekomendasi_rejected_by_level1_foreign` FOREIGN KEY (`rejected_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penutup_lha_rekomendasi_rejected_by_level2_foreign` FOREIGN KEY (`rejected_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `penutup_lha_rekomendasi_pic`
--
ALTER TABLE `penutup_lha_rekomendasi_pic`
  ADD CONSTRAINT `penutup_lha_rekomendasi_pic_master_user_id_foreign` FOREIGN KEY (`master_user_id`) REFERENCES `master_user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penutup_lha_rekomendasi_pic_penutup_lha_rekomendasi_id_foreign` FOREIGN KEY (`penutup_lha_rekomendasi_id`) REFERENCES `penutup_lha_rekomendasi` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penutup_lha_tindak_lanjut`
--
ALTER TABLE `penutup_lha_tindak_lanjut`
  ADD CONSTRAINT `penutup_lha_tindak_lanjut_penutup_lha_rekomendasi_id_foreign` FOREIGN KEY (`penutup_lha_rekomendasi_id`) REFERENCES `penutup_lha_rekomendasi` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `perencanaan_audit`
--
ALTER TABLE `perencanaan_audit`
  ADD CONSTRAINT `perencanaan_audit_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `master_area` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perencanaan_audit_auditee_id_foreign` FOREIGN KEY (`auditee_id`) REFERENCES `master_auditee` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `perencanaan_audit_jenis_audit_id_foreign` FOREIGN KEY (`jenis_audit_id`) REFERENCES `master_jenis_audit` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `perencanaan_audit_ketua_tim_id_foreign` FOREIGN KEY (`ketua_tim_id`) REFERENCES `master_user` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `perencanaan_audit_koordinator_id_foreign` FOREIGN KEY (`koordinator_id`) REFERENCES `master_user` (`id`) ON DELETE RESTRICT;

--
-- Ketidakleluasaan untuk tabel `pka_dokumen`
--
ALTER TABLE `pka_dokumen`
  ADD CONSTRAINT `pka_dokumen_approved_by_level1_foreign` FOREIGN KEY (`approved_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pka_dokumen_approved_by_level2_foreign` FOREIGN KEY (`approved_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pka_dokumen_program_kerja_audit_id_foreign` FOREIGN KEY (`program_kerja_audit_id`) REFERENCES `program_kerja_audit` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pka_dokumen_rejected_by_level1_foreign` FOREIGN KEY (`rejected_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pka_dokumen_rejected_by_level2_foreign` FOREIGN KEY (`rejected_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `pka_kontrol`
--
ALTER TABLE `pka_kontrol`
  ADD CONSTRAINT `pka_kontrol_pka_risiko_id_foreign` FOREIGN KEY (`pka_risiko_id`) REFERENCES `pka_risiko` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pka_milestone`
--
ALTER TABLE `pka_milestone`
  ADD CONSTRAINT `pka_milestone_program_kerja_audit_id_foreign` FOREIGN KEY (`program_kerja_audit_id`) REFERENCES `program_kerja_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pka_proses_bisnis`
--
ALTER TABLE `pka_proses_bisnis`
  ADD CONSTRAINT `pka_proses_bisnis_program_kerja_audit_id_foreign` FOREIGN KEY (`program_kerja_audit_id`) REFERENCES `program_kerja_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pka_risiko`
--
ALTER TABLE `pka_risiko`
  ADD CONSTRAINT `pka_risiko_pka_proses_bisnis_id_foreign` FOREIGN KEY (`pka_proses_bisnis_id`) REFERENCES `pka_proses_bisnis` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pka_risk_based_audit`
--
ALTER TABLE `pka_risk_based_audit`
  ADD CONSTRAINT `pka_risk_based_audit_program_kerja_audit_id_foreign` FOREIGN KEY (`program_kerja_audit_id`) REFERENCES `program_kerja_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `program_kerja_audit`
--
ALTER TABLE `program_kerja_audit`
  ADD CONSTRAINT `program_kerja_audit_approved_by_level1_foreign` FOREIGN KEY (`approved_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `program_kerja_audit_approved_by_level2_foreign` FOREIGN KEY (`approved_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `program_kerja_audit_perencanaan_audit_id_foreign` FOREIGN KEY (`perencanaan_audit_id`) REFERENCES `perencanaan_audit` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `program_kerja_audit_rejected_by_level1_foreign` FOREIGN KEY (`rejected_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `program_kerja_audit_rejected_by_level2_foreign` FOREIGN KEY (`rejected_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `realisasi_audits`
--
ALTER TABLE `realisasi_audits`
  ADD CONSTRAINT `realisasi_audits_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `realisasi_audits_approved_by_level1_foreign` FOREIGN KEY (`approved_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `realisasi_audits_approved_by_level2_foreign` FOREIGN KEY (`approved_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `realisasi_audits_perencanaan_audit_id_foreign` FOREIGN KEY (`perencanaan_audit_id`) REFERENCES `perencanaan_audit` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `realisasi_audits_rejected_by_level1_foreign` FOREIGN KEY (`rejected_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `realisasi_audits_rejected_by_level2_foreign` FOREIGN KEY (`rejected_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `tod_bpm_audit`
--
ALTER TABLE `tod_bpm_audit`
  ADD CONSTRAINT `tod_bpm_audit_approved_by_level1_foreign` FOREIGN KEY (`approved_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tod_bpm_audit_approved_by_level2_foreign` FOREIGN KEY (`approved_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tod_bpm_audit_perencanaan_audit_id_foreign` FOREIGN KEY (`perencanaan_audit_id`) REFERENCES `perencanaan_audit` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tod_bpm_audit_rejected_by_level1_foreign` FOREIGN KEY (`rejected_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tod_bpm_audit_rejected_by_level2_foreign` FOREIGN KEY (`rejected_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `tod_bpm_evaluasi`
--
ALTER TABLE `tod_bpm_evaluasi`
  ADD CONSTRAINT `tod_bpm_evaluasi_tod_bpm_audit_id_foreign` FOREIGN KEY (`tod_bpm_audit_id`) REFERENCES `tod_bpm_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tod_bpm_kontrol`
--
ALTER TABLE `tod_bpm_kontrol`
  ADD CONSTRAINT `tod_bpm_kontrol_pka_kontrol_id_foreign` FOREIGN KEY (`pka_kontrol_id`) REFERENCES `pka_kontrol` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tod_bpm_kontrol_tod_bpm_audit_id_foreign` FOREIGN KEY (`tod_bpm_audit_id`) REFERENCES `tod_bpm_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tod_bpm_risiko`
--
ALTER TABLE `tod_bpm_risiko`
  ADD CONSTRAINT `tod_bpm_risiko_pka_risiko_id_foreign` FOREIGN KEY (`pka_risiko_id`) REFERENCES `pka_risiko` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tod_bpm_risiko_tod_bpm_audit_id_foreign` FOREIGN KEY (`tod_bpm_audit_id`) REFERENCES `tod_bpm_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `toe_audit`
--
ALTER TABLE `toe_audit`
  ADD CONSTRAINT `toe_audit_approved_by_level1_foreign` FOREIGN KEY (`approved_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `toe_audit_approved_by_level2_foreign` FOREIGN KEY (`approved_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `toe_audit_perencanaan_audit_id_foreign` FOREIGN KEY (`perencanaan_audit_id`) REFERENCES `perencanaan_audit` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `toe_audit_rejected_by_level1_foreign` FOREIGN KEY (`rejected_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `toe_audit_rejected_by_level2_foreign` FOREIGN KEY (`rejected_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `toe_evaluasi`
--
ALTER TABLE `toe_evaluasi`
  ADD CONSTRAINT `toe_evaluasi_toe_audit_id_foreign` FOREIGN KEY (`toe_audit_id`) REFERENCES `toe_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `toe_kontrol`
--
ALTER TABLE `toe_kontrol`
  ADD CONSTRAINT `toe_kontrol_pka_kontrol_id_foreign` FOREIGN KEY (`pka_kontrol_id`) REFERENCES `pka_kontrol` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `toe_kontrol_toe_audit_id_foreign` FOREIGN KEY (`toe_audit_id`) REFERENCES `toe_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `toe_risiko`
--
ALTER TABLE `toe_risiko`
  ADD CONSTRAINT `toe_risiko_pka_risiko_id_foreign` FOREIGN KEY (`pka_risiko_id`) REFERENCES `pka_risiko` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `toe_risiko_toe_audit_id_foreign` FOREIGN KEY (`toe_audit_id`) REFERENCES `toe_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `walkthrough_audit`
--
ALTER TABLE `walkthrough_audit`
  ADD CONSTRAINT `walkthrough_audit_approved_by_level1_foreign` FOREIGN KEY (`approved_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `walkthrough_audit_approved_by_level2_foreign` FOREIGN KEY (`approved_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `walkthrough_audit_perencanaan_audit_id_foreign` FOREIGN KEY (`perencanaan_audit_id`) REFERENCES `perencanaan_audit` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `walkthrough_audit_program_kerja_audit_id_foreign` FOREIGN KEY (`program_kerja_audit_id`) REFERENCES `program_kerja_audit` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `walkthrough_audit_rejected_by_level1_foreign` FOREIGN KEY (`rejected_by_level1`) REFERENCES `master_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `walkthrough_audit_rejected_by_level2_foreign` FOREIGN KEY (`rejected_by_level2`) REFERENCES `master_user` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
