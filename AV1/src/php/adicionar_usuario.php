<?php
require_once 'funcoes.php'; // Inclui o arquivo de funções

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
        echo '<div class="erros">';
        foreach ($erros as $erro) {
            echo "<p>{$erro}</p>";
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
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    
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

    <div class="container">
        <?php exibirErros($erros); ?>
        <form method="POST" class="form-usuario">
            <div class="form-group">
                <label for="nome">Nome do Usuario</label>
                <input type="text" 
                       id="nome" 
                       name="nome" 
                       value="<?php echo htmlspecialchars($dadosFormulario['nome']); ?>"
                       placeholder="Ex: João da Silva"
                       required 
                       maxlength="100">
                <small>Nome completo da usuario</small>
            </div>

            <div class="form-group">
                <label for="tipo">Tipo</label>
                <input type="text" 
                       id="tipo" 
                       name="tipo" 
                       value="<?php echo htmlspecialchars($dadosFormulario['tipo']); ?>"
                       placeholder="Ex: ADM ou USER"
                       required
                       minlength="3"
                       maxlength="4"
                       style="text-transform: uppercase;">
                <small>Tipo de Usuario</small>
            </div>

            <div class="form-group">
                <label for="email">E-Mail</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?php echo htmlspecialchars($dadosFormulario['email']); ?>"
                       placeholder="Ex: example@example.com"
                       required>
                <small>E-Mail</small>
            </div>

                <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password"
                       id="senha"
                       name="senha"
                       value=""
                       placeholder="Ex: Teste123"
                       required>
                <small>Senha</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar Usuario</button>
                <a href="index.php" class="btn btn-secondary">❌ Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
