<?php
require_once 'auth.php';
$db = getDB(); $msg = ''; $editItem = null;

// Delete via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) { die('Token CSRF inválido'); }
    $db->prepare("DELETE FROM clients WHERE id=?")->execute([$_POST['delete_id']]);
    header("Location: clients.php?msg=ok"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) { die('Token CSRF inválido'); }
    $id=$_POST['id']??'';$name=$_POST['name'];$type=$_POST['type'];$desc=$_POST['description'];$icon=$_POST['logo_icon'];$color=$_POST['logo_color'];$loc=$_POST['location'];$order=$_POST['sort_order']??0;$active=isset($_POST['active'])?1:0;
    if ($id) $db->prepare("UPDATE clients SET name=?,type=?,description=?,logo_icon=?,logo_color=?,location=?,sort_order=?,active=? WHERE id=?")->execute([$name,$type,$desc,$icon,$color,$loc,$order,$active,$id]);
    else $db->prepare("INSERT INTO clients (name,type,description,logo_icon,logo_color,location,sort_order,active) VALUES(?,?,?,?,?,?,?,?)")->execute([$name,$type,$desc,$icon,$color,$loc,$order,$active]);
    header("Location: clients.php?msg=ok"); exit;
}
if (isset($_GET['edit'])){$s=$db->prepare("SELECT * FROM clients WHERE id=?");$s->execute([$_GET['edit']]);$editItem=$s->fetch();}
if (isset($_GET['new'])) $editItem=['id'=>'','name'=>'','type'=>'','description'=>'','logo_icon'=>'fas fa-hospital','logo_color'=>'#2d5016','location'=>'','sort_order'=>0,'active'=>1];
$items=$db->query("SELECT * FROM clients ORDER BY sort_order")->fetchAll();
if(isset($_GET['msg']))$msg='Operação realizada!';
$csrf = generateCsrfToken();
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Clientes - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"><link rel="stylesheet" href="admin.css"></head><body>
<div class="admin-wrapper"><?php include 'sidebar.php'; ?>
<div class="main-content"><div class="topbar"><div style="display:flex;align-items:center;gap:12px;"><button class="mobile-sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button><h2><i class="fas fa-handshake"></i> Clientes / Parceiros</h2></div></div>
<div class="content">
<?php if($msg):?><div class="alert alert-success"><i class="fas fa-check"></i> <?=e($msg)?></div><?php endif;?>
<?php if($editItem!==null):?>
<div class="admin-card"><form method="POST">
<input type="hidden" name="csrf_token" value="<?=e($csrf)?>">
<input type="hidden" name="id" value="<?=e($editItem['id'])?>">
<div class="form-grid">
<div class="form-group"><label>Nome</label><input type="text" name="name" value="<?=e($editItem['name'])?>" required></div>
<div class="form-group"><label>Tipo (ex: Clínica, Pet Shop)</label><input type="text" name="type" value="<?=e($editItem['type'])?>"></div>
<div class="form-group form-full"><label>Descrição</label><textarea name="description" rows="3"><?=e($editItem['description'])?></textarea></div>
<div class="form-group"><label>Ícone (FontAwesome)</label>
<div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
    <div id="iconPreview" style="width:44px;height:44px;border-radius:12px;background:#f0ede8;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#2d5016;border:1.5px solid #e5ddd0;"><i class="<?=e($editItem['logo_icon'])?>"></i></div>
    <input type="text" name="logo_icon" id="iconInput" value="<?=e($editItem['logo_icon'])?>" placeholder="Buscar ícone..." oninput="filterIcons(this.value)">
