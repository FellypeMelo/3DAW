<?php
require_once 'funcoes.php';
session_start();
verificarAcesso(['admin']);

$erros = [];
$dadosFormulario = [
    'tipo' => 'texto',
    'descricao' => '',
    'resposta' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dadosFormulario = [
        'tipo' => 'texto',
        'descricao' => $_POST['descricao'] ?? '',
        'resposta' => $_POST['resposta'] ?? ''
    ];

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
            'opcoes' => '',
            'correta' => $dadosFormulario['resposta']
        ];

        $perguntas[] = $novaPergunta;

        if (salvarDados(QUESTIONS_FILE, $perguntas)) {
            header('Location: listar_perguntas.php?msg=adicionado');
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
    <title>Adicionar Pergunta</title>
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

    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-keyboard text-blue-500 mr-3"></i>
                Adicionar Pergunta de Texto
            </h1>
            <p class="text-gray-600">Crie perguntas com resposta em texto livre</p>
        </div>

        <!-- Alertas -->
        <div id="mensagem-ajax" class="hidden rounded-lg p-4 mb-6"></div>
        <div id="loading" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin text-blue-500 mr-3"></i>
                <span class="text-blue-700">Salvando pergunta...</span>
            </div>
        </div>

        <?php exibirErros($erros); ?>

        <!-- Formulário -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <form id="form-pergunta" method="POST">
                <input type="hidden" name="tipo" value="texto">

                <!-- Descrição -->
                <div class="mb-6">
                    <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-question text-blue-500 mr-2"></i>
                        Descrição da Pergunta
                    </label>
                    <input 
                        type="text"
                        id="descricao"
                        name="descricao"
                        value="<?php echo htmlspecialchars($dadosFormulario['descricao']); ?>"
                        placeholder="Ex: Qual a cor do céu?"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                    >
                    <small class="text-gray-500 text-sm mt-1 block">Digite a pergunta que será exibida</small>
                </div>

                <!-- Resposta Correta -->
                <div class="mb-6">
                    <label for="resposta" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Resposta Correta
                    </label>
                    <input 
                        type="text"
                        id="resposta"
                        name="resposta"
                        value="<?php echo htmlspecialchars($dadosFormulario['resposta']); ?>"
                        placeholder="Ex: Azul"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                    >
                    <small class="text-gray-500 text-sm mt-1 block">Resposta esperada para a pergunta</small>
                </div>

                <!-- Botões -->
                <div class="flex flex-wrap gap-4 justify-between items-center pt-6 border-t border-gray-200">
                    <div class="flex gap-3">
                        <button 
                            type="button" 
                            onclick="salvarPergunta()" 
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
                        class="text-gray-600 hover:text-gray-800 px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Voltar para a Lista
                    </a>
                </div>
            </form>
        </div>

        <!-- Informações -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-blue-800 mb-1">Sobre Perguntas de Texto</h3>
                    <ul class="text-blue-700 text-sm space-y-1">
                        <li>• Os usuários digitarão a resposta em um campo de texto</li>
                        <li>• O sistema compara a resposta digitada com a resposta correta</li>
                        <li>• A comparação é case-insensitive (não diferencia maiúsculas/minúsculas)</li>
                        <li>• Ideal para perguntas com respostas curtas e objetivas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function salvarPergunta(adicionarOutra = false) {
            var form = document.getElementById('form-pergunta');
            var formData = new FormData(form);
            
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('mensagem-ajax').classList.add('hidden');

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'api_adicionar_pergunta.php', true);
            
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
            salvarPergunta(true);
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

        // Validação em tempo real
        document.getElementById('descricao').addEventListener('blur', validarDescricao);
        document.getElementById('resposta').addEventListener('blur', validarResposta);

        function validarDescricao() {
            var descricao = document.getElementById('descricao').value.trim();
            if (descricao.length < 5) {
                mostrarErroCampo('descricao', 'A descrição deve ter pelo menos 5 caracteres');
                return false;
            }
            limparErroCampo('descricao');
            return true;
        }

        function validarResposta() {
            var resposta = document.getElementById('resposta').value.trim();
            if (resposta.length === 0) {
                mostrarErroCampo('resposta', 'A resposta é obrigatória');
                return false;
            }
            limparErroCampo('resposta');
            return true;
        }

        function mostrarErroCampo(campoId, mensagem) {
            var campo = document.getElementById(campoId);
            campo.classList.add('border-red-500');
            
            // Remover mensagem de erro anterior se existir
            var erroAnterior = campo.parentNode.querySelector('.erro-campo');
            if (erroAnterior) {
                erroAnterior.remove();
            }
            
            var erroDiv = document.createElement('div');
            erroDiv.className = 'erro-campo text-red-500 text-sm mt-1';
            erroDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i>' + mensagem;
            campo.parentNode.appendChild(erroDiv);
        }

        function limparErroCampo(campoId) {
            var campo = document.getElementById(campoId);
            campo.classList.remove('border-red-500');
            
            var erroDiv = campo.parentNode.querySelector('.erro-campo');
            if (erroDiv) {
                erroDiv.remove();
            }
        }
    </script>
</body>
</html>