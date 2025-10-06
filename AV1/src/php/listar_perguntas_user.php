<?php
require_once 'funcoes.php';

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../html/index.html');
    exit;
}

$cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
$perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder Perguntas</title>
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
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            <i class="fas fa-question-circle text-blue-500 mr-2"></i>
            Responder Perguntas
        </h1>
        
        <button onclick="carregarPerguntas()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors mb-6">
            <i class="fas fa-sync-alt mr-2"></i>Atualizar Perguntas
        </button>
        
        <div id="loading" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin text-blue-500 mr-3"></i>
                <span class="text-blue-700">Carregando perguntas...</span>
            </div>
        </div>

        <div id="mensagem" class="hidden rounded-lg p-4 mb-6"></div>
        
        <div id="perguntas-container">
            <?php if (empty($perguntas)): ?>
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Nenhuma pergunta disponível.</p>
                </div>
            <?php else: ?>
                <div id="lista-perguntas" class="space-y-4">
                    <?php foreach ($perguntas as $p): ?>
                        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow" id="pergunta-<?php echo $p['id']; ?>">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full mb-2">
                                        <?php echo htmlspecialchars($p['tipo']); ?>
                                    </span>
                                    <p class="text-gray-800 text-lg"><?php echo htmlspecialchars($p['descricao']); ?></p>
                                </div>
                                <a href="ver_pergunta.php?id=<?php echo $p['id']; ?>" 
                                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                                    <i class="fas fa-reply mr-2"></i>Responder
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function carregarPerguntas() {
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('mensagem').classList.add('hidden');
            
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'api_carregar_perguntas_user.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    document.getElementById('loading').classList.add('hidden');
                    
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            atualizarListaPerguntas(response.perguntas);
                            mostrarMensagem('Perguntas atualizadas!', 'success');
                        } catch (e) {
                            mostrarMensagem('Erro ao carregar perguntas', 'error');
                        }
                    } else {
                        mostrarMensagem('Erro ao carregar perguntas', 'error');
                    }
                }
            };
            xhr.send();
        }

        function atualizarListaPerguntas(perguntas) {
            var container = document.getElementById('lista-perguntas');
            
            if (!perguntas || perguntas.length === 0) {
                container.innerHTML = '<div class="bg-white rounded-xl shadow-lg p-8 text-center"><i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i><p class="text-gray-600">Nenhuma pergunta disponível.</p></div>';
                return;
            }

            var html = '';
            perguntas.forEach(function(pergunta) {
                html += '<div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow" id="pergunta-' + pergunta.id + '">' +
                    '<div class="flex items-start justify-between">' +
                    '<div>' +
                    '<span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full mb-2">' + pergunta.tipo + '</span>' +
                    '<p class="text-gray-800 text-lg">' + pergunta.descricao + '</p>' +
                    '</div>' +
                    '<a href="ver_pergunta.php?id=' + pergunta.id + '" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">' +
                    '<i class="fas fa-reply mr-2"></i>Responder</a>' +
                    '</div></div>';
            });
            
            container.innerHTML = html;
        }

        function mostrarMensagem(mensagem, tipo) {
            var div = document.getElementById('mensagem');
            div.className = tipo === 'success' 
                ? 'bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6'
                : 'bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6';
            div.innerHTML = '<i class="fas ' + (tipo === 'success' ? 'fa-check' : 'fa-exclamation-triangle') + ' mr-2"></i>' + mensagem;
            div.classList.remove('hidden');
            
            setTimeout(function() {
                div.classList.add('hidden');
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            carregarPerguntas();
        });

        setInterval(carregarPerguntas, 60000);
    </script>
</body>
</html>