<?php
require_once 'auth.php';
$db = getDB();
$msg = '';
$editItem = null;

// Delete
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM articles WHERE id = ?")->execute([$_GET['delete']]);
    header('Location: articles.php?msg=deleted'); exit;
}

// Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'];
    $slug = $_POST['slug'] ?: preg_replace('/[^a-z0-9]+/', '-', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $title)));
    $excerpt = $_POST['excerpt'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $author = $_POST['author'];
    $read_time = $_POST['read_time'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    $active = isset($_POST['active']) ? 1 : 0;
    $image = $_POST['current_image'] ?? '';

    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedMime = ['image/jpeg','image/png','image/gif','image/webp'];
        $mime = mime_content_type($_FILES['image']['tmp_name']);
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExt = ['jpg','jpeg','png','gif','webp'];
        if (in_array($mime, $allowedMime) && in_array($ext, $allowedExt)) {
            $newName = 'blog_' . time() . '.' . $ext;
            if (!is_dir(__DIR__.'/../uploads')) mkdir(__DIR__.'/../uploads', 0755, true);
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/../uploads/'.$newName);
            $image = 'uploads/' . $newName;
        }
    }

    if ($id) {
        $db->prepare("UPDATE articles SET title=?, slug=?, excerpt=?, content=?, image=?, category=?, author=?, read_time=?, featured=?, active=? WHERE id=?")
           ->execute([$title, $slug, $excerpt, $content, $image, $category, $author, $read_time, $featured, $active, $id]);
    } else {
        $db->prepare("INSERT INTO articles (title, slug, excerpt, content, image, category, author, read_time, featured, active) VALUES (?,?,?,?,?,?,?,?,?,?)")
           ->execute([$title, $slug, $excerpt, $content, $image, $category, $author, $read_time, $featured, $active]);
    }
    header('Location: articles.php?msg=saved'); exit;
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editItem = $stmt->fetch();
}
if (isset($_GET['new'])) $editItem = ['id'=>'','title'=>'','slug'=>'','excerpt'=>'','content'=>'','image'=>'','category'=>'','author'=>'Dra. Samla Cristie','read_time'=>'5 min','featured'=>0,'active'=>1];

$articles = $db->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll();
if (isset($_GET['msg'])) $msg = $_GET['msg']==='saved'?'Artigo salvo!':'Artigo excluído!';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artigos - Admin</title>
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
                <h2><i class="fas fa-newspaper"></i> <?= $editItem ? ($editItem['id'] ? 'Editar Artigo' : 'Novo Artigo') : 'Artigos do Blog' ?></h2>
            </div>
        </div>
        <div class="content">
            <?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check"></i> <?= e($msg) ?></div><?php endif; ?>

            <?php if ($editItem !== null): ?>
            <div class="admin-card">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= e($editItem['id']) ?>">
                    <input type="hidden" name="current_image" value="<?= e($editItem['image']) ?>">
                    <div class="form-grid">
                        <div class="form-group form-full"><label>Título</label><input type="text" name="title" value="<?= e($editItem['title']) ?>" required></div>
                        <div class="form-group"><label>Slug (URL)</label><input type="text" name="slug" value="<?= e($editItem['slug']) ?>" placeholder="Gerado automaticamente"></div>
                        <div class="form-group"><label>Categoria</label><input type="text" name="category" value="<?= e($editItem['category']) ?>" placeholder="Ex: Cães, Gatos, Equinos"></div>
                        <div class="form-group"><label>Autor</label><input type="text" name="author" value="<?= e($editItem['author']) ?>"></div>
                        <div class="form-group"><label>Tempo de Leitura</label><input type="text" name="read_time" value="<?= e($editItem['read_time']) ?>"></div>
                        <div class="form-group form-full"><label>Resumo</label><textarea name="excerpt" rows="2"><?= e($editItem['excerpt']) ?></textarea></div>
                        <div class="form-group form-full"><label>Conteúdo (HTML)</label><textarea name="content" rows="12" style="font-family:monospace;font-size:0.85rem;"><?= e($editItem['content']) ?></textarea></div>
                        <div class="form-group"><label>Imagem</label>
                            <?php if ($editItem['image']): ?><img src="../<?= e($editItem['image']) ?>" class="img-preview"><br><?php endif; ?>
                            <input type="file" name="image" accept="image/*" style="margin-top:8px;">
                            <br><small>Ou caminho: </small><input type="text" name="current_image" value="<?= e($editItem['image']) ?>" style="margin-top:4px;">
                        </div>
                        <div class="form-group">
                            <label>Opções</label>
                            <label style="display:flex;align-items:center;gap:8px;font-size:0.9rem;text-transform:none;letter-spacing:0;margin-top:8px;">
                                <input type="checkbox" name="active" <?= $editItem['active']?'checked':'' ?>> Ativo
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;font-size:0.9rem;text-transform:none;letter-spacing:0;margin-top:8px;">
                                <input type="checkbox" name="featured" <?= $editItem['featured']?'checked':'' ?>> Destaque
                            </label>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Salvar</button>
                        <a href="articles.php" class="btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3><?= count($articles) ?> artigos</h3>
                    <a href="articles.php?new=1" class="btn-add"><i class="fas fa-plus"></i> Novo Artigo</a>
                </div>
                <table class="admin-table">
                    <thead><tr><th>Img</th><th>Título</th><th>Categoria</th><th>Views</th><th>Status</th><th>Ações</th></tr></thead>
                    <tbody>
                    <?php foreach ($articles as $a): ?>
                    <tr>
                        <td><img src="../<?= e($a['image']) ?>" alt=""></td>
                        <td><strong><?= e($a['title']) ?></strong></td>
                        <td><?= e($a['category']) ?></td>
                        <td><?= $a['views'] ?></td>
                        <td><?= $a['active']?'<span style="color:#2e7d32">Ativo</span>':'<span style="color:#888">Inativo</span>' ?></td>
                        <td>
                            <a href="articles.php?edit=<?= $a['id'] ?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                            <a href="articles.php?delete=<?= $a['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Excluir este artigo?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
