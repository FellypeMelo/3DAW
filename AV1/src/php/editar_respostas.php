<?php
// Wrapper para compatibilidade: chama editar_resposta.php (admin apenas)
require_once 'funcoes.php';
session_start();
verificarAcesso(['admin']);

// Redireciona para a página de edição específica (espera ?id=)
if (isset($_GET['id'])) {
    header('Location: editar_resposta.php?id=' . urlencode($_GET['id']));
    exit;
} else {
    // Sem id, redireciona para a lista de respostas
    header('Location: listar_respostas.php');
    exit;
}
