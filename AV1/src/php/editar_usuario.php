<?php
require_once 'funcoes.php';
session_start();
$caminhoArquivo = '../../arquivos/users.txt';
$cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
$usuarios = lerDados($caminhoArquivo, $cabecalhosUsuarios);
$usuarioParaEditar = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    foreach ($usuarios as $key => $usuario) {
        if ($usuario['id'] == $id) {
            $usuarioParaEditar = $usuario;
            $usuarioParaEditar['index'] = $key; // Armazena o índice para facilitar a atualização
            break;
        }
    }
}

if (!$usuarioParaEditar) {
    header('Location: listar_usuarios.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email']; // O campo de login agora é 'email'
    $tipo = $_POST['tipo'];
    $novaSenha = $_POST['senha'];

    foreach ($usuarios as $key => $usuario) {
        if ($usuario['id'] == $id) {
            $usuarios[$key]['nome'] = $nome;
            $usuarios[$key]['email'] = $email; // Atualiza o campo 'email'
            $usuarios[$key]['tipo'] = $tipo;

            if (!empty($novaSenha)) {
                $usuarios[$key]['senha'] = password_hash($novaSenha, PASSWORD_DEFAULT);
            }
            break;
        }
    }

    salvarDados($caminhoArquivo, $usuarios);
    header('Location: listar_usuarios.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="../../src/html/estilo.css"> <!-- Assumindo um arquivo de estilo comum -->
</head>
<body>
    <h1>Editar Usuário</h1>
    <form action="editar_usuario.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($usuarioParaEditar['id']) ?>">

        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuarioParaEditar['nome']) ?>" required>

        <label for="login">Login:</label>
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuarioParaEditar['email']) ?>" required>

        <label for="senha">Nova Senha (deixe em branco para manter a atual):</label>
        <input type="password" id="senha" name="senha">

        <label for="tipo">Tipo:</label>
        <select id="tipo" name="tipo" required>
            <option value="admin" <?= ($usuarioParaEditar['tipo'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
            <option value="user" <?= ($usuarioParaEditar['tipo'] === 'user') ? 'selected' : '' ?>>Usuário Comum</option>
        </select>

        <button type="submit">Atualizar</button>
        <a href="listar_usuarios.php">Cancelar</a>
    </form>
</body>
</html>