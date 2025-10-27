<?php
require_once 'funcoes.php';
session_start();
verificarAcesso(['admin']);

$erros = [];
$dadosFormulario = [
    'descricao' => '',
    'opcoes' => ['', '', '', '', ''],
    'correta' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = trim($_POST['descricao'] ?? '');
    $opcoes = $_POST['opcoes'] ?? [];
    $correta = $_POST['correta'] ?? '';

    // Processar opções
    $opcoes = array_slice($opcoes, 0, 5);
    while (count($opcoes) < 5) {
        $opcoes[] = '';
    }
    $opcoes = array_map('trim', $opcoes);
    
    // Filtrar opções não vazias
    $opcoesNaoVazias = array_filter($opcoes, function($v) { return $v !== ''; });
    
    // Validar
    if (empty($descricao)) {
        $erros[] = 'A descrição da pergunta é obrigatória.';
    }
    if (count($opcoesNaoVazias) < 2) {
        $erros[] = 'É necessário pelo menos duas opções de resposta.';
    }
    if (empty($correta)) {
        $erros[] = 'Selecione a opção correta.';
    } elseif (!isset($opcoes[$correta - 1]) || empty($opcoes[$correta - 1])) {
        $erros[] = 'A opção correta selecionada é inválida.';
    }

    if (empty($erros)) {
        $opcoesString = converterArrayParaString($opcoes);
        $respostaCorreta = $opcoes[$correta - 1];

        $novaPergunta = [
            'tipo' => 'multipla_escolha',
            'descricao' => $descricao,
            'opcoes' => $opcoesString,
            'correta' => $respostaCorreta
        ];

        if (salvarDados(QUESTIONS_TABLE, $novaPergunta)) {
            header('Location: listar_perguntas.php?msg=Pergunta de múltipla escolha adicionada com sucesso!');
            exit;
        } else {
            $erros[] = 'Erro ao salvar pergunta no banco de dados.';
        }
    } else {
        // Manter dados do formulário em caso de erro
        $dadosFormulario = [
            'descricao' => $descricao,
            'opcoes' => $opcoes,
            'correta' => $correta
        ];
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
    <title>Adicionar Pergunta de Múltipla Escolha</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php
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
                <i class="fas fa-list-ol text-blue-500 mr-3"></i>
                Adicionar Pergunta de Múltipla Escolha
            </h1>
            <p class="text-gray-600">Crie novas perguntas com múltiplas opções de resposta</p>
        </div>

        <?php exibirErros($erros); ?>

        <!-- Formulário -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <form method="POST">
                <!-- Descrição -->
                <div class="mb-6">
                    <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-question-circle text-blue-500 mr-2"></i>
                        Descrição da Pergunta
                    </label>
                    <textarea 
                        id="descricao" 
                        name="descricao" 
                        required 
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 resize-none"
                        placeholder="Digite a pergunta que será exibida para os usuários..."
                    ><?php echo htmlspecialchars($dadosFormulario['descricao']); ?></textarea>
                </div>

                <!-- Opções -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Opções de Resposta
                        </label>
                        <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                            Mínimo: 2 opções
                        </span>
                    </div>

                    <div class="space-y-4">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <div class="opcao-item bg-gray-50 border border-gray-200 rounded-lg p-4 transition-all duration-300">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <input 
                                            type="radio" 
                                            name="correta" 
                                            value="<?php echo $i + 1; ?>" 
                                            id="opcao_correta_<?php echo $i + 1; ?>" 
                                            <?php echo ($dadosFormulario['correta'] == $i + 1) ? 'checked' : ''; ?>
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                        >
                                        <label for="opcao_correta_<?php echo $i + 1; ?>" class="ml-2 text-sm font-medium text-gray-700">
                                            Correta
                                        </label>
                                    </div>
                                    <input 
                                        type="text" 
                                        name="opcoes[]" 
                                        value="<?php echo htmlspecialchars($dadosFormulario['opcoes'][$i] ?? ''); ?>"
                                        placeholder="Opção <?php echo $i + 1; ?>" 
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                                    >
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex flex-wrap gap-4 justify-between items-center pt-6 border-t border-gray-200">
                    <div class="flex gap-3">
                        <button 
                            type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                        >
                            <i class="fas fa-save mr-2"></i>
                            Salvar Pergunta
                        </button>
                        <a 
                            href="adicionar_pergunta_me.php" 
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                        >
                            <i class="fas fa-plus-circle mr-2"></i>
                            Adicionar Outra
                        </a>
                    </div>
                    <a 
                        href="listar_perguntas.php" 
                        class="text-gray-600 hover:text-gray-800 px-4 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Voltar para a Lista
                    </a>
                </div>
            </form>
        </div>

        <!-- Informações -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-blue-800 mb-1">Informações Importantes</h3>
                    <ul class="text-blue-700 text-sm space-y-1">
                        <li>• Preencha pelo menos 2 opções de resposta</li>
                        <li>• Selecione a opção correta marcando o radio button</li>
                        <li>• As duas primeiras opções são obrigatórias</li>
                        <li>• Você pode usar até 5 opções no total</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Adicionar loading no submit do formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Salvando...';
            button.disabled = true;
        });

        // Validação básica das opções
        function validarOpcoes() {
            const opcoes = document.getElementsByName('opcoes[]');
            let preenchidas = 0;
            
            for (let opcao of opcoes) {
                if (opcao.value.trim() !== '') {
                    preenchidas++;
                }
            }
            
            return preenchidas >= 2;
        }

        // Validação antes do envio
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!validarOpcoes()) {
                e.preventDefault();
                alert('Por favor, preencha pelo menos duas opções de resposta.');
                return false;
            }
            
            const corretaSelecionada = document.querySelector('input[name="correta"]:checked');
            if (!corretaSelecionada) {
                e.preventDefault();
                alert('Por favor, selecione a opção correta.');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>