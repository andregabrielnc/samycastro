<?php
require_once __DIR__ . '/../config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM admin_users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
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
    }
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
        .error { background: #fce4ec; color: #c62828; padding: 12px 16px; border-radius: 10px; font-size: 0.85rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #888; font-size: 0.82rem; text-decoration: none; }
        .back-link a:hover { color: #2d5016; }
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
                    <input type="text" name="username" placeholder="Seu usuário" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Sua senha" required>
                </div>
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Entrar</button>
        </form>
        <div class="back-link"><a href="../index.php"><i class="fas fa-arrow-left"></i> Voltar ao site</a></div>
    </div>
</body>
</html>
