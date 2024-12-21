-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Des 2024 pada 02.38
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
-- Database: `hospital_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `doctor_id`, `appointment_date`, `appointment_time`, `notes`, `status`, `created_at`) VALUES
(1, 6, 7, '2024-12-17', '11:00:00', 'KELUHAN AMAN', 'pending', '2024-12-17 14:44:46'),
(2, 7, 7, '2024-12-18', '09:00:00', 'KELUHAN', 'pending', '2024-12-17 14:50:10'),
(3, 7, 7, '2024-12-18', '09:00:00', 'cek', 'pending', '2024-12-18 00:59:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `schedule` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `specialization`, `photo`, `schedule`, `status`, `is_active`) VALUES
(7, 'Dr. Andi Wijaya', 'Dokter Umum', 'images/doctors/doctor-9.jpg', 'Senin - Jumat: 08:00 - 16:00\nSabtu: 08:00 - 12:00', 'active', 1),
(13, 'Dr. Budi Santoso, Sp.PD', 'Spesialis Penyakit Dalam', 'images/doctors/doctor-1.jpg', 'Senin: 08:00 - 14:00\nSelasa: 08:00 - 14:00\nKamis: 13:00 - 20:00', 'active', 1),
(14, 'Dr. Siti Rahayu, Sp.A', 'Spesialis Anak', 'images/doctors/doctor-2.jpg', 'Senin: 13:00 - 20:00\nRabu: 08:00 - 14:00\nJumat: 08:00 - 14:00', 'active', 1),
(15, 'Dr. Ahmad Wijaya, Sp.OG', 'Spesialis Kandungan', 'images/doctors/doctor-3.jpg', 'Selasa: 13:00 - 20:00\nKamis: 08:00 - 14:00\nSabtu: 08:00 - 13:00', 'active', 1),
(16, 'Dr. Maya Putri, Sp.JP', 'Spesialis Jantung', 'images/doctors/doctor-4.jpg', 'Rabu: 13:00 - 20:00\nJumat: 13:00 - 20:00\nSabtu: 08:00 - 13:00', 'active', 1),
(17, 'Dr. Rudi Hartono, Sp.THT', 'Spesialis THT', 'images/doctors/doctor-5.jpg', 'Senin: 08:00 - 14:00\nRabu: 13:00 - 20:00\nJumat: 08:00 - 14:00', 'active', 1),
(18, 'Dr. Diana Putri, Sp.M', 'Spesialis Mata', 'images/doctors/doctor-6.jpg', 'Selasa: 08:00 - 14:00\nKamis: 13:00 - 20:00\nSabtu: 08:00 - 13:00', 'active', 1),
(19, 'Dr. Hendra Wijaya, Sp.KJ', 'Spesialis Kejiwaan', 'images/doctors/doctor-7.jpg', 'Senin: 13:00 - 20:00\nRabu: 08:00 - 14:00\nJumat: 13:00 - 20:00', 'active', 1),
(20, 'Dr. Lisa Kusuma, Sp.KG', 'Spesialis Gigi', 'images/doctors/doctor-8.jpg', 'Selasa: 13:00 - 20:00\nKamis: 08:00 - 14:00\nSabtu: 08:00 - 13:00', 'active', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `health_articles`
--

CREATE TABLE `health_articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `published_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `health_articles`
--

INSERT INTO `health_articles` (`id`, `title`, `content`, `image`, `category`, `author_id`, `source`, `published_at`, `created_at`, `updated_at`) VALUES
(6, 'Pentingnya Vaksinasi COVID-19', '\'Vaksinasi COVID-19 merupakan langkah penting dalam mengendalikan pandemi. Berikut manfaat vaksinasi:\\n\\n\r\n    1. Membentuk antibodi spesifik terhadap virus\\n\r\n    2. Mengurangi risiko gejala berat\\n\r\n    3. Membantu menciptakan kekebalan kelompok\\n\r\n    4. Melindungi orang-orang rentan di sekitar kita\\n\\n\r\n    Jangan ragu untuk mendapatkan vaksinasi COVID-19. Konsultasikan dengan dokter jika Anda memiliki kondisi kesehatan tertentu.\',', 'images/articles/covid-vaccine.jpg', 'Kesehatan Umum', NULL, 'Kementerian Kesehatan RI', '2024-12-18 01:15:07', '2024-12-18 01:15:07', '2024-12-18 01:15:07'),
(7, 'Manfaat Olahraga Rutin bagi Kesehatan Mental', 'Olahraga tidak hanya bermanfaat bagi kesehatan fisik, tetapi juga mental. Penelitian menunjukkan bahwa olahraga rutin dapat:\r\n- Mengurangi stres dan kecemasan\r\n- Meningkatkan kualitas tidur\r\n- Meningkatkan mood dan energi\r\n- Membantu mencegah depresi\r\n- Meningkatkan konsentrasi\r\nMulailah dengan olahraga ringan 30 menit sehari, 3-4 kali seminggu untuk mendapatkan manfaat optimal bagi kesehatan mental Anda.', 'images/articles/exercise.jpg', 'Gaya Hidup Sehat', NULL, 'World Health Organization', '2024-12-18 02:50:04', '2024-12-18 02:50:04', '2024-12-18 02:50:04'),
(8, 'Pentingnya Imunisasi Dasar Lengkap untuk Anak', 'Imunisasi merupakan cara efektif untuk melindungi anak dari berbagai penyakit berbahaya. Program imunisasi dasar yang wajib diberikan meliputi:\r\n    1. BCG: untuk mencegah tuberkulosis\r\n    2. Hepatitis B: mencegah infeksi hati\r\n    3. DPT: mencegah difteri, pertusis, dan tetanus\r\n    4. Polio: mencegah kelumpuhan\r\n    5. Campak: mencegah penyakit campak\r\n    Pastikan anak Anda mendapatkan imunisasi sesuai jadwal yang direkomendasikan oleh dokter.', 'images/articles/vaccination.jpg', 'Kesehatan Anak', NULL, 'IDAI (Ikatan Dokter Anak Indonesia)', '2024-12-18 02:50:04', '2024-12-18 02:50:04', '2024-12-18 02:50:04'),
(9, 'Pentingnya Pemeriksaan Kesehatan Rutin', 'Pemeriksaan kesehatan rutin sangat penting untuk deteksi dini penyakit:\r\n    1. Cek tekanan darah\r\n    2. Pemeriksaan gula darah\r\n    3. Tes kolesterol\r\n    4. Pemeriksaan fungsi jantung\r\n    5. Skrining kanker\r\n    Lakukan medical check-up minimal setahun sekali.', 'images/articles/medical-checkup.jpg', 'Kesehatan Umum', NULL, 'Kementerian Kesehatan RI', '2024-12-18 07:13:38', '2024-12-18 07:13:38', '2024-12-18 07:13:38'),
(10, 'Manfaat Meditasi untuk Kesehatan', 'Meditasi memberikan berbagai manfaat kesehatan:\r\n    1. Mengurangi stres\r\n    2. Meningkatkan fokus\r\n    3. Memperbaiki kualitas tidur\r\n    4. Menurunkan tekanan darah\r\n    5. Meningkatkan kesejahteraan mental\r\n    Mulailah dengan 5-10 menit meditasi setiap hari.\'', 'images/articles/meditation.jpg', 'Gaya Hidup Sehat', NULL, 'Mindfulness Institute', '2024-12-18 07:13:38', '2024-12-18 07:13:38', '2024-12-18 07:13:38'),
(11, 'Panduan Nutrisi Seimbang untuk Anak', 'Nutrisi seimbang penting untuk tumbuh kembang anak:\r\n    1. Protein untuk pertumbuhan\r\n    2. Karbohidrat untuk energi\r\n    3. Vitamin dan mineral\r\n    4. Lemak sehat\r\n    5. Porsi makan yang tepat\r\n    Biasakan pola makan sehat sejak dini.', 'images/articles/child-nutrition.jpg', 'Kesehatan Anak', NULL, 'IDAI', '2024-12-18 07:15:04', '2024-12-18 07:15:04', '2024-12-18 07:15:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `profile_photo`, `password`, `phone`, `created_at`) VALUES
(6, 'Puji Astuti', 'pujiastuti@gmail.com', NULL, '$2y$10$d9Fp6IRDyclFB6W4sM2wXeonrBjDOvInKAf1bN334LnKj7J4wdyW2', '', '2024-12-17 14:34:04'),
(7, 'Ricard mahendra', 'ricardmahendra@gmail.com', 'images/profiles/67621ed649732.jpeg', '$2y$10$ppaW2EVN.Tyhr.6wKAYKduNSJwifvaSBfYNmTK6vvlNKGDnrdWKs6', '', '2024-12-17 14:45:59');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indeks untuk tabel `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `health_articles`
--
ALTER TABLE `health_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `health_articles`
--
ALTER TABLE `health_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`);

--
-- Ketidakleluasaan untuk tabel `health_articles`
--
ALTER TABLE `health_articles`
  ADD CONSTRAINT `health_articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
