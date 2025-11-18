-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 18 Nov 2025 pada 02.57
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `upfm_new`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `artists`
--

CREATE TABLE `artists` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'default_artist.jpg',
  `description` text DEFAULT NULL,
  `is_headliner` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `artists`
--

INSERT INTO `artists` (`id`, `name`, `genre`, `image_url`, `description`, `is_headliner`) VALUES
(1, 'Christo', 'Dangdut', '1762261367_image1.jpeg', 'Asoy geboy goyangan yahut abang christo', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `category` varchar(100) DEFAULT 'General',
  `added_by_user_id` int(11) DEFAULT NULL COMMENT 'ID User (admin) yang menambahkan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `media_type` enum('photo','video','aftermovie') NOT NULL,
  `media_url` varchar(255) NOT NULL COMMENT 'Bisa URL file atau link YouTube',
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `year` year(4) NOT NULL,
  `uploaded_by_user_id` int(11) DEFAULT NULL COMMENT 'ID User (admin) yang mengunggah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `media_type`, `media_url`, `thumbnail_url`, `year`, `uploaded_by_user_id`) VALUES
(2, 'Gambar', 'photo', '1762877693_2792_image1.jpeg', '1762877693_2792_image1.jpeg', '2025', 6);

-- --------------------------------------------------------

--
-- Struktur dari tabel `journal_articles`
--

CREATE TABLE `journal_articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author` varchar(100) DEFAULT 'Tim UpFM',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID User (admin) yang mempublikasikan',
  `category` enum('Artist Highlights','Festival Tips','Behind the Scenes','Update') NOT NULL,
  `publish_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `journal_articles`
--

INSERT INTO `journal_articles` (`id`, `title`, `content`, `author`, `user_id`, `category`, `publish_date`, `image_url`) VALUES
(1, 'Christo Goyang Dumang HEBOH BANGET !!!!', 'KATANYA CHHRISTO ABIS GOYANG DUMANG, SIAPA YANG MAU LIAT? GAS YUKKK\r\n\r\n\\', 'Tim UpFM', 6, 'Artist Highlights', '2025-11-11 15:44:01', '1762875841_ets2_20251025_215044_00.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `merchandise`
--

CREATE TABLE `merchandise` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT 'default_merch.jpg',
  `description` text DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `merchandise`
--

INSERT INTO `merchandise` (`id`, `item_name`, `price`, `image_url`, `description`, `stock`) VALUES
(1, 'Topi', 80000.00, '1762266051', '0', 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `merch_purchases`
--

CREATE TABLE `merch_purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `merch_id` int(11) NOT NULL,
  `quantity` int(5) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `transaction_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `stage_id` int(11) NOT NULL,
  `event_day` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `schedules`
--

INSERT INTO `schedules` (`id`, `artist_id`, `stage_id`, `event_day`, `start_time`, `end_time`) VALUES
(1, 1, 1, '2025-11-13', '00:01:00', '06:01:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stages`
--

CREATE TABLE `stages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location_description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `stages`
--

INSERT INTO `stages` (`id`, `name`, `location_description`) VALUES
(1, 'Panggung Merah Putih', 'Panggung utama, di tengah lapangan'),
(2, 'Gema Stage', 'Panggung indoor, area B'),
(3, 'Akustik Lounge', 'Dekat area food court');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `booth_type` varchar(100) NOT NULL COMMENT 'Contoh: F&B, Merchandise, Community',
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL COMMENT 'ID User jika pendaftar punya akun',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tenants`
--

INSERT INTO `tenants` (`id`, `brand_name`, `contact_person`, `email`, `phone_number`, `booth_type`, `submission_date`, `user_id`, `status`) VALUES
(1, 'estehmanis dong', 'masdut', 'duta@gmail.com', '01234567463524312', 'Makanan', '2025-11-11 17:11:52', 6, 'pending'),
(2, 'estehmanis dong', 'masdut', 'duta@gmail.com', '01234567463524312', 'Makanan', '2025-11-11 17:12:09', 6, 'pending'),
(3, 'estehmanis dong', 'masdut', 'duta@gmail.com', '01234567463524312', 'Makanan', '2025-11-11 17:12:12', 6, 'pending');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL COMMENT 'Contoh: Presale 1, VIP, Daily Pass',
  `filter_tag` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity_available` int(11) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tickets`
--

INSERT INTO `tickets` (`id`, `category_name`, `filter_tag`, `price`, `quantity_available`, `description`) VALUES
(1, 'Presale 1', 'presale', 125000.00, 100, 'Tiket presale tahap pertama dengan harga spesial. Amankan tempatmu lebih awal!'),
(2, 'Early Bird Access', 'presale', 95000.00, 50, 'Tiket harga paling murah untuk kamu yang paling gerak cepat. Stok sangat terbatas!'),
(3, 'Daily Pass - Day 1', 'day1', 180000.00, 200, 'Akses penuh untuk semua panggung dan pertunjukan di Hari Pertama festival.'),
(4, 'Daily Pass - Day 2', 'day2', 180000.00, 200, 'Akses penuh untuk semua panggung dan pertunjukan di Hari Kedua festival.'),
(5, '2-Day Pass (Reguler)', 'all-access', 320000.00, 150, 'Akses lengkap untuk dua hari festival. Pengalaman maksimal dengan harga terbaik.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ticket_images`
--

CREATE TABLE `ticket_images` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ticket_images`
--

INSERT INTO `ticket_images` (`id`, `ticket_id`, `image_url`) VALUES
(1, 1, 'ticket_presale_a.jpg'),
(2, 1, 'ticket_presale_b.jpg'),
(3, 2, 'ticket_earlybird_a.jpg'),
(4, 2, 'ticket_earlybird_b.jpg'),
(5, 3, 'ticket_day1_a.jpg'),
(6, 3, 'ticket_day1_b.jpg'),
(7, 4, 'ticket_day2_a.jpg'),
(8, 4, 'ticket_day2_b.jpg'),
(9, 5, 'ticket_2day_a.jpg'),
(10, 5, 'ticket_2day_b.jpg'),
(11, 5, '1762265655_image1.jpeg'),
(12, 1, '1762265695_image1.jpeg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ticket_purchases`
--

CREATE TABLE `ticket_purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `quantity` int(5) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `transaction_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ticket_purchases`
--

INSERT INTO `ticket_purchases` (`id`, `user_id`, `ticket_id`, `quantity`, `total_price`, `purchase_date`, `payment_status`, `transaction_code`) VALUES
(1, 1, 1, 1, 125000.00, '2025-10-14 16:02:20', 'success', 'UPFM-1-1760457740-1'),
(2, 1, 2, 1, 95000.00, '2025-10-14 16:06:56', 'success', 'UPFM-1-1760458016-2'),
(3, 1, 5, 1, 320000.00, '2025-10-14 16:08:29', 'success', 'UPFM-1-1760458109-5'),
(4, 2, 5, 1, 320000.00, '2025-10-14 16:16:23', 'success', 'UPFM-2-1760458583-5'),
(5, 3, 2, 1, 95000.00, '2025-10-24 10:17:38', 'success', 'UPFM-3-1761301058-2'),
(6, 3, 1, 1, 125000.00, '2025-10-24 10:17:38', 'success', 'UPFM-3-1761301058-1'),
(7, 3, 1, 1, 125000.00, '2025-10-24 10:18:15', 'success', 'UPFM-3-1761301095-1'),
(8, 3, 2, 1, 95000.00, '2025-10-24 10:19:07', 'success', 'UPFM-3-1761301147-2'),
(9, 3, 2, 1, 95000.00, '2025-10-24 10:20:12', 'success', 'UPFM-3-1761301212-2'),
(10, 3, 3, 1, 180000.00, '2025-10-24 10:20:53', 'success', 'UPFM-3-1761301253-3'),
(11, 3, 4, 1, 180000.00, '2025-10-24 10:20:53', 'success', 'UPFM-3-1761301253-4'),
(12, 3, 1, 1, 125000.00, '2025-10-24 10:20:53', 'success', 'UPFM-3-1761301253-1'),
(13, 8, 2, 6, 570000.00, '2025-11-04 15:02:45', 'success', 'UPFM-8-1762268565-2'),
(14, 1, 4, 2, 360000.00, '2025-11-11 15:02:23', 'success', 'UPFM-T-1-1762873343-4'),
(15, 1, 3, 5, 900000.00, '2025-11-11 15:02:23', 'success', 'UPFM-T-1-1762873343-3'),
(16, 1, 2, 3, 285000.00, '2025-11-11 15:02:23', 'success', 'UPFM-T-1-1762873343-2'),
(17, 1, 3, 1, 180000.00, '2025-11-11 15:12:44', 'success', 'UPFM-T-1-1762873964-3'),
(18, 1, 2, 5, 475000.00, '2025-11-11 15:23:33', 'success', 'UPFM-T-1-1762874613-2'),
(19, 6, 4, 10, 1800000.00, '2025-11-11 17:37:36', 'success', 'UPFM-T-6-1762882656-4');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ticket_types`
--

CREATE TABLE `ticket_types` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'HARUS disimpan dalam bentuk HASH',
  `role` enum('admin','petugas','pengunjung') NOT NULL DEFAULT 'pengunjung',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Masdut', 'duta@gmail.com', '$2y$10$EcuX7x4rr1T1x10SdlFPGeOOWXpLMkcdrPdRb18I7NGSFyhib5tum', 'pengunjung', '2025-10-14 15:43:31'),
(2, 'Christo Gadjaaaaa', 'gadja@gmail.com', '$2y$10$P4b4gNS21T12or0RNTkUse2G02shYAAOmcEY6jSt6o9prSPa/vyyC', 'pengunjung', '2025-10-14 16:15:52'),
(3, 'Meyta', 'meyta@gmail.com', '$2y$10$MQA/hu.UaQbbh0oJDFpvnemMgXyapjPmze.MfLnkIz42jnpUoTFJG', 'pengunjung', '2025-10-21 10:01:59'),
(5, 'admin', 'admin@gmail.com', '0192023a7bbd73250516f069df18b500', 'admin', '2025-11-04 12:49:00'),
(6, 'adminduta', 'adminduta@gmail.com', '$2y$10$EFV/7jsRMyc/FLW54odaa.nTO/5nGStWmfNCc2kbCed0oiT0Ahqyq', 'admin', '2025-11-04 12:56:35'),
(7, 'joko', 'joko@gmail.com', '$2y$10$HlQ6TNpBYIezxKvHpGZ6yeEdmrT4.hfrnoXDaIMkA7K/JTFb3Ktd6', 'pengunjung', '2025-11-04 14:42:55'),
(8, 'Christo', 'christogadja9@gmail.com', '$2y$10$Or1hfwyc7grmpL1J6QXS/Owx5OGsYGAlLcKGFqf9XzniBpc0TEjM2', 'pengunjung', '2025-11-04 15:01:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `volunteers`
--

CREATE TABLE `volunteers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `reason_to_join` text NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL COMMENT 'ID User jika pendaftar punya akun',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `volunteers`
--

INSERT INTO `volunteers` (`id`, `full_name`, `email`, `phone_number`, `reason_to_join`, `submission_date`, `user_id`, `status`) VALUES
(1, 'Masdut', 'duta@gmail.com', '01234567463524312', 'Pengen aja', '2025-11-11 17:11:10', 6, 'pending');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_faq_user` (`added_by_user_id`);

--
-- Indeks untuk tabel `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gallery_user` (`uploaded_by_user_id`);

--
-- Indeks untuk tabel `journal_articles`
--
ALTER TABLE `journal_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_journal_user` (`user_id`);

--
-- Indeks untuk tabel `merchandise`
--
ALTER TABLE `merchandise`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `merch_purchases`
--
ALTER TABLE `merch_purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `merch_id` (`merch_id`);

--
-- Indeks untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `stage_id` (`stage_id`);

--
-- Indeks untuk tabel `stages`
--
ALTER TABLE `stages`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tenant_user` (`user_id`);

--
-- Indeks untuk tabel `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `ticket_images`
--
ALTER TABLE `ticket_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indeks untuk tabel `ticket_purchases`
--
ALTER TABLE `ticket_purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indeks untuk tabel `ticket_types`
--
ALTER TABLE `ticket_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `volunteers`
--
ALTER TABLE `volunteers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_volunteer_user` (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `journal_articles`
--
ALTER TABLE `journal_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `merchandise`
--
ALTER TABLE `merchandise`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `merch_purchases`
--
ALTER TABLE `merch_purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `stages`
--
ALTER TABLE `stages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `ticket_images`
--
ALTER TABLE `ticket_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `ticket_purchases`
--
ALTER TABLE `ticket_purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `ticket_types`
--
ALTER TABLE `ticket_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `volunteers`
--
ALTER TABLE `volunteers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `faq`
--
ALTER TABLE `faq`
  ADD CONSTRAINT `fk_faq_user` FOREIGN KEY (`added_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `fk_gallery_user` FOREIGN KEY (`uploaded_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `journal_articles`
--
ALTER TABLE `journal_articles`
  ADD CONSTRAINT `fk_journal_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `merch_purchases`
--
ALTER TABLE `merch_purchases`
  ADD CONSTRAINT `fk_merch_purchase_merch` FOREIGN KEY (`merch_id`) REFERENCES `merchandise` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_merch_purchase_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`stage_id`) REFERENCES `stages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `fk_tenant_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ticket_images`
--
ALTER TABLE `ticket_images`
  ADD CONSTRAINT `fk_ticket_images_ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ticket_purchases`
--
ALTER TABLE `ticket_purchases`
  ADD CONSTRAINT `tp_fk_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tp_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ticket_types`
--
ALTER TABLE `ticket_types`
  ADD CONSTRAINT `fk_ticket_types_ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `volunteers`
--
ALTER TABLE `volunteers`
  ADD CONSTRAINT `fk_volunteer_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
