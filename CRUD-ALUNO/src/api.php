<?php
include 'cnx.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        handlePost($conn, $input);
        break;
    case 'PUT':
        handlePut($conn, $input);
        break;
    case 'DELETE':
        handleDelete($conn, $input);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}

function handleGet($conn) {
    if (isset($_GET['id'])) {
        $id = $conn->real_escape_string($_GET['id']);
        $sql = "SELECT * FROM alunos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Aluno não encontrado"]);
        }
    } else {
        $sql = "SELECT * FROM alunos ORDER BY nome";
        $result = $conn->query($sql);
        
        $alunos = [];
        while($row = $result->fetch_assoc()) {
            $alunos[] = $row;
        }
        echo json_encode($alunos);
    }
}

function handlePost($conn, $data) {
    if (!isset($data['nome']) || empty($data['nome'])) {
        http_response_code(400);
        echo json_encode(["error" => "Nome é obrigatório"]);
        return;
    }
    
    $nome = $conn->real_escape_string($data['nome']);
    $email = $conn->real_escape_string($data['email'] ?? '');
    $telefone = $conn->real_escape_string($data['telefone'] ?? '');
    $data_nascimento = $conn->real_escape_string($data['data_nascimento'] ?? '');
    $endereco = $conn->real_escape_string($data['endereco'] ?? '');
    $serie = $conn->real_escape_string($data['serie'] ?? '');
    
    $sql = "INSERT INTO alunos (nome, email, telefone, data_nascimento, endereco, serie) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nome, $email, $telefone, $data_nascimento, $endereco, $serie);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Aluno criado com sucesso",
            "id" => $stmt->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao criar aluno: " . $stmt->error]);
    }
}

function handlePut($conn, $data) {
    if (!isset($data['id']) || !isset($data['nome'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID e Nome são obrigatórios"]);
        return;
    }
    
    $id = $conn->real_escape_string($data['id']);
    $nome = $conn->real_escape_string($data['nome']);
    $email = $conn->real_escape_string($data['email'] ?? '');
    $telefone = $conn->real_escape_string($data['telefone'] ?? '');
    $data_nascimento = $conn->real_escape_string($data['data_nascimento'] ?? '');
    $endereco = $conn->real_escape_string($data['endereco'] ?? '');
    $serie = $conn->real_escape_string($data['serie'] ?? '');
    
    $sql = "UPDATE alunos SET nome=?, email=?, telefone=?, data_nascimento=?, endereco=?, serie=? 
            WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $nome, $email, $telefone, $data_nascimento, $endereco, $serie, $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Aluno atualizado com sucesso"
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao atualizar aluno: " . $stmt->error]);
    }
}

function handleDelete($conn, $data) {
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID é obrigatório"]);
        return;
    }
    
    $id = $conn->real_escape_string($data['id']);
    
    $sql = "DELETE FROM alunos WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Aluno deletado com sucesso"
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao deletar aluno: " . $stmt->error]);
    }
}

$conn->close();
?>