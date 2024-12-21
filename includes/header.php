<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4A90E2;
            --light-blue: #b3cee5;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #ffffff;
        }
        
        .navbar {
            background-color: var(--light-blue);
        }
        
        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">HealthSync</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctors.php">Dokter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointment.php">Janji Temu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blood_donation.php">Donor Darah</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Keluar</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Masuk</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html> 