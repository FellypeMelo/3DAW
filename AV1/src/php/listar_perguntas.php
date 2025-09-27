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

$cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
$perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);
$mensagem = '';
if (isset($_GET['status']) && $_GET['status'] === 'ok') {
    $mensagem = 'Resposta salva com sucesso!';
} else if (isset($_GET['msg'])) {
    $mensagem = $_GET['msg'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Perguntas</title>
</head>
<body>
        <div class="container">
        <header>
            <h1>📚 Listagem de Perguntas</h1>
            <p>Gerenciamento de Perguntas Water Fall</p>
        </header>

        <?php if ($mensagem): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <div class="actions">
            <a href="adicionar_pergunta.php" class="btn btn-primary">➕ Nova Pergunta de Texto</a>
            <a href="adicionar_pergunta_me.php" class="btn btn-primary">➕ Nova Pergunta de Múltipla Escolha</a>
        </div>

        <?php if (empty($perguntas)): ?>
            <div class="empty-state">
                <p>Nenhuma pergunta cadastrada.</p>
                <a href="adicionar_pergunta.php" class="btn btn-primary">Adicionar primeira Pergunta</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="disciplinas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Resposta Correta</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($perguntas as $pergunta): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pergunta['id']); ?></td>
                                <td><?php echo htmlspecialchars($pergunta['tipo']); ?></td>
                                <td><?php echo htmlspecialchars($pergunta['descricao']); ?></td>
                                <td><?php echo htmlspecialchars($pergunta['correta']); ?></td>
                                <td class="actions">
                                    <a href="ver_pergunta.php?id=<?php echo $pergunta['id']; ?>"
                                       class="btn btn-small btn-info"
                                       title="Ver Pergunta">
                                        👁️
                                    </a>
                                    <a href="editar_pergunta.php?id=<?php echo $pergunta['id']; ?>"
                                       class="btn btn-small btn-secondary"
                                       title="Editar Pergunta">
                                        ✏️
                                    </a>
                                    <a href="excluir_pergunta.php?id=<?php echo $pergunta['id']; ?>"
                                       class="btn btn-small btn-danger"
                                       title="Excluir Pergunta"
                                       onclick="return confirm('Tem certeza que deseja excluir esta pergunta?');">
                                        🗑️
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="stats">
                <p><strong>Total de Perguntas:</strong> <?php echo count($perguntas); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>