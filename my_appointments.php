<?php
session_start();
require_once 'config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Proses pembatalan janji temu
if (isset($_POST['cancel_appointment']) && isset($_POST['appointment_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
        $stmt->execute([$_POST['appointment_id'], $_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Janji temu berhasil dibatalkan.";
        }
        header("Location: my_appointments.php");
        exit();
    } catch(PDOException $e) {
        $error = "Terjadi kesalahan saat membatalkan janji temu.";
    }
}

// Ambil semua janji temu user
try {
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            d.name as doctor_name,
            d.specialization
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.user_id = ?
        ORDER BY 
            CASE 
                WHEN a.status = 'pending' THEN 1
                WHEN a.status = 'confirmed' THEN 2
                ELSE 3
            END,
            a.appointment_date DESC,
            a.appointment_time DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $appointments = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = "Terjadi kesalahan saat mengambil data janji temu.";
}

include 'includes/header.php';
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Riwayat Janji Temu Saya</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($appointments)): ?>
                <div class="text-center py-4">
                    <p class="mb-0">Anda belum memiliki janji temu.</p>
                    <a href="appointment.php" class="btn btn-primary mt-3">Buat Janji Temu</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Dokter</th>
                                <th>Spesialisasi</th>
                                <th>Catatan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach($appointments as $appointment): 
                                $status_class = [
                                    'pending' => 'text-warning',
                                    'confirmed' => 'text-success',
                                    'cancelled' => 'text-danger'
                                ][$appointment['status']] ?? '';

                                $can_cancel = $appointment['status'] === 'pending' && 
                                            strtotime($appointment['appointment_date']) > strtotime('today');
                            ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></td>
                                    <td>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['specialization']); ?></td>
                                    <td>
                                        <?php 
                                        if (!empty($appointment['notes'])) {
                                            echo nl2br(htmlspecialchars($appointment['notes']));
                                        } else {
                                            echo '<em class="text-muted">Tidak ada catatan</em>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="<?php echo $status_class; ?>">
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($can_cancel): ?>
                                            <form method="POST" action="" class="d-inline" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan janji temu ini?');">
                                                <input type="hidden" name="appointment_id" 
                                                       value="<?php echo $appointment['id']; ?>">
                                                <button type="submit" name="cancel_appointment" 
                                                        class="btn btn-danger btn-sm">
                                                    Batalkan
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-4">
                    <a href="appointment.php" class="btn btn-primary">Buat Janji Temu Baru</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 