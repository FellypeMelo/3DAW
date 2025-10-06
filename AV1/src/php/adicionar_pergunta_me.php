<?php
include 'funcoes.php';
verificarAcesso(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = trim($_POST['descricao'] ?? '');
    $opcoes = $_POST['opcoes'] ?? [];

    $opcoes = array_slice($opcoes, 0, 5);
    while (count($opcoes) < 5) {
        $opcoes[] = '';
    }

    $opcoes = array_map('trim', $opcoes);
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

        <!-- Alertas -->
        <div id="mensagem-ajax" class="hidden rounded-lg p-4 mb-6"></div>
        <div id="loading" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin text-blue-500 mr-3"></i>
                <span class="text-blue-700">Salvando pergunta...</span>
            </div>
        </div>

        <?php if (isset($erro)): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                    <span class="text-red-700"><?php echo $erro; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Formulário -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <form id="form-pergunta-me">
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
                        onblur="validarDescricao()"
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 resize-none"
                        placeholder="Digite a pergunta que será exibida para os usuários..."
                    ></textarea>
                    <div id="erro-descricao" class="hidden text-red-500 text-sm mt-2"></div>
                </div>

                <!-- Opções -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Opções de Resposta
                        </label>
                        <span id="contador-opcoes" class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                            Preenchidas: 0/5
                        </span>
                    </div>

                    <div id="opcoes-container" class="space-y-4">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="opcao-item bg-gray-50 border border-gray-200 rounded-lg p-4 transition-all duration-300" id="opcao-<?php echo $i; ?>">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <input 
                                            type="radio" 
                                            name="correta" 
                                            value="<?php echo $i; ?>" 
                                            id="opcao_correta_<?php echo $i; ?>" 
                                            onchange="atualizarOpcoesPreenchidas()"
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                        >
                                        <label for="opcao_correta_<?php echo $i; ?>" class="ml-2 text-sm font-medium text-gray-700">
                                            Correta
                                        </label>
                                    </div>
                                    <input 
                                        type="text" 
                                        name="opcoes[]" 
                                        placeholder="Opção <?php echo $i; ?>" 
                                        oninput="atualizarOpcoesPreenchidas()" 
                                        onblur="validarOpcao(<?php echo $i; ?>)"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                                    >
                                </div>
                                <span id="erro-opcao-<?php echo $i; ?>" class="hidden text-red-500 text-sm mt-2 block"></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex flex-wrap gap-4 justify-between items-center pt-6 border-t border-gray-200">
                    <div class="flex gap-3">
                        <button 
                            type="button" 
                            onclick="salvarPerguntaMultiplaEscolha()"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                        >
                            <i class="fas fa-save mr-2"></i>
                            Salvar Pergunta
                        </button>
                        <button 
                            type="button" 
                            onclick="salvarEAdicionarOutra()"
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                        >
                            <i class="fas fa-plus-circle mr-2"></i>
                            Salvar e Adicionar Outra
                        </button>
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
        function atualizarOpcoesPreenchidas() {
            var opcoes = document.getElementsByName('opcoes[]');
            var preenchidas = 0;
            
            for (var i = 0; i < opcoes.length; i++) {
                if (opcoes[i].value.trim() !== '') {
                    preenchidas++;
                }
            }
            
            document.getElementById('contador-opcoes').textContent = 'Preenchidas: ' + preenchidas + '/5';
            
            // Destacar opções preenchidas
            for (var i = 0; i < opcoes.length; i++) {
                var opcaoItem = document.getElementById('opcao-' + (i + 1));
                if (opcoes[i].value.trim() !== '') {
                    opcaoItem.classList.add('bg-green-50', 'border-green-200');
                    opcaoItem.classList.remove('bg-gray-50', 'border-gray-200');
                } else {
                    opcaoItem.classList.remove('bg-green-50', 'border-green-200');
                    opcaoItem.classList.add('bg-gray-50', 'border-gray-200');
                }
            }
            
            return preenchidas;
        }

        function validarDescricao() {
            var descricao = document.getElementById('descricao').value.trim();
            var erroDiv = document.getElementById('erro-descricao');
            
            if (descricao.length < 5) {
                erroDiv.textContent = 'A descrição deve ter pelo menos 5 caracteres';
                erroDiv.classList.remove('hidden');
                document.getElementById('descricao').classList.add('border-red-500');
                return false;
            } else {
                erroDiv.classList.add('hidden');
                document.getElementById('descricao').classList.remove('border-red-500');
                return true;
            }
        }

        function validarOpcao(numero) {
            var opcao = document.getElementsByName('opcoes[]')[numero - 1];
            var erroDiv = document.getElementById('erro-opcao-' + numero);
            
            if (opcao.value.trim() === '' && numero <= 2) {
                erroDiv.textContent = 'As duas primeiras opções são obrigatórias';
                erroDiv.classList.remove('hidden');
                opcao.classList.add('border-red-500');
                return false;
            } else {
                erroDiv.classList.add('hidden');
                opcao.classList.remove('border-red-500');
                return true;
            }
        }

        function validarFormulario() {
            var descricaoValida = validarDescricao();
            var opcoesPreenchidas = atualizarOpcoesPreenchidas();
            var opcaoCorretaSelecionada = document.querySelector('input[name="correta"]:checked') !== null;
            
            // Validar opções obrigatórias
            var opcoesValidas = true;
            for (var i = 1; i <= 2; i++) {
                if (!validarOpcao(i)) {
                    opcoesValidas = false;
                }
            }
            
            if (!opcaoCorretaSelecionada) {
                mostrarMensagem('Selecione a opção correta', 'error');
                return false;
            }
            
            return descricaoValida && opcoesValidas && opcoesPreenchidas >= 2;
        }

        function salvarPerguntaMultiplaEscolha(adicionarOutra = false) {
            if (!validarFormulario()) {
                mostrarMensagem('Por favor, corrija os erros no formulário', 'error');
                return;
            }

            var form = document.getElementById('form-pergunta-me');
            var formData = new FormData(form);
            
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('mensagem-ajax').classList.add('hidden');

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'api_adicionar_pergunta_me.php', true);
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    document.getElementById('loading').classList.add('hidden');
                    
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            
                            if (response.success) {
                                mostrarMensagem('Pergunta salva com sucesso!', 'success');
                                
                                if (adicionarOutra) {
                                    // Limpar formulário para nova pergunta
                                    form.reset();
                                    atualizarOpcoesPreenchidas();
                                    document.getElementById('descricao').focus();
                                } else {
                                    // Redirecionar após 1 segundo
                                    setTimeout(function() {
                                        window.location.href = 'listar_perguntas.php';
                                    }, 1000);
                                }
                            } else {
                                mostrarMensagem('Erro: ' + response.message, 'error');
                            }
                        } catch (e) {
                            mostrarMensagem('Erro ao processar resposta do servidor', 'error');
                        }
                    } else {
                        mostrarMensagem('Erro de comunicação com o servidor', 'error');
                    }
                }
            };
            
            xhr.send(formData);
        }

        function salvarEAdicionarOutra() {
            salvarPerguntaMultiplaEscolha(true);
        }

        function mostrarMensagem(mensagem, tipo) {
            var div = document.getElementById('mensagem-ajax');
            div.textContent = mensagem;
            div.className = tipo === 'success' 
                ? 'bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6' 
                : 'bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6';
            div.classList.remove('hidden');
            
            var icon = tipo === 'success' 
                ? '<i class="fas fa-check-circle mr-2"></i>' 
                : '<i class="fas fa-exclamation-triangle mr-2"></i>';
            
            div.innerHTML = icon + mensagem;
            
            setTimeout(function() {
                if (tipo === 'success' && !div.textContent.includes('outra')) {
                    div.classList.add('hidden');
                }
            }, 3000);
        }

        // Inicializar contador
        document.addEventListener('DOMContentLoaded', function() {
            atualizarOpcoesPreenchidas();
        });
    </script>
</body>
</html>