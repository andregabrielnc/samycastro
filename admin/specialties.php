<?php
require_once 'auth.php';
$db = getDB(); $msg = ''; $editItem = null;

// Delete via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) { die('Token CSRF inválido'); }
    $db->prepare("DELETE FROM specialties WHERE id=?")->execute([$_POST['delete_id']]);
    header("Location: specialties.php?msg=ok"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) { die('Token CSRF inválido'); }
    $id=$_POST['id']??'';$name=$_POST['name'];$icon=$_POST['icon'];$order=$_POST['sort_order']??0;$active=isset($_POST['active'])?1:0;
    if ($id) $db->prepare("UPDATE specialties SET name=?,icon=?,sort_order=?,active=? WHERE id=?")->execute([$name,$icon,$order,$active,$id]);
    else $db->prepare("INSERT INTO specialties (name,icon,sort_order,active) VALUES(?,?,?,?)")->execute([$name,$icon,$order,$active]);
    header("Location: specialties.php?msg=ok"); exit;
}
if (isset($_GET['edit'])){$s=$db->prepare("SELECT * FROM specialties WHERE id=?");$s->execute([$_GET['edit']]);$editItem=$s->fetch();}
if (isset($_GET['new'])) $editItem=['id'=>'','name'=>'','icon'=>'fas fa-star','sort_order'=>0,'active'=>1];
$items=$db->query("SELECT * FROM specialties ORDER BY sort_order")->fetchAll();
if(isset($_GET['msg']))$msg='Operação realizada!';
$csrf = generateCsrfToken();
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Especialidades - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"><link rel="stylesheet" href="admin.css"></head><body>
<div class="admin-wrapper"><?php include 'sidebar.php'; ?>
<div class="main-content"><div class="topbar"><div style="display:flex;align-items:center;gap:12px;"><button class="mobile-sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button><h2><i class="fas fa-award"></i> Especialidades</h2></div></div>
<div class="content">
<?php if($msg):?><div class="alert alert-success"><i class="fas fa-check"></i> <?=e($msg)?></div><?php endif;?>
<?php if($editItem!==null):?>
<div class="admin-card"><form method="POST">
<input type="hidden" name="csrf_token" value="<?=e($csrf)?>">
<input type="hidden" name="id" value="<?=e($editItem['id'])?>">
<div class="form-grid">
<div class="form-group"><label>Nome</label><input type="text" name="name" value="<?=e($editItem['name'])?>" required></div>
<div class="form-group"><label>Ícone (FontAwesome)</label>
<div style="display:flex;align-items:center;gap:10px;">
    <div class="icon-picker-preview" id="iconPreview"><i class="<?=e($editItem['icon'])?>"></i></div>
    <input type="text" name="icon" id="iconInput" value="<?=e($editItem['icon'])?>" placeholder="Buscar ícone...">
</div>
<div class="icon-picker-grid" id="iconGrid" data-icon-picker="icon"></div>
</div>
<div class="form-group"><label>Ordem</label><input type="number" name="sort_order" value="<?=e($editItem['sort_order'])?>"></div>
<div class="form-group"><label style="margin-bottom:10px;">Status</label><label class="toggle-switch"><input type="checkbox" name="active" <?=$editItem['active']?'checked':''?>><span class="toggle-track"></span><span class="toggle-label-on">Ativo</span><span class="toggle-label-off">Inativo</span></label></div>
</div><div class="form-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Salvar</button><a href="specialties.php" class="btn-cancel">Cancelar</a></div></form></div>
<?php else:?>
<div class="admin-card"><div class="admin-card-header"><h3><?=count($items)?> especialidades</h3><a href="specialties.php?new=1" class="btn-add"><i class="fas fa-plus"></i> Nova</a></div>
<div class="table-responsive"><table class="admin-table"><thead><tr><th>Ícone</th><th>Nome</th><th>Ordem</th><th>Ações</th></tr></thead><tbody>
<?php foreach($items as $i):?><tr><td><i class="<?=e($i['icon'])?>"></i></td><td><?=e($i['name'])?></td><td><?=$i['sort_order']?></td>
<td>
    <a href="specialties.php?edit=<?=$i['id']?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
    <form method="POST" style="display:inline;" onsubmit="return confirm('Excluir?')">
        <input type="hidden" name="csrf_token" value="<?=e($csrf)?>">
        <input type="hidden" name="delete_id" value="<?=$i['id']?>">
        <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
    </form>
</td></tr>
<?php endforeach;?></tbody></table></div></div>
<?php endif;?></div></div></div>
<script src="ui-components.js"></script>
</body></html>
