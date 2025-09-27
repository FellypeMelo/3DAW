<?php
require_once 'funcoes.php'; // Inclui o arquivo de funções
session_start();
verificarAcesso(['admin']);

$cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
$usuarios = lerDados(USERS_FILE, $cabecalhosUsuarios);
$mensagem = $_GET['msg'] ?? ''; // Para exibir mensagens de sucesso, se houver
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Usuarios</title>
</head>
<body>
        <div class="container">
        <header>
            <h1>📚 Listagem de Usuarios</h1>
            <p>Gerenciamento de Usuarios Water Fall</p>
        </header>

        <?php if ($mensagem): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <div class="actions">
            <a href="adicionar_usuario.php" class="btn btn-primary">➕ Nova Usuario</a>
        </div>

        <?php if (empty($usuarios)): ?>
            <div class="empty-state">
                <p>Nenhum Usuario cadastrado.</p>
                <a href="adicionar_usuario.php" class="btn btn-primary">Adicionar Primeiro Usuario</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="disciplinas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['tipo']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td class="actions">
                                    <a href="editar_usuario.php?id=<?= $usuario['id'] ?>"
                                       class="btn btn-small btn-secondary"
                                       title="Editar Usuário">
                                        ✏️
                                    </a>
                                    <a href="excluir_usuario.php?id=<?= $usuario['id'] ?>"
                                       class="btn btn-small btn-danger"
                                       title="Excluir Usuário"
                                       onclick="return confirm('Tem certeza?');">
                                        🗑️
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="stats">
                <p><strong>Total de Usuarios:</strong> <?php echo count($usuarios); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>