<?php
require_once __DIR__ . '/config.php';

$db = getDB();
$s = getAllSettings();
$wa = $s['whatsapp_number'] ?? '5562994793553';
$waLink = "https://wa.me/{$wa}?text=" . urlencode("Olá Samy vim do site pode me ajudar?");

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: blog.php'); exit; }

$stmt = $db->prepare("SELECT * FROM articles WHERE slug = ? AND active = 1");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) { header('Location: blog.php'); exit; }

// Increment views
$db->prepare("UPDATE articles SET views = views + 1 WHERE id = ?")->execute([$article['id']]);

// Related articles
$related = $db->prepare("SELECT * FROM articles WHERE category = ? AND id != ? AND active = 1 ORDER BY created_at DESC LIMIT 3");
$related->execute([$article['category'], $article['id']]);
$relatedArticles = $related->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($article['excerpt']) ?>">
    <title><?= e($article['title']) ?> - <?= e($s['hero_title'] ?? 'Dra. Samla Cristie') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .article-hero { position: relative; height: 450px; overflow: hidden; }
        .article-hero img { width: 100%; height: 100%; object-fit: cover; }
        .article-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 50%); }
        .article-hero-content { position: absolute; bottom: 0; left: 0; right: 0; padding: 40px; color: #fff; }
        .article-hero-content .article-cat { display: inline-block; background: var(--green-deep); padding: 6px 18px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .article-hero-content h1 { font-family: var(--font-heading); font-size: clamp(1.6rem, 4vw, 2.4rem); line-height: 1.3; max-width: 700px; }
        .article-hero-content .article-meta-hero { display: flex; gap: 20px; margin-top: 14px; font-size: 0.85rem; opacity: 0.85; }
        .article-body { max-width: 780px; margin: 0 auto; padding: 50px 20px; }
        .article-body h2 { font-family: var(--font-heading); font-size: 1.5rem; color: var(--green-deep); margin: 36px 0 16px; padding-bottom: 8px; border-bottom: 2px solid var(--green-pale); }
        .article-body h3 { font-family: var(--font-heading); font-size: 1.2rem; color: var(--text-dark); margin: 28px 0 12px; }
        .article-body p { color: var(--text-mid); font-size: 1rem; line-height: 1.8; margin-bottom: 16px; }
        .article-body ul, .article-body ol { color: var(--text-mid); margin: 12px 0 20px 24px; line-height: 1.8; }
        .article-body li { margin-bottom: 8px; }
        .article-body strong { color: var(--text-dark); }
        .article-share { display: flex; gap: 12px; align-items: center; margin-top: 40px; padding-top: 30px; border-top: 2px solid var(--border); flex-wrap: wrap; }
        .article-share span { font-weight: 600; color: var(--text-dark); font-size: 0.9rem; }
        .share-btn { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1rem; transition: var(--transition); }
        .share-btn:hover { transform: translateY(-3px); }
        .share-wa { background: #25D366; }
        .share-fb { background: #1877F2; }
        .share-tw { background: #1DA1F2; }
        .article-cta { background: linear-gradient(135deg, var(--green-deep), var(--brown-dark)); border-radius: var(--radius-xl); padding: 40px; text-align: center; color: #fff; margin-top: 40px; }
        .article-cta h3 { font-family: var(--font-heading); font-size: 1.4rem; margin-bottom: 12px; }
        .article-cta p { opacity: 0.9; margin-bottom: 20px; }
        .related-section { background: var(--cream-soft); padding: 60px 0; }
        .related-section h2 { font-family: var(--font-heading); text-align: center; font-size: 1.6rem; margin-bottom: 30px; color: var(--text-dark); }
        .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: var(--green-deep); font-weight: 600; font-size: 0.9rem; margin-bottom: 30px; padding: 8px 0; }
        .back-link:hover { gap: 12px; }
        @media (max-width: 768px) {
            .article-hero { height: 300px; }
            .article-hero-content { padding: 20px; }
            .related-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <nav class="navbar scrolled" id="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">
                <span class="logo-text"><?= e($s['hero_title'] ?? 'Dra. Samla Cristie') ?></span>
                <span class="logo-sub">Médica Veterinária</span>
            </a>
            <button class="mobile-toggle" id="mobileToggle" aria-label="Menu"><span></span><span></span><span></span></button>
            <ul class="nav-links" id="navLinks">
                <li><a href="index.php#sobre">Sobre</a></li>
                <li><a href="index.php#servicos">Serviços</a></li>
                <li><a href="blog.php" style="background:rgba(255,255,255,0.15)">Blog</a></li>
                <li><a href="index.php#faq">FAQ</a></li>
                <li><a href="index.php#contato">Contato</a></li>
            </ul>
        </div>
    </nav>

    <div class="article-hero">
        <img src="<?= e($article['image']) ?>" alt="<?= e($article['title']) ?>">
        <div class="article-hero-overlay"></div>
        <div class="article-hero-content">
            <div class="container">
                <span class="article-cat"><?= e($article['category']) ?></span>
                <h1><?= e($article['title']) ?></h1>
                <div class="article-meta-hero">
                    <span><i class="far fa-user"></i> <?= e($article['author']) ?></span>
                    <span><i class="far fa-calendar"></i> <?= date('d/m/Y', strtotime($article['created_at'])) ?></span>
                    <span><i class="far fa-clock"></i> <?= e($article['read_time']) ?> de leitura</span>
                    <span><i class="far fa-eye"></i> <?= $article['views'] ?> visualizações</span>
                </div>
            </div>
        </div>
    </div>

    <div class="article-body">
        <a href="blog.php" class="back-link"><i class="fas fa-arrow-left"></i> Voltar ao Blog</a>

        <?= $article['content'] ?>

        <div class="article-share">
            <span>Compartilhar:</span>
            <a href="https://wa.me/?text=<?= urlencode($article['title'] . ' - ' . 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" class="share-btn share-wa" target="_blank"><i class="fab fa-whatsapp"></i></a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" class="share-btn share-fb" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($article['title']) ?>&url=<?= urlencode('http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" class="share-btn share-tw" target="_blank"><i class="fab fa-twitter"></i></a>
        </div>

        <div class="article-cta">
            <h3>🐾 Precisa de atendimento veterinário?</h3>
            <p>Agende uma consulta e cuide da saúde do seu animal com quem entende!</p>
            <a href="<?= $waLink ?>" class="btn btn-primary" target="_blank"><i class="fab fa-whatsapp"></i> Falar com a Dra. Samla</a>
        </div>
    </div>

    <?php if (!empty($relatedArticles)): ?>
    <section class="related-section">
        <div class="container">
            <h2>Artigos Relacionados</h2>
            <div class="related-grid">
                <?php foreach ($relatedArticles as $rel): ?>
                <div class="blog-card">
                    <a href="artigo.php?slug=<?= urlencode($rel['slug']) ?>" class="blog-card-link">
                        <div class="blog-img">
                            <img src="<?= e($rel['image']) ?>" alt="<?= e($rel['title']) ?>" loading="lazy">
                            <span class="blog-category"><?= e($rel['category']) ?></span>
                        </div>
                        <div class="blog-content">
                            <h3><?= e($rel['title']) ?></h3>
                            <p><?= e(mb_substr($rel['excerpt'], 0, 120)) ?>...</p>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom"><p>&copy; <?= date('Y') ?> <?= e($s['hero_title'] ?? 'Dra. Samla Cristie') ?> — Todos os direitos reservados.</p></div>
        </div>
    </footer>

    <a href="<?= $waLink ?>" class="whatsapp-float" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i><span class="whatsapp-tooltip">Fale conosco!</span></a>
    <script src="script.js"></script>
</body>
</html>
