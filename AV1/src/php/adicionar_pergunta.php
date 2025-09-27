<?php
require_once 'funcoes.php'; // Inclui o arquivo de funções
session_start();
verificarAcesso(['admin']);

$erros = []; // Inicializa o array de erros

$dadosFormulario = [
    'tipo' => 'texto', // Valor fixo para esta tarefa
    'descricao' => '',
    'resposta' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dadosFormulario = [
        'tipo' => 'texto', // Valor fixo para esta tarefa
        'descricao' => $_POST['descricao'] ?? '',
        'resposta' => $_POST['resposta'] ?? ''
    ];

    // Validação básica (pode ser expandida)
    if (empty($dadosFormulario['descricao'])) {
        $erros[] = 'A descrição da pergunta é obrigatória.';
    }
    if (empty($dadosFormulario['resposta'])) {
        $erros[] = 'A resposta correta é obrigatória.';
    }

    if (empty($erros)) {
        $cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
        $perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);

        $novaPergunta = [
            'id' => gerarId($perguntas),
            'tipo' => $dadosFormulario['tipo'],
            'descricao' => $dadosFormulario['descricao'],
            'opcoes' => '', // Para perguntas de texto, opções ficam vazias
            'correta' => $dadosFormulario['resposta']
        ];

        $perguntas[] = $novaPergunta;

        if (salvarDados(QUESTIONS_FILE, $perguntas)) {
            header('Location: index.php?msg=adicionado');
            exit;
        } else {
            $erros[] = 'Erro ao salvar pergunta.';
        }
    }
}

// Função auxiliar para exibir erros (se não existir em funcoes.php)
function exibirErros(array $erros) {
    if (!empty($erros)) {
        echo '<div class="erros">';
        foreach ($erros as $erro) {
            echo "<p>{$erro}</p>";
        }
        echo '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Pergunta</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    
    <?php
        if (!isset($_SESSION)) {
            session_start();
        }
        if($_SESSION['tipo'] == 'admin'){
            include '../html/menu_admin.html';
        } else {
            include '../html/menu_user.html';
        }
    ?>

    <div class="container">
        <?php exibirErros($erros); ?>
        <form method="POST" class="form-usuario">
            <input type="hidden" name="tipo" value="texto">

            <div class="form-group">
                <label for="descricao">Descrição da Pergunta</label>
                <input type="text"
                       id="descricao"
                       name="descricao"
                       value="<?php echo htmlspecialchars($dadosFormulario['descricao']); ?>"
                       placeholder="Ex: Qual a cor do céu?"
                       required>
                <small>Descrição da Pergunta</small>
            </div>

            <div class="form-group">
                <label for="resposta">Resposta Correta</label>
                <input type="text"
                       id="resposta"
                       name="resposta"
                       value="<?php echo htmlspecialchars($dadosFormulario['resposta']); ?>"
                       placeholder="Ex: Azul"
                       required>
                <small>Resposta Correta da Pergunta</small>
            </div>


            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar Pergunta</button>
                <a href="index.php" class="btn btn-secondary">❌ Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
