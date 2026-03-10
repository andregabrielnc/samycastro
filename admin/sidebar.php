<?php // Sidebar template for admin pages
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
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

<div class="toast-container" id="toastContainer"></div>

<script>
(function() {
    // Sidebar overlay toggle
    var overlay = document.getElementById('sidebarOverlay');
    var sidebar = document.getElementById('sidebar');

    if (overlay && sidebar) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });

        // Sync overlay with sidebar open state
        var observer = new MutationObserver(function() {
            if (sidebar.classList.contains('open')) {
                overlay.classList.add('active');
            } else {
                overlay.classList.remove('active');
            }
        });
        observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });

        // Update all toggle buttons to also toggle overlay
        document.addEventListener('click', function(e) {
            if (e.target.closest('.mobile-sidebar-toggle')) {
                setTimeout(function() {
                    if (sidebar.classList.contains('open')) {
                        overlay.classList.add('active');
                    } else {
                        overlay.classList.remove('active');
                    }
                }, 10);
            }
        });
    }

    // Toast notification system
    var container = document.getElementById('toastContainer');

    function showToast(message, type, duration) {
        type = type || 'success';
        duration = duration || 4000;

        var icons = { success: 'fas fa-check-circle', error: 'fas fa-times-circle', warning: 'fas fa-exclamation-triangle' };
        var titles = { success: 'Sucesso', error: 'Erro', warning: 'Atenção' };

        var toast = document.createElement('div');
        toast.className = 'toast toast-' + type;
        toast.style.setProperty('--duration', (duration / 1000) + 's');
        toast.innerHTML =
            '<i class="' + icons[type] + ' toast-icon"></i>' +
            '<div class="toast-body">' +
                '<div class="toast-title">' + titles[type] + '</div>' +
                '<div class="toast-msg">' + message + '</div>' +
            '</div>' +
            '<button class="toast-close" aria-label="Fechar">&times;</button>' +
            '<div class="toast-progress"></div>';

        container.appendChild(toast);

        toast.querySelector('.toast-close').addEventListener('click', function() {
            dismissToast(toast);
        });

        var timer = setTimeout(function() { dismissToast(toast); }, duration);
        toast._timer = timer;
    }

    function dismissToast(toast) {
        clearTimeout(toast._timer);
        toast.style.animation = 'toastOut 0.3s ease forwards';
        setTimeout(function() {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 300);
    }

    // Convert existing .alert divs to toasts
    document.addEventListener('DOMContentLoaded', function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var type = 'success';
            if (alert.classList.contains('alert-error')) type = 'error';
            if (alert.classList.contains('alert-warning')) type = 'warning';

            // Get text content (strip HTML tags for safety)
            var msg = alert.textContent.trim();
            // Remove leading icon text (fa icon renders as empty text node)
            msg = msg.replace(/^\s*\S*\s*/, '').trim();
            if (!msg) msg = alert.textContent.trim();

            showToast(msg, type, 5000);
            alert.style.display = 'none';
        });
    });

    // Expose globally so pages can call window.showToast()
    window.showToast = showToast;
})();
</script>
