<?php
session_start();
require_once 'config/database.php';
include 'includes/header.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!-- Tambahkan ini setelah header -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container my-5">
    <div class="row">
        <!-- Informasi Donor Darah -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-tint text-danger me-2"></i>
                        Informasi Donor Darah
                    </h5>
                    <div class="mb-4">
                        <h6 class="fw-bold">Syarat Donor Darah:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Usia 17-60 tahun</li>
                            <li><i class="fas fa-check text-success me-2"></i>Berat badan minimal 45 kg</li>
                            <li><i class="fas fa-check text-success me-2"></i>Tekanan darah normal</li>
                            <li><i class="fas fa-check text-success me-2"></i>Kadar hemoglobin normal</li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h6 class="fw-bold">Jadwal Donor:</h6>
                        <p class="mb-2">Senin - Jumat: 08:00 - 15:00</p>
                        <p class="mb-2">Sabtu: 08:00 - 12:00</p>
                        <p class="mb-0">Minggu: Tutup</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Pendaftaran Donor -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Pendaftaran Donor Darah</h5>
                    
                    <script>
                    <?php if(isset($_SESSION['success_message'])): ?>
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: '<?php echo $_SESSION['success_message']; ?>',
                            timer: 3000,
                            showConfirmButton: false
                        });
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>

                    <?php if(isset($_SESSION['error_message'])): ?>
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: '<?php echo $_SESSION['error_message']; ?>'
                        });
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>
                    </script>

                    <form action="process_blood_donation.php" method="POST">
                        <div class="mb-3">
                            <label for="blood_type" class="form-label">Golongan Darah</label>
                            <select class="form-select" id="blood_type" name="blood_type" required>
                                <option value="">Pilih Golongan Darah</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="donation_date" class="form-label">Tanggal Donor</label>
                            <input type="date" class="form-control" id="donation_date" 
                                   name="donation_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="donation_time" class="form-label">Waktu Donor</label>
                            <input type="time" class="form-control" id="donation_time" 
                                   name="donation_time" required>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan Tambahan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Contoh: Riwayat penyakit, obat-obatan yang dikonsumsi, dll."></textarea>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya menyatakan bahwa informasi yang saya berikan adalah benar dan 
                                saya memenuhi semua persyaratan donor darah.
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Daftar Donor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Donor -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Riwayat Donor Darah</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Golongan Darah</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT * FROM blood_donations 
                                    WHERE user_id = ? 
                                    ORDER BY donation_date DESC, donation_time DESC
                                ");
                                $stmt->execute([$_SESSION['user_id']]);
                                while ($donation = $stmt->fetch()) {
                                    $status_class = [
                                        'pending' => 'text-warning',
                                        'approved' => 'text-primary',
                                        'completed' => 'text-success',
                                        'cancelled' => 'text-danger'
                                    ][$donation['status']] ?? '';
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($donation['donation_date'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($donation['donation_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($donation['blood_type']); ?></td>
                                    <td><span class="<?php echo $status_class; ?>">
                                        <?php echo ucfirst($donation['status']); ?>
                                    </span></td>
                                    <td><?php echo htmlspecialchars($donation['notes']); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Tambahkan script ini sebelum closing tag </body> -->
<script>
document.getElementById('donation_time').addEventListener('change', function() {
    const donationDate = document.getElementById('donation_date').value;
    const donationTime = this.value;
    
    if (donationDate && donationTime) {
        const date = new Date(donationDate);
        const time = donationTime.split(':');
        const hour = parseInt(time[0]);
        const dayOfWeek = date.getDay(); // 0 (Minggu) sampai 6 (Sabtu)
        
        let errorMessage = '';
        
        // Cek hari Minggu
        if (dayOfWeek === 0) {
            errorMessage = 'Maaf, donor darah tidak tersedia di hari Minggu.';
        }
        // Cek jadwal Sabtu
        else if (dayOfWeek === 6 && (hour < 8 || hour >= 12)) {
            errorMessage = 'Jadwal donor hari Sabtu hanya tersedia pukul 08:00 - 12:00.';
        }
        // Cek jadwal Senin-Jumat
        else if (dayOfWeek >= 1 && dayOfWeek <= 5 && (hour < 8 || hour >= 15)) {
            errorMessage = 'Jadwal donor hari Senin-Jumat tersedia pukul 08:00 - 15:00.';
        }
        
        if (errorMessage) {
            alert(errorMessage);
            this.value = '';
        }
    }
});

document.getElementById('donation_date').addEventListener('change', function() {
    document.getElementById('donation_time').value = ''; // Reset waktu saat tanggal berubah
});
</script> 