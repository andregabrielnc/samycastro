<?php
require_once 'auth.php';
$db = getDB(); $msg = ''; $editItem = null;
if (isset($_GET['delete'])) { $db->prepare("DELETE FROM clients WHERE id=?")->execute([$_GET['delete']]); header("Location: clients.php?msg=ok"); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id=$_POST['id']??'';$name=$_POST['name'];$type=$_POST['type'];$desc=$_POST['description'];$icon=$_POST['logo_icon'];$color=$_POST['logo_color'];$loc=$_POST['location'];$order=$_POST['sort_order']??0;$active=isset($_POST['active'])?1:0;
    if ($id) $db->prepare("UPDATE clients SET name=?,type=?,description=?,logo_icon=?,logo_color=?,location=?,sort_order=?,active=? WHERE id=?")->execute([$name,$type,$desc,$icon,$color,$loc,$order,$active,$id]);
    else $db->prepare("INSERT INTO clients (name,type,description,logo_icon,logo_color,location,sort_order,active) VALUES(?,?,?,?,?,?,?,?)")->execute([$name,$type,$desc,$icon,$color,$loc,$order,$active]);
    header("Location: clients.php?msg=ok"); exit;
}
if (isset($_GET['edit'])){$s=$db->prepare("SELECT * FROM clients WHERE id=?");$s->execute([$_GET['edit']]);$editItem=$s->fetch();}
if (isset($_GET['new'])) $editItem=['id'=>'','name'=>'','type'=>'','description'=>'','logo_icon'=>'fas fa-hospital','logo_color'=>'#2d5016','location'=>'','sort_order'=>0,'active'=>1];
$items=$db->query("SELECT * FROM clients ORDER BY sort_order")->fetchAll();
if(isset($_GET['msg']))$msg='Operação realizada!';
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Clientes - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"><link rel="stylesheet" href="admin.css"></head><body>
<div class="admin-wrapper"><?php include 'sidebar.php'; ?>
<div class="main-content"><div class="topbar"><div style="display:flex;align-items:center;gap:12px;"><button class="mobile-sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button><h2><i class="fas fa-handshake"></i> Clientes / Parceiros</h2></div></div>
<div class="content">
<?php if($msg):?><div class="alert alert-success"><i class="fas fa-check"></i> <?=e($msg)?></div><?php endif;?>
<?php if($editItem!==null):?>
<div class="admin-card"><form method="POST"><input type="hidden" name="id" value="<?=e($editItem['id'])?>">
<div class="form-grid">
<div class="form-group"><label>Nome</label><input type="text" name="name" value="<?=e($editItem['name'])?>" required></div>
<div class="form-group"><label>Tipo (ex: Clínica, Pet Shop)</label><input type="text" name="type" value="<?=e($editItem['type'])?>"></div>
<div class="form-group form-full"><label>Descrição</label><textarea name="description" rows="3"><?=e($editItem['description'])?></textarea></div>
<div class="form-group"><label>Ícone (FontAwesome)</label><input type="text" name="logo_icon" value="<?=e($editItem['logo_icon'])?>"></div>
<div class="form-group"><label>Cor do Ícone</label><input type="color" name="logo_color" value="<?=e($editItem['logo_color'])?>"></div>
<div class="form-group"><label>Localização</label><input type="text" name="location" value="<?=e($editItem['location'])?>"></div>
<div class="form-group"><label>Ordem</label><input type="number" name="sort_order" value="<?=e($editItem['sort_order'])?>"></div>
<div class="form-group"><label style="margin-bottom:10px;">Status</label><label style="display:flex;align-items:center;gap:8px;font-size:0.9rem;text-transform:none;letter-spacing:0;"><input type="checkbox" name="active" <?=$editItem['active']?'checked':''?>> Ativo</label></div>
</div><div class="form-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Salvar</button><a href="clients.php" class="btn-cancel">Cancelar</a></div></form></div>
<?php else:?>
<div class="admin-card"><div class="admin-card-header"><h3><?=count($items)?> clientes</h3><a href="clients.php?new=1" class="btn-add"><i class="fas fa-plus"></i> Novo</a></div>
<table class="admin-table"><thead><tr><th>Ícone</th><th>Nome</th><th>Tipo</th><th>Local</th><th>Ações</th></tr></thead><tbody>
<?php foreach($items as $i):?><tr>
<td><div style="width:36px;height:36px;border-radius:50%;background:<?=e($i['logo_color'])?>;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.9rem;"><i class="<?=e($i['logo_icon'])?>"></i></div></td>
<td><strong><?=e($i['name'])?></strong></td><td><?=e($i['type'])?></td><td><?=e($i['location'])?></td>
<td><a href="clients.php?edit=<?=$i['id']?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a> <a href="clients.php?delete=<?=$i['id']?>" class="btn-sm btn-delete" onclick="return confirm('Excluir?')"><i class="fas fa-trash"></i></a></td></tr>
<?php endforeach;?></tbody></table></div>
<?php endif;?></div></div></div></body></html>
