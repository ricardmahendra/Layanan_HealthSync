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
        $doctor_id = filter_input(INPUT_POST, 'doctor_id', FILTER_SANITIZE_NUMBER_INT);
        $appointment_date = filter_input(INPUT_POST, 'appointment_date', FILTER_SANITIZE_STRING);
        $appointment_time = filter_input(INPUT_POST, 'appointment_time', FILTER_SANITIZE_STRING);
        $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
        $user_id = $_SESSION['user_id'];

        // Validasi input
        if (empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
            throw new Exception("Mohon lengkapi semua field yang diperlukan!");
        }

        // Validasi tanggal (tidak boleh di masa lalu)
        $selected_datetime = strtotime("$appointment_date $appointment_time");
        if ($selected_datetime < time()) {
            throw new Exception("Tidak dapat membuat janji temu untuk waktu yang sudah lewat!");
        }

        // Cek apakah sudah ada janji temu di waktu yang sama untuk dokter tersebut
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM appointments 
            WHERE doctor_id = ? 
            AND appointment_date = ? 
            AND appointment_time = ? 
            AND status != 'cancelled'
        ");
        $stmt->execute([$doctor_id, $appointment_date, $appointment_time]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Maaf, jadwal ini sudah terisi. Silakan pilih waktu lain.");
        }

        // Siapkan dan eksekusi query
        $sql = "INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time, notes, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$user_id, $doctor_id, $appointment_date, $appointment_time, $notes])) {
            $_SESSION['success_message'] = "Janji temu berhasil dibuat! Silakan tunggu konfirmasi dari dokter.";
            header("Location: my_appointments.php");
            exit();
        } else {
            throw new Exception("Gagal membuat janji temu. Silakan coba lagi!");
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: appointment.php" . ($doctor_id ? "?doctor_id=$doctor_id" : ""));
        exit();
    }
} else {
    header("Location: doctors.php");
    exit();
}
?> 