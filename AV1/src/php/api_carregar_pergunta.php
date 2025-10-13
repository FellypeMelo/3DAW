<?php
require_once 'funcoes.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['tipo'] != 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$perguntaId = $_GET['id'] ?? '';

if (empty($perguntaId)) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

$cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
$perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);

$perguntaEncontrada = null;
foreach ($perguntas as $p) {
    if ($p['id'] == $perguntaId) {
        $perguntaEncontrada = $p;
        break;
    }
}

if (!$perguntaEncontrada) {
    echo json_encode(['success' => false, 'message' => 'Pergunta não encontrada']);
    exit;
}

if ($perguntaEncontrada['tipo'] === 'multipla_escolha') {
    $perguntaEncontrada['opcoes_detalhes'] = converterStringParaArray($perguntaEncontrada['opcoes']);
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'pergunta' => $perguntaEncontrada]);
?>