</div>
<div id="iconGrid" style="max-height:220px;overflow-y:auto;border:1.5px solid #e5ddd0;border-radius:10px;padding:10px;display:grid;grid-template-columns:repeat(auto-fill,minmax(42px,1fr));gap:6px;background:#faf8f4;"></div>
</div>
<div class="form-group"><label>Cor do Ícone</label><input type="color" name="logo_color" value="<?=e($editItem['logo_color'])?>"></div>
<div class="form-group"><label>Localização</label><input type="text" name="location" value="<?=e($editItem['location'])?>"></div>
<div class="form-group"><label>Ordem</label><input type="number" name="sort_order" value="<?=e($editItem['sort_order'])?>"></div>
<div class="form-group"><label style="margin-bottom:10px;">Status</label><label style="display:flex;align-items:center;gap:8px;font-size:0.9rem;text-transform:none;letter-spacing:0;"><input type="checkbox" name="active" <?=$editItem['active']?'checked':''?>> Ativo</label></div>
</div><div class="form-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Salvar</button><a href="clients.php" class="btn-cancel">Cancelar</a></div></form></div>
<?php else:?>
<div class="admin-card"><div class="admin-card-header"><h3><?=count($items)?> clientes</h3><a href="clients.php?new=1" class="btn-add"><i class="fas fa-plus"></i> Novo</a></div>
<div class="table-responsive"><table class="admin-table"><thead><tr><th>Ícone</th><th>Nome</th><th>Tipo</th><th>Local</th><th>Ações</th></tr></thead><tbody>
<?php foreach($items as $i):?><tr>
<td><div style="width:36px;height:36px;border-radius:50%;background:<?=e($i['logo_color'])?>;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.9rem;"><i class="<?=e($i['logo_icon'])?>"></i></div></td>
<td><strong><?=e($i['name'])?></strong></td><td><?=e($i['type'])?></td><td><?=e($i['location'])?></td>
<td>
    <a href="clients.php?edit=<?=$i['id']?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
    <form method="POST" style="display:inline;" onsubmit="return confirm('Excluir?')">
        <input type="hidden" name="csrf_token" value="<?=e($csrf)?>">
        <input type="hidden" name="delete_id" value="<?=$i['id']?>">
        <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
    </form>
