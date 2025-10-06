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

// Garantir exatamente 5 opções
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

$indiceCorreta = isset($_POST['correta']) ? intval($_POST['correta']) - 1 : -1;
if ($indiceCorreta < 0 || $indiceCorreta >= count($opcoes) || $opcoes[$indiceCorreta] === '') {
    echo json_encode(['success' => false, 'message' => 'Selecione uma opção correta válida']);
    exit;
}

$cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
$perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);

$opcoesString = converterArrayParaString($opcoes);
$respostaCorretaTexto = $opcoes[$indiceCorreta];

$novaPergunta = [
    'id' => gerarId($perguntas),
    'tipo' => 'multipla_escolha',
    'descricao' => $descricao,
    'opcoes' => $opcoesString,
    'correta' => $respostaCorretaTexto
];

$perguntas[] = $novaPergunta;

if (salvarDados(QUESTIONS_FILE, $perguntas)) {
    echo json_encode(['success' => true, 'message' => 'Pergunta adicionada com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar pergunta']);
}
?>