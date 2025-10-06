<?php
require_once 'funcoes.php';

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../html/index.html');
    exit;
}

$userId = null;
if (isset($_SESSION['user'])) {
    if (is_array($_SESSION['user']) && isset($_SESSION['user']['id'])) {
        $userId = $_SESSION['user']['id'];
    } elseif (is_string($_SESSION['user'])) {
        $cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
        $usuarios = lerDados(USERS_FILE, $cabecalhosUsuarios);
        foreach ($usuarios as $u) {
            if (isset($u['nome']) && $u['nome'] === $_SESSION['user']) {
                $userId = $u['id'];
                break;
            }
        }
    }
}
$cabecalhosRespostas = ['id', 'id_usuario', 'id_pergunta', 'resposta_dada', 'data_hora'];
$respostas = lerDados(ANSWERS_FILE, $cabecalhosRespostas);

$minhas = array_filter($respostas, function($r) use ($userId) {
    return isset($r['id_usuario']) && $r['id_usuario'] == $userId;
});

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Respostas</title>
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
            <i class="fas fa-history text-blue-500 mr-2"></i>
            Minhas Respostas
        </h1>
        <?php if (empty($minhas)): ?>
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Você ainda não respondeu nenhuma pergunta.</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pergunta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resposta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($minhas as $r): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($r['id']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($r['id_pergunta']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($r['resposta_dada'] ?? $r['resposta'] ?? ''); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($r['data_hora'] ?? $r['data_resposta'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>