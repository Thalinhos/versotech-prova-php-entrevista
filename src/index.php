<?php

session_start();
if (!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    header("Location: /seeder.php");
    exit;
}

require_once "userService.php";

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $colors = $_POST['colors'] ?? [];
    $userId = isset($_POST['id']) ? (int)$_POST['id'] : null;

    if ($name === '' || $email === '') {
        $error = "Nome e email são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email inválido.";
    } else {
        try {
            if ($userId) {
                updateUser($userId, $name, $email, $colors);
                $message = "Usuário atualizado com sucesso!";
            } else {
                createUser($name, $email, $colors);
                $message = "Usuário criado com sucesso!";
            }
        }
        catch (Exception $e) {
            $error = "Erro ao salvar usuário: " . $e->getMessage();
        } 
    }
}

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if (deleteUser($delId)) {
        $message = "Usuário excluído com sucesso!";
    } else {
        $error = "Erro ao excluir usuário.";
    }
    header("Location: index.php");
    exit;
}

$editUser = null;
if (isset($_GET['edit'])) {
    $editUser = getUserById((int)$_GET['edit']);
    if (!$editUser) {
        $error = "Usuário não encontrado.";
    }
}

$users = getAllUsers();
$colors = getAllColors();

// Implementação de ETag para cache
$etag = md5(json_encode($users) . json_encode($colors));

header("ETag: \"$etag\"");
header("Cache-Control: public");

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === "\"$etag\"") {
    http_response_code(304);
    exit;
}
// end ETag
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>CRUD Usuários com Cores</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .error { color: red; }
        .message { color: green; }
        form { margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; max-width: 700px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #eee; }
    </style>
</head>
<body>

<h1>CRUD Usuários com Vinculo de Cores</h1>

<?= htmlspecialchars(getenv('APP_NAME')?: 'nao deu bom'); ?>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<h2><?= $editUser ? "Editar Usuário" : "Novo Usuário" ?></h2>

<form method="post" action="index.php">
    <input type="hidden" name="id" value="<?= $editUser['id'] ?? '' ?>" />
    <div>
        <label>Nome:</label><br />
        <input type="text" name="name" value="<?= htmlspecialchars($editUser['name'] ?? '') ?>" required />
    </div>
    <div>
        <label>Email:</label><br />
        <input type="email" name="email" value="<?= htmlspecialchars($editUser['email'] ?? '') ?>" required />
    </div>
    <div>
        <label>Cores:</label><br />
        <?php
        $userColorsIds = $editUser ? array_column(getUserColors($editUser['id']), 'id') : [];
        foreach ($colors as $color):
            $checked = in_array($color['id'], $userColorsIds) ? 'checked' : '';
        ?>
            <label>
                <input type="checkbox" name="colors[]" value="<?= $color['id'] ?>" <?= $checked ?> />
                <?= htmlspecialchars($color['name']) ?>
            </label><br />
        <?php endforeach; ?>
    </div>
    <button type="submit"><?= $editUser ? "Atualizar" : "Cadastrar" ?></button>
    <?php if ($editUser): ?>
        <a href="index.php">Cancelar</a>
    <?php endif; ?>
</form>

<h2>Lista de Usuários</h2>
<table>
    <thead>
        <tr>
            <th>ID</th><th>Nome</th><th>Email</th><th>Cores</th><th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): 
        $userColors = getUserColors($user['id']);
        $colorNames = array_column($userColors, 'name');
    ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars(implode(', ', $colorNames)) ?></td>
            <td>
                <a href="index.php?edit=<?= $user['id'] ?>">Editar</a> | 
                <a href="index.php?delete=<?= $user['id'] ?>" onclick="return confirm('Excluir usuário?')">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
