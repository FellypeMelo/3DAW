<?php
require_once 'funcoes.php';
verificarAcesso(['admin']);
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
            <div id="info-pergunta" class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3 inline-block hidden">
                <span class="text-blue-700 text-sm">
                    <i class="fas fa-info-circle mr-1"></i>
                    Tipo: <strong id="info-tipo"></strong> | 
                    ID: <strong id="info-id"></strong>
                </span>
            </div>
        </div>

        <div id="mensagem-erro" class="hidden"></div>
        <div id="loading" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin text-blue-500 mr-3"></i>
                <span class="text-blue-700">Carregando pergunta...</span>
            </div>
        </div>

        <div id="form-container" class="hidden">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <form id="form-editar-pergunta" class="space-y-6" novalidate>
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
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                        >
                        <small class="text-gray-500 text-sm mt-1 block">Texto da pergunta que será exibido</small>
                    </div>

                    <!-- Container para pergunta de texto -->
                    <div id="container-texto" class="hidden">
                        <label for="resposta" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Resposta Correta
                        </label>
                        <input 
                            type="text"
                            id="resposta"
                            name="resposta"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                        >
                        <small class="text-gray-500 text-sm mt-1 block">Resposta esperada para esta pergunta</small>
                    </div>

                    <!-- Container para múltipla escolha -->
                    <div id="container-multipla-escolha" class="hidden">
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
                            <div id="opcoes-lista"></div>
                        </div>
                    </div>

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
        </div>

        <div id="nao-encontrado" class="hidden">
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
        </div>
    </div>

    <script>
        let perguntaAtual = null;
        let opcaoCount = 0;

        document.addEventListener('DOMContentLoaded', function() {
            carregarPergunta();
        });

        function carregarPergunta() {
            const urlParams = new URLSearchParams(window.location.search);
            const perguntaId = urlParams.get('id');

            if (!perguntaId) {
                mostrarErro('ID da pergunta não fornecido.');
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('nao-encontrado').classList.remove('hidden');
                return;
            }

            fetch(`api_carregar_pergunta.php?id=${perguntaId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loading').classList.add('hidden');
                    
                    if (data.success && data.pergunta) {
                        perguntaAtual = data.pergunta;
                        preencherFormulario(perguntaAtual);
                        document.getElementById('form-container').classList.remove('hidden');
                    } else {
                        mostrarErro(data.message || 'Pergunta não encontrada.');
                        document.getElementById('nao-encontrado').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    document.getElementById('loading').classList.add('hidden');
                    mostrarErro('Erro ao carregar pergunta: ' + error.message);
                    document.getElementById('nao-encontrado').classList.remove('hidden');
                });
        }

        function preencherFormulario(pergunta) {
            document.getElementById('descricao').value = pergunta.descricao;
            document.getElementById('info-tipo').textContent = pergunta.tipo;
            document.getElementById('info-id').textContent = pergunta.id;
            document.getElementById('info-pergunta').classList.remove('hidden');

            if (pergunta.tipo === 'texto') {
                document.getElementById('container-texto').classList.remove('hidden');
                document.getElementById('resposta').value = pergunta.correta;
                document.getElementById('resposta').required = true;
            } else if (pergunta.tipo === 'multipla_escolha') {
                document.getElementById('container-multipla-escolha').classList.remove('hidden');
                preencherOpcoes(pergunta.opcoes_detalhes || converterStringParaArray(pergunta.opcoes), pergunta.correta);
                configurarMultiplaEscolha();
                document.getElementById('resposta').required = false;
            }
        }

        function preencherOpcoes(opcoes, respostaCorreta) {
            const container = document.getElementById('opcoes-lista');
            container.innerHTML = '';
            opcaoCount = 0;

            opcoes.forEach((opcao, index) => {
                opcaoCount++;
                const opcaoId = opcaoCount;
                const isCorreta = opcao === respostaCorreta;

                const div = document.createElement('div');
                div.className = 'opcao-item bg-gray-50 border border-gray-200 rounded-lg p-4 mb-3 transition-all duration-300';
                div.innerHTML = `
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                name="correta" 
                                value="${opcaoId}" 
                                id="opcao_correta_${opcaoId}" 
                                ${isCorreta ? 'checked' : ''}
                                required
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                            >
                            <label for="opcao_correta_${opcaoId}" class="ml-2 text-sm font-medium text-gray-700">
                                Correta
                            </label>
                        </div>
                        <input 
                            type="text" 
                            name="opcoes[]" 
                            value="${opcao}" 
                            placeholder="Opção ${opcaoId}" 
                            required
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                            data-opcao-id="${opcaoId}"
                        >
                        ${opcaoId >= 2 ? `
                            <button 
                                type="button" 
                                class="remove-opcao text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-all duration-300"
                                title="Remover opção"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        ` : ''}
                    </div>
                `;
                container.appendChild(div);
            });
        }

        function configurarMultiplaEscolha() {
            document.getElementById('add-opcao').addEventListener('click', function() {
                if (opcaoCount >= 10) {
                    alert('Máximo de 10 opções permitidas');
                    return;
                }

                const container = document.getElementById('opcoes-lista');
                opcaoCount++;
                const newOptionValue = opcaoCount;

                const div = document.createElement('div');
                div.className = 'opcao-item bg-gray-50 border border-gray-200 rounded-lg p-4 mb-3 transition-all duration-300';
                div.innerHTML = `
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                name="correta" 
                                value="${newOptionValue}" 
                                id="opcao_correta_${newOptionValue}" 
                                required
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                            >
                            <label for="opcao_correta_${newOptionValue}" class="ml-2 text-sm font-medium text-gray-700">
                                Correta
                            </label>
                        </div>
                        <input 
                            type="text" 
                            name="opcoes[]" 
                            placeholder="Opção ${newOptionValue}" 
                            required
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                            data-opcao-id="${newOptionValue}"
                        >
                        <button 
                            type="button" 
                            class="remove-opcao text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-all duration-300"
                            title="Remover opção"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                container.appendChild(div);
                adicionarEventListenersRemover();
            });

            adicionarEventListenersRemover();
        }

        function adicionarEventListenersRemover() {
            document.querySelectorAll('.remove-opcao').forEach(button => {
                button.addEventListener('click', function() {
                    if (document.querySelectorAll('.opcao-item').length <= 2) {
                        alert('É necessário ter pelo menos 2 opções');
                        return;
                    }
                    this.closest('.opcao-item').remove();
                    opcaoCount--;
                    reorganizarOpcoes();
                });
            });
        }

        function reorganizarOpcoes() {
            const opcoes = document.querySelectorAll('.opcao-item');
            opcoes.forEach((opcao, index) => {
                const newId = index + 1;
                const radio = opcao.querySelector('input[type="radio"]');
                const label = opcao.querySelector('label');
                const textInput = opcao.querySelector('input[type="text"]');
                
                radio.value = newId;
                radio.id = `opcao_correta_${newId}`;
                label.htmlFor = `opcao_correta_${newId}`;
                textInput.placeholder = `Opção ${newId}`;
                textInput.setAttribute('data-opcao-id', newId);
            });
            opcaoCount = opcoes.length;
        }

        document.getElementById('form-editar-pergunta').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validarFormulario()) {
                return;
            }
            
            salvarPergunta();
        });

        function validarFormulario() {
            const descricao = document.getElementById('descricao').value.trim();
            if (!descricao) {
                mostrarErro('A descrição da pergunta é obrigatória.');
                document.getElementById('descricao').focus();
                return false;
            }

            if (perguntaAtual.tipo === 'texto') {
                const resposta = document.getElementById('resposta').value.trim();
                if (!resposta) {
                    mostrarErro('A resposta correta é obrigatória para perguntas de texto.');
                    document.getElementById('resposta').focus();
                    return false;
                }
            } else if (perguntaAtual.tipo === 'multipla_escolha') {
                const opcoes = Array.from(document.querySelectorAll('input[name="opcoes[]"]')).map(input => input.value.trim());
                const opcoesNaoVazias = opcoes.filter(opcao => opcao !== '');
                
                if (opcoesNaoVazias.length < 2) {
                    mostrarErro('Para perguntas de múltipla escolha, forneça pelo menos duas opções.');
                    return false;
                }

                const corretaSelecionada = document.querySelector('input[name="correta"]:checked');
                if (!corretaSelecionada) {
                    mostrarErro('Selecione a opção correta.');
                    return false;
                }
            }

            return true;
        }

        function salvarPergunta() {
            const formData = new FormData();
            formData.append('id', perguntaAtual.id);
            formData.append('descricao', document.getElementById('descricao').value);

            if (perguntaAtual.tipo === 'texto') {
                formData.append('resposta', document.getElementById('resposta').value);
            } else if (perguntaAtual.tipo === 'multipla_escolha') {
                const opcoes = Array.from(document.querySelectorAll('input[name="opcoes[]"]')).map(input => input.value);
                const corretaIndex = document.querySelector('input[name="correta"]:checked').value - 1;
                formData.append('opcoes', JSON.stringify(opcoes));
                formData.append('correta', corretaIndex);
            }

            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('loading').querySelector('span').textContent = 'Salvando alterações...';

            fetch('api_editar_pergunta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading').classList.add('hidden');
                
                if (data.success) {
                    mostrarMensagem('Pergunta atualizada com sucesso!', 'success');
                    setTimeout(() => {
                        window.location.href = 'listar_perguntas.php?msg=editado';
                    }, 1000);
                } else {
                    mostrarErro(data.message || 'Erro ao salvar pergunta.');
                }
            })
            .catch(error => {
                document.getElementById('loading').classList.add('hidden');
                mostrarErro('Erro ao salvar pergunta: ' + error.message);
            });
        }

        function mostrarErro(mensagem) {
            const container = document.getElementById('mensagem-erro');
            container.className = 'bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6';
            container.innerHTML = `<p class="text-red-700 flex items-center"><i class="fas fa-exclamation-circle mr-2"></i>${mensagem}</p>`;
            container.classList.remove('hidden');
        }

        function mostrarMensagem(mensagem, tipo) {
            const container = document.getElementById('mensagem-erro');
            container.className = tipo === 'success' 
                ? 'bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6'
                : 'bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6';
            container.innerHTML = `<p class="flex items-center"><i class="fas ${tipo === 'success' ? 'fa-check' : 'fa-exclamation-circle'} mr-2"></i>${mensagem}</p>`;
            container.classList.remove('hidden');
        }

        function converterStringParaArray(string) {
            if (!string) return [];
            return string.split('|').map(item => item.trim());
        }
    </script>
</body>
</html>