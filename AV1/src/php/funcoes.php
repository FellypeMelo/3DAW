<?php

// Constantes para os caminhos dos arquivos de dados
const USERS_FILE = '../../arquivos/users.txt';
const QUESTIONS_FILE = '../../arquivos/perguntas.txt';
const ANSWERS_FILE = '../../arquivos/respostas_users.txt';


/**
 * Gera o próximo ID disponível para um array de dados.
 *
 * @param array $dados Array de dados onde cada item possui uma chave 'id'.
 * @return int O próximo ID disponível.
 */
function gerarId(array $dados): int {
    if (empty($dados)) {
        return 1;
    }
    $maxId = 0;
    foreach ($dados as $item) {
        if (isset($item['id']) && $item['id'] > $maxId) {
            $maxId = $item['id'];
        }
    }
    return $maxId + 1;
}

/**
 * Lê dados de um arquivo e retorna um array de arrays associativos.
 *
 * @param string $caminhoArquivo O caminho completo do arquivo.
 * @param array $cabecalhos Um array que define os nomes das chaves para cada coluna.
 * @return array Um array de arrays associativos contendo os dados lidos do arquivo.
 */
function lerDados(string $caminhoArquivo, array $cabecalhos): array {
    if (!file_exists($caminhoArquivo)) {
        return [];
    }
    $conteudo = file_get_contents($caminhoArquivo);
    $linhas = explode(PHP_EOL, $conteudo);
    $dados = [];
    foreach ($linhas as $linha) {
        $linha = trim($linha);
        if (!empty($linha)) {
            $campos = explode('|', $linha);
            // Garante que o número de cabeçalhos e campos seja o mesmo para evitar erros
            if (count($cabecalhos) === count($campos)) {
                $dados[] = array_combine($cabecalhos, $campos);
            } else {
                // Opcional: logar um erro ou pular a linha mal formatada
                error_log("Linha mal formatada no arquivo {$caminhoArquivo}: {$linha}");
            }
        }
    }
    return $dados;
}

/**
 * Salva um array de dados em um arquivo, sobrescrevendo o conteúdo existente.
 * Cada item do array é codificado como JSON e escrito em uma nova linha.
 *
 * @param string $caminhoArquivo O caminho completo do arquivo.
 * @param array $dados O array de dados a ser salvo.
 * @return bool True se os dados foram salvos com sucesso, false caso contrário.
 */
/**
 * Salva um array de dados em um arquivo, sobrescrevendo o conteúdo existente.
 * Cada item do array é formatado com '|' como separador e escrito em uma nova linha.
 *
 * @param string $caminhoArquivo O caminho completo do arquivo.
 * @param array $dados O array de dados a ser salvo.
 * @return bool True se os dados foram salvos com sucesso, false caso contrário.
 */
function salvarDados(string $caminhoArquivo, array $dados): bool {
    $conteudo = '';
    foreach ($dados as $item) {
        $conteudo .= implode('|', $item) . PHP_EOL;
    }
    // Remove a última quebra de linha extra se houver dados
    if (!empty($conteudo)) {
        $conteudo = rtrim($conteudo, PHP_EOL);
    }
    return file_put_contents($caminhoArquivo, $conteudo, LOCK_EX) !== false;
}

function lerPerguntas(): array {
    $cabecalhos = ['id', 'tipo', 'descricao', 'opcoes', 'correta'];
    return lerDados(QUESTIONS_FILE, $cabecalhos);
}

function salvarPerguntas(array $perguntas): bool {
    return salvarDados(QUESTIONS_FILE, $perguntas);
}

function converterStringParaArray(string $string, string $delimitador = ','): array {
    return explode($delimitador, $string);
}

function converterArrayParaString(array $array, string $delimitador = ','): string {
    return implode($delimitador, $array);
}


/**
 * Verifica se o usuário está logado e se possui o tipo de perfil necessário.
 * Redireciona para a página de login se a sessão não for válida ou o perfil não for permitido.
 *
 * @param array $tiposPermitidos Array de strings com os tipos de usuário permitidos (ex: ['admin', 'user']).
 *                               Se vazio, apenas verifica se o usuário está logado.
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

?>