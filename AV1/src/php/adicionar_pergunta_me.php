<?php
include 'funcoes.php';
verificarAcesso(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = trim($_POST['descricao'] ?? '');
    $opcoes = $_POST['opcoes'] ?? [];

    // Garantir exatamente 5 opções (preencher com strings vazias se necessário)
    $opcoes = array_slice($opcoes, 0, 5);
    while (count($opcoes) < 5) {
        $opcoes[] = '';
    }

    // Remover espaços extras em cada opção
    $opcoes = array_map('trim', $opcoes);

    // Validações: descrição preenchida e pelo menos 2 opções não vazias
    $opcoesNaoVazias = array_filter($opcoes, function($v) { return $v !== ''; });
    if (!empty($descricao) && count($opcoesNaoVazias) >= 2) {
        $cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
        $perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);
        $novoId = gerarId($perguntas);

        $opcoesString = converterArrayParaString($opcoes);
        $indiceCorreta = isset($_POST['correta']) ? intval($_POST['correta']) - 1 : -1;
        $respostaCorretaTexto = ($indiceCorreta >= 0 && isset($opcoes[$indiceCorreta]) && $opcoes[$indiceCorreta] !== '') ? $opcoes[$indiceCorreta] : '';

        $novaPergunta = [
            'id' => $novoId,
            'tipo' => 'multipla_escolha',
            'descricao' => $descricao,
            'opcoes' => $opcoesString,
            'correta' => $respostaCorretaTexto
        ];

        $perguntas[] = $novaPergunta;
        salvarDados(QUESTIONS_FILE, $perguntas);
        header('Location: listar_perguntas.php');
        exit();
    } else {
        $erro = "Por favor, preencha a descrição da pergunta e forneça pelo menos duas opções.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Pergunta de Múltipla Escolha</title>
    <link rel="stylesheet" href="../../src/html/estilo.css">
</head>
<body>
    <div class="container">
        <h1>Adicionar Pergunta de Múltipla Escolha</h1>
        <?php if (isset($erro)): ?>
            <p style="color: red;"><?php echo $erro; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="descricao">Descrição da Pergunta:</label>
            <textarea id="descricao" name="descricao" required></textarea><br><br>

            <div id="opcoes-container">
                <label>Opções de Resposta (exatamente 5):</label><br>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <div class="opcao-item">
                        <input type="radio" name="correta" value="<?php echo $i; ?>" id="opcao_correta_<?php echo $i; ?>" <?php echo ($i <= 2 ? 'required' : ''); ?>>
                        <label for="opcao_correta_<?php echo $i; ?>">Correta</label>
                        <input type="text" name="opcoes[]" placeholder="Opção <?php echo $i; ?>" <?php echo ($i <= 2 ? 'required' : ''); ?>>
                    </div>
                <?php endfor; ?>
            </div>

            <button type="submit">Salvar Pergunta</button>
        </form>
        <br>
        <a href="listar_perguntas.php">Voltar para a Lista de Perguntas</a>
    </div>

        <script>
            // Simple client-side ensure 5 options UI (no add/remove)
        </script>
</body>
</html>