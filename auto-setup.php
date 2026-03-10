<?php
/**
 * SamyCastro - Auto Setup with Default Admin
 * This script is executed ONCE during the first access
 * It automatically creates the admin user with the specified password
 * WARNING: This file MUST be deleted after first access!
 */

// Simple lock to prevent execution
$lockFile = __DIR__ . '/.auto-setup-done';
if (file_exists($lockFile)) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Setup - Já Executado</title>
        <style>
            body { font-family: sans-serif; padding: 40px; background: #f5f5f5; }
            .message { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
            .success { color: #27ae60; margin-bottom: 20px; }
            .warning { background: #fff3cd; padding: 15px; border-radius: 6px; color: #856404; }
        </style>
    </head>
    <body>
        <div class="message">
            <h2 class="success">✅ Setup já foi executado!</h2>
            <p>A aplicação já está configurada. Para maior segurança, <strong>delete os arquivos setup.php e auto-setup.php</strong> do servidor.</p>
            <p><a href="/">Voltar ao site</a></p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Read config
if (!file_exists(__DIR__ . '/config.php')) {
    http_response_code(500);
    die('Erro: config.php não encontrado');
}

require_once __DIR__ . '/config.php';

try {
    $db = getDB();
    
    // First: Create all tables if they don't exist
    $db->exec('SET NAMES utf8mb4');
    $db->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    // Admin users table
    $db->exec('
        CREATE TABLE IF NOT EXISTS `admin_users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ');
    
    // Site settings table
    $db->exec('
        CREATE TABLE IF NOT EXISTS `settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `setting_key` VARCHAR(100) NOT NULL UNIQUE,
            `setting_value` TEXT,
            `setting_group` VARCHAR(50) DEFAULT "geral",
            `setting_label` VARCHAR(200),
            `setting_type` VARCHAR(20) DEFAULT "text",
            `sort_order` INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ');
    
    // Services table
    $db->exec('
        CREATE TABLE IF NOT EXISTS `services` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(200) NOT NULL,
            `description` TEXT,
            `icon` VARCHAR(50) DEFAULT "fas fa-paw",
            `image` VARCHAR(255),
            `whatsapp_text` VARCHAR(500),
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ');
    
    // Team table
    $db->exec('
        CREATE TABLE IF NOT EXISTS `team` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(200) NOT NULL,
            `role` VARCHAR(200),
            `description` TEXT,
            `image` VARCHAR(255),
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ');
    
    // Testimonials table
    $db->exec('
        CREATE TABLE IF NOT EXISTS `testimonials` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(200) NOT NULL,
            `initials` VARCHAR(5),
            `color` VARCHAR(20) DEFAULT "#4285f4",
            `rating` DECIMAL(2,1) DEFAULT 5.0,
            `text` TEXT NOT NULL,
            `date_label` VARCHAR(50),
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ');
    
    // Articles table
    $db->exec('
        CREATE TABLE IF NOT EXISTS `articles` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(300) NOT NULL,
            `slug` VARCHAR(300) NOT NULL UNIQUE,
            `excerpt` TEXT,
            `content` LONGTEXT,
            `image` VARCHAR(255),
            `category` VARCHAR(100),
            `author` VARCHAR(200) DEFAULT "Dra. Samla Cristie",
            `read_time` VARCHAR(20) DEFAULT "5 min",
            `featured` TINYINT(1) DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `views` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ');
    
    // FAQ table
    $db->exec('
        CREATE TABLE IF NOT EXISTS `faq` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `category` VARCHAR(100) NOT NULL,
            `category_icon` VARCHAR(50) DEFAULT "fas fa-paw",
            `question` VARCHAR(500) NOT NULL,
            `answer` TEXT NOT NULL,
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ');
    
    // Specialties table
    $db->exec('
        CREATE TABLE IF NOT EXISTS `specialties` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(200) NOT NULL,
            `icon` VARCHAR(50) DEFAULT "fas fa-star",
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ');
    
    // Clients table
    $db->exec('
        CREATE TABLE IF NOT EXISTS `clients` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(200) NOT NULL,
            `type` VARCHAR(100),
            `description` TEXT,
            `logo_icon` VARCHAR(50) DEFAULT "fas fa-hospital",
            `logo_color` VARCHAR(20) DEFAULT "#2d5016",
            `location` VARCHAR(200),
            `sort_order` INT DEFAULT 0,
            `active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ');
    
    $db->exec('SET FOREIGN_KEY_CHECKS = 1');
    
    // Now setup admin user
    $adminUsername = 'admin';
    $adminPassword = 'Ag570411@2026';
    $adminName = 'Administrador';
    
    // Hash the password
    $passwordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
    
    // Insert or update admin user
    $checkUser = $db->prepare('SELECT id FROM admin_users WHERE username = ?');
    $checkUser->execute(['admin']);
    
    if ($checkUser->rowCount() === 0) {
        $stmtAdmin = $db->prepare('INSERT INTO admin_users (username, password, name) VALUES (?, ?, ?)');
        $stmtAdmin->execute([$adminUsername, $passwordHash, $adminName]);
    } else {
        $stmtAdmin = $db->prepare('UPDATE admin_users SET password = ?, name = ?, updated_at = NOW() WHERE username = ?');
        $stmtAdmin->execute([$passwordHash, $adminName, $adminUsername]);
    }
    
    // Insert default settings
    $defaultSettings = [
        'site_name' => ['Dra. Samla Cristie', 'Nome do Site'],
        'site_email' => ['contato@samlavet.com', 'Email de Contato'],
        'site_phone' => ['(11) 99999-9999', 'Telefone'],
        'site_whatsapp' => ['5511999999999', 'WhatsApp'],
        'site_address' => ['Endereço da Clínica', 'Endereço'],
        'site_logo_text' => ['Dra. Samla Cristie', 'Texto do Logo'],
        'site_description' => ['Clínica Veterinária Dra. Samla Cristie', 'Descrição do Site'],
        'site_theme' => ['light', 'Tema'],
    ];
    
    $stmtSettings = $db->prepare('
        INSERT INTO settings (setting_key, setting_value, setting_group, setting_label, setting_type)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ');
    
    foreach ($defaultSettings as $key => [$value, $label]) {
        $stmtSettings->execute([$key, $value, 'geral', $label, 'text']);
    }
    
    // Create lock file
    file_put_contents($lockFile, date('Y-m-d H:i:s'));
    
    $success = true;
    $message = "Admin configurado com sucesso!";
    
} catch (Exception $e) {
    $success = false;
    $message = "Erro: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamyCastro - Auto Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
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
            padding: 50px;
            text-align: center;
        }
        .logo { font-size: 60px; margin-bottom: 20px; }
        h1 { color: #2d5016; margin-bottom: 20px; font-size: 28px; }
        .status {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 16px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #004085;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 14px;
            line-height: 1.6;
        }
        .credentials {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 13px;
            text-align: left;
        }
        .credentials p { margin: 8px 0; }
        .label { color: #666; }
        .value { color: #000; font-weight: bold; }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        a:hover { transform: translateY(-2px); }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 13px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🐾</div>
        <h1>SamyCastro Setup</h1>
        
        <?php if ($success): ?>
            <div class="status success">
                ✅ <?php echo $message; ?>
            </div>
            
            <div class="info-box">
                <strong>Sistema configurado com sucesso!</strong><br>
                O usuário admin foi criado com a senha fornecida.
            </div>
            
            <div class="credentials">
                <p><span class="label">👤 Usuário:</span> <span class="value">admin</span></p>
                <p><span class="label">🔐 Senha:</span> <span class="value">Ag570411@2026</span></p>
            </div>
            
            <div class="warning">
                <strong>⚠️ IMPORTANTE:</strong><br>
                1. Delete os arquivos <code>setup.php</code>, <code>auto-setup.php</code> e <code>install.php</code><br>
                2. Acesse <code>/admin/</code> e altere a senha padrão<br>
                3. Verifique as configurações do site
            </div>
            
            <a href="/admin/">Acessar Painel Admin →</a>
            
        <?php else: ?>
            <div class="status error">
                ❌ Erro: <?php echo $message; ?>
            </div>
            <p><a href="/">Voltar</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
