<?php
require_once __DIR__ . '/config.php';

$db = getDB();
$s = getAllSettings();
$wa = $s['whatsapp_number'] ?? '5562994793553';
$waLink = "https://wa.me/{$wa}?text=" . urlencode("Olá Samy vim do site pode me ajudar?");

// Filters
$category = $_GET['cat'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 6;
$offset = ($page - 1) * $perPage;

// Build query
$where = 'WHERE active = 1';
$params = [];
if ($category) {
    $where .= ' AND category = ?';
    $params[] = $category;
}

$total = $db->prepare("SELECT COUNT(*) FROM articles {$where}");
$total->execute($params);
$totalArticles = $total->fetchColumn();
$totalPages = ceil($totalArticles / $perPage);

$stmt = $db->prepare("SELECT * FROM articles {$where} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}");
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Categories
$cats = $db->query("SELECT DISTINCT category FROM articles WHERE active = 1 ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - <?= e($s['hero_title'] ?? 'Dra. Samla Cristie') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .blog-page-header { background: var(--green-deep); padding: 120px 0 60px; color: #fff; text-align: center; }
        .blog-page-header h1 { font-family: var(--font-heading); font-size: 2.4rem; margin-bottom: 10px; }
        .blog-page-header p { opacity: 0.85; }
        .blog-filters { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin: 30px 0 40px; }
        .blog-filters a { padding: 8px 20px; border-radius: 50px; font-size: 0.82rem; font-weight: 600; border: 2px solid var(--border); color: var(--text-mid); transition: var(--transition); }
        .blog-filters a.active, .blog-filters a:hover { background: var(--green-deep); color: #fff; border-color: var(--green-deep); }
        .blog-page-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; }
        .blog-card-link { display: block; color: inherit; text-decoration: none; }
        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 40px; }
        .pagination a, .pagination span { padding: 10px 16px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; }
        .pagination a { background: var(--white); border: 1px solid var(--border); color: var(--text-mid); }
        .pagination a:hover { border-color: var(--green-deep); color: var(--green-deep); }
        .pagination span.current { background: var(--green-deep); color: #fff; }
        .blog-empty { text-align: center; padding: 60px 20px; color: var(--text-light); }
        .blog-empty i { font-size: 3rem; margin-bottom: 16px; display: block; }
        @media (max-width: 768px) {
            .blog-page-grid { grid-template-columns: 1fr; }
            .blog-page-header { padding: 100px 0 40px; }
            .blog-page-header h1 { font-size: 1.8rem; }
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
                <li><a href="index.php#equipe">Equipe</a></li>
                <li><a href="blog.php" style="background:rgba(255,255,255,0.15)">Blog</a></li>
                <li><a href="index.php#faq">FAQ</a></li>
                <li><a href="index.php#contato">Contato</a></li>
            </ul>
        </div>
    </nav>

    <div class="blog-page-header">
        <div class="container">
            <h1><i class="fas fa-newspaper"></i> Blog Veterinário</h1>
            <p>Dicas essenciais para manter seus animais saudáveis e felizes</p>
        </div>
    </div>

    <section class="section" style="background: var(--cream-soft);">
        <div class="container">
            <div class="blog-filters">
                <a href="blog.php" class="<?= !$category ? 'active' : '' ?>">Todos</a>
                <?php foreach ($cats as $cat): ?>
                <a href="blog.php?cat=<?= urlencode($cat) ?>" class="<?= $category === $cat ? 'active' : '' ?>"><?= e($cat) ?></a>
                <?php endforeach; ?>
            </div>

            <?php if (empty($articles)): ?>
            <div class="blog-empty">
                <i class="far fa-newspaper"></i>
                <h3>Nenhum artigo encontrado</h3>
                <p>Volte em breve para novos conteúdos!</p>
            </div>
            <?php else: ?>
            <div class="blog-page-grid">
                <?php foreach ($articles as $art): ?>
                <div class="blog-card">
                    <a href="artigo.php?slug=<?= urlencode($art['slug']) ?>" class="blog-card-link">
                        <div class="blog-img">
                            <img src="<?= e($art['image']) ?>" alt="<?= e($art['title']) ?>" loading="lazy">
                            <span class="blog-category"><?= e($art['category']) ?></span>
                        </div>
                        <div class="blog-content">
                            <h3><?= e($art['title']) ?></h3>
                            <p><?= e($art['excerpt']) ?></p>
                            <div class="blog-meta">
                                <span><i class="far fa-calendar"></i> <?= date('d/m/Y', strtotime($art['created_at'])) ?></span>
                                <span><i class="far fa-clock"></i> <?= e($art['read_time']) ?></span>
                                <span><i class="far fa-eye"></i> <?= $art['views'] ?> views</span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="blog.php?page=<?= $page - 1 ?><?= $category ? '&cat=' . urlencode($category) : '' ?>"><i class="fas fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                    <span class="current"><?= $i ?></span>
                    <?php else: ?>
                    <a href="blog.php?page=<?= $i ?><?= $category ? '&cat=' . urlencode($category) : '' ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                <a href="blog.php?page=<?= $page + 1 ?><?= $category ? '&cat=' . urlencode($category) : '' ?>"><i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom"><p>&copy; <?= date('Y') ?> <?= e($s['hero_title'] ?? 'Dra. Samla Cristie') ?> — Todos os direitos reservados.</p></div>
        </div>
    </footer>

    <a href="<?= $waLink ?>" class="whatsapp-float" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i><span class="whatsapp-tooltip">Fale conosco!</span></a>
    <script src="script.js"></script>
</body>
</html>
