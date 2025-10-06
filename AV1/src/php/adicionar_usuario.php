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

$erros = []; 
$dadosFormulario = [
    'tipo' => '',
    'nome' => '',
    'email' => '',
    'senha' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dadosFormulario = [
        'tipo' => $_POST['tipo'] ?? '',
        'nome' => $_POST['nome'] ?? '',
        'email' => $_POST['email'] ?? '',
        'senha' => $_POST['senha'] ?? ''
    ];

    if (empty($dadosFormulario['tipo'])) {
        $erros[] = 'O tipo de usuário é obrigatório.';
    }
    if (empty($dadosFormulario['nome'])) {
        $erros[] = 'O nome é obrigatório.';
    }
    if (empty($dadosFormulario['email'])) {
        $erros[] = 'O e-mail é obrigatório.';
    }
    if (empty($dadosFormulario['senha'])) {
        $erros[] = 'A senha é obrigatória.';
    }

    if (empty($erros)) {
        $cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
        $usuarios = lerDados(USERS_FILE, $cabecalhosUsuarios);

        $novoUsuario = [
            'id' => gerarId($usuarios), 
            'tipo' => strtoupper($dadosFormulario['tipo']),
            'nome' => $dadosFormulario['nome'],
            'email' => $dadosFormulario['email'],
            'senha' => password_hash($dadosFormulario['senha'], PASSWORD_DEFAULT)
        ];

        $usuarios[] = $novoUsuario;

        if (salvarDados(USERS_FILE, $usuarios)) {
            header('Location: index.php?msg=adicionado');
            exit;
        } else {
            $erros[] = 'Erro ao salvar usuário.';
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
    <title>Adicionar Usuario</title>
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

    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-plus text-blue-500 mr-3"></i>
                Adicionar Novo Usuário
            </h1>
            <p class="text-gray-600">Cadastre novos usuários no sistema</p>
        </div>

        <!-- Formulário -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <?php exibirErros($erros); ?>
            
            <form method="POST" class="space-y-6">
                <!-- Nome -->
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-blue-500 mr-2"></i>
                        Nome do Usuário
                    </label>
                    <input 
                        type="text" 
                        id="nome" 
                        name="nome" 
                        value="<?php echo htmlspecialchars($dadosFormulario['nome']); ?>"
                        placeholder="Ex: João da Silva"
                        required 
                        maxlength="100"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                    >
                    <small class="text-gray-500 text-sm mt-1 block">Nome completo do usuário</small>
                </div>

                <!-- Tipo -->
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag text-green-500 mr-2"></i>
                        Tipo de Usuário
                    </label>
                    <select 
                        id="tipo" 
                        name="tipo" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 uppercase"
                    >
                        <option value="">Selecione o tipo</option>
                        <option value="ADM" <?php echo ($dadosFormulario['tipo'] == 'ADM') ? 'selected' : ''; ?>>Administrador</option>
                        <option value="USER" <?php echo ($dadosFormulario['tipo'] == 'USER') ? 'selected' : ''; ?>>Usuário Comum</option>
                    </select>
                    <small class="text-gray-500 text-sm mt-1 block">Tipo de acesso do usuário</small>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope text-purple-500 mr-2"></i>
                        E-Mail
                    </label>
                    <input 
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($dadosFormulario['email']); ?>"
                        placeholder="Ex: example@example.com"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                    >
                    <small class="text-gray-500 text-sm mt-1 block">E-mail para acesso ao sistema</small>
                </div>

                <!-- Senha -->
                <div>
                    <label for="senha" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock text-red-500 mr-2"></i>
                        Senha
                    </label>
                    <input 
                        type="password"
                        id="senha"
                        name="senha"
                        value=""
                        placeholder="Digite uma senha segura"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                    >
                    <small class="text-gray-500 text-sm mt-1 block">Senha para acesso ao sistema</small>
                </div>

                <!-- Botões -->
                <div class="flex flex-wrap gap-4 justify-between items-center pt-6 border-t border-gray-200">
                    <button 
                        type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                    >
                        <i class="fas fa-save mr-2"></i>
                        Salvar Usuário
                    </button>
                    <a 
                        href="index.php" 
                        class="text-gray-600 hover:text-gray-800 px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center"
                    >
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Informações -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
            <div class="flex items-start">
                <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-yellow-800 mb-1">Dicas Importantes</h3>
                    <ul class="text-yellow-700 text-sm space-y-1">
                        <li>• Use senhas fortes com letras, números e símbolos</li>
                        <li>• ADMIN tem acesso completo ao sistema</li>
                        <li>• USER tem acesso limitado às funcionalidades</li>
                        <li>• Verifique se o e-mail está correto antes de salvar</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>