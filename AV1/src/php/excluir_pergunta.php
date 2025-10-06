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

$perguntaId = $_GET['id'] ?? null;

// Se confirmado via POST, proceder com a exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $perguntaId) {
    $cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
    $perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);
    $perguntasAtualizadas = [];
    $encontrada = false;

    foreach ($perguntas as $p) {
        if ($p['id'] == $perguntaId) {
            $encontrada = true;
        } else {
            $perguntasAtualizadas[] = $p;
        }
    }

    if ($encontrada) {
        if (salvarDados(QUESTIONS_FILE, $perguntasAtualizadas)) {
            header('Location: listar_perguntas.php?msg=excluido');
            exit;
        } else {
            header('Location: listar_perguntas.php?erro=salvar');
            exit;
        }
    } else {
        header('Location: listar_perguntas.php?erro=nao_encontrada');
        exit;
    }
}

// Se não for POST, mostrar página de confirmação
if ($perguntaId) {
    $cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
    $perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);
    $pergunta = null;
    
    foreach ($perguntas as $p) {
        if ($p['id'] == $perguntaId) {
            $pergunta = $p;
            break;
        }
    }
    
    if (!$pergunta) {
        header('Location: listar_perguntas.php?erro=nao_encontrada');
        exit;
    }
} else {
    header('Location: listar_perguntas.php?erro=id_nao_fornecido');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Exclusão</title>
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
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Confirmar Exclusão</h1>
                <p class="text-gray-600">Tem certeza que deseja excluir esta pergunta?</p>
            </div>

            <!-- Detalhes da Pergunta -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-red-500 mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-red-800 mb-2">Pergunta a ser excluída:</h3>
                        <div class="bg-white rounded-lg p-3 border">
                            <p class="text-gray-800 font-medium mb-2"><?php echo htmlspecialchars($pergunta['descricao']); ?></p>
                            <div class="flex flex-wrap gap-2 text-sm text-gray-600">
                                <span class="bg-blue-100 px-2 py-1 rounded">ID: <?php echo htmlspecialchars($pergunta['id']); ?></span>
                                <span class="bg-green-100 px-2 py-1 rounded">Tipo: <?php echo htmlspecialchars($pergunta['tipo']); ?></span>
                                <span class="bg-purple-100 px-2 py-1 rounded">Resposta: <?php echo htmlspecialchars($pergunta['correta']); ?></span>
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
                            Esta ação não pode ser desfeita. A pergunta será permanentemente removida do sistema.
                            Todas as respostas associadas a esta pergunta também serão afetadas.
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
                        Sim, Excluir Permanentemente
                    </button>
                    <a 
                        href="listar_perguntas.php" 
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