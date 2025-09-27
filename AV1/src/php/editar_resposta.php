<?php
require_once 'funcoes.php';
session_start();
verificarAcesso(['admin']);

$id = $_GET['id'] ?? null;
$cabecalhosRespostas = ['id', 'id_usuario', 'id_pergunta', 'resposta_dada', 'data_hora'];
$respostas = lerDados(ANSWERS_FILE, $cabecalhosRespostas);
$erro = '';
$respostaAtual = null;
if ($id) {
    foreach ($respostas as $r) {
        if ($r['id'] == $id) {
            $respostaAtual = $r;
            break;
        }
    }
}

if (!$respostaAtual) {
    header('Location: listar_respostas.php?msg=Resposta não encontrada');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova = trim($_POST['resposta_dada'] ?? '');
    if ($nova === '') {
        $erro = 'Resposta não pode ser vazia.';
    } else {
        // Atualiza o array
        foreach ($respostas as &$r) {
            if ($r['id'] == $id) {
                $r['resposta_dada'] = $nova;
                $r['data_hora'] = date('Y-m-d H:i:s');
                break;
            }
        }
        unset($r);
        if (salvarDados(ANSWERS_FILE, $respostas)) {
            header('Location: listar_respostas.php?msg=Resposta atualizada');
            exit;
        } else {
            $erro = 'Erro ao salvar.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Resposta</title>
</head>
<body>
    <?php include '../html/menu_admin.html'; ?>
    <h1>Editar Resposta #<?php echo htmlspecialchars($respostaAtual['id']); ?></h1>
    <?php if ($erro): ?><p style="color:red"><?php echo htmlspecialchars($erro); ?></p><?php endif; ?>
    <form method="POST">
        <label>Resposta:</label><br>
        <textarea name="resposta_dada" required><?php echo htmlspecialchars($respostaAtual['resposta_dada']); ?></textarea><br>
        <button type="submit">Salvar</button>
    </form>
    <a href="listar_respostas.php">Voltar</a>
</body>
</html>
