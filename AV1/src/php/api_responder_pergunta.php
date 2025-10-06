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
$user_id = $_SESSION['user']['id'] ?? '';

if (empty($pergunta_id) || empty($resposta) || empty($user_id)) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

// Ler perguntas para verificar resposta correta
$cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
$perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);

$pergunta = null;
foreach ($perguntas as $p) {
    if ($p['id'] == $pergunta_id) {
        $pergunta = $p;
        break;
    }
}

if (!$pergunta) {
    echo json_encode(['success' => false, 'message' => 'Pergunta não encontrada']);
    exit;
}

// Verificar se resposta está correta
$acertou = (strtolower(trim($resposta)) === strtolower(trim($pergunta['correta'])));

// Salvar resposta
$cabecalhosRespostas = ['id', 'user_id', 'pergunta_id', 'resposta', 'data_hora'];
// Ler respostas do arquivo (constante definida em funcoes.php como ANSWERS_FILE)
$respostas = lerDados(ANSWERS_FILE, $cabecalhosRespostas);

$novaResposta = [
    'id' => gerarId($respostas),
    'user_id' => $user_id,
    'pergunta_id' => $pergunta_id,
    'resposta' => $resposta,
    'data_hora' => date('Y-m-d H:i:s')
];

$respostas[] = $novaResposta;

if (salvarDados(ANSWERS_FILE, $respostas)) {
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