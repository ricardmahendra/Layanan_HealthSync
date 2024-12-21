<?php
require_once 'config/database.php';

if (isset($_GET['doctor_id'])) {
    $stmt = $pdo->prepare("SELECT schedule FROM doctors WHERE id = ?");
    $stmt->execute([$_GET['doctor_id']]);
    $doctor = $stmt->fetch();
    
    header('Content-Type: application/json');
    echo json_encode(['schedule' => $doctor['schedule'] ?? null]);
}
?> 