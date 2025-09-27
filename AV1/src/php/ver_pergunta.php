<?php
require_once 'funcoes.php'; // Inclui o arquivo de funções

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../html/index.html');
    exit;
}

$erros = [];
$pergunta = null;
$perguntaId = $_GET['id'] ?? null;

if ($perguntaId) {
    $cabecalhosPerguntas = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
    $perguntas = lerDados(QUESTIONS_FILE, $cabecalhosPerguntas);
    foreach ($perguntas as $p) {
        if ($p['id'] == $perguntaId) {
            $pergunta = $p;
            break;
        }
    }

    if (!$pergunta) {
        $erros[] = 'Pergunta não encontrada.';
    } else {
        // Se a pergunta for de múltipla escolha, converter a string de opções para array
        if ($pergunta['tipo'] === 'multipla_escolha') {
            $pergunta['opcoes_detalhes'] = converterStringParaArray($pergunta['opcoes']);
        }
    }
} else {
    $erros[] = 'ID da pergunta não fornecido.';
}

// Lógica para salvar resposta (opcional, para ser implementada depois)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erros) && $pergunta) {
    // Tenta obter id do usuário a partir da sessão (suporta sessão antiga onde user era string)
    $id_usuario = null;
    if (isset($_SESSION['user'])) {
        if (is_array($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            $id_usuario = $_SESSION['user']['id'];
        } elseif (is_string($_SESSION['user'])) {
            // Procura o usuário pelo nome nos arquivos de users
            $cabecalhosUsuarios = ['id', 'tipo', 'nome', 'email', 'senha'];
            $usuarios = lerDados(USERS_FILE, $cabecalhosUsuarios);
            foreach ($usuarios as $u) {
                if (isset($u['nome']) && $u['nome'] === $_SESSION['user']) {
                    $id_usuario = $u['id'];
                    break;
                }
            }
        }
    }
    $id_pergunta = $pergunta['id'] ?? null;
    $resposta = $_POST['resposta_usuario'] ?? '';

    if (!$id_usuario) {
        $erros[] = 'ID do usuário não encontrado na sessão.';
    }
    if (!$id_pergunta) {
        $erros[] = 'ID da pergunta não encontrado.';
    }
    if (empty($resposta)) {
        $erros[] = 'A resposta não pode ser vazia.';
    }

    if (empty($erros)) {
        $cabecalhosRespostas = ['id', 'id_usuario', 'id_pergunta', 'resposta_dada', 'data_hora'];
        $respostasExistentes = lerDados(ANSWERS_FILE, $cabecalhosRespostas);
        $novoId = gerarId($respostasExistentes);
        $novaResposta = [
            'id' => $novoId,
            'id_usuario' => $id_usuario,
            'id_pergunta' => $id_pergunta,
            'resposta_dada' => $resposta,
            'data_hora' => date('Y-m-d H:i:s')
        ];
        $respostasExistentes[] = $novaResposta;

        if (salvarDados(ANSWERS_FILE, $respostasExistentes)) {
            header('Location: listar_perguntas_user.php?status=ok');
            exit;
        } else {
            $erros[] = 'Erro ao salvar a resposta.';
        }
    }
}

function exibirErros(array $erros) {
    if (!empty($erros)) {
        echo '<div class="erros">';
        foreach ($erros as $erro) {
            echo "<p>{$erro}</p>";
        }
        echo '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Pergunta</title>
    <link rel="stylesheet" href="../../src/html/estilo.css">
</head>
<body>
    <?php
        if (!isset($_SESSION)) {
            session_start();
        }
        if($_SESSION['tipo'] == 'admin'){
            include '../html/menu_admin.html';
        } else {
            include '../html/menu_user.html';
        }
    ?>

    <div class="container">
        <h2>Detalhes da Pergunta</h2>
        <?php exibirErros($erros); ?>

        <?php if ($pergunta): ?>
            <div class="pergunta-detalhes">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($pergunta['id']); ?></p>
                <p><strong>Tipo:</strong> <?php echo htmlspecialchars($pergunta['tipo']); ?></p>
                <p><strong>Descrição:</strong> <?php echo htmlspecialchars($pergunta['descricao']); ?></p>
                <?php if ($_SESSION['tipo'] == 'admin'): ?>
                    <?php if ($pergunta['tipo'] === 'texto'): ?>
                        <p><strong>Resposta Correta:</strong> <?php echo htmlspecialchars($pergunta['correta']); ?></p>
                    <?php elseif ($pergunta['tipo'] === 'multipla_escolha'): ?>
                        <p><strong>Opções:</strong></p>
                        <p><strong>Opções:</strong></p>
                        <ul>
                            <?php foreach ($pergunta['opcoes_detalhes'] as $opcaoTexto): ?>
                                <li>
                                    <?php echo htmlspecialchars($opcaoTexto); ?>
                                    <?php if ($opcaoTexto === $pergunta['correta']): ?>
                                        (Correta)
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if (isset($mensagemSucesso)): ?>
                <div class="sucesso">
                    <p><?php echo $mensagemSucesso; ?></p>
                </div>
            <?php endif; ?>

            <h3>Responder Pergunta</h3>
            <form method="POST" class="form-usuario">
                <?php if ($pergunta['tipo'] === 'texto'): ?>
                    <div class="form-group">
                        <label for="resposta_usuario">Sua Resposta:</label>
                        <input type="text"
                               id="resposta_usuario"
                               name="resposta_usuario"
                               placeholder="Digite sua resposta aqui"
                               required>
                    </div>
                <?php elseif ($pergunta['tipo'] === 'multipla_escolha'): ?>
                    <div class="form-group">
                    <div class="form-group">
                        <label>Selecione uma opção:</label><br>
                        <?php foreach ($pergunta['opcoes_detalhes'] as $index => $opcaoTexto): ?>
                            <input type="radio"
                                   id="opcao_<?php echo htmlspecialchars($index + 1); ?>"
                                   name="resposta_usuario"
                                   value="<?php echo htmlspecialchars($opcaoTexto); ?>"
                                   required>
                            <label for="opcao_<?php echo htmlspecialchars($index + 1); ?>"><?php echo htmlspecialchars($opcaoTexto); ?></label><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Responder</button>
                    <a href="<?php echo ($_SESSION['tipo'] === 'admin') ? 'listar_perguntas.php' : 'listar_perguntas_user.php'; ?>" class="btn btn-secondary">Voltar para Lista de Perguntas</a>
                </div>
            </form>
        <?php else: ?>
            <p>Não foi possível carregar os detalhes da pergunta.</p>
            <a href="listar_perguntas.php" class="btn btn-secondary">Voltar para Lista de Perguntas</a>
        <?php endif; ?>
    </div>
</body>
</html>