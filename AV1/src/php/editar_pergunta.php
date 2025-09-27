<?php
require_once 'funcoes.php'; // Inclui o arquivo de funções

verificarAcesso(['admin']);

$erros = [];
$pergunta = null;
$perguntaId = $_GET['id'] ?? null;

if ($perguntaId) {
    $cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
    $perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);
    foreach ($perguntas as $p) {
        if ($p['id'] == $perguntaId) {
            $pergunta = $p;
            break;
        }
    }

    if (!$pergunta) {
        $erros[] = 'Pergunta não encontrada.';
    } else {
        // Se a pergunta for de múltipla escolha, converter a string de opções para array
        if ($pergunta['tipo'] === 'multipla_escolha') {
            $pergunta['opcoes_detalhes'] = converterStringParaArray($pergunta['opcoes']);
            // Encontrar o índice da resposta correta para preencher o rádio button
            $pergunta['resposta_correta_id'] = array_search($pergunta['correta'], $pergunta['opcoes_detalhes']) + 1;
        }
    }
} else {
    $erros[] = 'ID da pergunta não fornecido.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erros)) {
    $novaDescricao = $_POST['descricao'] ?? '';
    $novaResposta = $_POST['resposta'] ?? ''; // Para perguntas de texto
    $novasOpcoes = $_POST['opcoes'] ?? []; // Para perguntas de múltipla escolha

    if (empty($novaDescricao)) {
        $erros[] = 'A descrição da pergunta é obrigatória.';
    }

    // Lógica de validação baseada no tipo de pergunta
    if ($pergunta['tipo'] === 'texto' && empty($novaResposta)) {
        $erros[] = 'A resposta correta é obrigatória para perguntas de texto.';
    } elseif ($pergunta['tipo'] === 'multipla_escolha') {
        // Remove opções vazias
        $novasOpcoes = array_filter($novasOpcoes, function($value) {
            return trim($value) !== '';
        });
        if (count($novasOpcoes) < 2) {
            $erros[] = 'Para perguntas de múltipla escolha, forneça pelo menos duas opções.';
        }
    }

    if (empty($erros)) {
        foreach ($perguntas as &$p) {
            if ($p['id'] == $perguntaId) {
                $p['descricao'] = $novaDescricao;
                if ($p['tipo'] === 'texto') {
                    $p['correta'] = $novaResposta; // Atualiza a coluna 'correta' para perguntas de texto
                } elseif ($p['tipo'] === 'multipla_escolha') {
                    $p['opcoes'] = converterArrayParaString($novasOpcoes);
                    $p['correta'] = $novasOpcoes[$_POST['correta'] - 1]; // Salva o texto da opção correta
                }
                break;
            }
        }

        if (salvarDados(QUESTIONS_FILE, $perguntas)) {
            header('Location: listar_perguntas.php?msg=editado');
            exit;
        } else {
            $erros[] = 'Erro ao salvar pergunta.';
        }
    }
}

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
    <title>Editar Pergunta</title>
    <link rel="stylesheet" href="../../src/html/estilo.css">
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
        <h2>Editar Pergunta</h2>
        <?php exibirErros($erros); ?>

        <?php if ($pergunta): ?>
            <form method="POST" class="form-usuario">
                <div class="form-group">
                    <label for="descricao">Descrição da Pergunta</label>
                    <input type="text" 
                           id="descricao" 
                           name="descricao" 
                           value="<?php echo htmlspecialchars($pergunta['descricao']); ?>"
                           required>
                    <small>Descrição da Pergunta</small>
                </div>
                <?php if ($pergunta['tipo'] === 'texto'): ?>
                    <div class="form-group">
                        <label for="resposta">Resposta Correta</label>
                        <input type="text"
                               id="resposta"
                               name="resposta"
                               value="<?php echo htmlspecialchars($pergunta['correta'] ?? ''); ?>"
                               required>
                       <small>Resposta Correta da Pergunta</small>
                   </div>
               <?php elseif ($pergunta['tipo'] === 'multipla_escolha'): ?>
                   <div class="form-group" id="opcoes-container">
                       <label>Opções de Resposta:</label><br>
                       <?php foreach ($pergunta['opcoes_detalhes'] as $index => $opcaoTexto): ?>
                           <div class="opcao-item">
                               <input type="radio" name="correta" value="<?php echo htmlspecialchars($index + 1); ?>" id="opcao_correta_<?php echo htmlspecialchars($index + 1); ?>" <?php echo ($pergunta['resposta_correta_id'] == ($index + 1)) ? 'checked' : ''; ?> required>
                               <label for="opcao_correta_<?php echo htmlspecialchars($index + 1); ?>">Correta</label>
                               <input type="text" name="opcoes[]" value="<?php echo htmlspecialchars($opcaoTexto); ?>" placeholder="Opção <?php echo $index + 1; ?>" required>
                           </div>
                       <?php endforeach; ?>
                       <button type="button" id="add-opcao">Adicionar Opção</button>
                   </div>
               <?php endif; ?>
               <div class="form-actions">
                   <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
                   <a href="listar_perguntas.php" class="btn btn-secondary">❌ Cancelar</a>
               </div>
           </form>
           <?php if ($pergunta['tipo'] === 'multipla_escolha'): ?>
           <script>
               document.getElementById('add-opcao').addEventListener('click', function() {
                   const container = document.getElementById('opcoes-container');
                   const newOptionIndex = container.children.length; // Conta os divs de opção existentes (incluindo o botão)
                   const newOptionValue = newOptionIndex; // O valor do rádio será o índice + 1

                   const div = document.createElement('div');
                   div.classList.add('opcao-item');

                   const radio = document.createElement('input');
                   radio.type = 'radio';
                   radio.name = 'correta';
                   radio.value = newOptionIndex;
                   radio.id = `opcao_correta_${newOptionIndex}`;
                   radio.required = true;

                   const label = document.createElement('label');
                   label.htmlFor = `opcao_correta_${newOptionIndex}`;
                   label.textContent = 'Correta';

                   const textInput = document.createElement('input');
                   textInput.type = 'text';
                   textInput.name = 'opcoes[]';
                   textInput.placeholder = `Opção ${newOptionIndex}`;
                   textInput.required = true;

                   div.appendChild(radio);
                   div.appendChild(label);
                   div.appendChild(textInput);
                   container.insertBefore(div, container.lastElementChild); // Insere antes do botão
               });
           </script>
            <?php endif; ?>
        <?php else: ?>
            <p>Não foi possível carregar a pergunta para edição.</p>
            <a href="listar_perguntas.php" class="btn btn-secondary">Voltar para Lista de Perguntas</a>
        <?php endif; ?>
    </div>
</body>
</html>