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
$resposta = trim($_POST['resposta'] ?? '');

if (empty($descricao) || empty($resposta)) {
    echo json_encode(['success' => false, 'message' => 'Descrição e resposta são obrigatórias']);
    exit;
}

try {
    $novaPergunta = [
        'tipo' => 'texto',
        'descricao' => $descricao,
        'opcoes' => '',
        'correta' => $resposta
    ];

    if (salvarDados(QUESTIONS_TABLE, $novaPergunta)) {
        echo json_encode(['success' => true, 'message' => 'Pergunta adicionada com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar pergunta no banco de dados']);
    }
} catch (Exception $e) {
    error_log("Erro API adicionar pergunta: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>