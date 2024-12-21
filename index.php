<?php
session_start();
require_once 'config/database.php';
include 'includes/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container my-5">
    <!-- Hero Section -->
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h1 class="display-4 mb-4">Selamat Datang di HealthSync</h1>
            <p class="lead">Kesehatan Anda adalah prioritas kami. Buat janji temu dengan dokter terbaik kami sekarang.</p>
            <a href="appointment.php" class="btn btn-primary btn-lg mb-3">Buat Janji Temu</a>
        </div>
        <div class="col-lg-6">
            <img src="images/bg.jpg" alt="Hospital" class="img-fluid rounded">
        </div>
    </div>

    <!-- Layanan Section -->
    <section class="mb-5">
        <h2 class="text-center mb-4">Layanan Kami</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-md fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Konsultasi Dokter</h5>
                        <p class="card-text">Konsultasikan kesehatan Anda dengan dokter spesialis terpercaya. Kami menyediakan layanan konsultasi dokter yang dirancang untuk memberikan solusi kesehatan terbaik bagi Anda. Dengan tim profesional medis yang berpengalaman, kami siap membantu Anda memahami dan menangani berbagai masalah kesehatan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Janji Temu Online</h5>
                        <p class="card-text">Buat janji temu dengan dokter secara online dengan mudah. Atur janji temu Anda dengan mudah dan cepat melalui layanan kami. Dengan sistem yang terintegrasi dan fleksibel, kami memastikan pengalaman Anda menjadi lebih nyaman dan efisien.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-newspaper fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Artikel Kesehatan</h5>
                        <p class="card-text">Baca informasi kesehatan terbaru dari para ahli. Tingkatkan wawasan Anda tentang kesehatan melalui koleksi artikel informatif kami. Kami menghadirkan konten yang relevan, terpercaya, dan mudah dipahami untuk membantu Anda menjalani gaya hidup sehat.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Artikel Terbaru -->
    <section>
        <h2 class="text-center mb-4">Artikel Kesehatan Terbaru</h2>
        <div class="row">
            <?php
            $stmt = $pdo->query("SELECT * FROM health_articles ORDER BY published_at DESC LIMIT 3");
            while($article = $stmt->fetch()) {
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if($article['image']): ?>
                        <img src="<?php echo $article['image']; ?>" class="card-img-top" alt="<?php echo $article['title']; ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $article['title']; ?></h5>
                        <p class="card-text"><?php echo substr($article['content'], 0, 100); ?>...</p>
                        <a href="article.php?id=<?php echo $article['id']; ?>" class="btn btn-primary">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>

    <!-- Kontak Kami & Maps -->
    <section class="mb-5">
        <h2 class="text-center mb-4">Kontak Kami</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="fas fa-comments me-2"></i>Kritik & Saran</h5>
                        <form action="process_feedback.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subjek</label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Pilih Subjek</option>
                                    <option value="kritik">Kritik</option>
                                    <option value="saran">Saran</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Pesan</label>
                                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="fas fa-map me-2"></i>Maps</h5>
                        <div class="ratio ratio-16x9">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.666667!2d106.816667!3d-6.200000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTInMDAuMCJTIDEwNsKwNDknMDAuMCJF!5e0!3m2!1sen!2sid!4v1234567890" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

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

<?php include 'includes/footer.php'; ?> 