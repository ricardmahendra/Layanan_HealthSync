<?php
session_start();
require_once 'config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch(PDOException $e) {
    $error = "Terjadi kesalahan saat mengambil data.";
}

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];

    // Handle Photo Upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_photo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (!in_array(strtolower($filetype), $allowed)) {
            $errors[] = "Format file harus JPG, JPEG, PNG, atau GIF";
        } else {
            $maxsize = 2 * 1024 * 1024; // 2MB
            if ($_FILES['profile_photo']['size'] > $maxsize) {
                $errors[] = "Ukuran file maksimal 2MB";
            }
        }
    }

    // Validasi nama
    if (strlen($name) < 3) {
        $errors[] = "Nama harus minimal 3 karakter";
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }

    // Cek email duplikat
    if ($email !== $user['email']) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Email sudah digunakan";
        }
    }

    // Validasi password
    if (!empty($new_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "Password saat ini tidak sesuai";
        }
        if (strlen($new_password) < 6) {
            $errors[] = "Password baru harus minimal 6 karakter";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "Konfirmasi password baru tidak cocok";
        }
    }

    if (empty($errors)) {
        try {
            // Upload foto jika ada
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
                $upload_dir = 'images/profiles/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $new_filename = uniqid() . '.' . $filetype;
                $destination = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $destination)) {
                    // Hapus foto lama jika ada
                    if ($user['profile_photo'] && file_exists($user['profile_photo'])) {
                        unlink($user['profile_photo']);
                    }
                    $profile_photo = $destination;
                }
            }

            // Update database
            if (!empty($new_password)) {
                $sql = "UPDATE users SET name = ?, email = ?, password = ?";
                $params = [$name, $email, password_hash($new_password, PASSWORD_DEFAULT)];
            } else {
                $sql = "UPDATE users SET name = ?, email = ?";
                $params = [$name, $email];
            }

            // Tambahkan foto ke query jika ada
            if (isset($profile_photo)) {
                $sql .= ", profile_photo = ?";
                $params[] = $profile_photo;
            }

            $sql .= " WHERE id = ?";
            $params[] = $_SESSION['user_id'];

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $success = "Profil berhasil diperbarui!";
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan saat memperbarui profil.";
        }
    }
}

function getProfileImage($user_image) {
    // Jika user memiliki foto profil
    if ($user_image && file_exists($user_image)) {
        return $user_image;
    }
    // Jika tidak ada foto profil, gunakan default
    return 'images/profiles/default-profile.jpg';
}

include 'includes/header.php';
?>
<style>
.profile-image {
    width: 150px;
    height: 150px;
    margin: 0 auto;
    overflow: hidden;
}

.profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="profile-image">
                            <img src="<?php echo getProfileImage($user['profile_photo']); ?>" alt="Profile Picture" class="img-fluid rounded-circle">
                        </div>
                        <h2 class="mb-0">Profil Saya</h2>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Foto Profil</label>
                            <input type="file" class="form-control" id="profile_photo" 
                                   name="profile_photo" accept="image/*">
                            <div class="form-text">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.</div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   required value="<?php echo htmlspecialchars($user['name']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   required value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Ubah Password</h5>
                        <p class="text-muted small">Kosongkan bagian ini jika tidak ingin mengubah password</p>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control" id="current_password" 
                                   name="current_password">
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="new_password" 
                                   name="new_password" minlength="6">
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password" minlength="6">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
                        </div>
                    </form>
                    <hr class="my-4">
                    <h5 class="text-center mb-3">Riwayat Janji Temu</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Dokter</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT a.*, d.name as doctor_name, d.specialization 
                                    FROM appointments a 
                                    JOIN doctors d ON a.doctor_id = d.id 
                                    WHERE a.user_id = ? 
                                    ORDER BY a.appointment_date DESC, a.appointment_time DESC
                                    LIMIT 5
                                ");
                                $stmt->execute([$_SESSION['user_id']]);
                                while ($appointment = $stmt->fetch()) {
                                    $status_class = [
                                        'pending' => 'text-warning',
                                        'confirmed' => 'text-success',
                                        'cancelled' => 'text-danger'
                                    ][$appointment['status']] ?? '';
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></td>
                                    <td>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                    <td><span class="<?php echo $status_class; ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="text-end">
                            <a href="my_appointments.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
