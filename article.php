<?php
session_start();
require_once 'config/database.php';

// Ambil artikel berdasarkan ID
if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                ha.*,
                u.name as author_name 
            FROM health_articles ha
            LEFT JOIN users u ON ha.author_id = u.id
            WHERE ha.id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $article = $stmt->fetch();

        if (!$article) {
            header("Location: articles.php");
            exit();
        }

        // Ambil artikel terkait
        $stmt = $pdo->prepare("
            SELECT * FROM health_articles 
            WHERE category = ? AND id != ? 
            ORDER BY published_at DESC 
            LIMIT 3
        ");
        $stmt->execute([$article['category'], $article['id']]);
        $related_articles = $stmt->fetchAll();

    } catch(PDOException $e) {
        $error = "Terjadi kesalahan saat mengambil artikel.";
    }
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM health_articles 
            WHERE category = ? 
            AND id != ? 
            AND published_at <= CURRENT_TIMESTAMP
            ORDER BY published_at DESC 
            LIMIT 3
        ");
        $stmt->execute([$article['category'], $article['id']]);
        $related_articles = $stmt->fetchAll();
    
    } catch(PDOException $e) {
        $error = "Terjadi kesalahan saat mengambil artikel terkait.";
    }
} else {
    header("Location: articles.php");
    exit();
}

include 'includes/header.php';
?>

<div class="container my-5">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php else: ?>
        <div class="row">
            <!-- Article Content -->
            <div class="col-lg-8">
                <article>
                    <?php if($article['image']): ?>
                        <img src="<?php echo htmlspecialchars($article['image']); ?>" 
                             alt="<?php echo htmlspecialchars($article['title']); ?>" 
                             class="img-fluid rounded mb-4">
                    <?php endif; ?>

                    <h1 class="mb-3"><?php echo htmlspecialchars($article['title']); ?></h1>
                    
                    <div class="text-muted mb-4">
                        <small>
                            <i class="fas fa-user me-2"></i>
                            <?php echo htmlspecialchars($article['author_name'] ?? 'Admin'); ?>
                            
                            <i class="fas fa-calendar ms-3 me-2"></i>
                            <?php echo date('d F Y', strtotime($article['published_at'])); ?>
                            
                            <i class="fas fa-folder ms-3 me-2"></i>
                            <?php echo htmlspecialchars($article['category']); ?>
                        </small>
                    </div>

                    <div class="article-content">
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    </div>

                    <?php if($article['source']): ?>
                        <div class="mt-4">
                            <small class="text-muted">
                                Sumber: <?php echo htmlspecialchars($article['source']); ?>
                            </small>
                        </div>
                    <?php endif; ?>
                </article>

                <!-- Social Share Buttons -->
                <div class="mt-4 mb-5">
                    <h5>Bagikan Artikel:</h5>
                    <?php
                    $share_url = urlencode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                    $share_title = urlencode($article['title']);
                    ?>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" 
                       class="btn btn-primary me-2" target="_blank">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>" 
                       class="btn btn-info me-2 text-white" target="_blank">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="https://wa.me/?text=<?php echo $share_title . '%20' . $share_url; ?>" 
                       class="btn btn-success" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Artikel Terkait</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($related_articles)): ?>
                            <p class="text-muted">Tidak ada artikel terkait.</p>
                        <?php else: ?>
                            <?php foreach($related_articles as $related): ?>
                                <div class="card mb-3">
                                    <?php if($related['image']): ?>
                                        <img src="<?php echo htmlspecialchars($related['image']); ?>" 
                                            alt="<?php echo htmlspecialchars($related['title']); ?>" 
                                            class="card-img-top">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="article.php?id=<?php echo $related['id']; ?>" 
                                            class="text-decoration-none">
                                                <?php echo htmlspecialchars($related['title']); ?>
                                            </a>
                                        </h6>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d F Y', strtotime($related['published_at'])); ?>
                                            </small>
                                        </p>
                                        <p class="card-text text-muted small">
                                            <?php 
                                            $excerpt = strip_tags($related['content']);
                                            echo substr($excerpt, 0, 100) . '...'; 
                                            ?>
                                        </p>
                                        <a href="article.php?id=<?php echo $related['id']; ?>" 
                                        class="btn btn-sm btn-outline-primary">
                                            Baca Selengkapnya
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.article-content {
    line-height: 1.8;
    font-size: 1.1em;
}

.article-content p {
    margin-bottom: 1.5rem;
}
.card-img-top {
    height: 200px;
    object-fit: cover;
}

.card .card-title {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.card .card-text {
    margin-bottom: 0.5rem;
}

.related-article {
    transition: transform 0.2s;
}

.related-article:hover {
    transform: translateY(-5px);
}
</style>

<?php include 'includes/footer.php'; ?> 