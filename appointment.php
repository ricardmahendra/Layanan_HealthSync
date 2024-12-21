<?php
session_start();
require_once 'config/database.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil doctor_id dari URL jika ada
$selected_doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : '';

// Ambil data dokter yang dipilih jika ada
$selected_doctor = null;
if ($selected_doctor_id) {
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->execute([$selected_doctor_id]);
    $selected_doctor = $stmt->fetch();
}

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Buat Janji Temu</h2>
                    
                    <form action="process_appointment.php" method="POST">
                        <div class="mb-3">
                            <label for="doctor_id" class="form-label">Pilih Dokter</label>
                            <select class="form-select" id="doctor_id" name="doctor_id" required>
                                <option value="">Pilih Dokter...</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM doctors WHERE status = 'active' ORDER BY name");
                                while($doctor = $stmt->fetch()) {
                                    $selected = ($doctor['id'] == $selected_doctor_id) ? 'selected' : '';
                                    echo "<option value='".$doctor['id']."' ".$selected.">";
                                    echo "Dr. ".$doctor['name']." - ".$doctor['specialization'];
                                    echo "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <?php if ($selected_doctor): ?>
                        <div class="mb-3" id="doctorSchedule">
                            <label class="form-label">Jadwal Praktik Dokter:</label>
                            <p class="text-muted">
                                <?php echo nl2br($selected_doctor['schedule']); ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="appointment_date" class="form-label">Tanggal Janji Temu</label>
                            <input type="date" class="form-control" id="appointment_date" 
                                   name="appointment_date" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="appointment_time" class="form-label">Waktu Janji Temu</label>
                            <input type="time" class="form-control" id="appointment_time" 
                                   name="appointment_time" required>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Buat Janji Temu</button>
                            <a href="doctors.php" class="btn btn-outline-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script untuk memuat jadwal dokter secara dinamis saat dokter dipilih
document.getElementById('doctor_id').addEventListener('change', function() {
    const doctorId = this.value;
    if (doctorId) {
        fetch('get_doctor_schedule.php?doctor_id=' + doctorId)
            .then(response => response.json())
            .then(data => {
                const scheduleDiv = document.getElementById('doctorSchedule');
                if (data.schedule) {
                    scheduleDiv.innerHTML = `
                        <label class="form-label">Jadwal Praktik Dokter:</label>
                        <p class="text-muted">${data.schedule.replace(/\n/g, '<br>')}</p>
                    `;
                } else {
                    scheduleDiv.innerHTML = '';
                }
            });
    }
});
</script>

<?php include 'includes/footer.php'; ?> 