<?php
require_once 'funcoes.php';
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
        if ($pergunta['tipo'] === 'multipla_escolha') {
            $pergunta['opcoes_detalhes'] = converterStringParaArray($pergunta['opcoes']);
            $pergunta['resposta_correta_id'] = array_search($pergunta['correta'], $pergunta['opcoes_detalhes']) + 1;
        }
    }
} else {
    $erros[] = 'ID da pergunta não fornecido.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erros)) {
    $novaDescricao = $_POST['descricao'] ?? '';
    $novaResposta = $_POST['resposta'] ?? '';
    $novasOpcoes = $_POST['opcoes'] ?? [];

    if (empty($novaDescricao)) {
        $erros[] = 'A descrição da pergunta é obrigatória.';
    }

    if ($pergunta['tipo'] === 'texto' && empty($novaResposta)) {
        $erros[] = 'A resposta correta é obrigatória para perguntas de texto.';
    } elseif ($pergunta['tipo'] === 'multipla_escolha') {
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
                    $p['correta'] = $novaResposta;
                } elseif ($p['tipo'] === 'multipla_escolha') {
                    $p['opcoes'] = converterArrayParaString($novasOpcoes);
                    $p['correta'] = $novasOpcoes[$_POST['correta'] - 1];
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
        echo '<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">';
        foreach ($erros as $erro) {
            echo '<p class="text-red-700 flex items-center"><i class="fas fa-exclamation-circle mr-2"></i>' . $erro . '</p>';
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
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

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-edit text-blue-500 mr-3"></i>
                Editar Pergunta
            </h1>
            <p class="text-gray-600">Atualize os dados da pergunta existente</p>
            <div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3 inline-block">
                <span class="text-blue-700 text-sm">
                    <i class="fas fa-info-circle mr-1"></i>
                    Tipo: <strong><?php echo htmlspecialchars($pergunta['tipo']); ?></strong> | 
                    ID: <strong><?php echo htmlspecialchars($perguntaId); ?></strong>
                </span>
            </div>
        </div>

        <?php exibirErros($erros); ?>

        <?php if ($pergunta): ?>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <form method="POST" class="space-y-6">
                    <!-- Descrição -->
                    <div>
                        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-question text-blue-500 mr-2"></i>
                            Descrição da Pergunta
                        </label>
                        <input 
                            type="text" 
                            id="descricao" 
                            name="descricao" 
                            value="<?php echo htmlspecialchars($pergunta['descricao']); ?>"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                        >
                        <small class="text-gray-500 text-sm mt-1 block">Texto da pergunta que será exibido</small>
                    </div>

                    <?php if ($pergunta['tipo'] === 'texto'): ?>
                        <!-- Resposta para pergunta de texto -->
                        <div>
                            <label for="resposta" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Resposta Correta
                            </label>
                            <input 
                                type="text"
                                id="resposta"
                                name="resposta"
                                value="<?php echo htmlspecialchars($pergunta['correta'] ?? ''); ?>"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                            >
                            <small class="text-gray-500 text-sm mt-1 block">Resposta esperada para esta pergunta</small>
                        </div>

                    <?php elseif ($pergunta['tipo'] === 'multipla_escolha'): ?>
                        <!-- Opções para múltipla escolha -->
                        <div id="opcoes-container">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-list-ol text-purple-500 mr-2"></i>
                                    Opções de Resposta
                                </label>
                                <button 
                                    type="button" 
                                    id="add-opcao"
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300 flex items-center"
                                >
                                    <i class="fas fa-plus mr-2"></i>
                                    Adicionar Opção
                                </button>
                            </div>

                            <?php foreach ($pergunta['opcoes_detalhes'] as $index => $opcaoTexto): ?>
                                <div class="opcao-item bg-gray-50 border border-gray-200 rounded-lg p-4 mb-3 transition-all duration-300">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center">
                                            <input 
                                                type="radio" 
                                                name="correta" 
                                                value="<?php echo htmlspecialchars($index + 1); ?>" 
                                                id="opcao_correta_<?php echo htmlspecialchars($index + 1); ?>" 
                                                <?php echo ($pergunta['resposta_correta_id'] == ($index + 1)) ? 'checked' : ''; ?> 
                                                required
                                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                            >
                                            <label for="opcao_correta_<?php echo htmlspecialchars($index + 1); ?>" class="ml-2 text-sm font-medium text-gray-700">
                                                Correta
                                            </label>
                                        </div>
                                        <input 
                                            type="text" 
                                            name="opcoes[]" 
                                            value="<?php echo htmlspecialchars($opcaoTexto); ?>" 
                                            placeholder="Opção <?php echo $index + 1; ?>" 
                                            required
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                                        >
                                        <?php if ($index >= 2): ?>
                                            <button 
                                                type="button" 
                                                class="remove-opcao text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-all duration-300"
                                                title="Remover opção"
                                            >
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Botões -->
                    <div class="flex flex-wrap gap-4 justify-between items-center pt-6 border-t border-gray-200">
                        <button 
                            type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                        >
                            <i class="fas fa-save mr-2"></i>
                            Salvar Alterações
                        </button>
                        <a 
                            href="listar_perguntas.php" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                        >
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

            <?php if ($pergunta['tipo'] === 'multipla_escolha'): ?>
            <script>
                let opcaoCount = <?php echo count($pergunta['opcoes_detalhes']); ?>;

                document.getElementById('add-opcao').addEventListener('click', function() {
                    if (opcaoCount >= 10) {
                        alert('Máximo de 10 opções permitidas');
                        return;
                    }

                    const container = document.getElementById('opcoes-container');
                    opcaoCount++;
                    const newOptionValue = opcaoCount;

                    const div = document.createElement('div');
                    div.classList.add('opcao-item', 'bg-gray-50', 'border', 'border-gray-200', 'rounded-lg', 'p-4', 'mb-3', 'transition-all', 'duration-300');

                    const innerDiv = document.createElement('div');
                    innerDiv.classList.add('flex', 'items-center', 'space-x-4');

                    const radioDiv = document.createElement('div');
                    radioDiv.classList.add('flex', 'items-center');

                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = 'correta';
                    radio.value = newOptionValue;
                    radio.id = `opcao_correta_${newOptionValue}`;
                    radio.required = true;
                    radio.classList.add('w-4', 'h-4', 'text-blue-600', 'border-gray-300', 'focus:ring-blue-500');

                    const label = document.createElement('label');
                    label.htmlFor = `opcao_correta_${newOptionValue}`;
                    label.classList.add('ml-2', 'text-sm', 'font-medium', 'text-gray-700');
                    label.textContent = 'Correta';

                    const textInput = document.createElement('input');
                    textInput.type = 'text';
                    textInput.name = 'opcoes[]';
                    textInput.placeholder = `Opção ${newOptionValue}`;
                    textInput.classList.add('flex-1', 'px-4', 'py-2', 'border', 'border-gray-300', 'rounded-lg', 'focus:ring-2', 'focus:ring-blue-500', 'focus:border-blue-500', 'transition-all', 'duration-300');

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.classList.add('remove-opcao', 'text-red-500', 'hover:text-red-700', 'p-2', 'rounded-full', 'hover:bg-red-50', 'transition-all', 'duration-300');
                    removeBtn.title = 'Remover opção';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';

                    removeBtn.addEventListener('click', function() {
                        div.remove();
                        opcaoCount--;
                    });

                    radioDiv.appendChild(radio);
                    radioDiv.appendChild(label);
                    innerDiv.appendChild(radioDiv);
                    innerDiv.appendChild(textInput);
                    innerDiv.appendChild(removeBtn);
                    div.appendChild(innerDiv);

                    // Inserir antes do botão "Adicionar Opção"
                    container.insertBefore(div, document.getElementById('add-opcao').parentElement.parentElement);
                });

                // Adicionar event listeners para os botões de remover existentes
                document.querySelectorAll('.remove-opcao').forEach(button => {
                    button.addEventListener('click', function() {
                        if (document.querySelectorAll('.opcao-item').length <= 2) {
                            alert('É necessário ter pelo menos 2 opções');
                            return;
                        }
                        this.closest('.opcao-item').remove();
                        opcaoCount--;
                    });
                });
            </script>
            <?php endif; ?>

        <?php else: ?>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Pergunta Não Encontrada</h3>
                <p class="text-gray-600 mb-4">Não foi possível carregar a pergunta para edição.</p>
                <a 
                    href="listar_perguntas.php" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 inline-flex items-center"
                >
                    <i class="fas fa-arrow-left mr-2"></i>
                    Voltar para Lista de Perguntas
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>