<?php
require_once 'funcoes.php';

$erros = [];
$disciplina = null;

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

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome' => $_POST['nome'] ?? '',
        'sigla' => $_POST['sigla'] ?? '',
        'cargaHoraria' => $_POST['cargaHoraria'] ?? ''
    ];
    
    // Validar dados
    $erros = validarDisciplina($dados);
    
    // Se não há erros, atualizar
    if (empty($erros)) {
        // Atualizar disciplina
        foreach ($disciplinas as &$d) {
            if ($d['id'] == $id) {
                $d['nome'] = sanitizar($dados['nome']);
                $d['sigla'] = strtoupper(sanitizar($dados['sigla']));
                $d['cargaHoraria'] = (int)$dados['cargaHoraria'];
                break;
            }
        }
        
        if (salvarDisciplinas($disciplinas)) {
            header('Location: index.php?msg=editado');
            exit;
        } else {
            $erros[] = 'Erro ao salvar alterações';
        }
    }
    
    // Atualizar dados para exibição
    $disciplina = array_merge($disciplina, $dados);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Disciplina - CRUD Disciplinas</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>✏️ Editar Disciplina</h1>
            <nav>
                <a href="index.php" class="btn btn-secondary">← Voltar</a>
            </nav>
        </header>

        <div class="disciplina-info">
            <h3>Disciplina ID: <?php echo htmlspecialchars($disciplina['id']); ?></h3>
        </div>

        <?php exibirErros($erros); ?>

        <form method="POST" class="form-disciplina">
            <div class="form-group">
                <label for="nome">Nome da Disciplina *</label>
                <input type="text" 
                       id="nome" 
                       name="nome" 
                       value="<?php echo htmlspecialchars($disciplina['nome']); ?>"
                       placeholder="Ex: Programação Web"
                       required 
                       maxlength="100">
                <small>Nome completo da disciplina</small>
            </div>

            <div class="form-group">
                <label for="sigla">Sigla *</label>
                <input type="text" 
                       id="sigla" 
                       name="sigla" 
                       value="<?php echo htmlspecialchars($disciplina['sigla']); ?>"
                       placeholder="Ex: PW"
                       required 
                       maxlength="10"
                       style="text-transform: uppercase;">
                <small>Abreviação da disciplina (será convertida para maiúsculas)</small>
            </div>

            <div class="form-group">
                <label for="cargaHoraria">Carga Horária (horas) *</label>
                <input type="number" 
                       id="cargaHoraria" 
                       name="cargaHoraria" 
                       value="<?php echo htmlspecialchars($disciplina['cargaHoraria']); ?>"
                       placeholder="Ex: 80"
                       required 
                       min="1" 
                       max="999">
                <small>Número total de horas da disciplina</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
                <a href="index.php" class="btn btn-secondary">❌ Cancelar</a>
            </div>
        </form>

        <div class="help">
            <h3>💡 Informações da edição:</h3>
            <ul>
                <li><strong>ID:</strong> <?php echo htmlspecialchars($disciplina['id']); ?> (não pode ser alterado)</li>
                <li><strong>Última modificação:</strong> <?php echo date('d/m/Y H:i:s'); ?></li>
                <li>Altere apenas os campos necessários</li>
            </ul>
        </div>
    </div>

    <script>
        // Auto-converter sigla para maiúsculas
        document.getElementById('sigla').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>
