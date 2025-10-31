<?php
// src/php/login.php
require_once 'funcoes.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senhaDigitada = $_POST['senha'] ?? '';
    
    // Validações básicas
    if (empty($email) || empty($senhaDigitada)) {
        header('Location: ../html/index.html?erro=campos_vazios');
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../html/index.html?erro=email_invalido');
        exit;
    }

    // Buscar usuário no banco
    $usuario = buscarUsuarioPorEmail($email);

    // Verificar credenciais
    if (!$usuario) {
        header('Location: ../html/index.html?erro=usuario_nao_encontrado');
        exit;
    }

    if (!password_verify($senhaDigitada, $usuario['senha'])) {
        header('Location: ../html/index.html?erro=senha_incorreta');
        exit;
    }

    // Login bem sucedido
    $_SESSION['user'] = $usuario;
    $_SESSION['tipo'] = $usuario['tipo'];
    
    header('Location: index.php');
    exit;
} else {
    header('Location: ../html/index.html');
    exit;
}
?>