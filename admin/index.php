<?php
require_once 'auth.php';
$db = getDB();
$totalArticles = $db->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$totalServices = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
$totalTestimonials = $db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
$totalFaq = $db->query("SELECT COUNT(*) FROM faq")->fetchColumn();
$totalViews = $db->query("SELECT SUM(views) FROM articles")->fetchColumn() ?: 0;
$recentArticles = $db->query("SELECT * FROM articles ORDER BY updated_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="mobile-sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button>
                <h2>Dashboard</h2>
            </div>
            <div class="topbar-right">
                <span>Olá, <?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
                <a href="auth.php?logout=1"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
        <div class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-icon" style="background:#2d5016;"><i class="fas fa-newspaper"></i></div>
                    <h3><?= $totalArticles ?></h3>
                    <p>Artigos no Blog</p>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon" style="background:#8B4513;"><i class="fas fa-stethoscope"></i></div>
                    <h3><?= $totalServices ?></h3>
                    <p>Serviços</p>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon" style="background:#c9a84c;"><i class="fas fa-star"></i></div>
                    <h3><?= $totalTestimonials ?></h3>
                    <p>Depoimentos</p>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon" style="background:#4285f4;"><i class="fas fa-eye"></i></div>
                    <h3><?= number_format($totalViews) ?></h3>
                    <p>Visualizações</p>
                </div>
            </div>
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3><i class="fas fa-clock"></i> Artigos Recentes</h3>
                    <a href="articles.php" class="btn-add"><i class="fas fa-plus"></i> Novo Artigo</a>
                </div>
                <table class="admin-table">
                    <thead><tr><th>Imagem</th><th>Título</th><th>Categoria</th><th>Views</th><th>Data</th><th>Ações</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentArticles as $a): ?>
                    <tr>
                        <td><img src="../<?= e($a['image']) ?>" alt=""></td>
                        <td><strong><?= e($a['title']) ?></strong></td>
                        <td><?= e($a['category']) ?></td>
                        <td><?= $a['views'] ?></td>
                        <td><?= date('d/m/Y', strtotime($a['updated_at'])) ?></td>
                        <td><a href="articles.php?edit=<?= $a['id'] ?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
