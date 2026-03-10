<?php
require_once 'auth.php';
$db = getDB();
$msg = '';
$editItem = null;
$table = 'services';
$fields = ['title','description','icon','image','whatsapp_text','sort_order','active'];
$title = 'Serviços';

if (isset($_GET['delete'])) { $db->prepare("DELETE FROM {$table} WHERE id = ?")->execute([$_GET['delete']]); header("Location: services.php?msg=ok"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $data = [];
    foreach ($fields as $f) $data[$f] = $_POST[$f] ?? '';
    $data['active'] = isset($_POST['active']) ? 1 : 0;
    if (!empty($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $allowedMime = ['image/jpeg','image/png','image/gif','image/webp'];
        $mime = mime_content_type($_FILES['image_file']['tmp_name']);
        $allowedExt = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        if (in_array($mime, $allowedMime) && in_array($ext, $allowedExt)) {
            $nm = 'svc_'.time().'.'.$ext;
            if (!is_dir(__DIR__.'/../uploads')) mkdir(__DIR__.'/../uploads',0755,true);
            move_uploaded_file($_FILES['image_file']['tmp_name'], __DIR__.'/../uploads/'.$nm);
            $data['image'] = 'uploads/'.$nm;
        }
    }
    if ($id) {
        $sets = implode(', ', array_map(fn($f)=>"$f=?", $fields));
        $db->prepare("UPDATE {$table} SET {$sets} WHERE id=?")->execute([...array_map(fn($f)=>$data[$f], $fields), $id]);
    } else {
        $cols = implode(',', $fields);
        $phs = implode(',', array_fill(0, count($fields), '?'));
        $db->prepare("INSERT INTO {$table} ({$cols}) VALUES ({$phs})")->execute(array_map(fn($f)=>$data[$f], $fields));
    }
    header("Location: services.php?msg=ok"); exit;
}

if (isset($_GET['edit'])) { $stmt = $db->prepare("SELECT * FROM {$table} WHERE id=?"); $stmt->execute([$_GET['edit']]); $editItem = $stmt->fetch(); }
if (isset($_GET['new'])) $editItem = ['id'=>'','title'=>'','description'=>'','icon'=>'fas fa-paw','image'=>'','whatsapp_text'=>'','sort_order'=>0,'active'=>1];
$items = $db->query("SELECT * FROM {$table} ORDER BY sort_order")->fetchAll();
if (isset($_GET['msg'])) $msg = 'Operação realizada!';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar"><div style="display:flex;align-items:center;gap:12px;"><button class="mobile-sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button><h2><i class="fas fa-stethoscope"></i> <?= $title ?></h2></div></div>
        <div class="content">
            <?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check"></i> <?= e($msg) ?></div><?php endif; ?>
            <?php if ($editItem !== null): ?>
            <div class="admin-card">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= e($editItem['id']) ?>">
                    <div class="form-grid">
                        <div class="form-group"><label>Título</label><input type="text" name="title" value="<?= e($editItem['title']) ?>" required></div>
                        <div class="form-group"><label>Ícone (FontAwesome)</label><input type="text" name="icon" value="<?= e($editItem['icon']) ?>"></div>
                        <div class="form-group form-full"><label>Descrição</label><textarea name="description" rows="4"><?= e($editItem['description']) ?></textarea></div>
                        <div class="form-group"><label>Imagem</label>
                            <input type="text" name="image" value="<?= e($editItem['image']) ?>">
                            <?php if ($editItem['image']): ?><img src="../<?= e($editItem['image']) ?>" class="img-preview"><?php endif; ?>
                            <input type="file" name="image_file" accept="image/*" style="margin-top:8px;">
                        </div>
                        <div class="form-group"><label>Texto WhatsApp</label><input type="text" name="whatsapp_text" value="<?= e($editItem['whatsapp_text']) ?>"></div>
                        <div class="form-group"><label>Ordem</label><input type="number" name="sort_order" value="<?= e($editItem['sort_order']) ?>"></div>
                        <div class="form-group"><label style="margin-bottom:10px;">Status</label><label style="display:flex;align-items:center;gap:8px;font-size:0.9rem;text-transform:none;letter-spacing:0;"><input type="checkbox" name="active" <?= $editItem['active']?'checked':'' ?>> Ativo</label></div>
                    </div>
                    <div class="form-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Salvar</button><a href="services.php" class="btn-cancel">Cancelar</a></div>
                </form>
            </div>
            <?php else: ?>
            <div class="admin-card">
                <div class="admin-card-header"><h3><?= count($items) ?> serviços</h3><a href="services.php?new=1" class="btn-add"><i class="fas fa-plus"></i> Novo</a></div>
                <table class="admin-table"><thead><tr><th>Img</th><th>Título</th><th>Ordem</th><th>Status</th><th>Ações</th></tr></thead><tbody>
                <?php foreach ($items as $i): ?>
                <tr><td><img src="../<?= e($i['image']) ?>"></td><td><?= e($i['title']) ?></td><td><?= $i['sort_order'] ?></td><td><?= $i['active']?'Ativo':'Inativo' ?></td>
                <td><a href="services.php?edit=<?= $i['id'] ?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a> <a href="services.php?delete=<?= $i['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Excluir?')"><i class="fas fa-trash"></i></a></td></tr>
                <?php endforeach; ?></tbody></table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body></html>
