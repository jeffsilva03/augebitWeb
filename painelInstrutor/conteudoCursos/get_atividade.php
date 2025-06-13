<?php
include('conecta.php');

header('Content-Type: application/json');

try {
    $curso_id = isset($_GET['curso_id']) ? (int)$_GET['curso_id'] : 0;
    
    if ($curso_id <= 0) {
        echo json_encode(['error' => 'ID do curso inválido']);
        exit;
    }
    
    $sql = "SELECT * FROM atividades WHERE curso_id = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $atividade = $result->fetch_assoc();
        echo json_encode($atividade);
    } else {
        echo json_encode(null);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['error' => 'Erro ao carregar atividade: ' . $e->getMessage()]);
}

$mysqli->close();
?>