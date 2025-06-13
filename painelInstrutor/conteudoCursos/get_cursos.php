<?php
include('conecta.php');

header('Content-Type: application/json');

try {
    $sql = "SELECT id, titulo FROM cursos ORDER BY titulo";
    $result = $mysqli->query($sql);
    
    $cursos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cursos[] = [
                'id' => $row['id'],
                'titulo' => $row['titulo']
            ];
        }
    }
    
    echo json_encode($cursos);
} catch (Exception $e) {
    echo json_encode(['error' => 'Erro ao carregar cursos: ' . $e->getMessage()]);
}

$mysqli->close();
?>