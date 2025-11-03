<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Alunos - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#64748b',
                        success: '#10b981',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                        info: '#06b6d4'
                    }
                }
            }
        }
    </script>
    <style>
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .table-row-hover:hover {
            background-color: #f8fafc;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-gradient-to-r from-primary to-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-user-graduate text-white text-2xl"></i>
                    <h1 class="text-white text-2xl font-bold">Sistema de Gestão de Alunos</h1>
                </div>
                <div class="text-white text-sm">
                    <span id="currentDate"></span>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total de Alunos</p>
                        <p id="totalAlunos" class="text-3xl font-bold text-gray-900">0</p>
                    </div>
                    <i class="fas fa-users text-primary text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-success">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">1º Ano</p>
                        <p id="totalPrimeiroAno" class="text-3xl font-bold text-gray-900">0</p>
                    </div>
                    <i class="fas fa-graduation-cap text-success text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-warning">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">2º Ano</p>
                        <p id="totalSegundoAno" class="text-3xl font-bold text-gray-900">0</p>
                    </div>
                    <i class="fas fa-book text-warning text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-info">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">3º Ano</p>
                        <p id="totalTerceiroAno" class="text-3xl font-bold text-gray-900">0</p>
                    </div>
                    <i class="fas fa-award text-info text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
            <h2 class="text-2xl font-bold text-gray-800">Lista de Alunos</h2>
            <div class="flex space-x-3">
                <button onclick="loadAlunos()" class="bg-secondary hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center space-x-2">
                    <i class="fas fa-refresh"></i>
                    <span>Atualizar</span>
                </button>
                <button onclick="openCreateModal()" class="bg-success hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Novo Aluno</span>
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden fade-in">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Série</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="alunosTable" class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
            
            <div id="emptyState" class="hidden text-center py-12">
                <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum aluno cadastrado</h3>
                <p class="text-gray-500 mb-4">Comece adicionando um novo aluno ao sistema.</p>
                <button onclick="openCreateModal()" class="bg-success hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    Adicionar Primeiro Aluno
                </button>
            </div>
        </div>
    </main>
    <div id="alunoModal" class="fixed inset-0 z-50 hidden">
        <div class="modal-backdrop fixed inset-0 bg-black opacity-50" onclick="closeModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto fade-in relative">
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Adicionar Aluno</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <form id="alunoForm" class="space-y-4">
                        <input type="hidden" id="alunoId">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                                <input type="text" id="nome" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" id="email" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="telefone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                <input type="text" id="telefone" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200">
                            </div>
                            
                            <div>
                                <label for="data_nascimento" class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                                <input type="date" id="data_nascimento" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200">
                            </div>
                        </div>
                        
                        <div>
                            <label for="serie" class="block text-sm font-medium text-gray-700 mb-1">Série</label>
                            <select id="serie" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200">
                                <option value="">Selecione a série</option>
                                <option value="1º Ano">1º Ano</option>
                                <option value="2º Ano">2º Ano</option>
                                <option value="3º Ano">3º Ano</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="endereco" class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                            <textarea id="endereco" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200"></textarea>
                        </div>
                    </form>
                </div>
                
                <div class="border-t border-gray-200 px-6 py-4">
                    <div class="flex justify-end space-x-3">
                        <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition duration-200 font-medium">
                            Cancelar
                        </button>
                        <button onclick="saveAluno()" class="bg-primary hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition duration-200 font-medium flex items-center space-x-2">
                            <i class="fas fa-save"></i>
                            <span>Salvar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingSpinner" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
        <div class="text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-primary mx-auto"></div>
            <p class="mt-4 text-gray-600 font-medium">Carregando dados...</p>
        </div>
    </div>

    <script>
        let alunos = [];
        let currentEditingId = null;

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('currentDate').textContent = new Date().toLocaleDateString('pt-BR');
            loadAlunos();
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        });

        function showLoading() {
            document.getElementById('loadingSpinner').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').classList.add('hidden');
        }

        function loadAlunos() {
            showLoading();
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'api.php', true);
            xhr.onload = function() {
                hideLoading();
                if (xhr.status === 200) {
                    try {
                        alunos = JSON.parse(xhr.responseText);
                        renderAlunos();
                        updateStats();
                    } catch (e) {
                        showNotification('Erro ao processar dados dos alunos', 'error');
                        console.error('Erro no parse JSON:', e);
                    }
                } else {
                    showNotification('Erro ao carregar alunos', 'error');
                }
            };
            xhr.onerror = function() {
                hideLoading();
                showNotification('Erro de conexão', 'error');
            };
            xhr.send();
        }

        function updateStats() {
            document.getElementById('totalAlunos').textContent = alunos.length;
            document.getElementById('totalPrimeiroAno').textContent = alunos.filter(a => a.serie === '1º Ano').length;
            document.getElementById('totalSegundoAno').textContent = alunos.filter(a => a.serie === '2º Ano').length;
            document.getElementById('totalTerceiroAno').textContent = alunos.filter(a => a.serie === '3º Ano').length;
        }

        function renderAlunos() {
            const tbody = document.getElementById('alunosTable');
            const emptyState = document.getElementById('emptyState');

            if (alunos.length === 0) {
                tbody.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            tbody.innerHTML = '';

            alunos.forEach(aluno => {
                const tr = document.createElement('tr');
                tr.className = 'table-row-hover';
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${aluno.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">${aluno.nome}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${aluno.email || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${aluno.telefone || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getSerieBadgeColor(aluno.serie)}">
                            ${aluno.serie || 'Não informada'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <button onclick="editAluno(${aluno.id})" class="text-warning hover:text-amber-600 transition duration-200" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteAluno(${aluno.id})" class="text-danger hover:text-red-600 transition duration-200" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function getSerieBadgeColor(serie) {
            switch(serie) {
                case '1º Ano': return 'bg-green-100 text-green-800';
                case '2º Ano': return 'bg-yellow-100 text-yellow-800';
                case '3º Ano': return 'bg-blue-100 text-blue-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Adicionar Aluno';
            document.getElementById('alunoForm').reset();
            document.getElementById('alunoId').value = '';
            currentEditingId = null;
            
            const modal = document.getElementById('alunoModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function editAluno(id) {
            console.log('Editando aluno ID:', id); // Debug
            const aluno = alunos.find(a => a.id == id);
            if (!aluno) {
                showNotification('Aluno não encontrado', 'error');
                return;
            }

            document.getElementById('modalTitle').textContent = 'Editar Aluno';
            document.getElementById('alunoId').value = aluno.id;
            document.getElementById('nome').value = aluno.nome || '';
            document.getElementById('email').value = aluno.email || '';
            document.getElementById('telefone').value = aluno.telefone || '';
            document.getElementById('data_nascimento').value = aluno.data_nascimento || '';
            document.getElementById('endereco').value = aluno.endereco || '';
            document.getElementById('serie').value = aluno.serie || '';
            
            currentEditingId = aluno.id;
            
            const modal = document.getElementById('alunoModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            console.log('Modal deve estar aberto agora'); // Debug
        }

        function closeModal() {
            const modal = document.getElementById('alunoModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            currentEditingId = null;
        }

        function saveAluno() {
            const id = document.getElementById('alunoId').value;
            const alunoData = {
                nome: document.getElementById('nome').value,
                email: document.getElementById('email').value,
                telefone: document.getElementById('telefone').value,
                data_nascimento: document.getElementById('data_nascimento').value,
                endereco: document.getElementById('endereco').value,
                serie: document.getElementById('serie').value
            };

            if (!alunoData.nome.trim()) {
                showNotification('Nome é obrigatório', 'warning');
                return;
            }

            showLoading();
            const xhr = new XMLHttpRequest();
            const url = 'api.php';
            
            if (id) {
                alunoData.id = id;
                xhr.open('PUT', url, true);
            } else {
                xhr.open('POST', url, true);
            }

            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function() {
                hideLoading();
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            closeModal();
                            loadAlunos();
                            showNotification(response.message, 'success');
                        } else {
                            showNotification('Erro: ' + response.error, 'error');
                        }
                    } catch (e) {
                        showNotification('Erro ao processar resposta', 'error');
                        console.error('Erro no parse JSON:', e);
                    }
                } else {
                    showNotification('Erro na requisição: ' + xhr.status, 'error');
                }
            };

            xhr.onerror = function() {
                hideLoading();
                showNotification('Erro de conexão', 'error');
            };

            xhr.send(JSON.stringify(alunoData));
        }

        function deleteAluno(id) {
            const aluno = alunos.find(a => a.id == id);
            if (!aluno) return;

            if (!confirm(`Tem certeza que deseja excluir o aluno "${aluno.nome}"? Esta ação não pode ser desfeita.`)) {
                return;
            }

            showLoading();
            const xhr = new XMLHttpRequest();
            xhr.open('DELETE', 'api.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function() {
                hideLoading();
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            loadAlunos();
                            showNotification(response.message, 'success');
                        } else {
                            showNotification('Erro: ' + response.error, 'error');
                        }
                    } catch (e) {
                        showNotification('Erro ao processar resposta', 'error');
                        console.error('Erro no parse JSON:', e);
                    }
                } else {
                    showNotification('Erro na requisição: ' + xhr.status, 'error');
                }
            };

            xhr.onerror = function() {
                hideLoading();
                showNotification('Erro de conexão', 'error');
            };

            xhr.send(JSON.stringify({ id: id }));
        }

        function showNotification(message, type = 'info') {
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(notif => notif.remove());

            const colors = {
                success: 'bg-green-500 border-green-600',
                error: 'bg-red-500 border-red-600',
                warning: 'bg-yellow-500 border-yellow-600',
                info: 'bg-blue-500 border-blue-600'
            };

            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg border-l-4 max-w-sm z-50 fade-in`;
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas ${icons[type]}"></i>
                    <span class="font-medium">${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }
    </script>
</body>
</html>