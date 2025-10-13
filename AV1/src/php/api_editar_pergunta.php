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

$id = $_POST['id'] ?? '';
$descricao = trim($_POST['descricao'] ?? '');

if (empty($id) || empty($descricao)) {
    echo json_encode(['success' => false, 'message' => 'ID e descrição são obrigatórios']);
    exit;
}

$cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
$perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);

$perguntaIndex = -1;
foreach ($perguntas as $index => $p) {
    if ($p['id'] == $id) {
        $perguntaIndex = $index;
        break;
    }
}

if ($perguntaIndex === -1) {
    echo json_encode(['success' => false, 'message' => 'Pergunta não encontrada']);
    exit;
}

$pergunta = $perguntas[$perguntaIndex];

$perguntas[$perguntaIndex]['descricao'] = $descricao;

if ($pergunta['tipo'] === 'texto') {
    $resposta = trim($_POST['resposta'] ?? '');
    if (empty($resposta)) {
        echo json_encode(['success' => false, 'message' => 'Resposta é obrigatória para perguntas de texto']);
        exit;
    }
    $perguntas[$perguntaIndex]['correta'] = $resposta;
} elseif ($pergunta['tipo'] === 'multipla_escolha') {
    $opcoes = json_decode($_POST['opcoes'] ?? '[]', true);
    $corretaIndex = intval($_POST['correta'] ?? -1);

    if (count($opcoes) < 2) {
        echo json_encode(['success' => false, 'message' => 'Pelo menos duas opções são obrigatórias']);
        exit;
    }

    if ($corretaIndex < 0 || $corretaIndex >= count($opcoes)) {
        echo json_encode(['success' => false, 'message' => 'Opção correta inválida']);
        exit;
    }

    $opcoesString = converterArrayParaString($opcoes);
    $respostaCorreta = $opcoes[$corretaIndex];

    $perguntas[$perguntaIndex]['opcoes'] = $opcoesString;
    $perguntas[$perguntaIndex]['correta'] = $respostaCorreta;
}

if (salvarDados(QUESTIONS_FILE, $perguntas)) {
    echo json_encode(['success' => true, 'message' => 'Pergunta atualizada com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar pergunta']);
}
?>