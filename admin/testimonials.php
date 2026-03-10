<?php
require_once 'auth.php';
$db = getDB(); $msg = ''; $editItem = null;
if (isset($_GET['delete'])) { $db->prepare("DELETE FROM testimonials WHERE id=?")->execute([$_GET['delete']]); header("Location: testimonials.php?msg=ok"); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id=$_POST['id']??'';$name=$_POST['name'];$initials=$_POST['initials'];$color=$_POST['color'];$rating=$_POST['rating'];$text=$_POST['text'];$date_label=$_POST['date_label'];$order=$_POST['sort_order']??0;$active=isset($_POST['active'])?1:0;
    if ($id) $db->prepare("UPDATE testimonials SET name=?,initials=?,color=?,rating=?,text=?,date_label=?,sort_order=?,active=? WHERE id=?")->execute([$name,$initials,$color,$rating,$text,$date_label,$order,$active,$id]);
    else $db->prepare("INSERT INTO testimonials (name,initials,color,rating,text,date_label,sort_order,active) VALUES(?,?,?,?,?,?,?,?)")->execute([$name,$initials,$color,$rating,$text,$date_label,$order,$active]);
    header("Location: testimonials.php?msg=ok"); exit;
}
if (isset($_GET['edit'])){$s=$db->prepare("SELECT * FROM testimonials WHERE id=?");$s->execute([$_GET['edit']]);$editItem=$s->fetch();}
if (isset($_GET['new'])) $editItem=['id'=>'','name'=>'','initials'=>'','color'=>'#4285f4','rating'=>5,'text'=>'','date_label'=>'','sort_order'=>0,'active'=>1];
$items=$db->query("SELECT * FROM testimonials ORDER BY sort_order")->fetchAll();
if(isset($_GET['msg']))$msg='Operação realizada!';
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Depoimentos - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"><link rel="stylesheet" href="admin.css"></head><body>
<div class="admin-wrapper"><?php include 'sidebar.php'; ?>
<div class="main-content"><div class="topbar"><div style="display:flex;align-items:center;gap:12px;"><button class="mobile-sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button><h2><i class="fas fa-star"></i> Depoimentos</h2></div></div>
<div class="content">
<?php if($msg):?><div class="alert alert-success"><i class="fas fa-check"></i> <?=e($msg)?></div><?php endif;?>
<?php if($editItem!==null):?>
<div class="admin-card"><form method="POST"><input type="hidden" name="id" value="<?=e($editItem['id'])?>">
<div class="form-grid">
<div class="form-group"><label>Nome</label><input type="text" name="name" value="<?=e($editItem['name'])?>" required></div>
<div class="form-group"><label>Iniciais (avatar)</label><input type="text" name="initials" value="<?=e($editItem['initials'])?>" maxlength="3"></div>
<div class="form-group"><label>Cor do Avatar</label><input type="color" name="color" value="<?=e($editItem['color'])?>"></div>
<div class="form-group"><label>Nota (1-5)</label><input type="number" name="rating" value="<?=e($editItem['rating'])?>" min="1" max="5" step="0.5"></div>
<div class="form-group form-full"><label>Depoimento</label><textarea name="text" rows="3"><?=e($editItem['text'])?></textarea></div>
<div class="form-group"><label>Data (ex: há 2 semanas)</label><input type="text" name="date_label" value="<?=e($editItem['date_label'])?>"></div>
<div class="form-group"><label>Ordem</label><input type="number" name="sort_order" value="<?=e($editItem['sort_order'])?>"></div>
<div class="form-group"><label style="margin-bottom:10px;">Status</label><label style="display:flex;align-items:center;gap:8px;font-size:0.9rem;text-transform:none;letter-spacing:0;"><input type="checkbox" name="active" <?=$editItem['active']?'checked':''?>> Ativo</label></div>
</div><div class="form-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Salvar</button><a href="testimonials.php" class="btn-cancel">Cancelar</a></div></form></div>
<?php else:?>
<div class="admin-card"><div class="admin-card-header"><h3><?=count($items)?> depoimentos</h3><a href="testimonials.php?new=1" class="btn-add"><i class="fas fa-plus"></i> Novo</a></div>
<div class="table-responsive"><table class="admin-table"><thead><tr><th>Avatar</th><th>Nome</th><th>Nota</th><th>Ações</th></tr></thead><tbody>
<?php foreach($items as $i):?><tr><td><div style="width:36px;height:36px;border-radius:50%;background:<?=e($i['color'])?>;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;"><?=e($i['initials'])?></div></td><td><?=e($i['name'])?></td><td><?=$i['rating']?> ⭐</td>
<td><a href="testimonials.php?edit=<?=$i['id']?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a> <a href="testimonials.php?delete=<?=$i['id']?>" class="btn-sm btn-delete" onclick="return confirm('Excluir?')"><i class="fas fa-trash"></i></a></td></tr>
<?php endforeach;?></tbody></table></div></div>
<?php endif;?></div></div></div></body></html>
