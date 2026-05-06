-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 06 Bulan Mei 2026 pada 01.38
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

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_sandy|127.0.0.1', 'i:3;', 1777969628),
('laravel_cache_sandy|127.0.0.1:timer', 'i:1777969628;', 1777969628);

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
(1, '2024-07-01', '2024-07-03', 1, 1, 'entry_meeting/undangan_1.pdf', 'entry_meeting/absensi_1.pdf', 'rejected', 'Lokasi entry meeting tidak dapat diakses pada waktu yang direncanakan, perlu koordinasi ulang.', 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, '2024-07-31', '2024-07-31', 2, 2, 'entry_meeting/undangan_2.pdf', 'entry_meeting/absensi_2.pdf', 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, '2024-08-30', '2024-08-30', 3, 3, 'entry_meeting/undangan_3.pdf', 'entry_meeting/absensi_3.pdf', 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, '2024-09-29', '2024-10-02', 4, 4, 'entry_meeting/undangan_4.pdf', 'entry_meeting/absensi_4.pdf', 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, '2024-10-29', '2024-10-31', 5, 5, 'entry_meeting/undangan_5.pdf', 'entry_meeting/absensi_5.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, '2024-11-28', '2024-11-30', 1, 6, 'entry_meeting/undangan_6.pdf', 'entry_meeting/absensi_6.pdf', 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, '2024-12-28', '2024-12-30', 2, 7, 'entry_meeting/undangan_7.pdf', 'entry_meeting/absensi_7.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, '2025-01-27', '2025-01-30', 3, 8, 'entry_meeting/undangan_8.pdf', 'entry_meeting/absensi_8.pdf', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
(1, 1, 'PKPT Tahunan', 3, '2024-07-01', '2024-07-10', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 1, 'PKPT Khusus', 2, '2024-08-01', '2024-08-05', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
(1, 'ASMAN KSPI', NULL, NULL),
(2, 'Manager', NULL, NULL),
(3, 'Assistant Manager', NULL, NULL),
(4, 'Auditee', NULL, NULL),
(5, 'ASMAN SPI', NULL, NULL),
(6, 'KSPI', NULL, NULL),
(7, 'Auditor', NULL, NULL),
(8, 'SUPER ADMIN', NULL, NULL),
(9, 'VIEW BOD', NULL, NULL),
(10, 'Superadmin', NULL, NULL),
(11, 'PIC Auditee', NULL, NULL),
(12, 'BOD', NULL, NULL);

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
(1, NULL, NULL, 'SPI'),
(2, NULL, NULL, 'KEUANGAN'),
(3, NULL, NULL, 'RENUS IT'),
(4, NULL, NULL, 'OPERASI'),
(5, NULL, NULL, 'HUMAN CAPITAL'),
(6, NULL, NULL, 'SEKPER'),
(7, NULL, NULL, 'BOD'),
(8, NULL, NULL, 'SUPER ADMIN'),
(9, NULL, NULL, 'CABANG KALTIMRA');

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
(1, 'Audit Operasional', 'SPI.01.02', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 'Audit Khusus', 'SPI.01.03', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 'Konsultasi', 'SPI.01.04', '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
(18, 'TEMUAN BERULANG', '07.06', 'Temuan berulang terkait Kasus yang Merugikan Perusahaan atau Negara', NULL, NULL);

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
(81, 'KEPATUHAN', 'K.3.2', 'Lingkungan', 'Risiko Sosial / Politik / Budaya', NULL, NULL);

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
  `master_akses_user_id` bigint UNSIGNED NOT NULL DEFAULT '2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_user`
--

INSERT INTO `master_user` (`id`, `nama`, `username`, `nip`, `email`, `no_telpon`, `jabatan`, `password`, `master_auditee_id`, `master_akses_user_id`, `created_at`, `updated_at`) VALUES
(1, 'System Administrator', 'superadmin', 'SUPERADMIN001', 'superadmin@pcn.co.id', '000000000000', 'System Administrator', '$2y$12$jrcSfSyoPngYGMg4CJ4fFe6XHrcUgVkzETdWQGPEzIMtZdUUAy/tm', 1, 10, '2026-05-04 21:23:44', '2026-05-04 21:23:44'),
(2, 'DINAR AFIDAH PRAVITA PUTRI', 'dinar.afidah', '01253007PST', 'dinar.afidah@pcn.co.id', '081234567001', 'JUNIOR OFFICER AUDITOR', '$2y$12$1oufIOuMKNu70lvzR57dYeonds7mu3JcEhOZC6r.BIMMO1vPFcF9a', 1, 7, '2026-05-04 21:23:44', '2026-05-04 21:23:44'),
(3, 'ASMAN SPI', 'asman.spi', '85012345SPI', 'asman.spi@pcn.co.id', '081234567002', 'ASISTEN MANAGER SPI', '$2y$12$p3Ze4ywfmoWzs7W4mImnpe/p7nqqIplZRpPoo5CpKJ4hArjrBPxfm', 1, 5, '2026-05-04 21:23:44', '2026-05-04 21:23:44'),
(4, 'AGIL FRASSETYO', 'agil.frassetyo', '84091962', 'agil.frassetyo@pcn.co.id', '081234567003', 'KEPALA SATUAN PENGAWAS INTERNAL', '$2y$12$CRT47.J4koMxqp.vgCUEMe1Yi47UdYlIUtHbODlMPF7Jb7oNF5uou', 1, 6, '2026-05-04 21:23:44', '2026-05-04 21:23:44'),
(5, 'DEWI SATYA NINGSIH', 'dewi.satya', '8010035TRK', 'dewi.satya@pcn.co.id', '081234567004', 'SUPERVISOR AKUNTANSI', '$2y$12$b9FhvK7bbbA0LLf/KAECiusfzQAl23CmkjuGC/5MQ5mjwC5S9G1W2', 2, 4, '2026-05-04 21:23:44', '2026-05-04 21:23:44'),
(6, 'ANDI RIPANSYAH', 'andi.ripansyah', '7610036TRK', 'andi.ripansyah@pcn.co.id', '081234567005', 'ASMAN KEUANGAN & ANGGARAN', '$2y$12$BYq5wPdhXyMTccXWwQYF8u.whY4gnCZpPgDT6y3QZrWDRcroGJMmi', 2, 4, '2026-05-04 21:23:45', '2026-05-04 21:23:45'),
(7, 'YUSUF SAEFUDIN', 'yusuf.saefudin', '7510005TRK', 'yusuf.saefudin@pcn.co.id', '081234567006', 'MANAGER KEUANGAN', '$2y$12$P3CkYdbV0AUyRRO59Zuy4OrSnKGMmYLTSQ.B3KLb7afw5J1hisZPu', 2, 4, '2026-05-04 21:23:45', '2026-05-04 21:23:45'),
(8, 'BUDI MULYONO', 'budi.mulyono', '8207283REN', 'budi.mulyono@pcn.co.id', '081234567007', 'ASMAN PERENCANAAN DAN PENGEMBANGAN USAHA', '$2y$12$A0IlGrPlHxcgKlV4MMqnUOY0Kq39YhRs1hIHVYxUkeLWQc46r/9qq', 3, 4, '2026-05-04 21:23:45', '2026-05-04 21:23:45'),
(9, 'RIZKA ABDULLAH', 'rizka.abdullah', '8507284REN', 'rizka.abdullah@pcn.co.id', '081234567008', 'MANAGER PERENCANAAN DAN PENGEMBANGAN USAHA', '$2y$12$OZc/1EoDmN2tatWVTnMAWOm5U5qTx2H9Fj0B52I9wYHuefulTeqpC', 3, 4, '2026-05-04 21:23:45', '2026-05-04 21:23:45'),
(10, 'FATAHUDDIN YOGI AMIBOWO', 'fatahuddin.yogi.renus', '7905004BREN', 'fatahuddin.yogi.renus@pcn.co.id', '081234567009', 'DIREKTUR OPERASI & PENGEMBANGAN USAHA', '$2y$12$ihnVrpqrqBdSYTL.Gl582ejk1KyQl9hO5/7Oqg4ZHt8rEqiG/jvYi', 3, 4, '2026-05-04 21:23:46', '2026-05-04 21:23:46'),
(11, 'WAHYU KURNIAWAN', 'wahyu.kurniawan', '6724001OPS', 'wahyu.kurniawan@pcn.co.id', '081234567010', 'SUPERVISOR LOGISTIK', '$2y$12$qMnD/kUyYvKAQhm7Zh/CleLaJZh0fdc25xgtzwAAEoDgfZO8z0efC', 4, 4, '2026-05-04 21:23:46', '2026-05-04 21:23:46'),
(12, 'ROESMIN', 'roesmin', '6824002OPS', 'roesmin@pcn.co.id', '081234567011', 'ASMAN OPHARDUNG', '$2y$12$UtKjqJNwDh.5q6lnXPA1muujX86sazcnKxzjotCsXxq9ZnhrDZVKa', 4, 4, '2026-05-04 21:23:46', '2026-05-04 21:23:46'),
(13, 'FATAHUDDIN YOGI AMIBOWO', 'fatahuddin.yogi.ops', '7905004BOPS', 'fatahuddin.yogi.ops@pcn.co.id', '081234567012', 'DIREKTUR OPERASI & PENGEMBANGAN USAHA', '$2y$12$jzH8mUtcxnL9B1zvwxeTZuBcmsVAUqNQuKj3hIVWbYhGjP08mhQ7u', 4, 4, '2026-05-04 21:23:46', '2026-05-04 21:23:46'),
(14, 'PRASETIO NINGSIH', 'prasetio.ningsih', '6924001HC', 'prasetio.ningsih@pcn.co.id', '081234567013', 'SPV. PELAYANAN HUMAN CAPITAL', '$2y$12$gTzyQ/WatCV3laDFnY.6feR0zLbw08w4PAtLn8u18/Rev9vsM2rLa', 5, 4, '2026-05-04 21:23:46', '2026-05-04 21:23:46'),
(15, 'EMAN SLAMET WIDODO', 'eman.slamet', '6924002HC', 'eman.slamet@pcn.co.id', '081234567014', 'ASMAN HUMAN CAPITAL', '$2y$12$dKz3S0h/CFPNPNg0nZK/.OVDOS6i.s.V1FQ0CreJAtgVkeTfJSxOu', 5, 4, '2026-05-04 21:23:47', '2026-05-04 21:23:47'),
(16, 'YAINUS SHOLEH', 'yainus.sholeh', '6924003HC', 'yainus.sholeh@pcn.co.id', '081234567015', 'MANAGER HUMAN CAPITAL DAN ADMINISTRASI UMUM', '$2y$12$SrC0BpuddKAAfMt.guGMpu6dM.RGenI/9omUp0Axbbk5av2v5L9Nu', 5, 4, '2026-05-04 21:23:47', '2026-05-04 21:23:47'),
(17, 'NURUL AZISAH', 'nurul.azisah', '7208027SEK', 'nurul.azisah@pcn.co.id', '081234567016', 'JUNIOR OFFICER KOMUNIKASI DAN TATA KELOLA', '$2y$12$lpDyeuQ1o849/KAiH.IKnu.UIL4dA9uK5FV46d.k4vFNqrnyji.8m', 6, 4, '2026-05-04 21:23:47', '2026-05-04 21:23:47'),
(18, 'ROMY HARYADI', 'romy.haryadi', '7208028SEK', 'romy.haryadi@pcn.co.id', '081234567017', 'ASMAN HUKUM DAN TATA KELOLA', '$2y$12$Aef7T0ecNQBRGdNCVM69eemPRIJe6wwpRK/.QINNL88vB6/pEcAUa', 6, 4, '2026-05-04 21:23:47', '2026-05-04 21:23:47'),
(19, 'IRAWAN HERNANDA', 'irawan.hernanda.sekper', '76020041SEK', 'irawan.hernanda.sekper@pcn.co.id', '081234567018', 'DIREKTUR UTAMA', '$2y$12$wGN/FqC4Vh5brQL38vRCiuEDogm7q6y4PIkTAQSD38bPzHpLpjyB6', 6, 4, '2026-05-04 21:23:47', '2026-05-04 21:23:47'),
(20, 'IRAWAN HERNANDA', 'irawan.hernanda', '76020041BOD', 'irawan.hernanda@pcn.co.id', '081234567019', 'DIREKTUR UTAMA', '$2y$12$MkPmsxbgC5TTP2drAzQbAe.Hmkhbn2yUF3M34C2Oncc8FG9OzosM.', 7, 9, '2026-05-04 21:23:48', '2026-05-04 21:23:48'),
(21, 'ANDRY APRIAWAN', 'andry.apriawan', '7705003BOD', 'andry.apriawan@pcn.co.id', '081234567020', 'DIREKTUR KEUANGAN DAN ADMINISTRASI', '$2y$12$98KvZCkfkjvEROteMAxI2.KK0IoEchsO54RbqS/ALTFnUtY3GalPG', 7, 9, '2026-05-04 21:23:48', '2026-05-04 21:23:48'),
(22, 'FATAHUDDIN YOGI AMIBOWO', 'fatahuddin.yogi', '7905004BOD', 'fatahuddin.yogi@pcn.co.id', '081234567021', 'DIREKTUR OPERASI & PENGEMBANGAN USAHA', '$2y$12$syuJT4LB2nYguXNNl5QNleX.Jv1Kuby.ZPsiEJxCZQckgS21XvR.y', 7, 9, '2026-05-04 21:23:48', '2026-05-04 21:23:48'),
(23, 'IRVAN SANJAYA', 'irvan.sanjaya', '90000001ADM', 'irvan.sanjaya@pcn.co.id', '081234567022', 'ASMAN IT', '$2y$12$3jSC2otzd8m.Te8b6smi1u/GQWKZaU/Mgo2CPbqJdFp2P9.IjsrOq', 8, 8, '2026-05-04 21:23:48', '2026-05-04 21:23:48'),
(24, 'OKTO INDRA LESMANA', 'okto.indra', '8013084KAL', 'okto.indra@pcn.co.id', '081234567023', 'JUNIOR OFFICER OPERASI CABANG/SITE', '$2y$12$c4ZXsq449oMJMRIAHh002OGMGepsgwDBsnF/VQOLFpoUL9E2WQIdW', 9, 4, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(25, 'JOKO SUTRISNO', 'joko.sutrisno', '8013085KAL', 'joko.sutrisno@pcn.co.id', '081234567024', 'SUPERVISOR OPERASI', '$2y$12$iwrUN8kzP4MP42.tEmwGFeuLcbn8eAiQcboI3VpTtVy5FTPShgXZS', 9, 4, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(26, 'DONY BAYUMAR', 'dony.bayumar', '8013086KAL', 'dony.bayumar@pcn.co.id', '081234567025', 'MANAGER CABANG KALTIMRA', '$2y$12$PYbjunRV9Khp/bqJNGFeVOOC1Z2M5deQyf5nSdUkcYm1n1ikW0jRq', 9, 4, '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
(78, '2025_12_18_062421_add_pic_type_to_penutup_lha_rekomendasi_pic_table', 1);

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
(1, 1, '2024', 1, '001.LHA/PO/SPI.01.02/SPI.PCN/2024', 'LHA', 'SPI.01.02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 2, '2024', 1, '002.LHK/KONSUL/SPI.01.03/SPI.PCN/2024', 'LHK', 'SPI.01.03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 3, '2024', 1, '003.LHA/PO/SPI.01.04/SPI.PCN/2024', 'LHA', 'SPI.01.04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
(1, 1, 1, 'Dokumentasi transaksi keuangan tidak lengkap dan tidak sesuai dengan standar akuntansi yang berlaku. Beberapa transaksi tidak memiliki bukti pendukung yang memadai.', 'Kurangnya pemahaman karyawan terhadap SOP yang berlaku.', 'Kurangnya pemahaman karyawan terhadap SOP yang berlaku. Proses workflow yang tidak terstruktur dengan baik. Kebijakan yang belum jelas dan tidak terkomunikasikan dengan baik. Sistem informasi yang belum terintegrasi dengan optimal. Perubahan regulasi yang belum diadaptasi dengan cepat.', 'Sesuai dengan standar pengendalian internal yang berlaku.', 'Terjadi kesalahan dalam pencatatan transaksi keuangan.', 'Potensi kerugian finansial dan reputasi perusahaan.', 'Tinggi', 1, 1, 'ISS.001/PO PCN/SPI.01.02/01/01/2024', '2024', 'approved', 1, '2026-05-04 21:23:49', NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 2, 1, 'Proses approval transaksi keuangan tidak dilakukan sesuai dengan hierarki yang telah ditetapkan. Beberapa transaksi dengan nilai besar tidak mendapat approval dari level manajemen yang sesuai.', 'Proses approval transaksi keuangan tidak sesuai hierarki.', 'Kurangnya pemahaman terhadap kebijakan approval. Proses workflow approval yang tidak terstruktur. Kebijakan approval yang belum jelas. Sistem approval yang belum otomatis. Perubahan regulasi approval yang belum diimplementasi.', 'Sesuai dengan standar pengendalian internal yang berlaku.', 'Transaksi besar tidak mendapat approval yang sesuai.', 'Potensi fraud dan kerugian finansial.', 'Tinggi', 2, 2, 'ISS.001/PO PCN/SPI.01.02/01/02/2024', '2024', 'approved', 1, '2026-05-04 21:23:49', NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 3, 2, 'Sistem pengendalian risiko operasional belum terintegrasi dengan baik. Identifikasi dan penilaian risiko tidak dilakukan secara sistematis dan berkelanjutan.', 'Sistem pengendalian risiko operasional belum optimal.', 'Kurangnya kompetensi dalam manajemen risiko. Proses identifikasi risiko yang tidak sistematis. Kebijakan manajemen risiko yang belum komprehensif. Sistem monitoring risiko yang belum real-time. Dinamika lingkungan bisnis yang cepat berubah.', 'Sesuai dengan framework manajemen risiko yang diakui.', 'Beberapa risiko operasional tidak terdeteksi tepat waktu.', 'Potensi gangguan operasional dan kerugian bisnis.', 'Medium', 3, 3, 'ISS.002/PO PCN/SPI.01.03/01/01/2024', '2024', 'pending', NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, 4, 2, 'Monitoring dan pelaporan risiko tidak dilakukan secara real-time. Informasi risiko tidak tersedia secara tepat waktu untuk pengambilan keputusan manajemen.', 'Monitoring dan pelaporan risiko tidak real-time.', 'Kurangnya awareness terhadap pentingnya monitoring risiko. Proses monitoring yang tidak terstruktur. Kebijakan monitoring yang belum jelas. Sistem monitoring yang belum otomatis. Tekanan bisnis yang mengharuskan keputusan cepat.', 'Sesuai dengan framework manajemen risiko yang diakui.', 'Informasi risiko tidak tersedia tepat waktu.', 'Potensi keputusan yang tidak optimal.', 'Medium', 4, 4, 'ISS.002/PO PCN/SPI.01.03/01/02/2024', '2024', 'pending', NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, 5, 3, 'Kepatuhan terhadap regulasi sektor keuangan belum optimal. Beberapa ketentuan regulator tidak diimplementasikan dengan baik dalam proses bisnis.', 'Kepatuhan terhadap regulasi sektor keuangan belum optimal.', 'Kesadaran kepatuhan yang masih rendah di beberapa unit. Proses monitoring kepatuhan yang tidak terstruktur. Kebijakan kepatuhan yang belum terintegrasi dengan baik. Sistem pelaporan kepatuhan yang belum otomatis. Perubahan regulasi yang sering terjadi.', 'Sesuai dengan standar kepatuhan yang berlaku.', 'Beberapa pelanggaran regulasi tidak terdeteksi.', 'Potensi sanksi regulator dan kerugian reputasi.', 'Tinggi', 5, 5, 'ISS.003/PO PCN/SPI.01.04/01/01/2024', '2024', 'approved', 1, '2026-05-04 21:23:49', NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, 6, 3, 'Sistem pelaporan kepatuhan tidak terintegrasi dan tidak menyediakan informasi yang komprehensif. Pelaporan kepada regulator sering terlambat dan tidak akurat.', 'Sistem pelaporan kepatuhan tidak terintegrasi.', 'Kurangnya koordinasi antar unit dalam pelaporan. Proses pelaporan yang tidak terstruktur. Kebijakan pelaporan yang belum jelas. Sistem pelaporan yang belum otomatis. Deadline regulator yang ketat.', 'Sesuai dengan standar kepatuhan yang berlaku.', 'Pelaporan kepada regulator terlambat dan tidak akurat.', 'Potensi sanksi regulator dan kerugian reputasi.', 'Tinggi', 1, 1, 'ISS.003/PO PCN/SPI.01.04/01/02/2024', '2024', 'approved', 1, '2026-05-04 21:23:49', NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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

-- --------------------------------------------------------

--
-- Struktur dari tabel `perencanaan_audit`
--

CREATE TABLE `perencanaan_audit` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal_surat_tugas` date NOT NULL,
  `nomor_surat_tugas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_audit_id` bigint UNSIGNED DEFAULT NULL,
  `jenis_audit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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

INSERT INTO `perencanaan_audit` (`id`, `tanggal_surat_tugas`, `nomor_surat_tugas`, `jenis_audit_id`, `jenis_audit`, `auditor`, `auditee_id`, `ruang_lingkup`, `tanggal_audit_mulai`, `tanggal_audit_sampai`, `periode_audit`, `created_at`, `updated_at`) VALUES
(1, '2024-07-01', '001.STG/SPI.01.02/SPI-PCN/2026', NULL, 'Audit Operasional', '[\"Auditor 1 - NIP: 123456789\"]', 1, '[\"Sistem Keuangan\", \"Sistem SDM\"]', '2024-07-10', '2024-07-15', 'Januari 2024 s/d Juni 2024', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, '2024-07-02', '002.STG/SPI.01.02/SPI-PCN/2026', NULL, 'Audit Operasional', '[\"Auditor 2 - NIP: 987654321\"]', 2, '[\"Sistem Operasional\", \"Sistem IT\"]', '2024-07-20', '2024-07-25', 'Januari 2024 s/d Juni 2024', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, '2024-07-03', '001.STG/SPI.01.03/SPI-PCN/2026', NULL, 'Audit Khusus', '[\"Auditor 3 - NIP: 456789123\"]', 3, '[\"Investigasi Khusus\", \"Pemeriksaan Khusus\"]', '2024-08-01', '2024-08-05', 'Januari 2024 s/d Desember 2024', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, '2024-07-04', '001.STG/SPI.01.04/SPI-PCN/2026', NULL, 'Konsultasi', '[\"Konsultan 1 - NIP: 789123456\"]', 4, '[\"Konsultasi Sistem\", \"Konsultasi Proses\"]', '2024-08-10', '2024-08-15', 'Januari 2024 s/d Desember 2024', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, '2024-07-05', '003.STG/SPI.01.02/SPI-PCN/2026', NULL, 'Audit Operasional', '[\"Auditor 4 - NIP: 321654987\", \"Auditor 5 - NIP: 654987321\"]', 5, '[\"Sistem Keamanan\", \"Sistem Monitoring\", \"Sistem Pelaporan\"]', '2024-08-20', '2024-08-30', 'Januari 2024 s/d Juni 2024', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, '2024-07-06', '004.STG/SPI.01.02/SPI-PCN/2026', NULL, 'Audit Kepatuhan', '[\"Auditor 6 - NIP: 147258369\"]', 1, '[\"Kepatuhan Regulasi\", \"Sistem Pengendalian\"]', '2024-09-01', '2024-09-10', 'Januari 2024 s/d Desember 2024', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, '2024-07-07', '005.STG/SPI.01.02/SPI-PCN/2026', NULL, 'Audit Sistem Informasi', '[\"Auditor 7 - NIP: 963852741\", \"Auditor 8 - NIP: 852963741\"]', 2, '[\"Sistem IT\", \"Keamanan Data\", \"Infrastruktur\"]', '2024-09-15', '2024-09-25', 'Januari 2024 s/d Desember 2024', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, '2024-07-08', '006.STG/SPI.01.02/SPI-PCN/2026', NULL, 'Audit Keuangan', '[\"Auditor 9 - NIP: 741852963\"]', 3, '[\"Laporan Keuangan\", \"Sistem Akuntansi\", \"Pengendalian Internal\"]', '2024-10-01', '2024-10-15', 'Januari 2024 s/d Desember 2024', '2026-05-04 21:23:49', '2026-05-04 21:23:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pka_dokumen`
--

CREATE TABLE `pka_dokumen` (
  `id` bigint UNSIGNED NOT NULL,
  `program_kerja_audit_id` bigint UNSIGNED NOT NULL,
  `nama_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_approval` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pka_dokumen`
--

INSERT INTO `pka_dokumen` (`id`, `program_kerja_audit_id`, `nama_dokumen`, `file_path`, `status_approval`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Program Kerja Audit 1', 'dokumen/pka_1.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 1, 'Lampiran Dokumen 1', 'dokumen/lampiran_1.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 1, 'Surat Tugas Audit 1', 'dokumen/surat_tugas_1.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, 2, 'Program Kerja Audit 2', 'dokumen/pka_2.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, 2, 'Lampiran Dokumen 2', 'dokumen/lampiran_2.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, 2, 'Surat Tugas Audit 2', 'dokumen/surat_tugas_2.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, 3, 'Program Kerja Audit 3', 'dokumen/pka_3.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, 3, 'Lampiran Dokumen 3', 'dokumen/lampiran_3.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(9, 3, 'Surat Tugas Audit 3', 'dokumen/surat_tugas_3.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(10, 4, 'Program Kerja Audit 4', 'dokumen/pka_4.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(11, 4, 'Lampiran Dokumen 4', 'dokumen/lampiran_4.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(12, 4, 'Surat Tugas Audit 4', 'dokumen/surat_tugas_4.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(13, 5, 'Program Kerja Audit 5', 'dokumen/pka_5.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(14, 5, 'Lampiran Dokumen 5', 'dokumen/lampiran_5.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(15, 5, 'Surat Tugas Audit 5', 'dokumen/surat_tugas_5.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(16, 6, 'Program Kerja Audit 6', 'dokumen/pka_6.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(17, 6, 'Lampiran Dokumen 6', 'dokumen/lampiran_6.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(18, 6, 'Surat Tugas Audit 6', 'dokumen/surat_tugas_6.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(19, 7, 'Program Kerja Audit 7', 'dokumen/pka_7.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(20, 7, 'Lampiran Dokumen 7', 'dokumen/lampiran_7.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(21, 7, 'Surat Tugas Audit 7', 'dokumen/surat_tugas_7.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(22, 8, 'Program Kerja Audit 8', 'dokumen/pka_8.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(23, 8, 'Lampiran Dokumen 8', 'dokumen/lampiran_8.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(24, 8, 'Surat Tugas Audit 8', 'dokumen/surat_tugas_8.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(25, 1, 'Program Kerja Audit 1', 'dokumen/pka_1.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(26, 1, 'Surat Tugas Audit 1', 'dokumen/surat_tugas_1.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(27, 1, 'Lampiran Dokumen 1', 'dokumen/lampiran_1.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(28, 2, 'Program Kerja Audit 2', 'dokumen/pka_2.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(29, 2, 'Surat Tugas Audit 2', 'dokumen/surat_tugas_2.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(30, 2, 'Lampiran Dokumen 2', 'dokumen/lampiran_2.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(31, 3, 'Program Kerja Audit 3', 'dokumen/pka_3.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(32, 3, 'Surat Tugas Audit 3', 'dokumen/surat_tugas_3.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(33, 3, 'Lampiran Dokumen 3', 'dokumen/lampiran_3.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(34, 4, 'Program Kerja Audit 4', 'dokumen/pka_4.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(35, 4, 'Surat Tugas Audit 4', 'dokumen/surat_tugas_4.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(36, 4, 'Lampiran Dokumen 4', 'dokumen/lampiran_4.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(37, 5, 'Program Kerja Audit 5', 'dokumen/pka_5.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(38, 5, 'Surat Tugas Audit 5', 'dokumen/surat_tugas_5.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(39, 5, 'Lampiran Dokumen 5', 'dokumen/lampiran_5.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(40, 6, 'Program Kerja Audit 6', 'dokumen/pka_6.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(41, 6, 'Surat Tugas Audit 6', 'dokumen/surat_tugas_6.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(42, 6, 'Lampiran Dokumen 6', 'dokumen/lampiran_6.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(43, 7, 'Program Kerja Audit 7', 'dokumen/pka_7.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(44, 7, 'Surat Tugas Audit 7', 'dokumen/surat_tugas_7.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(45, 7, 'Lampiran Dokumen 7', 'dokumen/lampiran_7.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(46, 8, 'Program Kerja Audit 8', 'dokumen/pka_8.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(47, 8, 'Surat Tugas Audit 8', 'dokumen/surat_tugas_8.pdf', 'approved', 1, '2026-05-04 21:23:49', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(48, 8, 'Lampiran Dokumen 8', 'dokumen/lampiran_8.pdf', 'pending', NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
(1, 1, 'Entry Meeting', '2024-07-01', '2024-07-05', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 1, 'Walkthrough', '2024-07-06', '2024-07-20', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 1, 'TOD', '2024-07-21', '2024-08-09', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, 1, 'TOE', '2024-08-10', '2024-08-24', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, 1, 'Draf LHA', '2024-08-25', '2024-09-08', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, 1, 'Exit Meeting', '2024-09-09', '2024-09-13', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, 2, 'Entry Meeting', '2024-07-31', '2024-08-04', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, 2, 'Walkthrough', '2024-08-05', '2024-08-19', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(9, 2, 'TOD', '2024-08-20', '2024-09-08', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(10, 2, 'TOE', '2024-09-09', '2024-09-23', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(11, 2, 'Draf LHA', '2024-09-24', '2024-10-08', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(12, 2, 'Exit Meeting', '2024-10-09', '2024-10-13', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(13, 3, 'Entry Meeting', '2024-08-30', '2024-09-03', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(14, 3, 'Walkthrough', '2024-09-04', '2024-09-18', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(15, 3, 'TOD', '2024-09-19', '2024-10-08', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(16, 3, 'TOE', '2024-10-09', '2024-10-23', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(17, 3, 'Draf LHA', '2024-10-24', '2024-11-07', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(18, 3, 'Exit Meeting', '2024-11-08', '2024-11-12', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(19, 4, 'Entry Meeting', '2024-09-29', '2024-10-03', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(20, 4, 'Walkthrough', '2024-10-04', '2024-10-18', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(21, 4, 'TOD', '2024-10-19', '2024-11-07', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(22, 4, 'TOE', '2024-11-08', '2024-11-22', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(23, 4, 'Draf LHA', '2024-11-23', '2024-12-07', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(24, 4, 'Exit Meeting', '2024-12-08', '2024-12-12', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(25, 5, 'Entry Meeting', '2024-10-29', '2024-11-02', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(26, 5, 'Walkthrough', '2024-11-03', '2024-11-17', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(27, 5, 'TOD', '2024-11-18', '2024-12-07', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(28, 5, 'TOE', '2024-12-08', '2024-12-22', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(29, 5, 'Draf LHA', '2024-12-23', '2025-01-06', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(30, 5, 'Exit Meeting', '2025-01-07', '2025-01-11', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(31, 6, 'Entry Meeting', '2024-11-28', '2024-12-02', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(32, 6, 'Walkthrough', '2024-12-03', '2024-12-17', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(33, 6, 'TOD', '2024-12-18', '2025-01-06', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(34, 6, 'TOE', '2025-01-07', '2025-01-21', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(35, 6, 'Draf LHA', '2025-01-22', '2025-02-05', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(36, 6, 'Exit Meeting', '2025-02-06', '2025-02-10', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(37, 7, 'Entry Meeting', '2024-12-28', '2025-01-01', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(38, 7, 'Walkthrough', '2025-01-02', '2025-01-16', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(39, 7, 'TOD', '2025-01-17', '2025-02-05', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(40, 7, 'TOE', '2025-02-06', '2025-02-20', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(41, 7, 'Draf LHA', '2025-02-21', '2025-03-07', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(42, 7, 'Exit Meeting', '2025-03-08', '2025-03-12', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(43, 8, 'Entry Meeting', '2025-01-27', '2025-01-31', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(44, 8, 'Walkthrough', '2025-02-01', '2025-02-15', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(45, 8, 'TOD', '2025-02-16', '2025-03-07', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(46, 8, 'TOE', '2025-03-08', '2025-03-22', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(47, 8, 'Draf LHA', '2025-03-23', '2025-04-06', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(48, 8, 'Exit Meeting', '2025-04-07', '2025-04-11', '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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

--
-- Dumping data untuk tabel `pka_risk_based_audit`
--

INSERT INTO `pka_risk_based_audit` (`id`, `program_kerja_audit_id`, `deskripsi_resiko`, `penyebab_resiko`, `dampak_resiko`, `pengendalian_eksisting`, `created_at`, `updated_at`) VALUES
(1, 1, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik', 'Sanksi dari regulator dan kerugian finansial', 'Sistem monitoring regulasi dan pelatihan berkala', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 1, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal', 'Peningkatan biaya operasional dan penurunan produktivitas', 'Review proses berkala dan implementasi best practices', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 1, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal', 'Gangguan layanan dan kehilangan data', 'Backup sistem dan disaster recovery plan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, 2, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik', 'Sanksi dari regulator dan kerugian finansial', 'Sistem monitoring regulasi dan pelatihan berkala', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, 2, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal', 'Peningkatan biaya operasional dan penurunan produktivitas', 'Review proses berkala dan implementasi best practices', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, 2, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal', 'Gangguan layanan dan kehilangan data', 'Backup sistem dan disaster recovery plan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, 3, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik', 'Sanksi dari regulator dan kerugian finansial', 'Sistem monitoring regulasi dan pelatihan berkala', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, 3, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal', 'Peningkatan biaya operasional dan penurunan produktivitas', 'Review proses berkala dan implementasi best practices', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(9, 3, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal', 'Gangguan layanan dan kehilangan data', 'Backup sistem dan disaster recovery plan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(10, 4, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik', 'Sanksi dari regulator dan kerugian finansial', 'Sistem monitoring regulasi dan pelatihan berkala', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(11, 4, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal', 'Peningkatan biaya operasional dan penurunan produktivitas', 'Review proses berkala dan implementasi best practices', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(12, 4, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal', 'Gangguan layanan dan kehilangan data', 'Backup sistem dan disaster recovery plan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(13, 5, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik', 'Sanksi dari regulator dan kerugian finansial', 'Sistem monitoring regulasi dan pelatihan berkala', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(14, 5, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal', 'Peningkatan biaya operasional dan penurunan produktivitas', 'Review proses berkala dan implementasi best practices', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(15, 5, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal', 'Gangguan layanan dan kehilangan data', 'Backup sistem dan disaster recovery plan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(16, 6, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik', 'Sanksi dari regulator dan kerugian finansial', 'Sistem monitoring regulasi dan pelatihan berkala', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(17, 6, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal', 'Peningkatan biaya operasional dan penurunan produktivitas', 'Review proses berkala dan implementasi best practices', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(18, 6, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal', 'Gangguan layanan dan kehilangan data', 'Backup sistem dan disaster recovery plan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(19, 7, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik', 'Sanksi dari regulator dan kerugian finansial', 'Sistem monitoring regulasi dan pelatihan berkala', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(20, 7, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal', 'Peningkatan biaya operasional dan penurunan produktivitas', 'Review proses berkala dan implementasi best practices', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(21, 7, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal', 'Gangguan layanan dan kehilangan data', 'Backup sistem dan disaster recovery plan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(22, 8, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik', 'Sanksi dari regulator dan kerugian finansial', 'Sistem monitoring regulasi dan pelatihan berkala', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(23, 8, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal', 'Peningkatan biaya operasional dan penurunan produktivitas', 'Review proses berkala dan implementasi best practices', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(24, 8, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal', 'Gangguan layanan dan kehilangan data', 'Backup sistem dan disaster recovery plan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(25, 1, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik dan kurangnya pemahaman terhadap regulasi baru', 'Sanksi dari regulator, kerugian finansial, dan kerusakan reputasi', 'Sistem monitoring regulasi, pelatihan berkala, dan review kepatuhan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(26, 1, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal dan kurangnya standardisasi', 'Peningkatan biaya operasional, penurunan produktivitas, dan kehilangan peluang', 'Review proses berkala, implementasi best practices, dan continuous improvement', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(27, 1, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal dan kurangnya maintenance', 'Gangguan layanan, kehilangan data, dan kerugian finansial', 'Backup sistem, disaster recovery plan, dan monitoring sistem', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(28, 2, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik dan kurangnya pemahaman terhadap regulasi baru', 'Sanksi dari regulator, kerugian finansial, dan kerusakan reputasi', 'Sistem monitoring regulasi, pelatihan berkala, dan review kepatuhan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(29, 2, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal dan kurangnya standardisasi', 'Peningkatan biaya operasional, penurunan produktivitas, dan kehilangan peluang', 'Review proses berkala, implementasi best practices, dan continuous improvement', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(30, 2, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal dan kurangnya maintenance', 'Gangguan layanan, kehilangan data, dan kerugian finansial', 'Backup sistem, disaster recovery plan, dan monitoring sistem', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(31, 3, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik dan kurangnya pemahaman terhadap regulasi baru', 'Sanksi dari regulator, kerugian finansial, dan kerusakan reputasi', 'Sistem monitoring regulasi, pelatihan berkala, dan review kepatuhan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(32, 3, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal dan kurangnya standardisasi', 'Peningkatan biaya operasional, penurunan produktivitas, dan kehilangan peluang', 'Review proses berkala, implementasi best practices, dan continuous improvement', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(33, 3, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal dan kurangnya maintenance', 'Gangguan layanan, kehilangan data, dan kerugian finansial', 'Backup sistem, disaster recovery plan, dan monitoring sistem', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(34, 4, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik dan kurangnya pemahaman terhadap regulasi baru', 'Sanksi dari regulator, kerugian finansial, dan kerusakan reputasi', 'Sistem monitoring regulasi, pelatihan berkala, dan review kepatuhan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(35, 4, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal dan kurangnya standardisasi', 'Peningkatan biaya operasional, penurunan produktivitas, dan kehilangan peluang', 'Review proses berkala, implementasi best practices, dan continuous improvement', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(36, 4, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal dan kurangnya maintenance', 'Gangguan layanan, kehilangan data, dan kerugian finansial', 'Backup sistem, disaster recovery plan, dan monitoring sistem', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(37, 5, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik dan kurangnya pemahaman terhadap regulasi baru', 'Sanksi dari regulator, kerugian finansial, dan kerusakan reputasi', 'Sistem monitoring regulasi, pelatihan berkala, dan review kepatuhan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(38, 5, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal dan kurangnya standardisasi', 'Peningkatan biaya operasional, penurunan produktivitas, dan kehilangan peluang', 'Review proses berkala, implementasi best practices, dan continuous improvement', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(39, 5, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal dan kurangnya maintenance', 'Gangguan layanan, kehilangan data, dan kerugian finansial', 'Backup sistem, disaster recovery plan, dan monitoring sistem', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(40, 6, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik dan kurangnya pemahaman terhadap regulasi baru', 'Sanksi dari regulator, kerugian finansial, dan kerusakan reputasi', 'Sistem monitoring regulasi, pelatihan berkala, dan review kepatuhan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(41, 6, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal dan kurangnya standardisasi', 'Peningkatan biaya operasional, penurunan produktivitas, dan kehilangan peluang', 'Review proses berkala, implementasi best practices, dan continuous improvement', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(42, 6, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal dan kurangnya maintenance', 'Gangguan layanan, kehilangan data, dan kerugian finansial', 'Backup sistem, disaster recovery plan, dan monitoring sistem', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(43, 7, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik dan kurangnya pemahaman terhadap regulasi baru', 'Sanksi dari regulator, kerugian finansial, dan kerusakan reputasi', 'Sistem monitoring regulasi, pelatihan berkala, dan review kepatuhan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(44, 7, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal dan kurangnya standardisasi', 'Peningkatan biaya operasional, penurunan produktivitas, dan kehilangan peluang', 'Review proses berkala, implementasi best practices, dan continuous improvement', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(45, 7, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal dan kurangnya maintenance', 'Gangguan layanan, kehilangan data, dan kerugian finansial', 'Backup sistem, disaster recovery plan, dan monitoring sistem', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(46, 8, 'Risiko ketidakpatuhan terhadap regulasi', 'Perubahan regulasi yang tidak diikuti dengan baik dan kurangnya pemahaman terhadap regulasi baru', 'Sanksi dari regulator, kerugian finansial, dan kerusakan reputasi', 'Sistem monitoring regulasi, pelatihan berkala, dan review kepatuhan', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(47, 8, 'Risiko inefisiensi operasional', 'Proses bisnis yang tidak optimal dan kurangnya standardisasi', 'Peningkatan biaya operasional, penurunan produktivitas, dan kehilangan peluang', 'Review proses berkala, implementasi best practices, dan continuous improvement', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(48, 8, 'Risiko kegagalan teknologi', 'Sistem IT yang tidak handal dan kurangnya maintenance', 'Gangguan layanan, kehilangan data, dan kerugian finansial', 'Backup sistem, disaster recovery plan, dan monitoring sistem', '2026-05-04 21:23:49', '2026-05-04 21:23:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `program_kerja_audit`
--

CREATE TABLE `program_kerja_audit` (
  `id` bigint UNSIGNED NOT NULL,
  `perencanaan_audit_id` bigint UNSIGNED NOT NULL,
  `tanggal_pka` date NOT NULL,
  `no_pka` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `informasi_umum` text COLLATE utf8mb4_unicode_ci,
  `kpi_tidak_tercapai` text COLLATE utf8mb4_unicode_ci,
  `data_awal_dokumen` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `program_kerja_audit`
--

INSERT INTO `program_kerja_audit` (`id`, `perencanaan_audit_id`, `tanggal_pka`, `no_pka`, `informasi_umum`, `kpi_tidak_tercapai`, `data_awal_dokumen`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-07-01', 'PKA-001/2024', 'Program Kerja Audit untuk Audit Operasional pada ', 'KPI yang tidak tercapai dalam audit 1: Efisiensi operasional, Kepatuhan regulasi, dan Pengelolaan risiko', 'Data awal dokumen untuk audit 1: Laporan keuangan, SOP, dan Dokumen pendukung lainnya', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 2, '2024-07-01', 'PKA-002/2024', 'Program Kerja Audit untuk Audit Operasional pada ', 'KPI yang tidak tercapai dalam audit 2: Efisiensi operasional, Kepatuhan regulasi, dan Pengelolaan risiko', 'Data awal dokumen untuk audit 2: Laporan keuangan, SOP, dan Dokumen pendukung lainnya', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 3, '2024-07-01', 'PKA-003/2024', 'Program Kerja Audit untuk Audit Khusus pada ', 'KPI yang tidak tercapai dalam audit 3: Efisiensi operasional, Kepatuhan regulasi, dan Pengelolaan risiko', 'Data awal dokumen untuk audit 3: Laporan keuangan, SOP, dan Dokumen pendukung lainnya', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, 4, '2024-07-01', 'PKA-004/2024', 'Program Kerja Audit untuk Konsultasi pada ', 'KPI yang tidak tercapai dalam audit 4: Efisiensi operasional, Kepatuhan regulasi, dan Pengelolaan risiko', 'Data awal dokumen untuk audit 4: Laporan keuangan, SOP, dan Dokumen pendukung lainnya', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, 5, '2024-07-01', 'PKA-005/2024', 'Program Kerja Audit untuk Audit Operasional pada ', 'KPI yang tidak tercapai dalam audit 5: Efisiensi operasional, Kepatuhan regulasi, dan Pengelolaan risiko', 'Data awal dokumen untuk audit 5: Laporan keuangan, SOP, dan Dokumen pendukung lainnya', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, 6, '2024-07-01', 'PKA-006/2024', 'Program Kerja Audit untuk Audit Kepatuhan pada ', 'KPI yang tidak tercapai dalam audit 6: Efisiensi operasional, Kepatuhan regulasi, dan Pengelolaan risiko', 'Data awal dokumen untuk audit 6: Laporan keuangan, SOP, dan Dokumen pendukung lainnya', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, 7, '2024-07-01', 'PKA-007/2024', 'Program Kerja Audit untuk Audit Sistem Informasi pada ', 'KPI yang tidak tercapai dalam audit 7: Efisiensi operasional, Kepatuhan regulasi, dan Pengelolaan risiko', 'Data awal dokumen untuk audit 7: Laporan keuangan, SOP, dan Dokumen pendukung lainnya', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, 8, '2024-07-01', 'PKA-008/2024', 'Program Kerja Audit untuk Audit Keuangan pada ', 'KPI yang tidak tercapai dalam audit 8: Efisiensi operasional, Kepatuhan regulasi, dan Pengelolaan risiko', 'Data awal dokumen untuk audit 8: Laporan keuangan, SOP, dan Dokumen pendukung lainnya', '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
(1, 1, '2024-07-10', '2024-07-15', 'selesai', NULL, NULL, 'approved', 1, '2026-04-29 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 6, '2024-07-11', '2024-07-16', 'on progress', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 2, '2024-07-12', '2024-07-17', 'belum', NULL, NULL, 'approved', 1, '2026-04-26 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, 7, '2024-07-13', '2024-07-18', 'selesai', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, 3, '2024-07-14', '2024-07-19', 'on progress', NULL, NULL, 'approved', 1, '2026-05-02 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, 8, '2024-07-15', '2024-07-20', 'belum', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, 4, '2024-07-16', '2024-07-21', 'selesai', NULL, NULL, 'approved', 1, '2026-04-26 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, 5, '2024-07-17', '2024-07-22', 'on progress', NULL, NULL, 'approved', 1, '2026-05-01 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
(1, 1, 'Business Process Mapping untuk Audit Operasional 1', 'BPO 1 - Direktorat', NULL, NULL, 'bpm/bpm_1.pdf', NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 2, 'Business Process Mapping untuk Audit Operasional 2', 'BPO 2 - Direktorat', NULL, NULL, 'bpm/bpm_2.pdf', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 3, 'Business Process Mapping untuk Audit Khusus 3', 'BPO 3 - Direktorat', NULL, NULL, 'bpm/bpm_3.pdf', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, 4, 'Business Process Mapping untuk Konsultasi 4', 'BPO 4 - Direktorat', NULL, NULL, 'bpm/bpm_4.pdf', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, 5, 'Business Process Mapping untuk Audit Operasional 5', 'BPO 5 - Direktorat', NULL, NULL, 'bpm/bpm_5.pdf', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, 6, 'Business Process Mapping untuk Audit Kepatuhan 6', 'BPO 6 - Direktorat', NULL, NULL, 'bpm/bpm_6.pdf', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, 7, 'Business Process Mapping untuk Audit Sistem Informasi 7', 'BPO 7 - Direktorat', NULL, NULL, 'bpm/bpm_7.pdf', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, 8, 'Business Process Mapping untuk Audit Keuangan 8', 'BPO 8 - Direktorat', NULL, NULL, 'bpm/bpm_8.pdf', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
(1, 1, 'Proses mapping sudah sesuai dengan standar yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 1, 'Dokumentasi proses lengkap dan mudah dipahami', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 1, 'Identifikasi risiko sudah dilakukan dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, 1, 'Pengendalian internal sudah teridentifikasi dengan jelas', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, 1, 'Rekomendasi perbaikan sudah disusun dengan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, 2, 'Proses mapping sudah sesuai dengan standar yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, 2, 'Dokumentasi proses lengkap dan mudah dipahami', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, 2, 'Identifikasi risiko sudah dilakukan dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(9, 2, 'Pengendalian internal sudah teridentifikasi dengan jelas', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(10, 2, 'Rekomendasi perbaikan sudah disusun dengan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(11, 3, 'Proses mapping sudah sesuai dengan standar yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(12, 3, 'Dokumentasi proses lengkap dan mudah dipahami', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(13, 3, 'Identifikasi risiko sudah dilakukan dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(14, 3, 'Pengendalian internal sudah teridentifikasi dengan jelas', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(15, 3, 'Rekomendasi perbaikan sudah disusun dengan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(16, 4, 'Proses mapping sudah sesuai dengan standar yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(17, 4, 'Dokumentasi proses lengkap dan mudah dipahami', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(18, 4, 'Identifikasi risiko sudah dilakukan dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(19, 4, 'Pengendalian internal sudah teridentifikasi dengan jelas', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(20, 4, 'Rekomendasi perbaikan sudah disusun dengan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(21, 5, 'Proses mapping sudah sesuai dengan standar yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(22, 5, 'Dokumentasi proses lengkap dan mudah dipahami', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(23, 5, 'Identifikasi risiko sudah dilakukan dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(24, 5, 'Pengendalian internal sudah teridentifikasi dengan jelas', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(25, 5, 'Rekomendasi perbaikan sudah disusun dengan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(26, 6, 'Proses mapping sudah sesuai dengan standar yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(27, 6, 'Dokumentasi proses lengkap dan mudah dipahami', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(28, 6, 'Identifikasi risiko sudah dilakukan dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(29, 6, 'Pengendalian internal sudah teridentifikasi dengan jelas', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(30, 6, 'Rekomendasi perbaikan sudah disusun dengan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(31, 7, 'Proses mapping sudah sesuai dengan standar yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(32, 7, 'Dokumentasi proses lengkap dan mudah dipahami', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(33, 7, 'Identifikasi risiko sudah dilakukan dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(34, 7, 'Pengendalian internal sudah teridentifikasi dengan jelas', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(35, 7, 'Rekomendasi perbaikan sudah disusun dengan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(36, 8, 'Proses mapping sudah sesuai dengan standar yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(37, 8, 'Dokumentasi proses lengkap dan mudah dipahami', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(38, 8, 'Identifikasi risiko sudah dilakukan dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(39, 8, 'Pengendalian internal sudah teridentifikasi dengan jelas', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(40, 8, 'Rekomendasi perbaikan sudah disusun dengan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(41, 1, 'Evaluasi BPM 1 - Satu', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(42, 2, 'Evaluasi BPM 2 - Satu', '2026-05-04 21:23:49', '2026-05-04 21:23:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `toe_audit`
--

CREATE TABLE `toe_audit` (
  `id` bigint UNSIGNED NOT NULL,
  `perencanaan_audit_id` bigint UNSIGNED NOT NULL,
  `judul_bpm` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pengendalian_eksisting` text COLLATE utf8mb4_unicode_ci NOT NULL,
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
(1, 1, 'Terms of Engagement untuk Audit Operasional 1', 'Pengendalian eksisting untuk Audit Operasional meliputi: SOP, monitoring berkala, dan review manajemen.', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 2, 'Terms of Engagement untuk Audit Operasional 2', 'Pengendalian eksisting untuk Audit Operasional meliputi: SOP, monitoring berkala, dan review manajemen.', NULL, NULL, NULL, NULL, 'rejected', 'Judul BPM dalam TOE tidak sesuai dengan scope audit yang direncanakan, perlu revisi.', 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 3, 'Terms of Engagement untuk Audit Khusus 3', 'Pengendalian eksisting untuk Audit Khusus meliputi: SOP, monitoring berkala, dan review manajemen.', NULL, NULL, NULL, NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, 4, 'Terms of Engagement untuk Konsultasi 4', 'Pengendalian eksisting untuk Konsultasi meliputi: SOP, monitoring berkala, dan review manajemen.', NULL, NULL, NULL, NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, 5, 'Terms of Engagement untuk Audit Operasional 5', 'Pengendalian eksisting untuk Audit Operasional meliputi: SOP, monitoring berkala, dan review manajemen.', NULL, NULL, NULL, NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, 6, 'Terms of Engagement untuk Audit Kepatuhan 6', 'Pengendalian eksisting untuk Audit Kepatuhan meliputi: SOP, monitoring berkala, dan review manajemen.', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, 7, 'Terms of Engagement untuk Audit Sistem Informasi 7', 'Pengendalian eksisting untuk Audit Sistem Informasi meliputi: SOP, monitoring berkala, dan review manajemen.', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, 8, 'Terms of Engagement untuk Audit Keuangan 8', 'Pengendalian eksisting untuk Audit Keuangan meliputi: SOP, monitoring berkala, dan review manajemen.', NULL, NULL, NULL, NULL, 'rejected', 'Evaluasi TOE menunjukkan hasil yang tidak memuaskan, perlu perbaikan.', 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
(1, 1, 'Terms of Engagement sudah sesuai dengan standar audit yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 1, 'Scope audit sudah didefinisikan dengan jelas dan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 1, 'Timeline audit sudah disusun dengan realistis', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, 1, 'Resource yang diperlukan sudah diidentifikasi dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, 1, 'Komunikasi dengan auditee sudah terjalin dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, 2, 'Terms of Engagement sudah sesuai dengan standar audit yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, 2, 'Scope audit sudah didefinisikan dengan jelas dan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, 2, 'Timeline audit sudah disusun dengan realistis', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(9, 2, 'Resource yang diperlukan sudah diidentifikasi dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(10, 2, 'Komunikasi dengan auditee sudah terjalin dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(11, 3, 'Terms of Engagement sudah sesuai dengan standar audit yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(12, 3, 'Scope audit sudah didefinisikan dengan jelas dan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(13, 3, 'Timeline audit sudah disusun dengan realistis', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(14, 3, 'Resource yang diperlukan sudah diidentifikasi dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(15, 3, 'Komunikasi dengan auditee sudah terjalin dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(16, 4, 'Terms of Engagement sudah sesuai dengan standar audit yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(17, 4, 'Scope audit sudah didefinisikan dengan jelas dan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(18, 4, 'Timeline audit sudah disusun dengan realistis', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(19, 4, 'Resource yang diperlukan sudah diidentifikasi dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(20, 4, 'Komunikasi dengan auditee sudah terjalin dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(21, 5, 'Terms of Engagement sudah sesuai dengan standar audit yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(22, 5, 'Scope audit sudah didefinisikan dengan jelas dan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(23, 5, 'Timeline audit sudah disusun dengan realistis', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(24, 5, 'Resource yang diperlukan sudah diidentifikasi dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(25, 5, 'Komunikasi dengan auditee sudah terjalin dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(26, 6, 'Terms of Engagement sudah sesuai dengan standar audit yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(27, 6, 'Scope audit sudah didefinisikan dengan jelas dan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(28, 6, 'Timeline audit sudah disusun dengan realistis', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(29, 6, 'Resource yang diperlukan sudah diidentifikasi dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(30, 6, 'Komunikasi dengan auditee sudah terjalin dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(31, 7, 'Terms of Engagement sudah sesuai dengan standar audit yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(32, 7, 'Scope audit sudah didefinisikan dengan jelas dan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(33, 7, 'Timeline audit sudah disusun dengan realistis', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(34, 7, 'Resource yang diperlukan sudah diidentifikasi dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(35, 7, 'Komunikasi dengan auditee sudah terjalin dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(36, 8, 'Terms of Engagement sudah sesuai dengan standar audit yang berlaku', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(37, 8, 'Scope audit sudah didefinisikan dengan jelas dan tepat', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(38, 8, 'Timeline audit sudah disusun dengan realistis', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(39, 8, 'Resource yang diperlukan sudah diidentifikasi dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(40, 8, 'Komunikasi dengan auditee sudah terjalin dengan baik', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(41, 1, 'Evaluasi TOE 1', '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(42, 2, 'Evaluasi TOE 2', '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
(1, 'Tapeli', 'demo@user.com', '2026-05-04 21:23:43', '$2y$12$oquJ5XBSMfepVchezGIoMeJlH.4Mnycqsad73MCqXi.myzBNJJFjW', 'cuHOE14YtH', '2026-05-04 21:23:43', '2026-05-04 21:23:43');

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
(1, 1, 1, '2024-07-09', '2024-07-06', '2024-07-09', 'HUMAN CAPITAL', 'Hasil walkthrough menunjukkan bahwa proses operasional berjalan sesuai dengan standar yang ditetapkan. Beberapa rekomendasi perbaikan telah diidentifikasi.', NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(2, 2, 2, '1970-01-01', '2024-08-05', '1970-01-01', 'HUMAN CAPITAL', 'Walkthrough mengungkapkan beberapa ketidaksesuaian dalam implementasi prosedur operasional. Perlu dilakukan perbaikan untuk meningkatkan efektivitas.', NULL, 'rejected', 'Dokumen SOP yang akan di-review belum tersedia, walkthrough perlu menunggu kelengkapan dokumen.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(3, 3, 3, '2024-09-05', '2024-09-04', '2024-09-05', 'RENUS IT', 'Hasil walkthrough audit khusus menunjukkan bahwa area yang diaudit telah memenuhi kriteria yang ditetapkan. Beberapa catatan perbaikan minor telah diidentifikasi.', NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(4, 4, 4, '2024-10-04', '2024-10-04', NULL, 'CABANG KALTIMRA', 'Walkthrough konsultasi telah dilaksanakan untuk memberikan pemahaman mendalam tentang proses yang dikonsultasikan. Beberapa saran perbaikan telah disampaikan.', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(5, 5, 5, '1970-01-01', '2024-11-03', '1970-01-01', 'KEUANGAN', 'Walkthrough mengungkapkan beberapa ketidaksesuaian dalam implementasi prosedur operasional. Perlu dilakukan perbaikan untuk meningkatkan efektivitas.', NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(6, 6, 6, '2024-12-05', '2024-12-03', '2024-12-05', 'CABANG KALTIMRA', 'Walkthrough mengungkapkan beberapa ketidaksesuaian dalam implementasi prosedur operasional. Perlu dilakukan perbaikan untuk meningkatkan efektivitas.', NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(7, 7, 7, '1970-01-01', '2025-01-02', '1970-01-01', 'BOD', 'Walkthrough telah dilaksanakan untuk memahami proses operasional. Ditemukan beberapa area yang memerlukan perhatian khusus dalam hal efisiensi dan kepatuhan SOP.', NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49'),
(8, 8, 8, '2025-02-01', '2025-02-01', '2025-02-01', 'KEUANGAN', 'Walkthrough mengungkapkan beberapa ketidaksesuaian dalam implementasi prosedur operasional. Perlu dilakukan perbaikan untuk meningkatkan efektivitas.', NULL, 'approved', NULL, 1, '2026-05-04 21:23:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 21:23:49', '2026-05-04 21:23:49');

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
-- Indeks untuk tabel `master_user`
--
ALTER TABLE `master_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `master_user_username_unique` (`username`),
  ADD KEY `master_user_master_auditee_id_foreign` (`master_auditee_id`),
  ADD KEY `master_user_master_akses_user_id_foreign` (`master_akses_user_id`);

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
  ADD KEY `perencanaan_audit_jenis_audit_id_foreign` (`jenis_audit_id`);

--
-- Indeks untuk tabel `pka_dokumen`
--
ALTER TABLE `pka_dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pka_dokumen_program_kerja_audit_id_foreign` (`program_kerja_audit_id`);

--
-- Indeks untuk tabel `pka_milestone`
--
ALTER TABLE `pka_milestone`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pka_milestone_program_kerja_audit_id_foreign` (`program_kerja_audit_id`);

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
  ADD KEY `program_kerja_audit_perencanaan_audit_id_foreign` (`perencanaan_audit_id`);

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
-- AUTO_INCREMENT untuk tabel `entry_meeting`
--
ALTER TABLE `entry_meeting`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `master_auditee`
--
ALTER TABLE `master_auditee`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `master_jenis_audit`
--
ALTER TABLE `master_jenis_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `master_kode_aoi`
--
ALTER TABLE `master_kode_aoi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `master_kode_risk`
--
ALTER TABLE `master_kode_risk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT untuk tabel `master_user`
--
ALTER TABLE `master_user`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pelaporan_isi_lha`
--
ALTER TABLE `pelaporan_isi_lha`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pelaporan_temuan`
--
ALTER TABLE `pelaporan_temuan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `penutup_lha_rekomendasi`
--
ALTER TABLE `penutup_lha_rekomendasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penutup_lha_rekomendasi_pic`
--
ALTER TABLE `penutup_lha_rekomendasi_pic`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penutup_lha_tindak_lanjut`
--
ALTER TABLE `penutup_lha_tindak_lanjut`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `perencanaan_audit`
--
ALTER TABLE `perencanaan_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `pka_dokumen`
--
ALTER TABLE `pka_dokumen`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `pka_milestone`
--
ALTER TABLE `pka_milestone`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `pka_risk_based_audit`
--
ALTER TABLE `pka_risk_based_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `program_kerja_audit`
--
ALTER TABLE `program_kerja_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `realisasi_audits`
--
ALTER TABLE `realisasi_audits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tod_bpm_audit`
--
ALTER TABLE `tod_bpm_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tod_bpm_evaluasi`
--
ALTER TABLE `tod_bpm_evaluasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT untuk tabel `toe_audit`
--
ALTER TABLE `toe_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `toe_evaluasi`
--
ALTER TABLE `toe_evaluasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `walkthrough_audit`
--
ALTER TABLE `walkthrough_audit`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

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
  ADD CONSTRAINT `perencanaan_audit_auditee_id_foreign` FOREIGN KEY (`auditee_id`) REFERENCES `master_auditee` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `perencanaan_audit_jenis_audit_id_foreign` FOREIGN KEY (`jenis_audit_id`) REFERENCES `master_jenis_audit` (`id`) ON DELETE RESTRICT;

--
-- Ketidakleluasaan untuk tabel `pka_dokumen`
--
ALTER TABLE `pka_dokumen`
  ADD CONSTRAINT `pka_dokumen_program_kerja_audit_id_foreign` FOREIGN KEY (`program_kerja_audit_id`) REFERENCES `program_kerja_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pka_milestone`
--
ALTER TABLE `pka_milestone`
  ADD CONSTRAINT `pka_milestone_program_kerja_audit_id_foreign` FOREIGN KEY (`program_kerja_audit_id`) REFERENCES `program_kerja_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pka_risk_based_audit`
--
ALTER TABLE `pka_risk_based_audit`
  ADD CONSTRAINT `pka_risk_based_audit_program_kerja_audit_id_foreign` FOREIGN KEY (`program_kerja_audit_id`) REFERENCES `program_kerja_audit` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `program_kerja_audit`
--
ALTER TABLE `program_kerja_audit`
  ADD CONSTRAINT `program_kerja_audit_perencanaan_audit_id_foreign` FOREIGN KEY (`perencanaan_audit_id`) REFERENCES `perencanaan_audit` (`id`) ON DELETE RESTRICT;

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
