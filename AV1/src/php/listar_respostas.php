<?php
require_once 'funcoes.php'; // Inclui o arquivo de funções

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../html/index.html');
    exit;
}

if ($_SESSION['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

$cabecalhosRespostas = ['id', 'id_usuario', 'id_pergunta', 'resposta_dada', 'data_hora'];
$respostas = lerDados(ANSWERS_FILE, $cabecalhosRespostas);
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
            <h1>📚 Listagem de Respostas Dos Usuarios</h1>
            <p>Gerenciamento de Respostas de Usuarios Water Fall</p>
        </header>

        <?php if ($mensagem): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <?php if (empty($respostas)): ?>
            <div class="empty-state">
                <p>Nenhum resposta cadastrado.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="disciplinas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ID Usuário</th>
                            <th>ID Pergunta</th>
                            <th>Resposta Dada</th>
                            <th>Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($respostas as $resposta): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($resposta['id']); ?></td>
                                <td><?php echo htmlspecialchars($resposta['id_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($resposta['id_pergunta']); ?></td>
                                <td><?php echo htmlspecialchars($resposta['resposta_dada']); ?></td>
                                <td><?php echo htmlspecialchars($resposta['data_hora']); ?></td>
                                <td class="actions">
                                    <a href="editar_resposta.php?id=<?php echo $resposta['id']; ?>"
                                       class="btn btn-small btn-secondary"
                                       title="Editar Resposta">
                                        ✏️
                                    </a>
                                    <a href="excluir_resposta.php?id=<?php echo $resposta['id']; ?>"
                                       class="btn btn-small btn-danger"
                                       title="Excluir Resposta">
                                        🗑️
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="stats">
                <p><strong>Total de Usuarios:</strong> <?php echo count($respostas); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>