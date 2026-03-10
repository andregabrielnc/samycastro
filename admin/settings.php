<?php
require_once 'auth.php';
$db = getDB();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $settingKey = substr($key, 8);
            $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?")->execute([$value, $settingKey]);
        }
    }
    // Handle image uploads
    if (!empty($_FILES)) {
        foreach ($_FILES as $key => $file) {
            if ($file['error'] === UPLOAD_ERR_OK && strpos($key, 'file_') === 0) {
                $settingKey = substr($key, 5);
                $allowedMime = ['image/jpeg','image/png','image/gif','image/webp'];
                $mime = mime_content_type($file['tmp_name']);
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedExt = ['jpg','jpeg','png','gif','webp'];
                if (in_array($mime, $allowedMime) && in_array($ext, $allowedExt)) {
                    $newName = $settingKey . '_' . time() . '.' . $ext;
                    $dest = __DIR__ . '/../uploads/' . $newName;
                    if (!is_dir(__DIR__ . '/../uploads')) mkdir(__DIR__ . '/../uploads', 0755, true);
                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?")->execute(['uploads/' . $newName, $settingKey]);
                    }
                }
            }
        }
    }
    $msg = 'Configurações salvas com sucesso!';
}

$settings = $db->query("SELECT * FROM settings ORDER BY setting_group, sort_order")->fetchAll();
$groups = [];
foreach ($settings as $s) {
    $groups[$s['setting_group']][] = $s;
}
$groupLabels = ['hero'=>'🏠 Hero / Página Inicial','sobre'=>'👤 Sobre Mim','cta'=>'📢 Chamada para Ação','equipe'=>'👥 Equipe','contato'=>'📞 Contato e Rodapé','blog'=>'📝 Blog'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Admin</title>
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
                <h2><i class="fas fa-cog"></i> Configurações do Site</h2>
            </div>
            <div class="topbar-right"><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Site</a></div>
        </div>
        <div class="content">
            <?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check"></i> <?= e($msg) ?></div><?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <?php foreach ($groups as $group => $items): ?>
                <div class="admin-card settings-group">
                    <h3><?= $groupLabels[$group] ?? ucfirst($group) ?></h3>
                    <div class="form-grid">
                        <?php foreach ($items as $item): ?>
                        <div class="form-group <?= $item['setting_type']==='textarea'?'form-full':'' ?>">
                            <label><?= e($item['setting_label']) ?></label>
                            <?php if ($item['setting_type'] === 'textarea'): ?>
                                <textarea name="setting_<?= e($item['setting_key']) ?>" rows="3"><?= e($item['setting_value']) ?></textarea>
                            <?php elseif ($item['setting_type'] === 'image'): ?>
                                <input type="text" name="setting_<?= e($item['setting_key']) ?>" value="<?= e($item['setting_value']) ?>">
                                <?php if ($item['setting_value']): ?><img src="../<?= e($item['setting_value']) ?>" class="img-preview"><?php endif; ?>
                                <input type="file" name="file_<?= e($item['setting_key']) ?>" accept="image/*" style="margin-top:8px;">
                            <?php else: ?>
                                <input type="text" name="setting_<?= e($item['setting_key']) ?>" value="<?= e($item['setting_value']) ?>">
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="form-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Salvar Todas as Configurações</button></div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
