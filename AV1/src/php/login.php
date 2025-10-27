<?php
// src/php/login.php
require_once 'funcoes.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senhaDigitada = $_POST['senha'] ?? '';

    $usuario = buscarUsuarioPorEmail($email);

    if ($usuario && password_verify($senhaDigitada, $usuario['senha'])) {
        $_SESSION['user'] = $usuario;
        $_SESSION['tipo'] = $usuario['tipo'];
        header('Location: index.php');
        exit;
    } else {
        header('Location: ../html/index.html?erro=1');
        exit;
    }
} else {
    header('Location: ../html/index.html');
    exit;
}
?>