<?php
require_once __DIR__ . '/../config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$dbError = false;
// Diagnostics only shown when DB fails AND request comes from localhost
$isLocalRequest = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1', 'localhost']);
$showDiagnostics = isset($_GET['diagnostics']) && $_GET['diagnostics'] === '1' && $isLocalRequest;

// Check database connection
$dbTest = testDatabaseConnection();
if (!$dbTest['success']) {
    $dbError = true;
    $error = 'Erro ao conectar ao banco de dados.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($dbError) {
        $error = 'Erro ao conectar ao banco de dados. Verifique as configurações em ?diagnostics=1';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare('SELECT * FROM admin_users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['name'];
                $_SESSION['admin_username'] = $user['username'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Usuário ou senha inválidos.';
            }
        } catch (Exception $e) {
            $error = 'Erro ao conectar ao banco de dados. Verifique as configurações.';
            $dbError = true;
        }
    }
}

// Get diagnostics info if requested
$diagnostics = [];
if ($showDiagnostics) {
    // Test connection again (might have recovered since initial check)
    $dbTest = testDatabaseConnection();
    if ($dbTest['success'] && $dbError) {
        $dbError = false;
        $error = '';
    }
    $diagnostics = [
        'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'não definido',
        'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'não definido',
        'DB_USER' => defined('DB_USER') ? DB_USER : 'não definido',
        'DB_PASS' => defined('DB_PASS') ? '***' : 'não definido',
        'PHP_VERSION' => phpversion(),
        'MYSQL_EXTENSION' => extension_loaded('pdo_mysql') ? 'OK' : 'NÃO INSTALADO',
        'DATABASE_TEST' => $dbTest,
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Administração</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #2d5016 0%, #5c3a1e 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-card { background: #fff; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); padding: 50px 40px; max-width: 420px; width: 100%; }
        .login-header { text-align: center; margin-bottom: 32px; }
        .login-header .logo { font-family: 'Great Vibes', cursive; font-size: 2.2rem; color: #2d5016; margin-bottom: 6px; }
        .login-header p { color: #888; font-size: 0.85rem; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.8rem; font-weight: 600; color: #333; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 0.95rem; }
        .input-wrap input { width: 100%; padding: 14px 16px 14px 46px; border: 2px solid #e5ddd0; border-radius: 12px; font-size: 0.95rem; outline: none; transition: border-color 0.3s; font-family: 'Inter', sans-serif; }
        .input-wrap input:focus { border-color: #2d5016; }
        .btn-login { width: 100%; padding: 16px; background: #2d5016; color: #fff; border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.3s; font-family: 'Inter', sans-serif; }
        .btn-login:hover { background: #3d6b22; }
        .btn-login:disabled { opacity: 0.6; cursor: not-allowed; }
        .error { background: #fce4ec; color: #c62828; padding: 12px 16px; border-radius: 10px; font-size: 0.85rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #888; font-size: 0.82rem; text-decoration: none; }
        .back-link a:hover { color: #2d5016; }
        .diagnostics { display: none; background: #f5f5f5; padding: 20px; border-radius: 12px; margin-top: 20px; font-family: monospace; font-size: 0.75rem; }
        .diagnostics.show { display: block; }
        .diagnostics-title { font-weight: bold; margin-bottom: 10px; }
        .diagnostics-item { padding: 8px 0; border-bottom: 1px solid #e0e0e0; }
        .diagnostics-item:last-child { border-bottom: none; }
        .diagnostics-key { color: #666; }
        .diagnostics-value { color: #2d5016; font-weight: bold; }
        .diagnostics-error { color: #c62828; }
        .diagnostics-success { color: #2e7d32; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="logo">Dra. Samla Cristie</div>
            <p><i class="fas fa-lock"></i> Painel Administrativo</p>
        </div>

        <?php if ($error): ?>
        <div class="error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Usuário</label>
                <div class="input-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Seu usuário" required autofocus <?= $dbError ? 'disabled' : '' ?>>
                </div>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Sua senha" required <?= $dbError ? 'disabled' : '' ?>>
                </div>
            </div>
            <button type="submit" class="btn-login" <?= $dbError ? 'disabled' : '' ?>><i class="fas fa-sign-in-alt"></i> Entrar</button>
        </form>

        <?php if ($dbError && $isLocalRequest): ?>
        <div style="margin-top: 20px; text-align: center;">
            <a href="?diagnostics=1" style="color: #c62828; text-decoration: underline; font-size: 0.85rem;">
                <i class="fas fa-wrench"></i> Ver Diagnóstico
            </a>
        </div>
        <?php endif; ?>

        <div class="back-link"><a href="../"><i class="fas fa-arrow-left"></i> Voltar ao site</a></div>

        <?php if ($showDiagnostics && !empty($diagnostics)): ?>
        <div class="diagnostics show">
            <div class="diagnostics-title">🔧 DIAGNÓSTICO DO SISTEMA</div>
            <div class="diagnostics-item">
                <span class="diagnostics-key">Host do BD:</span><br>
                <span class="diagnostics-value"><?= e($diagnostics['DB_HOST']) ?></span>
            </div>
            <div class="diagnostics-item">
                <span class="diagnostics-key">Nome do BD:</span><br>
                <span class="diagnostics-value"><?= e($diagnostics['DB_NAME']) ?></span>
            </div>
            <div class="diagnostics-item">
                <span class="diagnostics-key">Usuário BD:</span><br>
                <span class="diagnostics-value"><?= e($diagnostics['DB_USER']) ?></span>
            </div>
            <div class="diagnostics-item">
                <span class="diagnostics-key">PHP Version:</span><br>
                <span class="diagnostics-value"><?= e($diagnostics['PHP_VERSION']) ?></span>
            </div>
            <div class="diagnostics-item">
                <span class="diagnostics-key">Extensão PDO MySQL:</span><br>
                <span class="<?= $diagnostics['MYSQL_EXTENSION'] === 'OK' ? 'diagnostics-success' : 'diagnostics-error' ?>">
                    <?= e($diagnostics['MYSQL_EXTENSION']) ?>
                </span>
            </div>
            <div class="diagnostics-item">
                <span class="diagnostics-key">Teste de Conexão:</span><br>
                <span class="<?= $diagnostics['DATABASE_TEST']['success'] ? 'diagnostics-success' : 'diagnostics-error' ?>">
                    <?= $diagnostics['DATABASE_TEST']['success'] ? '✓ SUCESSO' : '✗ ERRO: ' . e($diagnostics['DATABASE_TEST']['error']) ?>
                </span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
