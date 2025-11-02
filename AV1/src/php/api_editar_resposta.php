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
$resposta = trim($_POST['resposta_dada'] ?? '');

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

if (empty($resposta)) {
    echo json_encode(['success' => false, 'message' => 'Resposta não pode ser vazia']);
    exit;
}

// Buscar resposta atual para verificar se existe
$respostaAtual = buscarRespostaPorId($id);
if (!$respostaAtual) {
    echo json_encode(['success' => false, 'message' => 'Resposta não encontrada']);
    exit;
}

// Atualizar a resposta
if (atualizarResposta($id, $resposta)) {
    echo json_encode([
        'success' => true, 
        'message' => 'Resposta atualizada com sucesso'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar resposta']);
}
?>