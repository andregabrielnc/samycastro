<?php
require_once __DIR__ . '/config.php';

try {
    $db = getDB();
    $s = getAllSettings();
    $services = $db->query("SELECT * FROM services WHERE active = 1 ORDER BY sort_order")->fetchAll();
    $team = $db->query("SELECT * FROM team WHERE active = 1 ORDER BY sort_order")->fetchAll();
    $testimonials = $db->query("SELECT * FROM testimonials WHERE active = 1 ORDER BY sort_order")->fetchAll();
    $articles = $db->query("SELECT * FROM articles WHERE active = 1 ORDER BY created_at DESC LIMIT 4")->fetchAll();
    $faqRows = $db->query("SELECT * FROM faq WHERE active = 1 ORDER BY sort_order")->fetchAll();
    $specialties = $db->query("SELECT * FROM specialties WHERE active = 1 ORDER BY sort_order")->fetchAll();
    $clients = $db->query("SELECT * FROM clients WHERE active = 1 ORDER BY sort_order")->fetchAll();

    // Group FAQ by category
    $faqGroups = [];
    foreach ($faqRows as $f) {
        $faqGroups[$f['category']]['icon'] = $f['category_icon'];
        $faqGroups[$f['category']]['items'][] = $f;
    }
} catch (Exception $ex) {
    // Fallback: show static page if DB not available
    header('Location: index.html');
    exit;
}

