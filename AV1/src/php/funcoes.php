<?php
// src/php/funcoes.php

require_once 'cnx.php';

/**
 * Gera o próximo ID disponível para uma tabela (para compatibilidade com código existente)
 * Nota: Em MySQL usamos AUTO_INCREMENT, mas mantemos para compatibilidade
 */
function gerarId($tabela): int {
    $conn = getDbConnection();
    $stmt = $conn->query("SELECT MAX(id) as max_id FROM $tabela");
    $result = $stmt->fetch();
    return ($result['max_id'] ?? 0) + 1;
}

/**
 * Lê dados de uma tabela - mantém compatibilidade com interface antiga
 */
function lerDados($tabela, $cabecalhos = []): array {
    $conn = getDbConnection();
    
    try {
        $stmt = $conn->query("SELECT * FROM $tabela ORDER BY id");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erro ao ler dados da tabela $tabela: " . $e->getMessage());
        return [];
    }
}

/**
 * Salva dados em uma tabela - função genérica para INSERT/UPDATE
 */
function salvarDados($tabela, $dados): bool {
    $conn = getDbConnection();
    
    try {
        // Suporta dois formatos para compatibilidade:
        // 1) $dados é um array associativo -> inserir/atualizar um registro
        // 2) $dados é um array indexado de arrays associativos -> substituir todo o conteúdo da tabela
        $isListOfRows = is_array($dados) && count($dados) > 0 && array_values($dados) === $dados && is_array($dados[0]);

        if ($isListOfRows) {
            // Substitui todo o conteúdo da tabela por um conjunto de linhas (compatibilidade com 'arquivo' antigo)
            try {
                $conn->beginTransaction();
                $conn->exec("DELETE FROM $tabela");

                if (count($dados) > 0) {
                    $firstRow = $dados[0];
                    $keys = array_keys($firstRow);
                    $placeholders = implode(',', array_fill(0, count($keys), '?'));
                    $sql = "INSERT INTO $tabela (" . implode(', ', $keys) . ") VALUES ($placeholders)";
                    $stmt = $conn->prepare($sql);

                    foreach ($dados as $row) {
                        // Garante a ordem dos parâmetros igual às chaves
                        $params = [];
                        foreach ($keys as $k) {
                            $params[] = $row[$k] ?? null;
                        }
                        $stmt->execute($params);
                    }
                }

                $conn->commit();
                return true;
            } catch (PDOException $e) {
                $conn->rollBack();
                error_log("Erro ao substituir dados na tabela $tabela: " . $e->getMessage());
                return false;
            }
        }

        // Se o dado tem ID, é UPDATE, senão INSERT
        if (isset($dados['id']) && !empty($dados['id'])) {
            $setParts = [];
            $params = [];
            foreach ($dados as $key => $value) {
                if ($key !== 'id') {
                    $setParts[] = "$key = ?";
                    $params[] = $value;
                }
            }
            $params[] = $dados['id'];
            $sql = "UPDATE $tabela SET " . implode(', ', $setParts) . " WHERE id = ?";
        } else {
            $keys = array_keys($dados);
            $placeholders = str_repeat('?,', count($keys) - 1) . '?';
            $sql = "INSERT INTO $tabela (" . implode(', ', $keys) . ") VALUES ($placeholders)";
            $params = array_values($dados);
        }
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Erro ao salvar dados na tabela $tabela: " . $e->getMessage());
        return false;
    }
}

// Funções específicas para manter compatibilidade
function lerPerguntas(): array {
    $conn = getDbConnection();
    $stmt = $conn->query("SELECT * FROM perguntas ORDER BY id");
    return $stmt->fetchAll();
}

function salvarPerguntas(array $perguntas): bool {
    // Esta função não é mais necessária da mesma forma, mas mantemos para compatibilidade
    // O código deve ser adaptado para usar salvarDados diretamente
    return true;
}

function converterStringParaArray(string $string, string $delimitador = ','): array {
    return array_map('trim', explode($delimitador, $string));
}

function converterArrayParaString(array $array, string $delimitador = ','): string {
    return implode($delimitador, array_map('trim', $array));
}

/**
 * Verifica se o usuário está logado e se possui o tipo de perfil necessário.
 */
function verificarAcesso(array $tiposPermitidos = []): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user']) || !isset($_SESSION['tipo'])) {
        header('Location: ../html/index.html?erro=sessao_expirada');
        exit;
    }

    if (!empty($tiposPermitidos) && !in_array($_SESSION['tipo'], $tiposPermitidos)) {
        header('Location: ../html/index.html?erro=acesso_negado');
        exit;
    }
}

// Novas funções específicas para MySQL
function buscarUsuarioPorEmail($email) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function buscarPerguntaPorId($id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM perguntas WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function buscarRespostasPorUsuario($usuarioId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        SELECT r.*, p.descricao as pergunta_descricao 
        FROM respostas r 
        LEFT JOIN perguntas p ON r.id_pergunta = p.id 
        WHERE r.id_usuario = ? 
        ORDER BY r.data_hora DESC
    ");
    $stmt->execute([$usuarioId]);
    return $stmt->fetchAll();
}

function inserirResposta($dados) {
    return salvarDados('respostas', $dados);
}

function excluirResposta($id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("DELETE FROM respostas WHERE id = ?");
    return $stmt->execute([$id]);
}

function buscarTodasRespostas() {
    $conn = getDbConnection();
    $stmt = $conn->query("
        SELECT r.*, u.nome as usuario_nome, p.descricao as pergunta_descricao 
        FROM respostas r 
        LEFT JOIN usuarios u ON r.id_usuario = u.id 
        LEFT JOIN perguntas p ON r.id_pergunta = p.id 
        ORDER BY r.data_hora DESC
    ");
    return $stmt->fetchAll();
}

function excluirPergunta($id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("DELETE FROM perguntas WHERE id = ?");
    return $stmt->execute([$id]);
}

function buscarRespostaPorId($id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM respostas WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function atualizarResposta($id, $resposta_dada) {
    return salvarDados(ANSWERS_TABLE, ['id' => $id, 'resposta_dada' => $resposta_dada]);
}

function excluirUsuario($id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    return $stmt->execute([$id]);
}

// Constantes para compatibilidade (não são mais arquivos)
define('USERS_TABLE', 'usuarios');
define('QUESTIONS_TABLE', 'perguntas');
define('ANSWERS_TABLE', 'respostas');
// Compatibilidade: alguns arquivos antigos usam constante _FILE; mapeamos para o nome da tabela
define('USERS_FILE', USERS_TABLE);
define('QUESTIONS_FILE', QUESTIONS_TABLE);
define('ANSWERS_FILE', ANSWERS_TABLE);
?>