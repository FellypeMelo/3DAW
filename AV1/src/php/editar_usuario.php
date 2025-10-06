<?php
require_once 'funcoes.php';
session_start();
$caminhoArquivo = '../../arquivos/users.txt';
$cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
$usuarios = lerDados($caminhoArquivo, $cabecalhosUsuarios);
$usuarioParaEditar = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    foreach ($usuarios as $key => $usuario) {
        if ($usuario['id'] == $id) {
            $usuarioParaEditar = $usuario;
            $usuarioParaEditar['index'] = $key;
            break;
        }
    }
}

if (!$usuarioParaEditar) {
    header('Location: listar_usuarios.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $tipo = $_POST['tipo'];
    $novaSenha = $_POST['senha'];

    foreach ($usuarios as $key => $usuario) {
        if ($usuario['id'] == $id) {
            $usuarios[$key]['nome'] = $nome;
            $usuarios[$key]['email'] = $email;
            $usuarios[$key]['tipo'] = $tipo;

            if (!empty($novaSenha)) {
                $usuarios[$key]['senha'] = password_hash($novaSenha, PASSWORD_DEFAULT);
            }
            break;
        }
    }

    salvarDados($caminhoArquivo, $usuarios);
    header('Location: listar_usuarios.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
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

    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-edit text-blue-500 mr-3"></i>
                Editar Usuário
            </h1>
            <p class="text-gray-600">Atualize os dados do usuário</p>
            <div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3 inline-block">
                <span class="text-blue-700 text-sm">
                    <i class="fas fa-info-circle mr-1"></i>
                    ID: <strong><?php echo htmlspecialchars($usuarioParaEditar['id']); ?></strong>
                </span>
            </div>
        </div>

        <!-- Formulário -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <form method="POST" class="space-y-6">
                <input type="hidden" name="id" value="<?= htmlspecialchars($usuarioParaEditar['id']) ?>">

                <!-- Nome -->
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-blue-500 mr-2"></i>
                        Nome Completo
                    </label>
                    <input 
                        type="text" 
                        id="nome" 
                        name="nome" 
                        value="<?= htmlspecialchars($usuarioParaEditar['nome']) ?>" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                    >
                    <small class="text-gray-500 text-sm mt-1 block">Nome completo do usuário</small>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope text-purple-500 mr-2"></i>
                        E-mail
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?= htmlspecialchars($usuarioParaEditar['email']) ?>" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                    >
                    <small class="text-gray-500 text-sm mt-1 block">E-mail para acesso ao sistema</small>
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
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                    >
                        <option value="admin" <?= ($usuarioParaEditar['tipo'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                        <option value="user" <?= ($usuarioParaEditar['tipo'] === 'user') ? 'selected' : '' ?>>Usuário Comum</option>
                    </select>
                    <small class="text-gray-500 text-sm mt-1 block">Nível de acesso do usuário</small>
                </div>

                <!-- Senha -->
                <div>
                    <label for="senha" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock text-red-500 mr-2"></i>
                        Nova Senha
                    </label>
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha"
                        placeholder="Deixe em branco para manter a senha atual"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                    >
                    <small class="text-gray-500 text-sm mt-1 block">
                        Digite uma nova senha apenas se desejar alterar a atual
                    </small>
                </div>

                <!-- Botões -->
                <div class="flex flex-wrap gap-4 justify-between items-center pt-6 border-t border-gray-200">
                    <button 
                        type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                    >
                        <i class="fas fa-save mr-2"></i>
                        Atualizar Usuário
                    </button>
                    <a 
                        href="listar_usuarios.php" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
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
                    <h3 class="font-semibold text-yellow-800 mb-1">Informações Importantes</h3>
                    <ul class="text-yellow-700 text-sm space-y-1">
                        <li>• <strong>Administrador:</strong> Acesso completo a todas as funcionalidades</li>
                        <li>• <strong>Usuário Comum:</strong> Acesso limitado para responder perguntas</li>
                        <li>• A senha só será alterada se um novo valor for fornecido</li>
                        <li>• O e-mail será usado para login no sistema</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>