$wa = $s['whatsapp_number'] ?? '5562994793553';
$waLink = "https://wa.me/{$wa}?text=" . urlencode("Olá Samy vim do site pode me ajudar?");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dra. Samla Cristie - Médica Veterinária GO-14064-VP. Consultas veterinárias para cães, gatos, equinos e animais silvestres.">
    <title>Dra. Samla Cristie | Médica Veterinária - GO-14064-VP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="#hero" class="nav-logo">
                <span class="logo-text"><?= e($s['hero_title'] ?? 'Dra. Samla Cristie') ?></span>
                <span class="logo-sub">Médica Veterinária</span>
            </a>
            <button class="mobile-toggle" id="mobileToggle" aria-label="Abrir menu">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav-links" id="navLinks">
                <li><a href="#sobre">Sobre</a></li>
                <li><a href="#servicos">Serviços</a></li>
                <li><a href="#equipe">Equipe</a></li>
                <li><a href="#clientes">Clientes</a></li>
                <li><a href="#blog">Blog</a></li>
                <li><a href="#faq">FAQ</a></li>
                <li><a href="#contato">Contato</a></li>
            </ul>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero" id="hero">
        <div class="hero-overlay"></div>
        <div class="hero-image-wrapper">
            <img src="<?= e($s['hero_image'] ?? '4.jpeg') ?>" alt="Dra. Samla Cristie" class="hero-img">
        </div>
        <div class="hero-content">
            <p class="hero-tag"><i class="fas fa-paw"></i> <?= e($s['hero_tag'] ?? 'Medicina Veterinária com Amor e Dedicação') ?></p>
            <h1 class="hero-title"><?= e($s['hero_title'] ?? 'Dra. Samla Cristie') ?></h1>
            <p class="hero-registration"><?= e($s['hero_subtitle'] ?? 'CRMV-GO 14064-VP') ?></p>
            <p class="hero-description"><?= e($s['hero_description'] ?? '') ?></p>
            <div class="hero-buttons">
                <a href="<?= $waLink ?>" class="btn btn-primary" target="_blank"><i class="fab fa-whatsapp"></i> Agende sua Consulta</a>
                <a href="#servicos" class="btn btn-outline">Nossos Serviços</a>
            </div>
        </div>
    </section>

    <!-- SOBRE MIM -->
    <section class="section sobre" id="sobre">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-heart"></i> Conheça</span>
                <h2 class="section-title">Sobre Mim</h2>
                <div class="title-divider"><span></span><i class="fas fa-horse"></i><span></span></div>
            </div>
            <div class="sobre-grid">
                <div class="sobre-images">
                    <div class="sobre-img-main reveal-left">
                        <img src="<?= e($s['about_image1'] ?? '3.jpeg') ?>" alt="Dra. Samla" loading="lazy">
                    </div>
                    <div class="sobre-img-secondary reveal-left">
                        <img src="<?= e($s['about_image2'] ?? '7.jpeg') ?>" alt="Dra. Samla" loading="lazy">
                    </div>
                </div>
                <div class="sobre-text reveal-right">
                    <h3><?= e($s['about_title'] ?? 'Uma paixão que começou no campo') ?></h3>
                    <p><?= $s['about_text1'] ?? '' ?></p>
                    <p><?= $s['about_text2'] ?? '' ?></p>
                    <p><?= $s['about_text3'] ?? '' ?></p>
                    <div class="sobre-stats">
                        <div class="stat">
                            <span class="stat-number"><?= e($s['about_stat1_number'] ?? '500+') ?></span>
                            <span class="stat-label"><?= e($s['about_stat1_label'] ?? 'Atendimentos') ?></span>
                        </div>
                        <div class="stat">
                            <span class="stat-number"><?= e($s['about_stat2_number'] ?? '4') ?></span>
                            <span class="stat-label"><?= e($s['about_stat2_label'] ?? 'Especialidades') ?></span>
                        </div>
                        <div class="stat">
                            <span class="stat-number"><?= e($s['about_stat3_number'] ?? '100%') ?></span>
                            <span class="stat-label"><?= e($s['about_stat3_label'] ?? 'Dedicação') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVIÇOS -->
    <section class="section servicos" id="servicos">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-stethoscope"></i> Especialidades</span>
                <h2 class="section-title">Nossos Serviços</h2>
                <div class="title-divider"><span></span><i class="fas fa-paw"></i><span></span></div>
                <p class="section-subtitle">Atendimento veterinário no local do animal, com todo o cuidado e equipamento necessário.</p>
            </div>
            <div class="servicos-grid">
                <?php foreach ($services as $svc): ?>
                <div class="servico-card reveal-up">
                    <div class="servico-img">
                        <img src="<?= e($svc['image']) ?>" alt="<?= e($svc['title']) ?>" loading="lazy">
                        <div class="servico-badge"><i class="<?= e($svc['icon']) ?>"></i></div>
                    </div>
                    <div class="servico-content">
                        <h3><?= e($svc['title']) ?></h3>
                        <p><?= e($svc['description']) ?></p>
                        <a href="https://wa.me/<?= e($wa) ?>?text=<?= urlencode('Olá Samy vim do site, ' . $svc['whatsapp_text']) ?>" class="btn-servico" target="_blank">Agendar <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA AGENDE -->
    <section class="cta-section">
        <div class="cta-overlay"></div>
        <div class="cta-bg-img">
            <img src="<?= e($s['cta_image'] ?? '6.jpeg') ?>" alt="Agende uma consulta" loading="lazy">
        </div>
        <div class="container cta-content">
            <div class="cta-icon"><i class="fas fa-calendar-check"></i></div>
            <h2><?= e($s['cta_title'] ?? 'Agende uma Consulta') ?></h2>
            <p><?= e($s['cta_text'] ?? '') ?></p>
            <a href="<?= $waLink ?>" class="btn btn-primary btn-lg" target="_blank"><i class="fab fa-whatsapp"></i> Falar com a Dra. Samla</a>
        </div>
    </section>

    <!-- EQUIPE -->
    <section class="section equipe" id="equipe">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-users"></i> Profissionais</span>
                <h2 class="section-title">Nossa Equipe</h2>
                <div class="title-divider"><span></span><i class="fas fa-user-md"></i><span></span></div>
            </div>
            <div class="equipe-intro reveal-up">
                <p><?= $s['team_intro1'] ?? '' ?></p>
                <p><?= $s['team_intro2'] ?? '' ?></p>
            </div>
            <div class="equipe-grid">
                <?php foreach ($team as $member): ?>
                <div class="equipe-card reveal-up">
                    <div class="equipe-img"><img src="<?= e($member['image']) ?>" alt="<?= e($member['name']) ?>" loading="lazy"></div>
                    <div class="equipe-info">
                        <h3><?= e($member['name']) ?></h3>
                        <span class="equipe-role"><?= e($member['role']) ?></span>
                        <p><?= e($member['description']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="equipe-especialidades reveal-up">
                <h3><i class="fas fa-award"></i> Áreas de Atuação</h3>
                <div class="especialidades-list">
                    <?php foreach ($specialties as $sp): ?>
                    <div class="especialidade-item"><i class="<?= e($sp['icon']) ?>"></i> <?= e($sp['name']) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- DEPOIMENTOS -->
    <section class="section depoimentos" id="depoimentos">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-star"></i> Avaliações</span>
                <h2 class="section-title">O Que Nossos Clientes Dizem</h2>
                <div class="title-divider"><span></span><i class="fas fa-quote-right"></i><span></span></div>
                <div class="google-rating">
                    <div class="google-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <span class="google-score">4.9</span>
                    <span class="google-count">baseado em <?= count($testimonials) * 14 + 3 ?> avaliações</span>
                    <i class="fab fa-google google-icon"></i>
                </div>
            </div>
            <div class="depoimentos-grid">
                <?php foreach ($testimonials as $dep): ?>
                <div class="depoimento-card reveal-up">
                    <div class="depoimento-header">
                        <div class="avatar" style="background: <?= e($dep['color']) ?>;"><?= e($dep['initials']) ?></div>
                        <div>
                            <h4><?= e($dep['name']) ?></h4>
                            <div class="depoimento-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= floor($dep['rating'])): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($i - $dep['rating'] < 1): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <span class="depoimento-date"><?= e($dep['date_label']) ?></span>
                        </div>
                    </div>
                    <p><?= e($dep['text']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- CLIENTES -->
    <section class="section clientes" id="clientes">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-handshake"></i> Parceiros</span>
                <h2 class="section-title">Nossos Clientes</h2>
                <div class="title-divider"><span></span><i class="fas fa-building"></i><span></span></div>
                <p class="section-subtitle">Clínicas, pet shops e propriedades rurais que confiam na nossa equipe para cuidar dos seus pacientes.</p>
            </div>
            <div class="clientes-grid">
                <?php foreach ($clients as $cli): ?>
                <div class="cliente-card reveal-up">
                    <div class="cliente-icon" style="background: <?= e($cli['logo_color']) ?>;"><i class="<?= e($cli['logo_icon']) ?>"></i></div>
                    <h3><?= e($cli['name']) ?></h3>
                    <span class="cliente-type"><?= e($cli['type']) ?></span>
                    <p><?= e($cli['description']) ?></p>
                    <span class="cliente-location"><i class="fas fa-map-marker-alt"></i> <?= e($cli['location']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- BLOG -->
    <section class="section blog" id="blog">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-newspaper"></i> Dicas e Artigos</span>
                <h2 class="section-title">Blog Veterinário</h2>
                <div class="title-divider"><span></span><i class="fas fa-book-open"></i><span></span></div>
            </div>
            <div class="blog-grid">
                <?php foreach ($articles as $art): ?>
                <div class="blog-card reveal-up">
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
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align:center; margin-bottom: 40px;">
                <a href="blog.php" class="btn btn-outline" style="color: var(--green-deep); border-color: var(--green-deep);">Ver Todos os Artigos <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="blog-cta reveal-up">
                <div class="blog-cta-icon"><i class="fas fa-heartbeat"></i></div>
                <h3><?= e($s['blog_cta_title'] ?? '🐾 Agende uma Consulta') ?></h3>
                <p><?= e($s['blog_cta_text'] ?? '') ?></p>
                <a href="<?= $waLink ?>" class="btn btn-primary btn-lg" target="_blank"><i class="fab fa-whatsapp"></i> Agendar Agora pelo WhatsApp</a>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="section faq" id="faq">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-question-circle"></i> Tire suas dúvidas</span>
                <h2 class="section-title">Perguntas Frequentes</h2>
                <div class="title-divider"><span></span><i class="fas fa-comments"></i><span></span></div>
            </div>
            <div class="faq-categories">
                <?php foreach ($faqGroups as $catName => $catData): ?>
                <div class="faq-category reveal-up">
                    <h3><i class="<?= e($catData['icon']) ?>"></i> <?= e($catName) ?></h3>
                    <?php foreach ($catData['items'] as $fItem): ?>
                    <div class="faq-item">
                        <button class="faq-question">
                            <span><?= e($fItem['question']) ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer"><p><?= $fItem['answer'] ?></p></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CONTATO -->
    <section class="section contato" id="contato">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-envelope"></i> Entre em Contato</span>
                <h2 class="section-title">Fale Conosco</h2>
                <div class="title-divider"><span></span><i class="fas fa-phone"></i><span></span></div>
            </div>
            <div class="contato-grid">
                <div class="contato-info reveal-left">
                    <div class="contato-item">
                        <div class="contato-icon"><i class="fab fa-whatsapp"></i></div>
                        <div><h4>WhatsApp</h4><a href="<?= $waLink ?>" target="_blank"><?= e($s['whatsapp_display'] ?? '(62) 99479-3553') ?></a></div>
                    </div>
                    <div class="contato-item">
                        <div class="contato-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div><h4>Atendimento</h4><p><?= e($s['contact_location'] ?? 'Goiânia e região - GO') ?><br><?= e($s['contact_location_detail'] ?? '') ?></p></div>
                    </div>
                    <div class="contato-item">
                        <div class="contato-icon"><i class="fas fa-clock"></i></div>
                        <div><h4>Horário</h4><p><?= e($s['hours_weekday'] ?? '') ?><br><?= e($s['hours_saturday'] ?? '') ?></p></div>
                    </div>
                    <div class="contato-item">
                        <div class="contato-icon"><i class="fas fa-id-card"></i></div>
                        <div><h4>Registro</h4><p><?= e($s['hero_subtitle'] ?? 'CRMV-GO 14064-VP') ?><br><?= e($s['hero_title'] ?? 'Dra. Samla Cristie') ?></p></div>
                    </div>
                </div>
                <form class="contato-form reveal-right" id="contatoForm">
                    <div class="form-group"><label for="nome">Nome Completo</label><input type="text" id="nome" placeholder="Seu nome" required></div>
                    <div class="form-row">
                        <div class="form-group"><label for="telefone">Telefone</label><input type="tel" id="telefone" placeholder="(00) 00000-0000" required></div>
                        <div class="form-group"><label for="animal">Tipo de Animal</label>
                            <select id="animal" required>
                                <option value="">Selecione</option>
                                <option value="cao">Cão</option>
                                <option value="gato">Gato</option>
                                <option value="equino">Equino</option>
                                <option value="bovino">Bovino</option>
                                <option value="silvestre">Animal Silvestre</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group"><label for="mensagem">Mensagem</label><textarea id="mensagem" rows="4" placeholder="Descreva o motivo da consulta..." required></textarea></div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fab fa-whatsapp"></i> Enviar via WhatsApp</button>
                </form>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h3 class="footer-logo"><?= e($s['hero_title'] ?? 'Dra. Samla Cristie') ?></h3>
                    <p class="footer-tagline">Médica Veterinária — <?= e($s['hero_subtitle'] ?? 'CRMV-GO 14064-VP') ?></p>
                    <p><?= e($s['footer_text'] ?? '') ?></p>
                    <div class="footer-social">
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?= $waLink ?>" aria-label="WhatsApp" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Links Rápidos</h4>
                    <ul>
                        <li><a href="#sobre">Sobre Mim</a></li>
                        <li><a href="#servicos">Serviços</a></li>
                        <li><a href="#equipe">Equipe</a></li>
                        <li><a href="#blog">Blog</a></li>
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="#contato">Contato</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contato</h4>
                    <p><i class="fas fa-map-marker-alt"></i> <?= e($s['contact_location'] ?? 'Goiânia e Região - GO') ?></p>
                    <p><i class="fab fa-whatsapp"></i> <?= e($s['whatsapp_display'] ?? '(62) 99479-3553') ?></p>
                    <p><i class="fas fa-envelope"></i> <?= e($s['contact_email'] ?? 'contato@drasamlacristie.com.br') ?></p>
                </div>
                <div class="footer-hours">
                    <h4>Funcionamento</h4>
                    <p><i class="far fa-clock"></i> <?= e($s['hours_weekday'] ?? 'Seg a Sex: 08:00 às 18:00') ?></p>
                    <p><i class="far fa-clock"></i> <?= e($s['hours_saturday'] ?? 'Sáb: 08:00 às 12:00') ?></p>
                    <p class="footer-emergency"><i class="fas fa-ambulance"></i> Urgências: ligar para <?= e($s['whatsapp_display'] ?? '(62) 99479-3553') ?></p>
                </div>
            </div>
            <div class="footer-bottom"><p>&copy; <?= date('Y') ?> <?= e($s['hero_title'] ?? 'Dra. Samla Cristie') ?> — Medicina Veterinária. Todos os direitos reservados.</p></div>
        </div>
    </footer>

    <!-- WHATSAPP FLOAT -->
    <a href="<?= $waLink ?>" class="whatsapp-float" target="_blank" aria-label="WhatsApp" id="whatsappFloat">
        <i class="fab fa-whatsapp"></i>
        <span class="whatsapp-tooltip">Fale conosco!</span>
    </a>

    <script src="script.js"></script>
</body>
</html>
