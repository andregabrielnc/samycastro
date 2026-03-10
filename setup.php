<?php
/**
 * SamyCastro - One-time Setup Script
 * This script initializes the admin user with the provided password.
 * Delete this file after successful setup!
 */

// Check if setup already completed
$setupFile = __DIR__ . '/.setup_complete';
if (file_exists($setupFile)) {
    http_response_code(403);
    die('Setup already completed. Delete setup.php file for security.');
}

// Get database config
require_once __DIR__ . '/config.php';

$message = '';
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? 'admin');
    $password = $_POST['password'] ?? '';
    $adminName = trim($_POST['name'] ?? 'Administrador');

    // Validate input
    if (empty($username) || strlen($username) < 3) {
        $error = 'Username deve ter no mínimo 3 caracteres.';
    } elseif (empty($password) || strlen($password) < 8) {
        $error = 'Senha deve ter no mínimo 8 caracteres.';
    } elseif (empty($adminName)) {
        $error = 'Nome do administrador é obrigatório.';
    } else {
        try {
            $db = getDB();
            
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // Update the admin user
            $stmt = $db->prepare('
                UPDATE admin_users 
                SET username = ?, password = ?, name = ?, updated_at = NOW()
                WHERE id = 1
            ');
            $stmt->execute([$username, $passwordHash, $adminName]);
            
            // Insert default settings if not exist
            $settings = [
                'site_name' => 'Dra. Samla Cristie',
                'site_email' => 'contato@samlavet.com',
                'site_phone' => '(11) 99999-9999',
                'site_whatsapp' => '5511999999999',
                'site_address' => 'Endereço da Clínica',
                'site_description' => 'Clínica Veterinária Dra. Samla Cristie',
                'site_theme' => 'light',
            ];
            
            $stmtSettings = $db->prepare('
                INSERT IGNORE INTO settings (setting_key, setting_value, setting_group, setting_label, setting_type)
                VALUES (?, ?, ?, ?, ?)
            ');
            
            foreach ($settings as $key => $value) {
                $stmtSettings->execute([$key, $value, 'geral', ucfirst(str_replace('_', ' ', $key)), 'text']);
            }
            
            // Mark setup as complete
            file_put_contents($setupFile, date('Y-m-d H:i:s'));
            
            $success = true;
            $message = "✅ Setup concluído com sucesso!<br>Usuário: <strong>$username</strong>";
            
        } catch (Exception $e) {
            $error = 'Erro ao configurar: ' . $e->getMessage();
        }
    }
}

// Pre-filled values
$defaultUsername = 'admin';
$defaultName = 'Administrador';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamyCastro - Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2d5016;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .success-actions {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }
        
        .success-actions p {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .success-actions code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Monaco', 'Courier New', monospace;
            color: #e83e8c;
        }
        
        a.btn-link {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        a.btn-link:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .security-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            font-size: 13px;
            margin-top: 20px;
            line-height: 1.5;
        }
        
        .logo {
            font-size: 40px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🐾</div>
            <h1>SamyCastro Setup</h1>
            <p>Configure o usuário administrador</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
            </div>
            
            <div class="success-actions">
                <p><strong>✅ Setup concluído com sucesso!</strong></p>
                <p>Você pode agora acessar o painel administrativo.</p>
                <p><strong>Próximos passos:</strong></p>
                <ol style="margin-left: 20px; margin-top: 10px;">
                    <li>Acesse <code>/admin/</code> com suas credenciais</li>
                    <li>Valide as configurações do site</li>
                    <li>Delete este arquivo <code>setup.php</code> por segurança</li>
                </ol>
                
                <a href="/admin/" class="btn-link">→ Ir para Admin</a>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Usuário Admin</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="<?php echo e($defaultUsername); ?>" 
                        required
                        minlength="3"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Senha</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Mínimo 8 caracteres" 
                        required
                        minlength="8"
                    >
                </div>
                
                <div class="form-group">
                    <label for="name">Nome do Administrador</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="<?php echo e($defaultName); ?>" 
                        required
                    >
                </div>
                
                <button type="submit" class="button">Configurar Admin</button>
                
                <div class="security-warning">
                    ⚠️ <strong>Importante para Segurança:</strong><br>
                    Após completar o setup, delete este arquivo <code>setup.php</code> do servidor.
                    Deixar este arquivo acessível é um risco de segurança.
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
