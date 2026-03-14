<?php
require_once 'auth.php';
$db = getDB(); $msg = ''; $editItem = null;

// Delete via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) { die('Token CSRF inválido'); }
    $db->prepare("DELETE FROM team WHERE id=?")->execute([$_POST['delete_id']]);
    header("Location: team.php?msg=ok"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) { die('Token CSRF inválido'); }
    $id=$_POST['id']??''; $name=$_POST['name']; $role=$_POST['role']; $desc=$_POST['description']; $image=$_POST['image']??''; $order=$_POST['sort_order']??0; $active=isset($_POST['active'])?1:0;
    if (!empty($_FILES['image_file'])&&$_FILES['image_file']['error']===UPLOAD_ERR_OK) {
        $result = secureUpload($_FILES['image_file'], 'team');
        if (isset($result['success'])) { $image = $result['path']; }
    }
    if ($id) $db->prepare("UPDATE team SET name=?,role=?,description=?,image=?,sort_order=?,active=? WHERE id=?")->execute([$name,$role,$desc,$image,$order,$active,$id]);
    else $db->prepare("INSERT INTO team (name,role,description,image,sort_order,active) VALUES(?,?,?,?,?,?)")->execute([$name,$role,$desc,$image,$order,$active]);
    header("Location: team.php?msg=ok"); exit;
}
if (isset($_GET['edit'])){$s=$db->prepare("SELECT * FROM team WHERE id=?");$s->execute([$_GET['edit']]);$editItem=$s->fetch();}
if (isset($_GET['new'])) $editItem=['id'=>'','name'=>'','role'=>'','description'=>'','image'=>'','sort_order'=>0,'active'=>1];
$items=$db->query("SELECT * FROM team ORDER BY sort_order")->fetchAll();
if(isset($_GET['msg']))$msg='Operação realizada!';
$csrf = generateCsrfToken();
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Equipe - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"><link rel="stylesheet" href="admin.css"></head><body>
<div class="admin-wrapper"><?php include 'sidebar.php'; ?>
<div class="main-content">
<div class="topbar"><div style="display:flex;align-items:center;gap:12px;"><button class="mobile-sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button><h2><i class="fas fa-users"></i> Equipe</h2></div></div>
<div class="content">
<?php if($msg):?><div class="alert alert-success"><i class="fas fa-check"></i> <?=e($msg)?></div><?php endif;?>
<?php if($editItem!==null):?>
<div class="admin-card"><form method="POST" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?=e($csrf)?>">
<input type="hidden" name="id" value="<?=e($editItem['id'])?>">
<div class="form-grid">
<div class="form-group"><label>Nome</label><input type="text" name="name" value="<?=e($editItem['name'])?>" required></div>
<div class="form-group"><label>Cargo/Função</label><input type="text" name="role" value="<?=e($editItem['role'])?>"></div>
<div class="form-group form-full"><label>Descrição</label><textarea name="description" rows="3"><?=e($editItem['description'])?></textarea></div>
<div class="form-group"><label>Imagem</label><input type="text" name="image" value="<?=e($editItem['image'])?>">
<?php if($editItem['image']):?><img src="../<?=e($editItem['image'])?>" class="img-preview"><?php endif;?>
<input type="file" name="image_file" accept="image/*" style="margin-top:8px;"></div>
<div class="form-group"><label>Ordem</label><input type="number" name="sort_order" value="<?=e($editItem['sort_order'])?>"></div>
<div class="form-group"><label style="margin-bottom:10px;">Status</label><label style="display:flex;align-items:center;gap:8px;font-size:0.9rem;text-transform:none;letter-spacing:0;"><input type="checkbox" name="active" <?=$editItem['active']?'checked':''?>> Ativo</label></div>
</div><div class="form-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Salvar</button><a href="team.php" class="btn-cancel">Cancelar</a></div></form></div>
<?php else:?>
<div class="admin-card"><div class="admin-card-header"><h3><?=count($items)?> membros</h3><a href="team.php?new=1" class="btn-add"><i class="fas fa-plus"></i> Novo</a></div>
<div class="table-responsive"><table class="admin-table"><thead><tr><th>Img</th><th>Nome</th><th>Cargo</th><th>Ações</th></tr></thead><tbody>
<?php foreach($items as $i):?><tr><td><img src="../<?=e($i['image'])?>"></td><td><?=e($i['name'])?></td><td><?=e($i['role'])?></td>
<td>
    <a href="team.php?edit=<?=$i['id']?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
    <form method="POST" style="display:inline;" onsubmit="return confirm('Excluir?')">
        <input type="hidden" name="csrf_token" value="<?=e($csrf)?>">
        <input type="hidden" name="delete_id" value="<?=$i['id']?>">
        <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
    </form>
</td></tr>
<?php endforeach;?></tbody></table></div></div>
<?php endif;?></div></div></div></body></html>
