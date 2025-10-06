<?php
require_once 'funcoes.php';
session_start();
verificarAcesso(['admin']);

$id = $_GET['id'] ?? null;

// Se confirmado via POST, proceder com a exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    $cabecalhosRespostas = ['id', 'id_usuario', 'id_pergunta', 'resposta_dada', 'data_hora'];
    $respostas = lerDados(ANSWERS_FILE, $cabecalhosRespostas);
    
    $filtradas = array_filter($respostas, function($r) use ($id) {
        return $r['id'] != $id;
    });
    
    if (count($filtradas) < count($respostas)) {
        if (salvarDados(ANSWERS_FILE, array_values($filtradas))) {
            header('Location: listar_respostas.php?msg=excluido');
            exit;
        }
    }
    header('Location: listar_respostas.php?msg=erro');
    exit;
}

// Se não for POST, mostrar página de confirmação
if ($id) {
    $cabecalhosRespostas = ['id', 'id_usuario', 'id_pergunta', 'resposta_dada', 'data_hora'];
    $respostas = lerDados(ANSWERS_FILE, $cabecalhosRespostas);
    $respostaAtual = null;
    
    foreach ($respostas as $r) {
        if ($r['id'] == $id) {
            $respostaAtual = $r;
            break;
        }
    }
    
    if (!$respostaAtual) {
        header('Location: listar_respostas.php?msg=nao_encontrada');
        exit;
    }
} else {
    header('Location: listar_respostas.php?msg=id_nao_fornecido');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Exclusão de Resposta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../html/menu_admin.html'; ?>
    
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comment-slash text-2xl text-red-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Confirmar Exclusão</h1>
                <p class="text-gray-600">Tem certeza que deseja excluir esta resposta?</p>
            </div>

            <!-- Detalhes da Resposta -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-red-500 mt-1 mr-3"></i>
                    <div class="w-full">
                        <h3 class="font-semibold text-red-800 mb-3">Resposta a ser excluída:</h3>
                        <div class="bg-white rounded-lg p-4 border">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">ID Resposta</label>
                                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($respostaAtual['id']); ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">ID Usuário</label>
                                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($respostaAtual['id_usuario']); ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">ID Pergunta</label>
                                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($respostaAtual['id_pergunta']); ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Data/Hora</label>
                                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($respostaAtual['data_hora']); ?></p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Resposta</label>
                                <div class="bg-gray-50 rounded-lg p-3 border">
                                    <p class="text-gray-800"><?php echo htmlspecialchars($respostaAtual['resposta_dada']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aviso Importante -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-yellow-500 mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800 mb-1">Atenção!</h3>
                        <p class="text-yellow-700 text-sm">
                            Esta ação não pode ser desfeita. A resposta será permanentemente removida do sistema.
                            Esta ação é recomendada apenas para correção de dados incorretos.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulário de Confirmação -->
            <form method="POST" class="space-y-4">
                <div class="flex flex-wrap gap-4 justify-center">
                    <button 
                        type="submit" 
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                    >
                        <i class="fas fa-trash mr-2"></i>
                        Sim, Excluir Resposta
                    </button>
                    <a 
                        href="listar_respostas.php" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                    >
                        <i class="fas fa-times mr-2"></i>
                        Cancelar e Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>