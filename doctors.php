<?php
session_start();
require_once 'config/database.php';
include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Tim Dokter Kami</h1>

    <!-- Filter Spesialisasi -->
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <form action="" method="GET" class="d-flex">
                <select name="specialization" class="form-select me-2">
                    <option value="">Semua Spesialisasi</option>
                    <?php
                    $stmt = $pdo->query("SELECT DISTINCT specialization FROM doctors ORDER BY specialization");
                    while($row = $stmt->fetch()) {
                        $selected = (isset($_GET['specialization']) && $_GET['specialization'] == $row['specialization']) ? 'selected' : '';
                        echo "<option value='".$row['specialization']."' ".$selected.">".$row['specialization']."</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <!-- Daftar Dokter -->
    <div class="row">
        <?php
        $query = "SELECT * FROM doctors WHERE status = 'active'";
        if(isset($_GET['specialization']) && !empty($_GET['specialization'])) {
            $spec = $_GET['specialization'];
            $query .= " AND specialization = :specialization";
        }
        $query .= " ORDER BY name";
        
        $stmt = $pdo->prepare($query);
        if(isset($spec)) {
            $stmt->bindParam(':specialization', $spec);
        }
        $stmt->execute();

        while($doctor = $stmt->fetch()) {
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <?php if($doctor['photo']): ?>
                        <img src="<?php echo $doctor['photo']; ?>" alt="<?php echo $doctor['name']; ?>" 
                             class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <img src="images/doctor-default.jpg" alt="Default Doctor Image" 
                             class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php endif; ?>
                    
                    <h5 class="card-title"><?php echo $doctor['name']; ?></h5>
                    <p class="card-text text-muted"><?php echo $doctor['specialization']; ?></p>
                    
                    <?php if($doctor['schedule']): ?>
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Jadwal Praktik:<br>
                                <?php echo nl2br($doctor['schedule']); ?>
                            </small>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="appointment.php?doctor_id=<?php echo $doctor['id']; ?>" 
                           class="btn btn-primary">Buat Janji Temu</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary">Login untuk Buat Janji</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <?php if($stmt->rowCount() == 0): ?>
        <div class="text-center my-5">
            <p class="lead">Tidak ada dokter yang ditemukan untuk spesialisasi ini.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Detail Dokter -->
<div class="modal fade" id="doctorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Dokter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .doctor-schedule {
        font-size: 0.9em;
        color: #666;
    }
</style>

<?php include 'includes/footer.php'; ?> 