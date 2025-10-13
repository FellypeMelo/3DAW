<?php
require_once 'funcoes.php';
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../html/index.html');
    exit;
}

if ($_SESSION['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

$mensagem = '';
if (isset($_GET['status']) && $_GET['status'] === 'ok') {
    $mensagem = 'Resposta salva com sucesso!';
} else if (isset($_GET['msg'])) {
    $mensagem = $_GET['msg'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Perguntas</title>
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
        <header class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-list text-blue-500 mr-2"></i>
                Listagem de Perguntas
            </h1>
            <p class="text-gray-600">Gerenciamento de Perguntas Water Fall</p>
        </header>

        <?php if ($mensagem): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <div class="flex flex-wrap gap-4 mb-6">
            <a href="adicionar_pergunta.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Nova Pergunta de Texto
            </a>
            <a href="adicionar_pergunta_me.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Nova Pergunta de Múltipla Escolha
            </a>
            <button onclick="carregarPerguntas()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>Atualizar Lista
            </button>
        </div>

        <div id="loading" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin text-blue-500 mr-3"></i>
                <span class="text-blue-700">Carregando perguntas...</span>
            </div>
        </div>

        <div id="perguntas-container">
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 mb-4">Nenhuma pergunta carregada.</p>
                <button onclick="carregarPerguntas()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                    Carregar Perguntas
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            carregarPerguntas();
        });

        function carregarPerguntas() {
            document.getElementById('loading').classList.remove('hidden');
            
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'api_carregar_perguntas.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    document.getElementById('loading').classList.add('hidden');
                    
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            atualizarTabelaPerguntas(response.perguntas);
                            mostrarMensagem('Lista atualizada com sucesso!', 'success');
                        } catch (e) {
                            mostrarMensagem('Erro ao processar resposta', 'error');
                        }
                    } else {
                        mostrarMensagem('Erro ao carregar perguntas', 'error');
                    }
                }
            };
            xhr.send();
        }

        function atualizarTabelaPerguntas(perguntas) {
            var container = document.getElementById('perguntas-container');
            
            if (perguntas.length === 0) {
                container.innerHTML = 
                    '<div class="bg-white rounded-xl shadow-lg p-8 text-center"><i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i><p class="text-gray-600 mb-4">Nenhuma pergunta cadastrada.</p></div>';
                return;
            }

            var html = `
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resposta Correta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-perguntas" class="divide-y divide-gray-200">
            `;

            perguntas.forEach(function(pergunta) {
                html += `
                <tr id="pergunta-${pergunta.id}" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">${pergunta.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${pergunta.tipo}</td>
                    <td class="px-6 py-4">${pergunta.descricao}</td>
                    <td class="px-6 py-4">${pergunta.correta}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex space-x-2">
                            <a href="ver_pergunta.php?id=${pergunta.id}"
                               class="text-blue-600 hover:text-blue-900 transition-colors"
                               title="Ver Pergunta">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="editar_pergunta.php?id=${pergunta.id}"
                               class="text-green-600 hover:text-green-900 transition-colors"
                               title="Editar Pergunta">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="excluirPergunta(${pergunta.id})"
                               class="text-red-600 hover:text-red-900 transition-colors"
                               title="Excluir Pergunta">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    <p><strong>Total de Perguntas:</strong> <span id="total-perguntas">${perguntas.length}</span></p>
                </div>
            `;

            container.innerHTML = html;
        }

        function excluirPergunta(id) {
            if (!confirm('Tem certeza que deseja excluir esta pergunta?')) {
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'api_excluir_pergunta.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                document.getElementById('pergunta-' + id).remove();
                                var totalElement = document.getElementById('total-perguntas');
                                totalElement.textContent = parseInt(totalElement.textContent) - 1;
                                mostrarMensagem('Pergunta excluída com sucesso!', 'success');
                            } else {
                                mostrarMensagem('Erro: ' + response.message, 'error');
                            }
                        } catch (e) {
                            mostrarMensagem('Erro ao processar resposta', 'error');
                        }
                    } else {
                        mostrarMensagem('Erro ao excluir pergunta', 'error');
                    }
                }
            };
            
            xhr.send('id=' + encodeURIComponent(id));
        }

        function mostrarMensagem(mensagem, tipo) {
            var existingMsg = document.getElementById('mensagem-dinamica');
            if (existingMsg) existingMsg.remove();

            var div = document.createElement('div');
            div.id = 'mensagem-dinamica';
            div.className = tipo === 'success' 
                ? 'bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6'
                : 'bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6';
            div.innerHTML = '<i class="fas ' + (tipo === 'success' ? 'fa-check' : 'fa-exclamation-triangle') + ' mr-2"></i>' + mensagem;
            
            document.querySelector('.container').insertBefore(div, document.querySelector('.container').firstChild);
            
            setTimeout(function() {
                div.remove();
            }, 5000);
        }

        setInterval(carregarPerguntas, 30000);
    </script>
</body>
</html>