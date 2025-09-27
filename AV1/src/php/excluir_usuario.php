<?php
require_once 'funcoes.php';
session_start();

$caminhoArquivo = '../../arquivos/users.txt';

if (isset($_GET['id'])) {
    $idParaExcluir = $_GET['id'];
    $cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha']; // Já está correto
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
    }
}

header('Location: listar_usuarios.php');
exit();
?>