<?php
require_once 'funcoes.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['tipo'] != 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$id = $_POST['id'] ?? '';

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

// Excluir diretamente no banco
if (excluirPergunta($id)) {
    echo json_encode(['success' => true, 'message' => 'Pergunta excluída']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir pergunta']);
}
?>