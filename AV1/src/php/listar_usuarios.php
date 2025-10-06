<?php
require_once 'funcoes.php';
session_start();
verificarAcesso(['admin']);

$cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
$usuarios = lerDados(USERS_FILE, $cabecalhosUsuarios);
$mensagem = $_GET['msg'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Usuários</title>
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
                <i class="fas fa-users text-blue-500 mr-2"></i>
                Listagem de Usuários
            </h1>
            <p class="text-gray-600">Gerenciamento de Usuários Water Fall</p>
        </header>

        <?php if ($mensagem): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <div class="mb-6">
            <a href="adicionar_usuario.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Novo Usuário
            </a>
        </div>

        <?php if (empty($usuarios)): ?>
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 mb-4">Nenhum usuário cadastrado.</p>
                <a href="adicionar_usuario.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                    Adicionar Primeiro Usuário
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mail</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($usuario['id']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($usuario['tipo']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>"
                                           class="text-green-600 hover:text-green-900 transition-colors"
                                           title="Editar Usuário">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="excluir_usuario.php?id=<?= $usuario['id'] ?>"
                                           class="text-red-600 hover:text-red-900 transition-colors"
                                           title="Excluir Usuário"
                                           onclick="return confirm('Tem certeza?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 text-sm text-gray-600">
                <p><strong>Total de Usuários:</strong> <?php echo count($usuarios); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>