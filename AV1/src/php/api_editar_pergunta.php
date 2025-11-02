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

// Buscar pergunta e atualizar apenas o registro
$pergunta = buscarPerguntaPorId($id);
if (!$pergunta) {
    echo json_encode(['success' => false, 'message' => 'Pergunta não encontrada']);
    exit;
}

$dadosAtualizados = ['id' => $id, 'descricao' => $descricao];

if ($pergunta['tipo'] === 'texto') {
    $resposta = trim($_POST['resposta'] ?? '');
    if (empty($resposta)) {
        echo json_encode(['success' => false, 'message' => 'Resposta obrigatória para pergunta de texto']);
        exit;
    }
    $dadosAtualizados['correta'] = $resposta;
    $dadosAtualizados['opcoes'] = '';
} elseif ($pergunta['tipo'] === 'multipla_escolha') {
    $opcoes = json_decode($_POST['opcoes'] ?? '[]', true);
    $corretaIndex = intval($_POST['correta'] ?? -1);

    if (count($opcoes) < 2) {
        echo json_encode(['success' => false, 'message' => 'É necessário ao menos 2 opções']);
        exit;
    }
    if ($corretaIndex < 0 || $corretaIndex >= count($opcoes)) {
        echo json_encode(['success' => false, 'message' => 'Índice de opção correta inválido']);
        exit;
    }

    $opcoesString = converterArrayParaString($opcoes);
    $dadosAtualizados['opcoes'] = $opcoesString;
    $dadosAtualizados['correta'] = $opcoes[$corretaIndex];
}

if (salvarDados(QUESTIONS_TABLE, $dadosAtualizados)) {
    echo json_encode(['success' => true, 'message' => 'Pergunta atualizada com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar pergunta']);
}
?>