<?php
require_once 'funcoes.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$pergunta_id = $_POST['pergunta_id'] ?? '';
$resposta = $_POST['resposta'] ?? '';
// O login armazena o usuário inteiro em $_SESSION['user'] (ver login.php)
// Em DB usamos id do usuário
$user_id = $_SESSION['user']['id'] ?? '';

if (empty($pergunta_id) || empty($resposta) || empty($user_id)) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

// Buscar pergunta diretamente no banco
$pergunta = buscarPerguntaPorId($pergunta_id);
if (!$pergunta) {
    echo json_encode(['success' => false, 'message' => 'Pergunta não encontrada']);
    exit;
}

// Verificar se resposta está correta
// Verificar acerto
$acertou = (strtolower(trim($resposta)) === strtolower(trim($pergunta['correta'])));

// Inserir resposta no banco usando nomes de colunas corretos
$novaResposta = [
    'id_usuario' => $user_id,
    'id_pergunta' => $pergunta_id,
    'resposta_dada' => $resposta,
    // 'data_hora' será preenchido pelo banco com TIMESTAMP DEFAULT
];

if (inserirResposta($novaResposta)) {
    echo json_encode([
        'success' => true,
        'acertou' => $acertou,
        'resposta_correta' => $pergunta['correta'],
        'message' => $acertou ? 'Resposta correta!' : 'Resposta incorreta!'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar resposta']);
}
?>