<?php
require_once 'funcoes.php'; // Inclui o arquivo de funções

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../html/index.html');
    exit;
}

if ($_SESSION['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

$perguntaId = $_GET['id'] ?? null;

if ($perguntaId) {
    $cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
    $perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);
    $perguntasAtualizadas = [];
    $encontrada = false;

    foreach ($perguntas as $p) {
        if ($p['id'] == $perguntaId) {
            $encontrada = true;
        } else {
            $perguntasAtualizadas[] = $p;
        }
    }

    if ($encontrada) {
        // A lógica de remoção de opções de múltipla escolha foi removida,
        // pois as opções agora estão no próprio arquivo de perguntas.

        if (salvarDados(QUESTIONS_FILE, $perguntasAtualizadas)) {
            header('Location: listar_perguntas.php?msg=excluido');
            exit;
        } else {
            // Erro ao salvar
            header('Location: listar_perguntas.php?erro=salvar');
            exit;
        }
    } else {
        // Pergunta não encontrada
        header('Location: listar_perguntas.php?erro=nao_encontrada');
        exit;
    }
} else {
    // ID não fornecido
    header('Location: listar_perguntas.php?erro=id_nao_fornecido');
    exit;
}
?>