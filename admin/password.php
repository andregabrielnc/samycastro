<?php
require_once 'auth.php';
$db = getDB(); $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    $stmt = $db->prepare("SELECT password FROM admin_users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch();
    if (!password_verify($current, $user['password'])) { $msg = 'error:Senha atual incorreta.'; }
    elseif (strlen($new) < 6) { $msg = 'error:A nova senha deve ter no mínimo 6 caracteres.'; }
    elseif ($new !== $confirm) { $msg = 'error:As senhas não conferem.'; }
    else {
        $db->prepare("UPDATE admin_users SET password = ? WHERE id = ?")->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['admin_id']]);
        $msg = 'success';
    }
}
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Alterar Senha - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"><link rel="stylesheet" href="admin.css"></head><body>
<div class="admin-wrapper"><?php include 'sidebar.php'; ?>
<div class="main-content"><div class="topbar"><div style="display:flex;align-items:center;gap:12px;"><button class="mobile-sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button><h2><i class="fas fa-key"></i> Alterar Senha</h2></div></div>
<div class="content">
<?php if($msg==='success'):?><div class="alert alert-success"><i class="fas fa-check"></i> Senha alterada com sucesso!</div>
<?php elseif($msg):?><div class="alert alert-error"><i class="fas fa-times"></i> <?=e(str_replace('error:','',$msg))?></div><?php endif;?>
<div class="admin-card" style="max-width:500px;">
<form method="POST">
<div class="form-group"><label>Senha Atual</label><input type="password" name="current_password" required></div>
<div class="form-group"><label>Nova Senha</label><input type="password" name="new_password" required minlength="6"></div>
<div class="form-group"><label>Confirmar Nova Senha</label><input type="password" name="confirm_password" required></div>
<div class="form-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Alterar Senha</button></div>
</form></div></div></div></div></body></html>
