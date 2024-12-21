<?php
session_start();
require_once 'config/database.php';

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];

    // Validation
    if (strlen($name) < 3) {
        $errors[] = "Nama harus minimal 3 karakter";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password harus minimal 6 karakter";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Email sudah terdaftar";
    }

    if (empty($errors)) {
        try {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password]);
            
            // Auto login after registration
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            // Redirect to homepage
            header("Location: index.php");
            exit();
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
}

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center mb-4">Daftar Akun</h2>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   required minlength="6">
                            <div class="form-text">Password minimal 6 karakter</div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password" required minlength="6">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    Saya setuju dengan <a href="terms.php">syarat dan ketentuan</a>
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Daftar</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        Sudah punya akun? <a href="login.php">Masuk</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 