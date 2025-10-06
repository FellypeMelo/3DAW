<?php
require_once 'funcoes.php';

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../html/index.html');
    exit;
}

$erros = [];
$pergunta = null;
$perguntaId = $_GET['id'] ?? null;

if ($perguntaId) {
    $cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
    $perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);
    foreach ($perguntas as $p) {
        if ($p['id'] == $perguntaId) {
            $pergunta = $p;
            break;
        }
    }

    if (!$pergunta) {
        $erros[] = 'Pergunta não encontrada.';
    } else {
        if ($pergunta['tipo'] === 'multipla_escolha') {
            $pergunta['opcoes_detalhes'] = converterStringParaArray($pergunta['opcoes']);
        }
    }
} else {
    $erros[] = 'ID da pergunta não fornecido.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erros) && $pergunta) {
    $id_usuario = null;
    if (isset($_SESSION['user'])) {
        if (is_array($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            $id_usuario = $_SESSION['user']['id'];
        } elseif (is_string($_SESSION['user'])) {
            $cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
            $usuarios = lerDados(USERS_FILE, $cabecalhosUsuarios);
            foreach ($usuarios as $u) {
                if (isset($u['nome']) && $u['nome'] === $_SESSION['user']) {
                    $id_usuario = $u['id'];
                    break;
                }
            }
        }
    }
    $id_pergunta = $pergunta['id'] ?? null;
    $resposta = $_POST['resposta_usuario'] ?? '';

    if (!$id_usuario) {
        $erros[] = 'ID do usuário não encontrado na sessão.';
    }
    if (!$id_pergunta) {
        $erros[] = 'ID da pergunta não encontrado.';
    }
    if (empty($resposta)) {
        $erros[] = 'A resposta não pode ser vazia.';
    }

    if (empty($erros)) {
        $cabecalhosRespostas = ['id', 'id_usuario', 'id_pergunta', 'resposta_dada', 'data_hora'];
        $respostasExistentes = lerDados(ANSWERS_FILE, $cabecalhosRespostas);
        $novoId = gerarId($respostasExistentes);
        $novaResposta = [
            'id' => $novoId,
            'id_usuario' => $id_usuario,
            'id_pergunta' => $id_pergunta,
            'resposta_dada' => $resposta,
            'data_hora' => date('Y-m-d H:i:s')
        ];
        $respostasExistentes[] = $novaResposta;

        if (salvarDados(ANSWERS_FILE, $respostasExistentes)) {
            header('Location: listar_perguntas_user.php?status=ok');
            exit;
        } else {
            $erros[] = 'Erro ao salvar a resposta.';
        }
    }
}

function exibirErros(array $erros) {
    if (!empty($erros)) {
        echo '<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">';
        foreach ($erros as $erro) {
            echo '<p class="text-red-700 flex items-center"><i class="fas fa-exclamation-circle mr-2"></i>' . $erro . '</p>';
        }
        echo '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Pergunta</title>
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
        <header class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-eye text-blue-500 mr-2"></i>
                Detalhes da Pergunta
            </h1>
            <p class="text-gray-600">Visualize e responda às perguntas do sistema</p>
        </header>

        <?php exibirErros($erros); ?>

        <?php if ($pergunta): ?>
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Informações da Pergunta</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-600">ID</p>
                        <p class="font-medium"><?php echo htmlspecialchars($pergunta['id']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tipo</p>
                        <p class="font-medium">
                            <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                <?php echo htmlspecialchars($pergunta['tipo']); ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600">Descrição</p>
                    <p class="text-lg text-gray-800 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <?php echo htmlspecialchars($pergunta['descricao']); ?>
                    </p>
                </div>

                <?php if ($_SESSION['tipo'] == 'admin'): ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
                        <h3 class="font-semibold text-yellow-800 mb-2 flex items-center">
                            <i class="fas fa-lock mr-2"></i>
                            Informações do Administrador
                        </h3>
                        
                        <?php if ($pergunta['tipo'] === 'texto'): ?>
                            <p class="text-yellow-700">
                                <strong>Resposta Correta:</strong> 
                                <span class="bg-yellow-100 px-2 py-1 rounded"><?php echo htmlspecialchars($pergunta['correta']); ?></span>
                            </p>
                        <?php elseif ($pergunta['tipo'] === 'multipla_escolha'): ?>
                            <p class="text-yellow-700 font-semibold mb-2">Opções:</p>
                            <ul class="space-y-2">
                                <?php foreach ($pergunta['opcoes_detalhes'] as $opcaoTexto): ?>
                                    <li class="flex items-center">
                                        <span class="w-6 h-6 flex items-center justify-center rounded-full mr-2 
                                            <?php echo $opcaoTexto === $pergunta['correta'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'; ?>">
                                            <?php echo $opcaoTexto === $pergunta['correta'] ? '✓' : '○'; ?>
                                        </span>
                                        <span class="<?php echo $opcaoTexto === $pergunta['correta'] ? 'text-green-700 font-semibold' : 'text-gray-700'; ?>">
                                            <?php echo htmlspecialchars($opcaoTexto); ?>
                                        </span>
                                        <?php if ($opcaoTexto === $pergunta['correta']): ?>
                                            <span class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Correta</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Responder Pergunta</h2>
                
                <form method="POST" class="space-y-6">
                    <?php if ($pergunta['tipo'] === 'texto'): ?>
                        <div class="space-y-2">
                            <label for="resposta_usuario" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-keyboard text-blue-500 mr-2"></i>
                                Sua Resposta
                            </label>
                            <input type="text"
                                   id="resposta_usuario"
                                   name="resposta_usuario"
                                   placeholder="Digite sua resposta aqui"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
                            <small class="text-gray-500 text-sm">Digite sua resposta para esta pergunta de texto</small>
                        </div>
                    <?php elseif ($pergunta['tipo'] === 'multipla_escolha'): ?>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-list-ol text-blue-500 mr-2"></i>
                                Selecione uma opção
                            </label>
                            <div class="space-y-3">
                                <?php foreach ($pergunta['opcoes_detalhes'] as $index => $opcaoTexto): ?>
                                    <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                        <input type="radio"
                                               id="opcao_<?php echo htmlspecialchars($index + 1); ?>"
                                               name="resposta_usuario"
                                               value="<?php echo htmlspecialchars($opcaoTexto); ?>"
                                               required
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="opcao_<?php echo htmlspecialchars($index + 1); ?>" 
                                               class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                                            <?php echo htmlspecialchars($opcaoTexto); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <small class="text-gray-500 text-sm">Selecione a opção que você considera correta</small>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex flex-wrap gap-4 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Enviar Resposta
                        </button>
                        <a href="<?php echo ($_SESSION['tipo'] === 'admin') ? 'listar_perguntas.php' : 'listar_perguntas_user.php'; ?>" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Voltar para Lista de Perguntas
                        </a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mb-4"></i>
                <p class="text-gray-600 mb-4">Não foi possível carregar os detalhes da pergunta.</p>
                <a href="listar_perguntas.php" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                    Voltar para Lista de Perguntas
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>