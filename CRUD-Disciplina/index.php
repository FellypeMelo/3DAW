<?php
require_once 'funcoes.php';

// Processar exclusão se solicitada
if (isset($_GET['excluir']) && is_numeric($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    $disciplinas = carregarDisciplinas();
    
    // Filtrar disciplina a ser excluída
    $disciplinas = array_filter($disciplinas, function($disciplina) use ($id) {
        return $disciplina['id'] != $id;
    });
    
    if (salvarDisciplinas($disciplinas)) {
        header('Location: index.php?msg=excluido');
        exit;
    } else {
        $erro = 'Erro ao excluir disciplina';
    }
}

// Carregar disciplinas
$disciplinas = carregarDisciplinas();

// Mensagem de feedback
$mensagem = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'adicionado':
            $mensagem = 'Disciplina adicionada com sucesso!';
            break;
        case 'editado':
            $mensagem = 'Disciplina editada com sucesso!';
            break;
        case 'excluido':
            $mensagem = 'Disciplina excluída com sucesso!';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Disciplinas</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📚 Sistema de Disciplinas</h1>
            <p>Gerenciamento de disciplinas acadêmicas</p>
        </header>

        <?php if ($mensagem): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <div class="actions">
            <a href="adicionar.php" class="btn btn-primary">➕ Nova Disciplina</a>
        </div>

        <?php if (empty($disciplinas)): ?>
            <div class="empty-state">
                <p>Nenhuma disciplina cadastrada.</p>
                <a href="adicionar.php" class="btn btn-primary">Adicionar primeira disciplina</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="disciplinas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Sigla</th>
                            <th>Carga Horária</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($disciplinas as $disciplina): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($disciplina['id']); ?></td>
                                <td><?php echo htmlspecialchars($disciplina['nome']); ?></td>
                                <td><span class="sigla"><?php echo htmlspecialchars($disciplina['sigla']); ?></span></td>
                                <td><?php echo htmlspecialchars($disciplina['cargaHoraria']); ?>h</td>
                                <td class="actions">
                                    <a href="editar.php?id=<?php echo $disciplina['id']; ?>" 
                                       class="btn btn-small btn-secondary" 
                                       title="Editar disciplina">
                                        ✏️
                                    </a>
                                    <a href="excluir.php?id=<?php echo $disciplina['id']; ?>" 
                                       class="btn btn-small btn-danger" 
                                       title="Excluir disciplina">
                                        🗑️
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="stats">
                <p><strong>Total de disciplinas:</strong> <?php echo count($disciplinas); ?></p>
                <p><strong>Carga horária total:</strong> 
                    <?php 
                    $totalHoras = array_sum(array_column($disciplinas, 'cargaHoraria'));
                    echo $totalHoras . 'h';
                    ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
