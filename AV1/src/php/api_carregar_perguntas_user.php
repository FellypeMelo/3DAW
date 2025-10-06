<?php
require_once 'funcoes.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

$cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
$perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);

header('Content-Type: application/json');
echo json_encode(['perguntas' => $perguntas]);
?>