</td></tr>
<?php endforeach;?></tbody></table></div></div>
<?php endif;?></div></div></div>
<?php if($editItem!==null):?>
<script>
(function(){
var icons = [
    'fas fa-hospital','fas fa-clinic-medical','fas fa-stethoscope','fas fa-heartbeat','fas fa-paw',
    'fas fa-dog','fas fa-cat','fas fa-horse','fas fa-dove','fas fa-kiwi-bird','fas fa-fish','fas fa-spider',
    'fas fa-bone','fas fa-syringe','fas fa-pills','fas fa-capsules','fas fa-prescription-bottle',
    'fas fa-briefcase-medical','fas fa-first-aid','fas fa-ambulance','fas fa-medkit','fas fa-band-aid',
    'fas fa-teeth','fas fa-tooth','fas fa-x-ray','fas fa-microscope','fas fa-vial','fas fa-vials',
    'fas fa-dna','fas fa-flask','fas fa-thermometer-half','fas fa-weight','fas fa-eye','fas fa-ear-listen',
    'fas fa-hand-holding-heart','fas fa-hands-holding','fas fa-handshake','fas fa-house',
    'fas fa-store','fas fa-shop','fas fa-building','fas fa-city','fas fa-warehouse',
    'fas fa-truck','fas fa-car','fas fa-location-dot','fas fa-map-marker-alt','fas fa-phone',
    'fas fa-envelope','fas fa-globe','fas fa-wifi','fas fa-shield-halved','fas fa-lock',
    'fas fa-star','fas fa-heart','fas fa-circle-check','fas fa-award','fas fa-trophy','fas fa-medal',
    'fas fa-crown','fas fa-gem','fas fa-certificate','fas fa-ribbon','fas fa-thumbs-up',
    'fas fa-users','fas fa-user','fas fa-user-doctor','fas fa-user-nurse','fas fa-people-group',
    'fas fa-scissors','fas fa-shower','fas fa-bath','fas fa-soap','fas fa-spray-can',
    'fas fa-basket-shopping','fas fa-cart-shopping','fas fa-bag-shopping','fas fa-box','fas fa-boxes-stacked',
    'fas fa-leaf','fas fa-seedling','fas fa-tree','fas fa-sun','fas fa-moon','fas fa-cloud',
    'fas fa-droplet','fas fa-fire','fas fa-bolt','fas fa-snowflake',
    'fas fa-utensils','fas fa-mug-hot','fas fa-wheat-awn','fas fa-apple-whole','fas fa-carrot',
    'fas fa-camera','fas fa-image','fas fa-video','fas fa-music','fas fa-palette',
    'fas fa-graduation-cap','fas fa-book','fas fa-chalkboard-user','fas fa-school',
    'fas fa-wrench','fas fa-screwdriver-wrench','fas fa-gear','fas fa-gears','fas fa-hammer',
    'fas fa-cross','fas fa-plus','fas fa-circle-plus','fas fa-square-plus',
    'fas fa-chart-line','fas fa-chart-bar','fas fa-chart-pie',
    'fas fa-money-bill','fas fa-coins','fas fa-credit-card','fas fa-wallet',
    'fas fa-clock','fas fa-calendar','fas fa-bell','fas fa-flag','fas fa-bookmark',
    'fas fa-comment','fas fa-comments','fas fa-quote-left','fas fa-bullhorn',
    'fas fa-link','fas fa-paperclip','fas fa-file','fas fa-folder','fas fa-database',
    'fas fa-code','fas fa-terminal','fas fa-laptop','fas fa-desktop','fas fa-mobile-screen',
    'fas fa-print','fas fa-fax','fas fa-headset','fas fa-satellite-dish',
    'fas fa-plane','fas fa-ship','fas fa-bicycle','fas fa-motorcycle',
    'fas fa-puzzle-piece','fas fa-dice','fas fa-gamepad','fas fa-futbol','fas fa-baseball',
    'fas fa-recycle','fas fa-trash','fas fa-broom','fas fa-filter',
    'fas fa-circle-info','fas fa-circle-question','fas fa-circle-exclamation','fas fa-triangle-exclamation',
    'fab fa-instagram','fab fa-facebook','fab fa-whatsapp','fab fa-youtube','fab fa-tiktok','fab fa-twitter'
];

var grid = document.getElementById('iconGrid');
var input = document.getElementById('iconInput');
var preview = document.getElementById('iconPreview');
var currentValue = input.value;

function renderIcons(filter) {
    grid.innerHTML = '';
    var f = (filter || '').toLowerCase().replace('fas ','').replace('fab ','').replace('fa-','');
    icons.forEach(function(ic) {
        if (f && ic.toLowerCase().replace('fas ','').replace('fab ','').replace('fa-','').indexOf(f) === -1) return;
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.title = ic;
        btn.style.cssText = 'width:42px;height:42px;border:1.5px solid transparent;border-radius:8px;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#444;transition:all 0.15s;';
        if (ic === currentValue) btn.style.cssText += 'border-color:#2d5016;background:#e8f0e2;color:#2d5016;';
        btn.innerHTML = '<i class="' + ic + '"></i>';
        btn.onmouseover = function(){ if(ic!==currentValue) this.style.background='#f0ede8'; };
        btn.onmouseout = function(){ if(ic!==currentValue) this.style.background='#fff'; };
        btn.onclick = function(){
            currentValue = ic;
            input.value = ic;
            preview.innerHTML = '<i class="' + ic + '"></i>';
            renderIcons(f);
        };
        grid.appendChild(btn);
    });
}

window.filterIcons = function(val) {
    currentValue = val;
    preview.innerHTML = '<i class="' + val + '"></i>';
    renderIcons(val);
};

renderIcons('');
})();
</script>
<?php endif;?>
</body></html>
