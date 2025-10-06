<?php
require_once 'funcoes.php';
session_start();

$caminhoArquivo = '../../arquivos/users.txt';

// Se confirmado via POST, proceder com a exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idParaExcluir = $_POST['id'];
    $cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
    $usuarios = lerDados($caminhoArquivo, $cabecalhosUsuarios);
    $usuariosAtualizados = [];
    $usuarioEncontrado = false;

    foreach ($usuarios as $usuario) {
        if ($usuario['id'] != $idParaExcluir) {
            $usuariosAtualizados[] = $usuario;
        } else {
            $usuarioEncontrado = true;
        }
    }

    if ($usuarioEncontrado) {
        salvarDados($caminhoArquivo, $usuariosAtualizados);
        header('Location: listar_usuarios.php?msg=excluido');
        exit();
    } else {
        header('Location: listar_usuarios.php?erro=nao_encontrado');
        exit();
    }
}

// Se não for POST, mostrar página de confirmação
if (isset($_GET['id'])) {
    $idParaExcluir = $_GET['id'];
    $cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
    $usuarios = lerDados($caminhoArquivo, $cabecalhosUsuarios);
    $usuarioParaExcluir = null;

    foreach ($usuarios as $usuario) {
        if ($usuario['id'] == $idParaExcluir) {
            $usuarioParaExcluir = $usuario;
            break;
        }
    }

    if (!$usuarioParaExcluir) {
        header('Location: listar_usuarios.php?erro=nao_encontrado');
        exit();
    }
} else {
    header('Location: listar_usuarios.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Exclusão de Usuário</title>
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
                    <i class="fas fa-user-slash text-2xl text-red-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Confirmar Exclusão</h1>
                <p class="text-gray-600">Tem certeza que deseja excluir este usuário?</p>
            </div>

            <!-- Detalhes do Usuário -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-red-500 mt-1 mr-3"></i>
                    <div class="w-full">
                        <h3 class="font-semibold text-red-800 mb-3">Usuário a ser excluído:</h3>
                        <div class="bg-white rounded-lg p-4 border">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Nome</label>
                                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($usuarioParaExcluir['nome']); ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">E-mail</label>
                                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($usuarioParaExcluir['email']); ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Tipo</label>
                                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($usuarioParaExcluir['tipo']); ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">ID</label>
                                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($usuarioParaExcluir['id']); ?></p>
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
                            Esta ação não pode ser desfeita. O usuário perderá permanentemente o acesso ao sistema.
                            Todas as respostas e dados associados a este usuário serão mantidos para histórico.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulário de Confirmação -->
            <form method="POST" class="space-y-4">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuarioParaExcluir['id']); ?>">
                
                <div class="flex flex-wrap gap-4 justify-center">
                    <button 
                        type="submit" 
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center"
                    >
                        <i class="fas fa-trash mr-2"></i>
                        Sim, Excluir Usuário
                    </button>
                    <a 
                        href="listar_usuarios.php" 
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