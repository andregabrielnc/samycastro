<?php // Sidebar template for admin pages
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">Dra. Samla Cristie</div>
        <div class="sidebar-sub">Painel Admin</div>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php" class="<?= $currentPage==='index'?'active':'' ?>"><i class="fas fa-home"></i> Dashboard</a>
        <a href="settings.php" class="<?= $currentPage==='settings'?'active':'' ?>"><i class="fas fa-cog"></i> Configurações do Site</a>
        <div class="divider"></div>
        <a href="articles.php" class="<?= $currentPage==='articles'?'active':'' ?>"><i class="fas fa-newspaper"></i> Blog / Artigos</a>
        <a href="services.php" class="<?= $currentPage==='services'?'active':'' ?>"><i class="fas fa-stethoscope"></i> Serviços</a>
        <a href="team.php" class="<?= $currentPage==='team'?'active':'' ?>"><i class="fas fa-users"></i> Equipe</a>
        <a href="testimonials.php" class="<?= $currentPage==='testimonials'?'active':'' ?>"><i class="fas fa-star"></i> Depoimentos</a>
        <a href="faq.php" class="<?= $currentPage==='faq'?'active':'' ?>"><i class="fas fa-question-circle"></i> FAQ</a>
        <a href="specialties.php" class="<?= $currentPage==='specialties'?'active':'' ?>"><i class="fas fa-award"></i> Especialidades</a>
        <a href="clients.php" class="<?= $currentPage==='clients'?'active':'' ?>"><i class="fas fa-handshake"></i> Clientes / Parceiros</a>
        <div class="divider"></div>
        <a href="credentials.php" class="<?= $currentPage==='credentials'?'active':'' ?>"><i class="fas fa-database"></i> Banco de Dados</a>
        <a href="password.php" class="<?= $currentPage==='password'?'active':'' ?>"><i class="fas fa-key"></i> Alterar Senha</a>
        <div class="divider"></div>
        <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Site</a>
        <a href="auth.php?logout=1"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </nav>
</div>
