<?php
require_once 'funcoes.php';
session_start();
verificarAcesso(['admin']);

$id = $_GET['id'] ?? null;
$cabecalhosRespostas = ['id', 'id_usuario', 'id_pergunta', 'resposta_dada', 'data_hora'];
$respostas = lerDados(ANSWERS_FILE, $cabecalhosRespostas);
$erro = '';
$respostaAtual = null;
if ($id) {
    foreach ($respostas as $r) {
        if ($r['id'] == $id) {
            $respostaAtual = $r;
            break;
        }
    }
}

if (!$respostaAtual) {
    header('Location: listar_respostas.php?msg=Resposta não encontrada');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova = trim($_POST['resposta_dada'] ?? '');
    if ($nova === '') {
        $erro = 'Resposta não pode ser vazia.';
    } else {
        foreach ($respostas as &$r) {
            if ($r['id'] == $id) {
                $r['resposta_dada'] = $nova;
                $r['data_hora'] = date('Y-m-d H:i:s');
                break;
            }
        }
        unset($r);
        if (salvarDados(ANSWERS_FILE, $respostas)) {
            header('Location: listar_respostas.php?msg=Resposta atualizada');
            exit;
        } else {
            $erro = 'Erro ao salvar.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Resposta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../html/menu_admin.html'; ?>
    
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-edit text-blue-500 mr-3"></i>
                Editar Resposta
            </h1>
            <p class="text-gray-600">Atualize a resposta do usuário</p>
            <div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3 inline-block">
                <span class="text-blue-700 text-sm">
                    <i class="fas fa-info-circle mr-1"></i>
                    ID Resposta: <strong><?php echo htmlspecialchars($respostaAtual['id']); ?></strong>
                </span>
            </div>
        </div>

        <!-- Alertas -->
        <?php if ($erro): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                    <span class="text-red-700"><?php echo htmlspecialchars($erro); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Formulário -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <form method="POST" class="space-y-6">
                <!-- Informações da Resposta -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">ID Usuário</label>
                        <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($respostaAtual['id_usuario']); ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">ID Pergunta</label>
                        <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($respostaAtual['id_pergunta']); ?></p>
                    </div>
                </div>

                <!-- Campo de Resposta -->
                <div>
                    <label for="resposta_dada" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment text-green-500 mr-2"></i>
                        Resposta do Usuário
                    </label>
                    <textarea 
                        name="resposta_dada" 
                        required
                        rows="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 resize-none"
                        placeholder="Digite a resposta corrigida..."
                    ><?php echo htmlspecialchars($respostaAtual['resposta_dada']); ?></textarea>
                    <small class="text-gray-500 text-sm mt-1 block">
                        <i class="fas fa-clock mr-1"></i>
                        Última atualização: <?php echo htmlspecialchars($respostaAtual['data_hora']); ?>
                    </small>
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
                        href="listar_respostas.php" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Voltar para Lista
                    </a>
                </div>
            </form>
        </div>

        <!-- Informações -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
            <div class="flex items-start">
                <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-yellow-800 mb-1">Atenção</h3>
                    <p class="text-yellow-700 text-sm">
                        Ao editar esta resposta, você está modificando a resposta original do usuário. 
                        Esta ação ficará registrada no histórico do sistema.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>