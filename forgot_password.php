<?php
session_start();
require_once 'config/database.php';

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Save token to database
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
            $stmt->execute([$token, $token_expiry, $email]);
            
            // Send reset email
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
            $to = $email;
            $subject = "Reset Password - RS Sehat Sejahtera";
            $message = "
                <html>
                <head>
                    <title>Reset Password</title>
                </head>
                <body>
                    <h2>Reset Password</h2>
                    <p>Silakan klik link di bawah ini untuk mereset password Anda:</p>
                    <p><a href='$reset_link'>Reset Password</a></p>
                    <p>Link ini akan kadaluarsa dalam 1 jam.</p>
                    <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
                </body>
                </html>
            ";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: RS Sehat Sejahtera <noreply@rssehat.com>";

            mail($to, $subject, $message, $headers);
            
            $success = "Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.";
        } else {
            $error = "Email tidak ditemukan";
        }
    } catch(PDOException $e) {
        $error = "Terjadi kesalahan. Silakan coba lagi.";
    }
}

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center mb-4">Lupa Password</h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <div class="form-text">
                                Masukkan email yang terdaftar untuk menerima link reset password
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="login.php">Kembali ke halaman login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 