<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Sanitasi input
        $blood_type = filter_input(INPUT_POST, 'blood_type', FILTER_SANITIZE_STRING);
        $donation_date = filter_input(INPUT_POST, 'donation_date', FILTER_SANITIZE_STRING);
        $donation_time = filter_input(INPUT_POST, 'donation_time', FILTER_SANITIZE_STRING);
        $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
        $user_id = $_SESSION['user_id'];

        // Validasi input
        if (empty($blood_type) || empty($donation_date) || empty($donation_time)) {
            throw new Exception("Mohon lengkapi semua field yang diperlukan!");
        }

        // Validasi tanggal (tidak boleh di masa lalu)
        $selected_datetime = strtotime("$donation_date $donation_time");
        if ($selected_datetime < time()) {
            throw new Exception("Tidak dapat mendaftar donor untuk waktu yang sudah lewat!");
        }

        // Validasi waktu donor (sesuai jadwal)
        $day_of_week = date('N', strtotime($donation_date)); // 1 (Senin) sampai 7 (Minggu)
        $time_hour = (int)date('H', strtotime($donation_time));
        
        // Cek hari Minggu
        if ($day_of_week == 7) {
            throw new Exception("Maaf, donor darah tidak tersedia di hari Minggu.");
        }
        
        // Cek jadwal
        if ($day_of_week == 6) { // Sabtu
            if ($time_hour < 8 || $time_hour >= 12) {
                throw new Exception("Jadwal donor hari Sabtu hanya tersedia pukul 08:00 - 12:00.");
            }
        } else { // Senin-Jumat
            if ($time_hour < 8 || $time_hour >= 15) {
                throw new Exception("Jadwal donor hari Senin-Jumat tersedia pukul 08:00 - 15:00.");
            }
        }

        // Cek apakah sudah ada jadwal di waktu yang sama
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM blood_donations 
            WHERE donation_date = ? 
            AND donation_time = ? 
            AND status != 'cancelled'
        ");
        $stmt->execute([$donation_date, $donation_time]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Maaf, jadwal ini sudah terisi. Silakan pilih waktu lain.");
        }

        // Siapkan dan eksekusi query
        $sql = "INSERT INTO blood_donations (user_id, blood_type, donation_date, donation_time, notes, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$user_id, $blood_type, $donation_date, $donation_time, $notes])) {
            $_SESSION['success_message'] = "Pendaftaran donor darah berhasil! Kami akan menghubungi Anda untuk konfirmasi jadwal.";
        } else {
            throw new Exception("Terjadi kesalahan saat mendaftar donor darah. Silakan coba lagi!");
        }
        
        header("Location: blood_donation.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: blood_donation.php");
        exit();
    }
} else {
    header("Location: blood_donation.php");
    exit();
}
?> 