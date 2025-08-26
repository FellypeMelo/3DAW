<?php
/**
 * Funções auxiliares para o CRUD de Disciplinas
 * Aplicação procedural em PHP
 */

// Arquivo de dados
define('ARQUIVO_DISCIPLINAS', 'disciplinas.txt');

/**
 * Carrega todas as disciplinas do arquivo
 * @return array Array de disciplinas
 */
function carregarDisciplinas() {
    if (!file_exists(ARQUIVO_DISCIPLINAS)) {
        return [];
    }
    
    $conteudo = file_get_contents(ARQUIVO_DISCIPLINAS);
    if (empty($conteudo)) {
        return [];
    }
    
    $linhas = explode("\n", trim($conteudo));
    $disciplinas = [];
    
    foreach ($linhas as $linha) {
        if (!empty($linha)) {
            $dados = explode('|', $linha);
            if (count($dados) === 4) {
                $disciplinas[] = [
                    'id' => $dados[0],
                    'nome' => $dados[1],
                    'sigla' => $dados[2],
                    'cargaHoraria' => $dados[3]
                ];
            }
        }
    }
    
    return $disciplinas;
}

/**
 * Salva disciplinas no arquivo
 * @param array $disciplinas Array de disciplinas
 * @return bool True se salvou com sucesso
 */
function salvarDisciplinas($disciplinas) {
    $conteudo = '';
    
    foreach ($disciplinas as $disciplina) {
        $conteudo .= $disciplina['id'] . '|' . 
                    $disciplina['nome'] . '|' . 
                    $disciplina['sigla'] . '|' . 
                    $disciplina['cargaHoraria'] . "\n";
    }
    
    return file_put_contents(ARQUIVO_DISCIPLINAS, $conteudo) !== false;
}

/**
 * Gera ID único para nova disciplina
 * @param array $disciplinas Array de disciplinas existentes
 * @return int Novo ID
 */
function gerarId($disciplinas) {
    if (empty($disciplinas)) {
        return 1;
    }
    
    $maxId = 0;
    foreach ($disciplinas as $disciplina) {
        if ($disciplina['id'] > $maxId) {
            $maxId = $disciplina['id'];
        }
    }
    
    return $maxId + 1;
}

/**
 * Busca disciplina por ID
 * @param array $disciplinas Array de disciplinas
 * @param int $id ID da disciplina
 * @return array|null Disciplina encontrada ou null
 */
function buscarDisciplinaPorId($disciplinas, $id) {
    foreach ($disciplinas as $disciplina) {
        if ($disciplina['id'] == $id) {
            return $disciplina;
        }
    }
    return null;
}

/**
 * Valida dados da disciplina
 * @param array $dados Dados para validar
 * @return array Array com erros (vazio se válido)
 */
function validarDisciplina($dados) {
    $erros = [];
    
    if (empty(trim($dados['nome']))) {
        $erros[] = 'Nome é obrigatório';
    }
    
    if (empty(trim($dados['sigla']))) {
        $erros[] = 'Sigla é obrigatória';
    }
    
    if (empty($dados['cargaHoraria']) || !is_numeric($dados['cargaHoraria']) || $dados['cargaHoraria'] <= 0) {
        $erros[] = 'Carga horária deve ser um número positivo';
    }
    
    return $erros;
}

/**
 * Exibe mensagem de sucesso
 * @param string $mensagem Mensagem a exibir
 */
function exibirSucesso($mensagem) {
    echo '<div class="alert alert-success">' . htmlspecialchars($mensagem) . '</div>';
}

/**
 * Exibe mensagem de erro
 * @param string $mensagem Mensagem a exibir
 */
function exibirErro($mensagem) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($mensagem) . '</div>';
}

/**
 * Exibe lista de erros
 * @param array $erros Array de erros
 */
function exibirErros($erros) {
    if (!empty($erros)) {
        echo '<div class="alert alert-danger"><ul>';
        foreach ($erros as $erro) {
            echo '<li>' . htmlspecialchars($erro) . '</li>';
        }
        echo '</ul></div>';
    }
}

/**
 * Sanitiza entrada do usuário
 * @param string $dado Dado a sanitizar
 * @return string Dado sanitizado
 */
function sanitizar($dado) {
    return htmlspecialchars(trim($dado), ENT_QUOTES, 'UTF-8');
}
?>
