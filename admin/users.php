<?php
require_once 'auth.php';
$db = getDB();
$msg = '';
$msgType = 'success';
$editing = null;

// Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id === (int) ($_SESSION['admin_id'] ?? 0)) {
        $msg = 'Você não pode excluir seu próprio usuário.';
        $msgType = 'error';
    } else {
        $total = $db->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        if ($total <= 1) {
            $msg = 'Não é possível excluir o último usuário.';
            $msgType = 'error';
        } else {
            $stmt = $db->prepare("DELETE FROM admin_users WHERE id = ?");
            $stmt->execute([$id]);
            $msg = 'Usuário excluído com sucesso.';
        }
    }
}

// Edit - load data
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->execute([(int) $_GET['edit']]);
    $editing = $stmt->fetch();
}

// Save (create or update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $name === '') {
        $msg = 'Nome e usuário são obrigatórios.';
        $msgType = 'error';
    } else {
        // Check duplicate username
        $check = $db->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ?");
        $check->execute([$username, $id ?? 0]);
        if ($check->fetch()) {
            $msg = 'Este nome de usuário já está em uso.';
            $msgType = 'error';
        } else {
            if ($id) {
                // Update
                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE admin_users SET username = ?, name = ?, password = ? WHERE id = ?");
                    $stmt->execute([$username, $name, $hash, $id]);
                } else {
                    $stmt = $db->prepare("UPDATE admin_users SET username = ?, name = ? WHERE id = ?");
                    $stmt->execute([$username, $name, $id]);
                }
                $msg = 'Usuário atualizado com sucesso.';
            } else {
                // Create
                if ($password === '') {
                    $msg = 'A senha é obrigatória para novos usuários.';
                    $msgType = 'error';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("INSERT INTO admin_users (username, name, password) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $name, $hash]);
                    $msg = 'Usuário criado com sucesso.';
                }
            }
        }
    }
    $editing = null;
}

$users = $db->query("SELECT id, username, name, created_at, updated_at FROM admin_users ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Admin</title>
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
                <h2><i class="fas fa-user-cog"></i> Usuários</h2>
            </div>
        </div>
        <div class="content">
            <?php if ($msg): ?>
            <div class="alert alert-<?= $msgType ?>"><i class="fas fa-<?= $msgType === 'success' ? 'check' : 'times' ?>-circle"></i> <?= e($msg) ?></div>
            <?php endif; ?>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h3><i class="fas fa-<?= $editing ? 'edit' : 'user-plus' ?>"></i> <?= $editing ? 'Editar Usuário' : 'Novo Usuário' ?></h3>
                </div>
                <form method="POST">
                    <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?= $editing['id'] ?>">
                    <?php endif; ?>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" placeholder="Nome completo" required>
                        </div>
                        <div class="form-group">
                            <label>Usuário</label>
                            <input type="text" name="username" value="<?= e($editing['username'] ?? '') ?>" placeholder="Login de acesso" required>
                        </div>
                        <div class="form-group">
                            <label>Senha <?= $editing ? '(deixe em branco para manter)' : '' ?></label>
                            <input type="password" name="password" placeholder="<?= $editing ? 'Manter senha atual' : 'Senha de acesso' ?>" <?= $editing ? '' : 'required' ?>>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> <?= $editing ? 'Atualizar' : 'Criar Usuário' ?></button>
                        <?php if ($editing): ?>
                        <a href="users.php" class="btn-cancel"><i class="fas fa-times"></i> Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h3><i class="fas fa-list"></i> Usuários Cadastrados (<?= count($users) ?>)</h3>
                </div>
                <?php if (empty($users)): ?>
                <div class="empty-state"><i class="fas fa-users"></i><p>Nenhum usuário cadastrado.</p></div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead><tr><th>ID</th><th>Nome</th><th>Usuário</th><th>Criado em</th><th>Atualizado</th><th>Ações</th></tr></thead>
                        <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><strong><?= e($u['name']) ?></strong></td>
                            <td><?= e($u['username']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($u['updated_at'])) ?></td>
                            <td style="white-space:nowrap;">
                                <a href="?edit=<?= $u['id'] ?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i> Editar</a>
                                <?php if ($u['id'] !== ($_SESSION['admin_id'] ?? 0)): ?>
                                <a href="?delete=<?= $u['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Tem certeza que deseja excluir este usuário?')"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
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
</div>
</body>
</html>
