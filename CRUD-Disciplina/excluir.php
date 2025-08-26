<?php
require_once 'funcoes.php';

$disciplina = null;
$erro = '';

// Verificar se ID foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$disciplinas = carregarDisciplinas();
$disciplina = buscarDisciplinaPorId($disciplinas, $id);

// Se disciplina não existe, redirecionar
if (!$disciplina) {
    header('Location: index.php?erro=nao_encontrada');
    exit;
}

// Processar exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'sim') {
        // Filtrar disciplina a ser excluída
        $disciplinas = array_filter($disciplinas, function($d) use ($id) {
            return $d['id'] != $id;
        });
        
        if (salvarDisciplinas($disciplinas)) {
            header('Location: index.php?msg=excluido');
            exit;
        } else {
            $erro = 'Erro ao excluir disciplina';
        }
    } else {
        // Usuário cancelou
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Disciplina - CRUD Disciplinas</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🗑️ Excluir Disciplina</h1>
            <nav>
                <a href="index.php" class="btn btn-secondary">← Voltar</a>
            </nav>
        </header>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <div class="confirmacao-exclusao">
            <div class="warning-box">
                <h2>⚠️ Atenção!</h2>
                <p>Você está prestes a excluir a seguinte disciplina:</p>
            </div>

            <div class="disciplina-detalhes">
                <table class="disciplina-info-table">
                    <tr>
                        <th>ID:</th>
                        <td><?php echo htmlspecialchars($disciplina['id']); ?></td>
                    </tr>
                    <tr>
                        <th>Nome:</th>
                        <td><?php echo htmlspecialchars($disciplina['nome']); ?></td>
                    </tr>
                    <tr>
                        <th>Sigla:</th>
                        <td><span class="sigla"><?php echo htmlspecialchars($disciplina['sigla']); ?></span></td>
                    </tr>
                    <tr>
                        <th>Carga Horária:</th>
                        <td><?php echo htmlspecialchars($disciplina['cargaHoraria']); ?>h</td>
                    </tr>
                </table>
            </div>

            <div class="warning-message">
                <p><strong>Esta ação não pode ser desfeita!</strong></p>
                <p>Tem certeza que deseja continuar?</p>
            </div>

            <form method="POST" class="form-confirmacao">
                <div class="form-actions">
                    <button type="submit" 
                            name="confirmar" 
                            value="sim" 
                            class="btn btn-danger"
                            onclick="return confirm('Confirma a exclusão desta disciplina?')">
                        🗑️ Sim, Excluir Disciplina
                    </button>
                    <a href="index.php" class="btn btn-secondary">❌ Cancelar</a>
                </div>
            </form>
        </div>

        <div class="help">
            <h3>💡 Sobre a exclusão:</h3>
            <ul>
                <li>A exclusão é <strong>permanente</strong> e não pode ser desfeita</li>
                <li>O ID da disciplina será liberado para uso futuro</li>
                <li>Se houver erro, verifique as permissões do arquivo</li>
            </ul>
        </div>
    </div>
</body>
</html>
