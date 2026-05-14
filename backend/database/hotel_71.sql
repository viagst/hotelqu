-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Bulan Mei 2026 pada 14.52
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_71`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pemesanans`
--

CREATE TABLE `detail_pemesanans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_pemesanan` bigint(20) UNSIGNED NOT NULL,
  `id_tipe_kamar` bigint(20) UNSIGNED NOT NULL,
  `id_kamar` bigint(20) UNSIGNED NOT NULL,
  `nama_tamu_spesifik` varchar(100) DEFAULT NULL,
  `harga_kamar_instance` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `detail_pemesanans`
--

INSERT INTO `detail_pemesanans` (`id`, `id_pemesanan`, `id_tipe_kamar`, `id_kamar`, `nama_tamu_spesifik`, `harga_kamar_instance`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Livia', 250000, '2026-05-07 18:30:56', '2026-05-07 18:30:56'),
(2, 2, 1, 1, 'as', 250000, '2026-05-07 18:35:35', '2026-05-07 18:35:35'),
(3, 3, 1, 2, 'livia', 250000, '2026-05-10 19:04:58', '2026-05-10 19:04:58'),
(4, 4, 4, 22, 'agus', 850000, '2026-05-10 23:16:24', '2026-05-10 23:16:24'),
(5, 5, 2, 13, 'beryl', 450000, '2026-05-10 23:23:40', '2026-05-10 23:23:40'),
(6, 6, 1, 10, 'Azizah', 300000, '2026-05-10 23:44:44', '2026-05-10 23:44:44'),
(7, 7, 4, 24, 'masayu', 850000, '2026-05-10 23:47:50', '2026-05-10 23:47:50'),
(8, 8, 3, 17, 'sisi', 950000, '2026-05-11 18:56:55', '2026-05-11 18:56:55'),
(9, 9, 1, 3, 'zaza', 300000, '2026-05-14 05:06:49', '2026-05-14 05:06:49'),
(10, 10, 1, 10, 'indah', 300000, '2026-05-14 05:50:57', '2026-05-14 05:50:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kamars`
--

CREATE TABLE `kamars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_tipe_kamar` bigint(20) UNSIGNED NOT NULL,
  `nomor_kamar` varchar(10) NOT NULL,
  `status_kamar` enum('tersedia','kotor','perbaikan','bersih','kosong') DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kamars`
--

INSERT INTO `kamars` (`id`, `id_tipe_kamar`, `nomor_kamar`, `status_kamar`, `created_at`, `updated_at`) VALUES
(1, 1, '101', 'kotor', '2026-05-07 17:47:30', '2026-05-10 18:56:32'),
(2, 1, '102', 'tersedia', '2026-05-07 17:47:30', '2026-05-10 23:36:23'),
(3, 1, '103', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(4, 1, '104', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(5, 1, '105', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(6, 1, '201', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(7, 1, '202', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(8, 1, '203', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(9, 1, '204', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(10, 1, '205', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(11, 2, '301', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(12, 2, '302', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(13, 2, '303', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(14, 2, '304', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(15, 2, '305', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(16, 2, '306', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(17, 3, '401', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(18, 3, '402', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(19, 3, '403', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(20, 4, '501', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(21, 4, '502', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(22, 4, '503', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(23, 4, '504', 'tersedia', '2026-05-07 17:47:30', '2026-05-07 17:47:30'),
(24, 4, '505', 'kotor', '2026-05-07 17:47:30', '2026-05-14 05:42:43'),
(25, 3, '506', 'tersedia', '2026-05-07 23:04:05', '2026-05-10 18:48:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_01_01_000001_create_users_table', 1),
(2, '2024_01_01_000002_create_tipe_kamars_table', 1),
(3, '2024_01_01_000003_create_kamars_table', 1),
(4, '2024_01_01_000004_create_pemesanans_table', 1),
(5, '2024_01_01_000005_create_detail_pemesanans_table', 1),
(6, '2024_01_01_000006_create_password_resets_table', 1),
(7, '2024_01_01_000007_create_personal_access_tokens_table', 1),
(8, '2026_04_02_021204_add_status_pembayaran_to_pemesanans', 1),
(9, '2026_05_08_013712_update_kamars_status_enum', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `otp`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'admin@71hotel.com', '011722', '2026-05-07 18:19:15', '2026-05-07 18:14:15', '2026-05-07 18:14:15'),
(2, 'resepsionis@gmail.com', '986288', '2026-05-10 23:24:28', '2026-05-10 23:19:28', '2026-05-10 23:19:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanans`
--

CREATE TABLE `pemesanans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomor_pemesanan` varchar(20) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `email_pemesan` varchar(100) NOT NULL,
  `tgl_pemesanan` timestamp NOT NULL DEFAULT current_timestamp(),
  `tgl_check_in` date NOT NULL,
  `tgl_check_out` date NOT NULL,
  `jumlah_kamar` int(11) NOT NULL,
  `jumlah_malam` int(11) NOT NULL,
  `total_harga` bigint(20) NOT NULL,
  `metode_pembayaran` enum('transfer','cash') NOT NULL DEFAULT 'transfer',
  `bukti_transfer` text DEFAULT NULL,
  `denda` bigint(20) NOT NULL DEFAULT 0,
  `jumlah_bayar` bigint(20) NOT NULL DEFAULT 0,
  `kembalian` bigint(20) NOT NULL DEFAULT 0,
  `status_pemesanan` enum('baru','check_in','check_out') NOT NULL DEFAULT 'baru',
  `status_pembayaran` enum('belum_dibayar','menunggu_validasi','lunas') NOT NULL DEFAULT 'belum_dibayar',
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pemesanans`
--

INSERT INTO `pemesanans` (`id`, `nomor_pemesanan`, `nama_pemesan`, `email_pemesan`, `tgl_pemesanan`, `tgl_check_in`, `tgl_check_out`, `jumlah_kamar`, `jumlah_malam`, `total_harga`, `metode_pembayaran`, `bukti_transfer`, `denda`, `jumlah_bayar`, `kembalian`, `status_pemesanan`, `status_pembayaran`, `id_user`, `created_at`, `updated_at`) VALUES
(1, 'BO-202605080001', 'Tamu Demo', 'tamu@gmail.com', '2026-05-07 18:30:56', '2026-05-08', '2026-05-12', 1, 4, 1000000, 'transfer', 'bukti_transfer/x5RyctR1kvropSBGzOA4Am4RRUNXkn8bcoPzhGfh.jpg', 0, 0, 0, 'check_out', 'lunas', 3, '2026-05-07 18:30:56', '2026-05-10 18:56:32'),
(2, 'BO-202605080002', 'Tamu Demo', 'tamu@gmail.com', '2026-05-07 18:35:35', '2026-05-16', '2026-05-19', 1, 3, 750000, 'transfer', 'bukti_transfer/w488hoAIdieZmGvQhDBGD3hbphHJhIPbX8i3hIbq.jpg', 0, 0, 0, 'check_out', 'lunas', 3, '2026-05-07 18:35:35', '2026-05-07 18:55:45'),
(3, 'BO-202605110001', 'Tamu Demo', 'tamuyaa@gmail.com', '2026-05-10 19:04:58', '2026-05-11', '2026-05-12', 1, 1, 250000, 'cash', NULL, 2650000, 0, 0, 'check_out', 'lunas', 3, '2026-05-10 19:04:58', '2026-05-10 19:17:26'),
(4, 'BO-202605110002', 'Tamu Demo', 'tamuyaa@gmail.com', '2026-05-10 23:16:24', '2026-05-11', '2026-05-14', 1, 3, 2550000, 'transfer', NULL, 0, 0, 0, 'baru', 'belum_dibayar', 3, '2026-05-10 23:16:24', '2026-05-10 23:16:24'),
(5, 'BO-202605110003', 'Tamu Demo', 'tamuyaa@gmail.com', '2026-05-10 23:23:39', '2026-05-11', '2026-05-13', 1, 2, 900000, 'transfer', NULL, 0, 0, 0, 'baru', 'belum_dibayar', 3, '2026-05-10 23:23:39', '2026-05-10 23:38:37'),
(6, 'BO-202605110004', 'Tamu Demo', 'tamuyaa@gmail.com', '2026-05-10 23:44:43', '2026-05-11', '2026-05-12', 1, 1, 300000, 'cash', NULL, 0, 0, 0, 'baru', 'lunas', 3, '2026-05-10 23:44:43', '2026-05-10 23:44:43'),
(7, 'BO-202605110005', 'Tamu Demo', 'tamuyaa@gmail.com', '2026-05-10 23:47:49', '2026-05-11', '2026-05-15', 1, 4, 3400000, 'transfer', 'bukti_transfer/ekrlRxAdKXnfyi6DmQVwKHX3n41ZwG2EKuEReCNG.jpg', 0, 0, 0, 'check_out', 'lunas', 3, '2026-05-10 23:47:49', '2026-05-14 05:42:43'),
(8, 'BO-202605120001', 'Tamu Demo', 'tamuyaa@gmail.com', '2026-05-11 18:56:55', '2026-05-12', '2026-05-13', 1, 1, 950000, 'transfer', NULL, 0, 0, 0, 'baru', 'belum_dibayar', 3, '2026-05-11 18:56:55', '2026-05-11 18:58:44'),
(9, 'BO-202605140001', 'Tamu Demo', 'tamuyaa@gmail.com', '2026-05-14 05:06:49', '2026-05-14', '2026-05-15', 1, 1, 300000, 'cash', NULL, 0, 0, 0, 'check_in', 'lunas', 3, '2026-05-14 05:06:49', '2026-05-14 05:42:51'),
(10, 'BO-202605140002', 'sisi\r\n', 'tamuyaa@gmail.com', '2026-05-14 05:50:56', '2026-05-14', '2026-05-15', 1, 1, 300000, 'cash', NULL, 0, 0, 0, 'baru', 'belum_dibayar', 3, '2026-05-14 05:50:56', '2026-05-14 05:50:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(23, 'App\\Models\\User', 1, 'auth_token', '100ec2b2a40108a40896735470f5f258f54b9029f4a7f1ae0e98c7430ada1077', '[\"*\"]', '2026-05-07 23:58:49', NULL, '2026-05-07 23:49:52', '2026-05-07 23:58:49'),
(50, 'App\\Models\\User', 3, 'auth_token', '5c0a9ec256aa86b7c38fd8bb6ef07c9cf47145167e02ac7dfa62250651e9d6f1', '[\"*\"]', '2026-05-14 05:50:56', NULL, '2026-05-14 05:50:18', '2026-05-14 05:50:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tipe_kamars`
--

CREATE TABLE `tipe_kamars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_tipe_kamar` varchar(100) NOT NULL,
  `harga` bigint(20) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` text DEFAULT NULL,
  `fasilitas_tipe` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fasilitas_tipe`)),
  `stok` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tipe_kamars`
--

INSERT INTO `tipe_kamars` (`id`, `nama_tipe_kamar`, `harga`, `deskripsi`, `foto`, `fasilitas_tipe`, `stok`, `created_at`, `updated_at`) VALUES
(1, 'Standard Room', 300000, 'Kamar standar yang nyaman dengan fasilitas lengkap untuk istirahat Anda. Dilengkapi dengan tempat tidur single/double, AC, TV LED, dan kamar mandi dalam.', 'tipe_kamar/kuhod8qjDgQJnhCi7NTksZhfYH2Rudx66PcHC9Gq.jpg', '[\"WIFI\",\"TV 34\\\"\",\"DF\",\"HANDUK MINI\"]', 10, '2026-05-07 17:47:30', '2026-05-10 20:14:37'),
(2, 'Deluxe Room', 450000, 'Kamar deluxe yang luas dan elegan dengan pemandangan kota. Dilengkapi dengan tempat tidur king size, sofa, meja kerja, dan minibar.', 'tipe_kamar/r28JzeZCCt4IolvXE3qKZHmcD6rjqry9qXVBlQrR.jpg', '[\"AC\",\"TV LED 43\\\"\",\"WiFi Gratis\",\"Kamar Mandi Dalam\",\"Air Panas\",\"Bathtub\",\"Minibar\",\"Sofa\",\"Meja Kerja\",\"Safe Box\",\"Handuk\",\"Toiletries\",\"Coffee Maker\"]', 6, '2026-05-07 17:47:30', '2026-05-10 18:26:10'),
(3, 'Suite Room', 950000, 'Suite mewah dengan ruang tamu terpisah dan pemandangan panoramic. Pengalaman menginap premium dengan layanan butler dan amenities eksklusif.', 'tipe_kamar/mqA9FT1r0pjHsqi6jMy1WnXXxKDyPheuuUwf6LBt.jpg', '[\"TV LED 55\\\"\",\"WiFi Premium\",\"Kamar Mandi Mewah\",\"Jacuzzi\",\"Bathtub\",\"Minibar Premium\",\"Ruang Tamu\",\"Sofa Set\",\"Meja Kerja\",\"Safe Box\",\"Handuk Premium\",\"Toiletries Premium\",\"Nespresso Machine\",\"Butler Service\",\"Balkon Privat\"]', 3, '2026-05-07 17:47:30', '2026-05-10 20:15:46'),
(4, 'Family Room', 850000, 'Kamar keluarga yang luas dengan dua tempat tidur dan ruang bermain anak. Cocok untuk liburan keluarga yang menyenangkan.', 'tipe_kamar/oVITsMe6XNaU8fbrQz2SUgpeWtWy0W7wSE423X40.jpg', '[\"WiFi Gratis\",\"Kamar Mandi Dalam\",\"Air Panas\",\"2 Double Bed\",\"Minibar\",\"Meja Kerja\",\"Safe Box\",\"Handuk\",\"Toiletries\",\"Extra Bed Available\"]', 5, '2026-05-07 17:47:30', '2026-05-10 20:16:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `foto` text DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `password` text NOT NULL,
  `role` enum('admin','resepsionis','tamu') NOT NULL DEFAULT 'tamu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_user`, `foto`, `email`, `no_hp`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', NULL, 'admin@sysygmail.com', '081228435753', '$2y$10$ds7cjggXQrlpHE1laX9rPucHRTNIhXiSNcks8r/gapPX7j8xJsEP2', 'admin', '2026-05-07 17:47:29', '2026-05-07 17:47:29'),
