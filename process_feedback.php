<?php
session_start();
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Sanitasi input
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
        
        // Validasi input
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            throw new Exception("Mohon lengkapi semua field yang diperlukan!");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format email yang Anda masukkan tidak valid!");
        }
        
        // Siapkan query
        $sql = "INSERT INTO feedback (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        
        // Eksekusi query
        if ($stmt->execute([$name, $email, $subject, $message])) {
            $_SESSION['success_message'] = "Pesan Anda telah berhasil terkirim. Terima kasih atas masukan Anda!";
        } else {
            throw new Exception("Terjadi kesalahan saat mengirim pesan. Silakan coba lagi!");
        }
        
        // Redirect kembali ke halaman utama
        header("Location: index.php#kontak");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: index.php#kontak");
        exit();
    }
} else {
    // Jika bukan method POST, redirect ke halaman utama
    header("Location: index.php");
    exit();
}
?> 