<?php
require_once 'funcoes.php'; 

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? ''); // Usar 'email' como campo de login
    $senhaDigitada = $_POST['senha'] ?? '';

    $cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
    $usuarios = lerDados(USERS_FILE, $cabecalhosUsuarios);
    $usuarioAutenticado = null;

    foreach ($usuarios as $usuario) {
        if (isset($usuario['email']) && $usuario['email'] === $email && isset($usuario['senha']) && password_verify($senhaDigitada, $usuario['senha'])) {
            $usuarioAutenticado = $usuario;
            break;
        }
    }

    if ($usuarioAutenticado) {
        // Armazena o usuário completo na sessão (id, tipo, nome, email)
        $_SESSION['user'] = $usuarioAutenticado;
        $_SESSION['tipo'] = $usuarioAutenticado['tipo'];
        // Redireciona para o dashboard (index.php)
        header('Location: index.php');
        exit;
    } else {
        header('Location: ../html/index.html?erro=1'); // Redireciona com erro se a autenticação falhar
        exit;
    }
} else {
    header('Location: ../html/index.html');
    exit;
}
?>