(2, 'Resepsionis', NULL, 'resepsionis@gmail.com', '081234567891', '$2y$10$YaznZgPSP/oxtU0m3cHSTOZbOOofQFkwzHRQE3nvwaQuyQZBiBmSy', 'resepsionis', '2026-05-07 17:47:29', '2026-05-07 17:47:29'),
(3, 'sisi\r\n', NULL, 'tamuyaa@gmail.com', '081234567892', '$2y$10$ckQZRrI2htjwyzoRcsA/9uyRU/8M3qsIG81dPbIVRf3cQcjuEDEfq', 'tamu', '2026-05-07 17:47:30', '2026-05-07 17:47:30');

--
-- Indexes for dumped tables
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
-- Indeks untuk tabel `detail_pemesanans`
--
ALTER TABLE `detail_pemesanans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_pemesanans_id_pemesanan_foreign` (`id_pemesanan`),
  ADD KEY `detail_pemesanans_id_tipe_kamar_foreign` (`id_tipe_kamar`),
  ADD KEY `detail_pemesanans_id_kamar_foreign` (`id_kamar`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indeks untuk tabel `kamars`
--
ALTER TABLE `kamars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kamars_id_tipe_kamar_foreign` (`id_tipe_kamar`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pemesanans`
--
ALTER TABLE `pemesanans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pemesanans_nomor_pemesanan_unique` (`nomor_pemesanan`),
  ADD KEY `pemesanans_id_user_foreign` (`id_user`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `tipe_kamars`
--
ALTER TABLE `tipe_kamars`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_pemesanans`
--
ALTER TABLE `detail_pemesanans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kamars`
--
ALTER TABLE `kamars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pemesanans`
--
ALTER TABLE `pemesanans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `tipe_kamars`
--
ALTER TABLE `tipe_kamars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_pemesanans`
--
ALTER TABLE `detail_pemesanans`
  ADD CONSTRAINT `detail_pemesanans_id_kamar_foreign` FOREIGN KEY (`id_kamar`) REFERENCES `kamars` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pemesanans_id_pemesanan_foreign` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pemesanans_id_tipe_kamar_foreign` FOREIGN KEY (`id_tipe_kamar`) REFERENCES `tipe_kamars` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kamars`
--
ALTER TABLE `kamars`
  ADD CONSTRAINT `kamars_id_tipe_kamar_foreign` FOREIGN KEY (`id_tipe_kamar`) REFERENCES `tipe_kamars` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pemesanans`
--
ALTER TABLE `pemesanans`
  ADD CONSTRAINT `pemesanans_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
