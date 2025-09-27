<?php
require_once 'funcoes.php';
session_start();
verificarAcesso(['admin']);

$id = $_GET['id'] ?? null;
$cabecalhosRespostas = ['id', 'id_usuario', 'id_pergunta', 'resposta_dada', 'data_hora'];
$respostas = lerDados(ANSWERS_FILE, $cabecalhosRespostas);

if ($id) {
    $filtradas = array_filter($respostas, function($r) use ($id) {
        return $r['id'] != $id;
    });
    if (count($filtradas) < count($respostas)) {
        if (salvarDados(ANSWERS_FILE, array_values($filtradas))) {
            header('Location: listar_respostas.php?msg=excluido');
            exit;
        }
    }
}
header('Location: listar_respostas.php?msg=erro');
exit;
