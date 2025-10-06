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

$cabecalhosRespostas = ['id', 'id_usuario', 'id_pergunta', 'resposta_dada', 'data_hora'];
$respostas = lerDados(ANSWERS_FILE, $cabecalhosRespostas);
$mensagem = $_GET['msg'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Respostas</title>
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
                <i class="fas fa-list-check text-blue-500 mr-2"></i>
                Listagem de Respostas dos Usuários
            </h1>
            <p class="text-gray-600">Gerenciamento de Respostas de Usuários Water Fall</p>
        </header>

        <?php if ($mensagem): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($respostas)): ?>
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Nenhuma resposta cadastrada.</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Usuário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pergunta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resposta Dada</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($respostas as $resposta): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($resposta['id']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($resposta['id_usuario']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($resposta['id_pergunta']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($resposta['resposta_dada']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($resposta['data_hora']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <a href="editar_resposta.php?id=<?php echo $resposta['id']; ?>"
                                           class="text-green-600 hover:text-green-900 transition-colors"
                                           title="Editar Resposta">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="excluir_resposta.php?id=<?php echo $resposta['id']; ?>"
                                           class="text-red-600 hover:text-red-900 transition-colors"
                                           title="Excluir Resposta">
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
                <p><strong>Total de Respostas:</strong> <?php echo count($respostas); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>