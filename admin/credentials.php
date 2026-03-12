<?php
require_once 'auth.php';
$msg = '';
$configFile = __DIR__ . '/../config.php';
$configContent = file_get_contents($configFile);

// Extract current values
preg_match("/define\('DB_HOST',\s*'([^']*)'\)/", $configContent, $m1);
preg_match("/define\('DB_NAME',\s*'([^']*)'\)/", $configContent, $m2);
preg_match("/define\('DB_USER',\s*'([^']*)'\)/", $configContent, $m3);
preg_match("/define\('DB_PASS',\s*'([^']*)'\)/", $configContent, $m4);
$current = ['host'=>$m1[1]??'localhost','name'=>$m2[1]??'samlavet','user'=>$m3[1]??'root','pass'=>$m4[1]??''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newHost = $_POST['db_host'];
    $newName = $_POST['db_name'];
    $newUser = $_POST['db_user'];
    $newPass = $_POST['db_pass'];

    // Test connection
    try {
        $testPdo = new PDO("mysql:host={$newHost};dbname={$newName};charset=utf8mb4", $newUser, $newPass);
        $testPdo = null;

        // Update config.php
        $configContent = preg_replace("/define\('DB_HOST',\s*'[^']*'\)/", "define('DB_HOST', " . var_export($newHost, true) . ")", $configContent);
        $configContent = preg_replace("/define\('DB_NAME',\s*'[^']*'\)/", "define('DB_NAME', " . var_export($newName, true) . ")", $configContent);
        $configContent = preg_replace("/define\('DB_USER',\s*'[^']*'\)/", "define('DB_USER', " . var_export($newUser, true) . ")", $configContent);
        $configContent = preg_replace("/define\('DB_PASS',\s*'[^']*'\)/", "define('DB_PASS', " . var_export($newPass, true) . ")", $configContent);
        file_put_contents($configFile, $configContent);

        $current = ['host'=>$newHost,'name'=>$newName,'user'=>$newUser,'pass'=>$newPass];
        $msg = 'success';
    } catch (PDOException $e) {
        $msg = 'Erro: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Banco de Dados - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"><link rel="stylesheet" href="admin.css"></head><body>
<div class="admin-wrapper"><?php include 'sidebar.php'; ?>
<div class="main-content"><div class="topbar"><div style="display:flex;align-items:center;gap:12px;"><button class="mobile-sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button><h2><i class="fas fa-database"></i> Credenciais do Banco de Dados</h2></div></div>
<div class="content">
<?php if($msg==='success'):?><div class="alert alert-success"><i class="fas fa-check"></i> Credenciais atualizadas com sucesso! Conexão testada e funcionando.</div>
<?php elseif($msg):?><div class="alert alert-error"><i class="fas fa-times"></i> <?=e($msg)?></div><?php endif;?>
<div class="admin-card">
    <h3 style="margin-bottom:20px;"><i class="fas fa-server"></i> Configuração MySQL</h3>
    <p style="color:#888;font-size:0.88rem;margin-bottom:24px;">As credenciais serão testadas antes de salvar. Se a conexão falhar, as configurações anteriores serão mantidas.</p>
    <form method="POST">
        <div class="form-grid">
            <div class="form-group"><label>Host</label><input type="text" name="db_host" value="<?=e($current['host'])?>" required></div>
            <div class="form-group"><label>Nome do Banco</label><input type="text" name="db_name" value="<?=e($current['name'])?>" required></div>
            <div class="form-group"><label>Usuário</label><input type="text" name="db_user" value="<?=e($current['user'])?>" required></div>
            <div class="form-group"><label>Senha</label><input type="password" name="db_pass" value="<?=e($current['pass'])?>"></div>
        </div>
        <div class="form-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Testar e Salvar</button></div>
    </form>
</div></div></div></div></body></html>
