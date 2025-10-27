<?php
require_once 'funcoes.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['tipo'] != 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$descricao = trim($_POST['descricao'] ?? '');
$opcoes = $_POST['opcoes'] ?? [];
$correta = $_POST['correta'] ?? '';

$opcoes = array_slice($opcoes, 0, 5);
while (count($opcoes) < 5) {
    $opcoes[] = '';
}

$opcoes = array_map('trim', $opcoes);
$opcoesNaoVazias = array_filter($opcoes, function($v) { return $v !== ''; });

if (empty($descricao) || count($opcoesNaoVazias) < 2) {
    echo json_encode(['success' => false, 'message' => 'Descrição e pelo menos duas opções são obrigatórias']);
    exit;
}

if (empty($correta) || !isset($opcoes[$correta - 1]) || $opcoes[$correta - 1] === '') {
    echo json_encode(['success' => false, 'message' => 'Selecione uma opção correta válida']);
    exit;
}

try {
    $opcoesString = converterArrayParaString($opcoes);
    $respostaCorretaTexto = $opcoes[$correta - 1];

    $novaPergunta = [
        'tipo' => 'multipla_escolha',
        'descricao' => $descricao,
        'opcoes' => $opcoesString,
        'correta' => $respostaCorretaTexto
    ];

    if (salvarDados(QUESTIONS_TABLE, $novaPergunta)) {
        echo json_encode(['success' => true, 'message' => 'Pergunta adicionada com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar pergunta no banco de dados']);
    }
} catch (Exception $e) {
    error_log("Erro API adicionar pergunta ME: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>