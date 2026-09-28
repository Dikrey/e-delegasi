-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 25 Sep 2026 pada 06.22
-- Versi server: 8.0.30
-- Versi PHP: 8.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `surat`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `module`, `reference_id`, `ip_address`, `payload`, `created_at`, `updated_at`) VALUES
(1, 1, 'Mengirim delegasi: :title', 'delegation', 8, '192.168.1.10', '{\"title\":\"Tindak Lanjut Rapat Lobusona\"}', '2026-09-17 13:53:21', '2026-09-17 13:53:21'),
(2, 1, 'Membuat delegasi: :title', 'delegation', 8, '192.168.1.10', '{\"title\":\"Tindak Lanjut Rapat Lobusona\"}', '2026-09-17 13:53:21', '2026-09-17 13:53:21'),
(3, 1, 'Memperbarui delegasi: :title', 'delegation', 8, '192.168.1.10', '{\"title\":\"Tindak Lanjut Rapat Lobusona\"}', '2026-09-17 13:53:38', '2026-09-17 13:53:38'),
(4, 1, 'Mengubah status tugas: Tindak Lanjut Rapat Lobusona', 'task', 15, '192.168.1.10', '{\"title\":\"Tindak Lanjut Rapat Lobusona\",\"note\":\"bisa mewing\"}', '2026-09-17 14:02:36', '2026-09-17 14:02:36'),
(5, 1, 'Memperbarui progres tugas: :title', 'task', 15, '192.168.1.10', '{\"title\":\"Tindak Lanjut Rapat Lobusona\"}', '2026-09-17 14:02:36', '2026-09-17 14:02:36'),
(6, 1, 'Mengubah status tugas: Verifikasi berkas permohonan bantuan', 'task', 6, '192.168.1.10', '{\"title\":\"Verifikasi berkas permohonan bantuan\",\"note\":null}', '2026-09-17 14:11:09', '2026-09-17 14:11:09'),
(7, 1, 'Memperbarui progres tugas: :title', 'task', 6, '192.168.1.10', '{\"title\":\"Verifikasi berkas permohonan bantuan\"}', '2026-09-17 14:11:09', '2026-09-17 14:11:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `agendas`
--

CREATE TABLE `agendas` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `agenda_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'rapat | undangan | kunjungan | audiensi | pelatihan | lainnya',
  `delegation_id` bigint UNSIGNED DEFAULT NULL,
  `letter_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'terjadwal' COMMENT 'terjadwal | selesai | dibatalkan',
  `created_by` bigint UNSIGNED NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `agendas`
--

INSERT INTO `agendas` (`id`, `title`, `description`, `agenda_type`, `delegation_id`, `letter_id`, `date`, `start_time`, `end_time`, `location`, `status`, `created_by`, `note`, `created_at`, `updated_at`) VALUES
(1, 'Rapat: Tindak lanjut undangan rapat koordinasi bidang', 'Menghadiri dan menyusun laporan hasil rapat koordinasi bidang.', 'rapat', 1, 29, '2026-09-17', '09:00:00', '11:00:00', 'Ruang Rapat Utama', 'terjadwal', 1, 'Agenda tindak lanjut delegasi.', '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(2, 'Rapat: Penyusunan laporan kinerja triwulan', 'Menyusun rekapitulasi laporan kinerja triwulan berjalan.', 'undangan', 2, 14, '2026-09-18', '09:00:00', '11:00:00', 'Ruang Rapat Utama', 'terjadwal', 1, 'Agenda tindak lanjut delegasi.', '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(3, 'Rapat: Verifikasi berkas permohonan bantuan', 'Memeriksa kelengkapan berkas permohonan bantuan masyarakat.', 'audiensi', 3, 33, '2026-09-19', '09:00:00', '11:00:00', 'Ruang Rapat Utama', 'terjadwal', 1, 'Agenda tindak lanjut delegasi.', '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(4, 'Rapat: Persiapan kunjungan kerja pimpinan', 'Menyiapkan seluruh kebutuhan kunjungan kerja pimpinan.', 'undangan', 5, 50, '2026-09-21', '09:00:00', '11:00:00', 'Ruang Rapat Utama', 'terjadwal', 1, 'Agenda tindak lanjut delegasi.', '2026-09-17 09:51:57', '2026-09-17 09:51:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `attachments`
--

CREATE TABLE `attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pdf',
  `letter_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `classifications`
--

CREATE TABLE `classifications` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `classifications`
--

INSERT INTO `classifications` (`id`, `code`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ADM', 'Administrasi', 'Jenis surat yang berkaitan dengan administrasi', '2026-09-17 09:51:53', '2026-09-17 09:51:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `configs`
--

CREATE TABLE `configs` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `configs`
--

INSERT INTO `configs` (`id`, `code`, `value`, `created_at`, `updated_at`) VALUES
(1, 'default_password', 'admin', NULL, NULL),
(2, 'page_size', '5', NULL, NULL),
(3, 'app_name', 'E-Delegasi', NULL, NULL),
(4, 'institution_name', '404nfid', NULL, NULL),
(5, 'institution_address', 'Jl. Padat Karya', NULL, NULL),
(6, 'institution_phone', '082121212121', NULL, NULL),
(7, 'institution_email', 'admin@admin.com', NULL, NULL),
(8, 'language', 'id', NULL, NULL),
(9, 'pic', 'M. Iqbal Effendi', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `delegations`
--

CREATE TABLE `delegations` (
  `id` bigint UNSIGNED NOT NULL,
  `letter_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `instruction` text COLLATE utf8mb4_unicode_ci,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT 'rendah | normal | tinggi | urgent',
  `deadline` datetime DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT 'draft | dikirim | diterima | dalam_pengerjaan | menunggu_verifikasi | selesai | ditolak',
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Lampiran delegasi',
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `delegations`
--

INSERT INTO `delegations` (`id`, `letter_id`, `title`, `description`, `instruction`, `priority`, `deadline`, `status`, `attachment`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 29, 'Tindak lanjut undangan rapat koordinasi bidang', 'Menghadiri dan menyusun laporan hasil rapat koordinasi bidang.', 'Konfirmasi kehadiran, siapkan materi, dan susun notulen rapat.', 'tinggi', '2026-09-20 16:51:56', 'dalam_pengerjaan', NULL, 2, '2026-09-07 09:51:56', '2026-09-15 09:51:56'),
(2, 14, 'Penyusunan laporan kinerja triwulan', 'Menyusun rekapitulasi laporan kinerja triwulan berjalan.', 'Kumpulkan data dari masing-masing bidang lalu susun rekapitulasi.', 'normal', '2026-09-24 16:51:56', 'dikirim', NULL, 2, '2026-09-08 09:51:57', '2026-09-15 09:51:57'),
(3, 33, 'Verifikasi berkas permohonan bantuan', 'Memeriksa kelengkapan berkas permohonan bantuan masyarakat.', 'Periksa kelengkapan dan buat catatan kekurangan berkas.', 'urgent', '2026-09-16 16:51:56', 'dalam_pengerjaan', NULL, 2, '2026-09-09 09:51:57', '2026-09-17 14:11:09'),
(4, 2, 'Pengumpulan data potensi pertanian', 'Mengumpulkan data potensi pertanian untuk laporan tahunan.', 'Koordinasi dengan penyuluh lapangan untuk data terbaru.', 'normal', '2026-09-14 16:51:56', 'selesai', NULL, 2, '2026-09-10 09:51:57', '2026-09-15 09:51:57'),
(5, 50, 'Persiapan kunjungan kerja pimpinan', 'Menyiapkan seluruh kebutuhan kunjungan kerja pimpinan.', 'Siapkan jadwal, transportasi, dan bahan paparan.', 'tinggi', '2026-09-27 16:51:56', 'diterima', NULL, 2, '2026-09-11 09:51:57', '2026-09-15 09:51:57'),
(6, 30, 'Tindak lanjut surat dari Kementerian', 'Menyusun jawaban atas surat permintaan data dari Kementerian.', 'Susun draf jawaban dan koordinasikan dengan pimpinan.', 'urgent', '2026-09-22 16:51:56', 'ditolak', NULL, 2, '2026-09-12 09:51:57', '2026-09-15 09:51:57'),
(7, 29, 'Draf surat edaran monitoring', 'Menyusun draf surat edaran monitoring kegiatan lapangan.', 'Susun draf, ajukan untuk ditandatangani pimpinan.', 'rendah', '2026-10-01 16:51:56', 'draft', NULL, 2, '2026-09-13 09:51:57', '2026-09-15 09:51:57'),
(8, 29, 'Tindak Lanjut Rapat Lobusona', 'MENUGASI DENGAN INI SAUDARA BAYU PRADIKA', 'Mengumpulkan Data Pangan 2029', 'rendah', '2026-09-19 19:52:00', 'dalam_pengerjaan', NULL, 1, '2026-09-17 13:53:20', '2026-09-17 14:02:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `departments`
--

CREATE TABLE `departments` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dispositions`
--

CREATE TABLE `dispositions` (
  `id` bigint UNSIGNED NOT NULL,
  `to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forwarded_to` json DEFAULT NULL COMMENT 'Diteruskan kepada (daftar kunci pihak tujuan)',
  `forwarded_to_custom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'UPT ........ isi sendiri',
  `due_date` date NOT NULL,
  `received_at` date DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `honor` json DEFAULT NULL COMMENT 'Dengan hormat harap (daftar kunci perlakuan)',
  `honor_custom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dengan hormat harap - isi sendiri',
  `note` text COLLATE utf8mb4_unicode_ci,
  `instruction` text COLLATE utf8mb4_unicode_ci COMMENT 'Instruksi panjang untuk penerima disposisi',
  `direction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Arah disposisi hasil verifikasi sekretaris',
  `is_received` tinyint(1) DEFAULT NULL COMMENT 'Apakah surat diterima oleh pihak tujuan (verifikasi sekretaris)',
  `verification_note` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan verifikasi sekretaris',
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `letter_status` bigint UNSIGNED NOT NULL,
  `letter_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dispositions`
--

INSERT INTO `dispositions` (`id`, `to`, `forwarded_to`, `forwarded_to_custom`, `due_date`, `received_at`, `content`, `honor`, `honor_custom`, `note`, `instruction`, `direction`, `is_received`, `verification_note`, `verified_by`, `verified_at`, `letter_status`, `letter_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Prof. Annabell Feest PhD', NULL, NULL, '2025-08-31', NULL, 'Autem rerum fugit debitis illum praesentium ea expedita hic.', NULL, NULL, 'Ratione tenetur tempore quo aliquid.', NULL, NULL, NULL, NULL, NULL, NULL, 1, 20, 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(2, 'Dillon Ruecker', NULL, NULL, '2002-01-30', NULL, 'Dolor officia sed nihil molestiae placeat qui fugit quia fugiat.', NULL, NULL, 'Ipsa omnis culpa.', NULL, NULL, NULL, NULL, NULL, NULL, 2, 4, 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(3, 'Dereck King', NULL, NULL, '1988-01-26', NULL, 'Quasi earum fugiat incidunt molestiae non non ipsa dolores illo.', NULL, NULL, 'Adipisci aperiam nobis.', NULL, NULL, NULL, NULL, NULL, NULL, 2, 29, 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(4, 'Brooke Gutmann', NULL, NULL, '1971-11-25', NULL, 'Voluptate dolores dolor aut dolorem pariatur velit.', NULL, NULL, 'Qui aspernatur aspernatur aliquid.', NULL, NULL, NULL, NULL, NULL, NULL, 1, 24, 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(5, 'Mrs. Loma Pagac IV', NULL, NULL, '1998-12-23', NULL, 'A omnis vel nihil iusto a libero accusantium ut repellat quia.', NULL, NULL, 'Ea voluptas et non.', NULL, NULL, NULL, NULL, NULL, NULL, 1, 8, 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(6, 'Carey Weissnat III', NULL, NULL, '1993-11-13', NULL, 'Animi consectetur nesciunt rem et dicta in doloribus.', NULL, NULL, 'Nisi commodi ut ea.', NULL, NULL, NULL, NULL, NULL, NULL, 1, 4, 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(7, 'Bert Lebsack', NULL, NULL, '2022-10-15', NULL, 'Praesentium et ut quia illum incidunt ex nulla non.', NULL, NULL, 'Alias fugiat ullam vel.', NULL, NULL, NULL, NULL, NULL, NULL, 3, 9, 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(8, 'Miss Brionna Hartmann III', NULL, NULL, '2011-01-05', NULL, 'Nobis est fugiat dolorem officiis qui et suscipit.', NULL, NULL, 'Doloremque nesciunt tenetur sit.', NULL, NULL, NULL, NULL, NULL, NULL, 1, 37, 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(9, 'Ms. Yasmine Pfannerstill', NULL, NULL, '1989-08-24', NULL, 'Consequuntur perspiciatis velit voluptatem perferendis et inventore nihil voluptas ea veniam et.', NULL, NULL, 'Sed suscipit sint optio.', NULL, NULL, NULL, NULL, NULL, NULL, 2, 28, 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(10, 'Dr. Courtney Streich PhD', NULL, NULL, '1993-10-27', NULL, 'Et aut in et deserunt odit corrupti consequuntur laudantium omnis numquam omnis provident possimus.', NULL, NULL, 'Laboriosam ut non.', NULL, NULL, NULL, NULL, NULL, NULL, 2, 27, 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(11, 'Muriel Dach', NULL, NULL, '1979-01-16', NULL, 'Consectetur quas modi culpa sit totam cum esse ratione ipsa exercitationem dolor molestias consequatur.', NULL, NULL, 'Esse nemo ab harum.', NULL, NULL, NULL, NULL, NULL, NULL, 1, 37, 1, '2026-09-17 09:51:56', '2026-09-17 09:51:56'),
(12, 'Ashlynn Romaguera III', NULL, NULL, '1979-10-27', NULL, 'Harum repudiandae accusantium et est debitis numquam nulla occaecati et eum omnis.', NULL, NULL, 'Qui deleniti deserunt.', NULL, NULL, NULL, NULL, NULL, NULL, 1, 22, 1, '2026-09-17 09:51:56', '2026-09-17 09:51:56'),
(13, 'Prof. Jeffrey Zboncak IV', NULL, NULL, '1970-11-22', NULL, 'At et alias ut rerum quasi id rerum repudiandae consequatur repellendus est amet eveniet.', NULL, NULL, 'Rem autem.', NULL, NULL, NULL, NULL, NULL, NULL, 2, 12, 1, '2026-09-17 09:51:56', '2026-09-17 09:51:56'),
(14, 'Prof. Harrison Blick Sr.', NULL, NULL, '2011-10-21', NULL, 'Vel dolores vero magni voluptatibus laborum aut esse rerum aut qui.', NULL, NULL, 'Et aut labore.', NULL, NULL, NULL, NULL, NULL, NULL, 3, 40, 1, '2026-09-17 09:51:56', '2026-09-17 09:51:56'),
(15, 'Mr. Edmond Treutel', NULL, NULL, '1996-12-22', NULL, 'Commodi et sit totam est delectus iure cumque.', NULL, NULL, 'Quo doloremque libero.', NULL, NULL, NULL, NULL, NULL, NULL, 1, 13, 1, '2026-09-17 09:51:56', '2026-09-17 09:51:56');

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
-- Struktur dari tabel `letters`
--

CREATE TABLE `letters` (
  `id` bigint UNSIGNED NOT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nomor Surat',
  `agenda_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `letter_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `note` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'incoming' COMMENT 'Surat Masuk (incoming)/Surat Keluar (outgoing)',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Status surat (waiting_for_final_file / final)',
  `verification_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'menunggu_verifikasi' COMMENT 'Status verifikasi sekretaris terhadap surat masuk',
  `verification_note` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan verifikasi sekretaris',
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `classification_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `letters`
--

INSERT INTO `letters` (`id`, `reference_number`, `agenda_number`, `from`, `to`, `letter_date`, `received_date`, `description`, `note`, `type`, `status`, `verification_status`, `verification_note`, `verified_by`, `verified_at`, `classification_code`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '1236635222541', '92043', 'Mohammad Koss Jr.', 'Miss Myrtis Thompson', '1984-10-07', '1996-10-26', 'Officia quidem in temporibus in.', 'Voluptas similique.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(2, '2724098437415', '84666', 'Mr. Conor Kuhn IV', 'America Predovic DDS', '2017-12-11', '2025-07-15', 'Natus sequi eius minima qui minus sed dicta velit quae.', 'Consectetur earum laboriosam illo.', 'incoming', NULL, 'didelegasikan', NULL, 2, '2026-09-17 09:51:56', 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:56'),
(3, '4896653588486', '28214', 'Jed Tromp', 'Alverta Jast', '1980-06-29', '1984-01-19', 'Facilis alias consequatur porro labore non qui.', 'Dignissimos sit est.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(4, '4117430190396', '79611', 'Demond Gusikowski', 'Anna Boehm', '1989-10-02', '2002-11-02', 'Alias sunt aspernatur impedit sit facilis quia ratione.', 'Molestias nostrum et magni.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(5, '4633964357833', '26114', 'Dr. Devan Ebert', 'Etha Rolfson', '1982-07-01', '2021-06-30', 'Nostrum cupiditate sit tempore odit ex quasi autem.', 'Perspiciatis qui est mollitia.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(6, '6839521246116', '19967', 'Dr. Deshaun Bins', 'Lina Monahan', '2005-01-27', '2001-12-10', 'Doloremque est itaque id voluptas.', 'Iure dolores quasi sunt.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(7, '4837105512055', '91420', 'Elwin Kris', 'Amelia Zboncak II', '2010-11-21', '1984-07-28', 'Sed ratione qui et aut eum quam sunt.', 'Rem voluptatum aut.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(8, '2446895474997', '84600', 'Bertrand Collins', 'Prof. Josephine Homenick MD', '1996-11-16', '1996-03-08', 'Necessitatibus quis enim quo fugiat aut.', 'In animi maiores ad voluptas.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(9, '0250195618171', '94672', 'Eriberto Altenwerth', 'Patsy Okuneva', '1994-04-10', '2005-04-14', 'Omnis non sit nesciunt modi consectetur.', 'Amet magnam cum.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(10, '4920798351176', '47989', 'Mr. Herbert Wyman', 'Virgie Hyatt', '2005-05-01', '1992-07-15', 'Dolorem natus nobis repellendus rerum quam qui quas deserunt.', 'Fugiat dolor aut ea.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(11, '0877160096873', '8243', 'Friedrich Schmidt', 'Cora Nader', '1977-11-26', '2010-08-13', 'Eaque voluptatem ullam ex quaerat ab numquam.', 'Corrupti quo quidem.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(12, '0805150260720', '15557', 'Kadin Hauck', 'Madie Lesch I', '1987-05-26', '2025-07-10', 'Qui et doloribus molestiae reiciendis omnis tenetur quia ipsum.', 'Ratione eos et temporibus.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(13, '0576221081529', '19906', 'Dr. Bradly Collier DVM', 'Elisha Ledner', '2018-05-07', '2006-12-07', 'Laudantium ut eveniet quibusdam omnis similique cupiditate odio.', 'Nihil minima enim sed.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(14, '8242880574192', '49476', 'Austin Towne V', 'Ms. Lue Mohr III', '2018-12-01', '1975-01-14', 'Perspiciatis sit laudantium consequatur est molestiae exercitationem.', 'Ipsum odio harum ullam.', 'incoming', NULL, 'memerlukan_tindak_lanjut', NULL, 2, '2026-09-17 09:51:56', 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:56'),
(15, '5155525827849', '75283', 'Odell Lehner', 'Fatima Veum', '2002-10-19', '2026-09-07', 'Aut voluptatem sint harum necessitatibus aliquam eos quo harum quia.', 'Ut pariatur debitis nihil.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(16, '7186617312828', '19361', 'Kamryn Huel', 'Dr. Ardella Kulas I', '1983-08-29', '1990-06-23', 'Sed consequatur fugit et beatae et sit odio impedit.', 'Quis et.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(17, '3248153166212', '24185', 'Dr. Sanford Gerhold', 'Charlotte Kohler', '2013-09-08', '1990-07-02', 'Dolorem dolor quam numquam commodi.', 'Accusantium est earum molestias.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(18, '3749767193262', '86140', 'Mr. Coby Pfannerstill IV', 'Ms. Name Nienow V', '2021-11-29', '1980-02-11', 'Saepe quo alias fuga non sunt corporis quaerat quo.', 'Excepturi velit inventore.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(19, '9452426268853', '19744', 'Jeff Hermann', 'Ms. Euna Streich MD', '2008-12-11', '1971-03-25', 'Aut necessitatibus nam eveniet assumenda dolorem sit eos officiis.', 'Odit ut inventore repudiandae.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(20, '3662522951849', '11444', 'Baylee Schroeder', 'Gwen Hahn', '2011-05-09', '1971-08-09', 'Illo qui voluptate velit ut laboriosam consequatur non.', 'Nihil iste quaerat quis esse.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(21, '1620255231555', '86397', 'Nikolas Ullrich', 'Jade Hill', '1996-11-30', '2021-06-14', 'Ea iste minus at ea.', 'Rerum saepe veritatis id.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(22, '4512318699945', '9874', 'Seth Goyette I', 'Letha Moore', '1988-06-28', '2025-09-12', 'Adipisci dignissimos rerum ea cumque nisi.', 'Minus voluptatem consequatur.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(23, '4643447150073', '77629', 'Dr. Leopold Kris', 'Chanelle Stamm', '1992-04-20', '1977-03-13', 'Vitae libero neque voluptatem fugiat error similique quaerat.', 'Eius sit in.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(24, '5363422221213', '53649', 'Kole Hermann', 'Amira Sanford Sr.', '1990-11-19', '2000-03-29', 'Quia unde nemo inventore ex odio.', 'Non repellat saepe.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(25, '6890796847149', '1079', 'Prof. Alf Bosco Jr.', 'Wilma Prohaska', '1997-06-29', '1996-10-29', 'Vel odit molestias nostrum omnis est maiores quo.', 'Eos autem et.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(26, '9018024874416', '99479', 'Marlin Little', 'Dr. Margarette Ryan', '2014-07-28', '1987-02-14', 'Ipsa quos alias iure delectus.', 'Ipsum velit.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(27, '9638869073272', '69369', 'Hermann Waters', 'Maryjane Pfannerstill', '1979-08-25', '2005-08-17', 'Optio explicabo nobis mollitia iusto est ad tenetur vel.', 'Dolores eos doloremque.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(28, '8975624516843', '93600', 'Sidney Howell', 'Kiera Beahan', '2013-12-17', '1980-04-05', 'Nihil ad aut et voluptatum ut alias.', 'Porro sit neque.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(29, '9859690583079', '5599', 'Lavern Ondricka', 'Hope Rogahn', '2025-12-20', '2026-04-12', 'Numquam qui sequi voluptatem eum.', 'Impedit tenetur eligendi cupiditate.', 'incoming', NULL, 'didelegasikan', NULL, 1, '2026-09-17 13:53:20', 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 13:53:20'),
(30, '6533495763326', '14921', 'Prof. Clair Will V', 'Roxanne Cummerata', '2015-02-14', '2001-01-29', 'Dicta velit ut perferendis mollitia.', 'Minima autem consectetur laudantium.', 'incoming', NULL, 'didelegasikan', NULL, 2, '2026-09-17 09:51:56', 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:56'),
(31, '8208170915427', '79564', 'Ole Botsford', 'Danyka Herman', '1975-10-25', '2021-03-10', 'Consequatur harum est dolorem sed voluptas minima.', 'Itaque quisquam provident.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(32, '4833376947237', '70266', 'Mr. Willis Becker', 'Alessandra White Jr.', '1994-09-22', '2015-07-22', 'Labore officia dicta aut aut consectetur soluta.', 'Minima ducimus qui sequi rem.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(33, '0432665361576', '88789', 'Green Lesch', 'Gerry Stanton', '2018-01-30', '1996-08-30', 'A nihil at quidem qui.', 'Occaecati perferendis.', 'incoming', NULL, 'didelegasikan', NULL, 2, '2026-09-17 09:51:56', 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:56'),
(34, '7993564281646', '38488', 'Lesley Franecki V', 'Brooke Stiedemann', '1998-06-29', '2025-12-07', 'Quo autem occaecati itaque doloribus delectus porro voluptatem.', 'Sit dolorem consectetur.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(35, '8738554120036', '47958', 'Mr. Sherwood Cummings II', 'Demetris Howell DDS', '1983-09-18', '2003-10-21', 'Beatae repudiandae rerum consequatur esse aut.', 'Quis nam sed libero.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(36, '1087538018676', '88367', 'Luigi Graham', 'Hilda O\'Connell DDS', '1986-01-21', '1983-05-14', 'Impedit officiis adipisci animi eaque repudiandae culpa tempora.', 'Sequi dolorem ipsam reiciendis.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(37, '3968133614297', '76430', 'Kelton Boyle', 'Erna Nikolaus', '2021-06-29', '1974-10-23', 'Maiores et dolor ipsa dolorem repudiandae praesentium ipsa velit.', 'Quo minus similique nisi.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(38, '2596449418622', '30166', 'Moshe Carroll', 'Lindsay Treutel', '1997-08-13', '1996-06-24', 'Officia aliquam rerum similique ut inventore.', 'Nihil modi id aut.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(39, '1046895310885', '87102', 'Mac Legros Jr.', 'Lorena Dickinson', '1973-07-21', '1997-07-18', 'Labore reprehenderit quia minus est vero suscipit.', 'Aut hic est.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(40, '6381625837856', '57704', 'Jerry Bayer', 'Esperanza Huels', '2022-02-07', '2011-08-24', 'Aut impedit itaque voluptate amet doloremque fugiat et et aut.', 'Expedita atque.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(41, '9835408247440', '17219', 'Dr. Jonathan Bednar Sr.', 'Annetta Balistreri', '1999-06-28', '1987-08-29', 'Praesentium earum amet similique est nam dolor omnis.', 'Dolores dolore.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(42, '3715383218504', '13643', 'Desmond Wuckert', 'Joanne Wunsch MD', '1973-11-13', '1987-09-17', 'Earum beatae dolore eveniet nisi culpa.', 'Aut culpa reprehenderit.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(43, '7752659773547', '67349', 'Dr. Deron Dickinson V', 'Miss Tamara Welch', '1993-09-07', '2023-02-06', 'Voluptatem nesciunt non veritatis dolor optio consequatur corrupti.', 'Quam magni ratione.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(44, '5221221438372', '76413', 'Clyde Reinger', 'Dr. Theodora Franecki', '1984-11-04', '1977-03-08', 'Est harum nam consequatur atque aperiam.', 'Eos nam.', 'outgoing', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(45, '7409858123313', '13105', 'Prof. Curtis Dare DVM', 'Miss Clemmie Botsford', '1986-07-23', '1999-05-14', 'Nemo autem error maiores voluptas accusamus et ab vel.', 'Repellat enim debitis.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(46, '6071381970224', '33383', 'Keshawn Padberg', 'Michelle Ritchie II', '1990-12-30', '2016-03-09', 'Quia ut omnis ut optio ab.', 'Atque asperiores culpa.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(47, '5540281410945', '4876', 'Devan Gaylord', 'Cassie Connelly Sr.', '1989-12-13', '2020-07-28', 'Vitae iste ipsam et voluptatem perferendis amet voluptatum.', 'Ducimus consequatur.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(48, '3085880462732', '60461', 'Anastacio Leuschke', 'Antoinette Zieme', '1997-01-26', '1983-10-11', 'Corporis voluptates dolorum est dolor recusandae.', 'Porro non voluptatem.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(49, '5627955936458', '11919', 'Kaley Keeling', 'Pamela Blick PhD', '2004-08-28', '2009-03-15', 'Odit eos praesentium molestias expedita.', 'Voluptatem nihil placeat.', 'incoming', NULL, 'menunggu_verifikasi', NULL, NULL, NULL, 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:55'),
(50, '4036806478596', '52712', 'Jedediah Moore I', 'Theresa Fritsch', '2016-02-07', '2014-09-17', 'Enim tenetur autem sit tenetur.', 'Repellendus ut sit occaecati.', 'incoming', NULL, 'didelegasikan', NULL, 2, '2026-09-17 09:51:56', 'ADM', 1, '2026-09-17 09:51:55', '2026-09-17 09:51:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `letter_statuses`
--

CREATE TABLE `letter_statuses` (
  `id` bigint UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `letter_statuses`
--

INSERT INTO `letter_statuses` (`id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Rahasia', '2026-09-17 09:51:53', '2026-09-17 09:51:53'),
(2, 'Segera', '2026-09-17 09:51:53', '2026-09-17 09:51:53'),
(3, 'Biasa', '2026-09-17 09:51:53', '2026-09-17 09:51:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `login_sessions`
--

CREATE TABLE `login_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `device` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `login_sessions`
--

INSERT INTO `login_sessions` (`id`, `user_id`, `session_id`, `ip_address`, `user_agent`, `device`, `browser`, `platform`, `location`, `last_activity_at`, `created_at`, `updated_at`) VALUES
(9, 1, 'kMSa1KksVtnA7YYUURk8NztBJ2DJfw45G0TGZpGP', '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Komputer', 'Chrome', 'Windows 10/11', 'Jaringan Lokal (Privat)', '2026-09-17 16:55:35', '2026-09-17 15:21:34', '2026-09-17 16:55:35'),
(10, 1, 'uUMSsoDcBmdYr1vgiNc9Nv3bUIJyUYhPsJSLJxCD', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Komputer', 'Chrome', 'Windows 10/11', 'Lokal (Localhost)', '2026-09-19 05:41:17', '2026-09-19 05:34:01', '2026-09-19 05:41:17'),
(24, 3, '5zRJriJo0FUEfncRoyKMdcskw2iZEMxwfWlRarVK', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Komputer', 'Chrome', 'Windows 10/11', 'Lokal (Localhost)', '2026-09-19 12:29:16', '2026-09-19 12:29:16', '2026-09-19 12:29:16');

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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2022_12_05_081849_create_configs_table', 1),
(7, '2022_12_05_083409_create_letter_statuses_table', 1),
(8, '2022_12_05_083945_create_classifications_table', 1),
(9, '2022_12_05_084544_create_letters_table', 1),
(10, '2022_12_05_092303_create_dispositions_table', 1),
(11, '2022_12_05_093329_create_attachments_table', 1),
(12, '2026_09_11_000001_create_login_sessions_table', 1),
(13, '2026_09_11_000001_create_nomor_surat_requests_table', 1),
(14, '2026_09_11_000002_add_session_version_to_users_table', 1),
(15, '2026_09_11_000002_update_letters_for_booking', 1),
(16, '2026_09_11_000003_add_code_part_and_unique_reference', 1),
(17, '2026_09_14_000001_add_fields_to_dispositions_table', 1),
(18, '2026_09_14_000002_add_received_at_to_dispositions_table', 1),
(19, '2026_09_17_000001_add_verification_columns_to_letters_table', 1),
(20, '2026_09_17_000002_create_delegations_table', 1),
(21, '2026_09_17_000003_create_tasks_table', 1),
(22, '2026_09_17_000004_create_task_updates_table', 1),
(23, '2026_09_17_000005_create_agendas_table', 1),
(24, '2026_09_17_000006_create_notifications_table', 1),
(25, '2026_09_17_000007_create_activity_logs_table', 1),
(26, '2026_09_17_000008_create_departments_and_task_categories_tables', 1),
(27, '2026_09_17_221555_ensure_task_unique_per_delegation_and_staff', 2),
(28, '2026_09_19_000001_drop_booking_nomor_surat_requests', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system' COMMENT 'delegation | task | deadline | agenda | system',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 3, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Tindak lanjut undangan rapat koordinasi bidang', 'http://localhost/task/1', 1, '2026-09-17 14:20:12', '2026-09-17 09:51:57', '2026-09-17 14:20:12'),
(2, 4, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Tindak lanjut undangan rapat koordinasi bidang', 'http://localhost/task/2', 0, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(3, 4, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Penyusunan laporan kinerja triwulan', 'http://localhost/task/3', 0, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(4, 5, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Penyusunan laporan kinerja triwulan', 'http://localhost/task/4', 0, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(5, 3, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Verifikasi berkas permohonan bantuan', 'http://localhost/task/5', 1, '2026-09-17 14:20:12', '2026-09-17 09:51:57', '2026-09-17 14:20:12'),
(6, 4, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Verifikasi berkas permohonan bantuan', 'http://localhost/task/6', 0, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(7, 4, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Pengumpulan data potensi pertanian', 'http://localhost/task/7', 0, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(8, 5, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Pengumpulan data potensi pertanian', 'http://localhost/task/8', 0, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(9, 3, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Persiapan kunjungan kerja pimpinan', 'http://localhost/task/9', 1, '2026-09-17 14:20:12', '2026-09-17 09:51:57', '2026-09-17 14:20:12'),
(10, 4, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Persiapan kunjungan kerja pimpinan', 'http://localhost/task/10', 0, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(11, 4, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Tindak lanjut surat dari Kementerian', 'http://localhost/task/11', 0, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(12, 5, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Tindak lanjut surat dari Kementerian', 'http://localhost/task/12', 0, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(13, 3, 'task', 'Tugas Baru', 'Anda menerima tugas baru: Tindak Lanjut Rapat Lobusona', 'http://192.168.1.10:8000/task/15', 1, '2026-09-17 14:20:12', '2026-09-17 13:53:21', '2026-09-17 14:20:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint UNSIGNED NOT NULL,
  `delegation_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `staff_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT 'rendah | normal | tinggi | urgent',
  `task_category_id` bigint UNSIGNED DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `progress` smallint UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 - 100',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baru' COMMENT 'baru | diterima | dalam_pengerjaan | menunggu_review | selesai | ditolak | terlambat',
  `agenda_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Agenda terkait (histori)',
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tasks`
--

INSERT INTO `tasks` (`id`, `delegation_id`, `title`, `description`, `staff_id`, `created_by`, `priority`, `task_category_id`, `deadline`, `progress`, `status`, `agenda_id`, `attachment`, `note`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Tindak lanjut undangan rapat koordinasi bidang', 'Menghadiri dan menyusun laporan hasil rapat koordinasi bidang.', 3, 2, 'tinggi', NULL, '2026-09-20 16:51:56', 45, 'dalam_pengerjaan', NULL, NULL, NULL, NULL, '2026-09-17 09:51:56', '2026-09-17 09:51:56'),
(2, 1, 'Tindak lanjut undangan rapat koordinasi bidang', 'Menghadiri dan menyusun laporan hasil rapat koordinasi bidang.', 4, 2, 'tinggi', NULL, '2026-09-20 16:51:56', 45, 'dalam_pengerjaan', NULL, NULL, NULL, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(3, 2, 'Penyusunan laporan kinerja triwulan', 'Menyusun rekapitulasi laporan kinerja triwulan berjalan.', 4, 2, 'normal', NULL, '2026-09-24 16:51:56', 0, 'baru', NULL, NULL, NULL, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(4, 2, 'Penyusunan laporan kinerja triwulan', 'Menyusun rekapitulasi laporan kinerja triwulan berjalan.', 5, 2, 'normal', NULL, '2026-09-24 16:51:56', 0, 'baru', NULL, NULL, NULL, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(5, 3, 'Verifikasi berkas permohonan bantuan', 'Memeriksa kelengkapan berkas permohonan bantuan masyarakat.', 3, 2, 'urgent', NULL, '2026-09-16 16:51:56', 100, 'menunggu_review', NULL, NULL, NULL, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(6, 3, 'Verifikasi berkas permohonan bantuan', 'Memeriksa kelengkapan berkas permohonan bantuan masyarakat.', 4, 2, 'urgent', NULL, '2026-09-16 16:51:56', 100, 'menunggu_review', NULL, NULL, NULL, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(7, 4, 'Pengumpulan data potensi pertanian', 'Mengumpulkan data potensi pertanian untuk laporan tahunan.', 4, 2, 'normal', NULL, '2026-09-14 16:51:56', 100, 'selesai', NULL, NULL, NULL, '2026-09-15 09:51:57', '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(8, 4, 'Pengumpulan data potensi pertanian', 'Mengumpulkan data potensi pertanian untuk laporan tahunan.', 5, 2, 'normal', NULL, '2026-09-14 16:51:56', 100, 'selesai', NULL, NULL, NULL, '2026-09-15 09:51:57', '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(9, 5, 'Persiapan kunjungan kerja pimpinan', 'Menyiapkan seluruh kebutuhan kunjungan kerja pimpinan.', 3, 2, 'tinggi', NULL, '2026-09-27 16:51:56', 10, 'diterima', NULL, NULL, NULL, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(10, 5, 'Persiapan kunjungan kerja pimpinan', 'Menyiapkan seluruh kebutuhan kunjungan kerja pimpinan.', 4, 2, 'tinggi', NULL, '2026-09-27 16:51:56', 10, 'diterima', NULL, NULL, NULL, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(11, 6, 'Tindak lanjut surat dari Kementerian', 'Menyusun jawaban atas surat permintaan data dari Kementerian.', 4, 2, 'urgent', NULL, '2026-09-22 16:51:56', 0, 'ditolak', NULL, NULL, 'Tidak dapat dikerjakan karena benturan jadwal.', NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(12, 6, 'Tindak lanjut surat dari Kementerian', 'Menyusun jawaban atas surat permintaan data dari Kementerian.', 5, 2, 'urgent', NULL, '2026-09-22 16:51:56', 0, 'ditolak', NULL, NULL, 'Tidak dapat dikerjakan karena benturan jadwal.', NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(13, 7, 'Draf surat edaran monitoring', 'Menyusun draf surat edaran monitoring kegiatan lapangan.', 3, 2, 'rendah', NULL, '2026-10-01 16:51:56', 0, 'baru', NULL, NULL, NULL, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(14, 7, 'Draf surat edaran monitoring', 'Menyusun draf surat edaran monitoring kegiatan lapangan.', 4, 2, 'rendah', NULL, '2026-10-01 16:51:56', 0, 'baru', NULL, NULL, NULL, NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(15, 8, 'Tindak Lanjut Rapat Lobusona', 'MENUGASI DENGAN INI SAUDARA BAYU PRADIKA', 3, 1, 'rendah', 1, '2026-09-19 19:52:00', 10, 'dalam_pengerjaan', NULL, NULL, 'bisa mewing', NULL, '2026-09-17 13:53:20', '2026-09-17 14:02:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `task_categories`
--

CREATE TABLE `task_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#696cff',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `task_categories`
--

INSERT INTO `task_categories` (`id`, `name`, `color`, `description`, `created_at`, `updated_at`) VALUES
(1, 'administrasi', '#696cff', 'administrasi', '2026-09-17 13:52:00', '2026-09-17 13:52:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `task_updates`
--

CREATE TABLE `task_updates` (
  `id` bigint UNSIGNED NOT NULL,
  `task_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `progress` smallint UNSIGNED NOT NULL DEFAULT '0',
  `note` text COLLATE utf8mb4_unicode_ci,
  `document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dokumen hasil pekerjaan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `task_updates`
--

INSERT INTO `task_updates` (`id`, `task_id`, `user_id`, `progress`, `note`, `document`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 45, 'Progres pengerjaan diperbarui.', NULL, '2026-09-17 09:51:56', '2026-09-17 09:51:56'),
(2, 2, 4, 45, 'Progres pengerjaan diperbarui.', NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(3, 5, 3, 100, 'Progres pengerjaan diperbarui.', NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(4, 6, 4, 100, 'Progres pengerjaan diperbarui.', NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(5, 7, 4, 100, 'Progres pengerjaan diperbarui.', NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(6, 8, 5, 100, 'Progres pengerjaan diperbarui.', NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(7, 9, 3, 10, 'Progres pengerjaan diperbarui.', NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(8, 10, 4, 10, 'Progres pengerjaan diperbarui.', NULL, '2026-09-17 09:51:57', '2026-09-17 09:51:57'),
(9, 15, 1, 10, 'bisa mewing', 'storage/task-documents/1789650154-Screenshot-2026-09-08-224722.png', '2026-09-17 14:02:36', '2026-09-17 14:02:36'),
(10, 6, 1, 100, NULL, NULL, '2026-09-17 14:11:09', '2026-09-17 14:11:09');

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
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_version` bigint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `phone`, `role`, `department_id`, `is_active`, `profile_picture`, `remember_token`, `session_version`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@admin.com', NULL, '$2y$10$L5lkw9PtFOmHcyKZh5.YMex2sRVwoj4EDgGzp7MrPDHJHTkIOCut6', NULL, NULL, '082121212121', 'admin', NULL, 1, NULL, NULL, 0, '2026-09-17 09:51:52', '2026-09-17 09:51:52'),
(2, 'Sekretaris Dinas', 'sekretaris@admin.com', NULL, '$2y$10$d3JFph9ui.a/OmhTo0TyX.ldSCOjOV3eJeAJnwCXndvVlhiiq2vmW', NULL, NULL, '082121212122', 'sekretaris', NULL, 1, NULL, NULL, 0, '2026-09-17 09:51:52', '2026-09-17 09:51:52'),
(3, 'Budi Santoso', 'staff@admin.com', NULL, '$2y$10$cJzKjXG41SGZq3wLWV8umu34UuCTMMgcUNbDu.tZjVwgSvIB45Jn.', NULL, NULL, '082121212123', 'staff', NULL, 1, NULL, NULL, 0, '2026-09-17 09:51:53', '2026-09-17 09:51:53'),
(4, 'Siti Aminah', 'siti@admin.com', NULL, '$2y$10$NBvEx.6hlKmODnhI1od4I.kKnLOiSqWJzHCWz6BC3IYbBb0oc6hHe', NULL, NULL, '082121212124', 'staff', NULL, 1, NULL, NULL, 0, '2026-09-17 09:51:53', '2026-09-17 09:51:53'),
(5, 'Andi Pratama', 'andi@admin.com', NULL, '$2y$10$N/9yukw8BSDelbxMKvDA9euSp.m3q/ra/3Brm3ouBd3FL485PPZgK', NULL, NULL, '082121212125', 'staff', NULL, 1, NULL, NULL, 0, '2026-09-17 09:51:53', '2026-09-17 09:51:53'),
(6, 'Dewi Lestari', 'dewi@admin.com', NULL, '$2y$10$W8hQfI3YlRK.18rVqfvWcOPsiMfhT1/WusSreYtWU9y4CsSC2wkOa', NULL, NULL, '082121212126', 'staff', NULL, 1, NULL, NULL, 0, '2026-09-17 09:51:53', '2026-09-17 09:51:53');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `activity_logs_module_index` (`module`);

--
-- Indeks untuk tabel `agendas`
--
ALTER TABLE `agendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agendas_delegation_id_foreign` (`delegation_id`),
  ADD KEY `agendas_letter_id_foreign` (`letter_id`),
  ADD KEY `agendas_created_by_foreign` (`created_by`),
  ADD KEY `agendas_date_status_index` (`date`,`status`),
  ADD KEY `agendas_agenda_type_index` (`agenda_type`);

--
-- Indeks untuk tabel `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachments_letter_id_foreign` (`letter_id`),
  ADD KEY `attachments_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `classifications`
--
ALTER TABLE `classifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `classifications_code_unique` (`code`);

--
-- Indeks untuk tabel `configs`
--
ALTER TABLE `configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `configs_code_unique` (`code`);

--
-- Indeks untuk tabel `delegations`
--
ALTER TABLE `delegations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delegations_letter_id_foreign` (`letter_id`),
  ADD KEY `delegations_created_by_foreign` (`created_by`),
  ADD KEY `delegations_status_deadline_index` (`status`,`deadline`),
  ADD KEY `delegations_priority_index` (`priority`);

--
-- Indeks untuk tabel `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_code_unique` (`code`);

--
-- Indeks untuk tabel `dispositions`
--
ALTER TABLE `dispositions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dispositions_letter_status_foreign` (`letter_status`),
  ADD KEY `dispositions_letter_id_foreign` (`letter_id`),
  ADD KEY `dispositions_user_id_foreign` (`user_id`),
  ADD KEY `dispositions_verified_by_foreign` (`verified_by`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `letters`
--
ALTER TABLE `letters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `letters_reference_number_unique` (`reference_number`),
  ADD KEY `letters_classification_code_foreign` (`classification_code`),
  ADD KEY `letters_user_id_foreign` (`user_id`),
  ADD KEY `letters_verified_by_foreign` (`verified_by`);

--
-- Indeks untuk tabel `letter_statuses`
--
ALTER TABLE `letter_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `login_sessions`
--
ALTER TABLE `login_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_sessions_user_id_session_id_index` (`user_id`,`session_id`),
  ADD KEY `login_sessions_session_id_index` (`session_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tasks_delegation_id_staff_id_unique` (`delegation_id`,`staff_id`),
  ADD KEY `tasks_staff_id_foreign` (`staff_id`),
  ADD KEY `tasks_created_by_foreign` (`created_by`),
  ADD KEY `tasks_status_staff_id_index` (`status`,`staff_id`),
  ADD KEY `tasks_deadline_progress_index` (`deadline`,`progress`),
  ADD KEY `tasks_task_category_id_foreign` (`task_category_id`);

--
-- Indeks untuk tabel `task_categories`
--
ALTER TABLE `task_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `task_updates`
--
ALTER TABLE `task_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_updates_user_id_foreign` (`user_id`),
  ADD KEY `task_updates_task_id_created_at_index` (`task_id`,`created_at`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_department_id_foreign` (`department_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `agendas`
--
ALTER TABLE `agendas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `classifications`
--
ALTER TABLE `classifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `configs`
--
ALTER TABLE `configs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `delegations`
--
ALTER TABLE `delegations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dispositions`
--
ALTER TABLE `dispositions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `letters`
--
ALTER TABLE `letters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT untuk tabel `letter_statuses`
--
ALTER TABLE `letter_statuses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `login_sessions`
--
ALTER TABLE `login_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `task_categories`
--
ALTER TABLE `task_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `task_updates`
--
ALTER TABLE `task_updates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `agendas`
--
ALTER TABLE `agendas`
  ADD CONSTRAINT `agendas_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `agendas_delegation_id_foreign` FOREIGN KEY (`delegation_id`) REFERENCES `delegations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `agendas_letter_id_foreign` FOREIGN KEY (`letter_id`) REFERENCES `letters` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_letter_id_foreign` FOREIGN KEY (`letter_id`) REFERENCES `letters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `delegations`
--
ALTER TABLE `delegations`
  ADD CONSTRAINT `delegations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `delegations_letter_id_foreign` FOREIGN KEY (`letter_id`) REFERENCES `letters` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `dispositions`
--
ALTER TABLE `dispositions`
  ADD CONSTRAINT `dispositions_letter_id_foreign` FOREIGN KEY (`letter_id`) REFERENCES `letters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dispositions_letter_status_foreign` FOREIGN KEY (`letter_status`) REFERENCES `letter_statuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dispositions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `dispositions_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `letters`
--
ALTER TABLE `letters`
  ADD CONSTRAINT `letters_classification_code_foreign` FOREIGN KEY (`classification_code`) REFERENCES `classifications` (`code`),
  ADD CONSTRAINT `letters_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `letters_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `login_sessions`
--
ALTER TABLE `login_sessions`
  ADD CONSTRAINT `login_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_delegation_id_foreign` FOREIGN KEY (`delegation_id`) REFERENCES `delegations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_task_category_id_foreign` FOREIGN KEY (`task_category_id`) REFERENCES `task_categories` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `task_updates`
--
ALTER TABLE `task_updates`
  ADD CONSTRAINT `task_updates_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_updates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
