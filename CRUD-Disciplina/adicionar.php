<?php
require_once 'funcoes.php';

$erros = [];
$dados = [
    'nome' => '',
    'sigla' => '',
    'cargaHoraria' => ''
];

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome' => $_POST['nome'] ?? '',
        'sigla' => $_POST['sigla'] ?? '',
        'cargaHoraria' => $_POST['cargaHoraria'] ?? ''
    ];
    
    // Validar dados
    $erros = validarDisciplina($dados);
    
    // Se não há erros, salvar
    if (empty($erros)) {
        $disciplinas = carregarDisciplinas();
        $novaDisciplina = [
            'id' => gerarId($disciplinas),
            'nome' => sanitizar($dados['nome']),
            'sigla' => strtoupper(sanitizar($dados['sigla'])),
            'cargaHoraria' => (int)$dados['cargaHoraria']
        ];
        
        $disciplinas[] = $novaDisciplina;
        
        if (salvarDisciplinas($disciplinas)) {
            header('Location: index.php?msg=adicionado');
            exit;
        } else {
            $erros[] = 'Erro ao salvar disciplina';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Disciplina - CRUD Disciplinas</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>➕ Adicionar Nova Disciplina</h1>
            <nav>
                <a href="index.php" class="btn btn-secondary">← Voltar</a>
            </nav>
        </header>

        <?php exibirErros($erros); ?>

        <form method="POST" class="form-disciplina">
            <div class="form-group">
                <label for="nome">Nome da Disciplina *</label>
                <input type="text" 
                       id="nome" 
                       name="nome" 
                       value="<?php echo htmlspecialchars($dados['nome']); ?>"
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
                       value="<?php echo htmlspecialchars($dados['sigla']); ?>"
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
                       value="<?php echo htmlspecialchars($dados['cargaHoraria']); ?>"
                       placeholder="Ex: 80"
                       required 
                       min="1" 
                       max="999">
                <small>Número total de horas da disciplina</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar Disciplina</button>
                <a href="index.php" class="btn btn-secondary">❌ Cancelar</a>
            </div>
        </form>

        <div class="help">
            <h3>💡 Dicas para preenchimento:</h3>
            <ul>
                <li><strong>Nome:</strong> Use o nome completo e descritivo da disciplina</li>
                <li><strong>Sigla:</strong> Abreviação curta e fácil de lembrar</li>
                <li><strong>Carga Horária:</strong> Total de horas (teóricas + práticas)</li>
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
