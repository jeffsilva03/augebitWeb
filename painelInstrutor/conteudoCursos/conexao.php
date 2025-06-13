<?php
include('conecta.php');
date_default_timezone_set('America/Sao_Paulo');

// Conexão com o banco
$mysqli = new mysqli($host, $login, $password, $bd);
if ($mysqli->connect_error) {
    die("Erro na conexão com o banco de dados: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar dados do formulário
    $tituloAtiv = $_POST['tituloAtiv'] ?? '';
    $curso_id = (int)($_POST['curso_id'] ?? 0);
    $questoes = [];
    $descricoes = [];
    $imagens = [];

    // Validar curso_id
    if ($curso_id <= 0) {
        die("Erro: ID do curso inválido.");
    }

    for ($i = 1; $i <= 10; $i++) {
        $questoes[$i] = $_POST["questao$i"] ?? '';
        $descricoes[$i] = $_POST["descricao$i"] ?? '';
        $imagens[$i] = ''; // Inicialmente vazio
    }

    // Diretório para upload das imagens
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Processar upload das imagens
    for ($i = 1; $i <= 10; $i++) {
        if (isset($_FILES["imagem$i"]) && $_FILES["imagem$i"]['error'] == 0) {
            $extensao = pathinfo($_FILES["imagem$i"]['name'], PATHINFO_EXTENSION);
            $nomeArquivo = "imagem{$i}_" . time() . ".$extensao";
            $caminhoCompleto = $uploadDir . $nomeArquivo;

            if (move_uploaded_file($_FILES["imagem$i"]['tmp_name'], $caminhoCompleto)) {
                $imagens[$i] = $caminhoCompleto;
            }
        }
    }

    // Verificar se já existe atividade para este curso
    $checkSql = "SELECT id FROM atividades WHERE curso_id = ? LIMIT 1";
    $checkStmt = $mysqli->prepare($checkSql);
    $checkStmt->bind_param("i", $curso_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // ATUALIZAR - preservar imagens existentes se não foram enviadas novas
        $existingData = $result->fetch_assoc();
        
        // Buscar imagens existentes
        $sqlExisting = "SELECT * FROM atividades WHERE curso_id = ? LIMIT 1";
        $stmtExisting = $mysqli->prepare($sqlExisting);
        $stmtExisting->bind_param("i", $curso_id);
        $stmtExisting->execute();
        $resultExisting = $stmtExisting->get_result();
        $existing = $resultExisting->fetch_assoc();
        
        // Preservar imagens existentes se não foram enviadas novas
        for ($i = 1; $i <= 10; $i++) {
            if (empty($imagens[$i]) && !empty($existing["imagem$i"])) {
                $imagens[$i] = $existing["imagem$i"];
            }
        }
        
        $sql = "UPDATE atividades SET 
            tituloAtiv = ?,
            questao1 = ?, descricao1 = ?, imagem1 = ?,
            questao2 = ?, descricao2 = ?, imagem2 = ?,
            questao3 = ?, descricao3 = ?, imagem3 = ?,
            questao4 = ?, descricao4 = ?, imagem4 = ?,
            questao5 = ?, descricao5 = ?, imagem5 = ?,
            questao6 = ?, descricao6 = ?, imagem6 = ?,
            questao7 = ?, descricao7 = ?, imagem7 = ?,
            questao8 = ?, descricao8 = ?, imagem8 = ?,
            questao9 = ?, descricao9 = ?, imagem9 = ?,
            questao10 = ?, descricao10 = ?, imagem10 = ?
            WHERE curso_id = ?";
            
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param(
            "sssssssssssssssssssssssssssssssi",
            $tituloAtiv,
            $questoes[1], $descricoes[1], $imagens[1],
            $questoes[2], $descricoes[2], $imagens[2],
            $questoes[3], $descricoes[3], $imagens[3],
            $questoes[4], $descricoes[4], $imagens[4],
            $questoes[5], $descricoes[5], $imagens[5],
            $questoes[6], $descricoes[6], $imagens[6],
            $questoes[7], $descricoes[7], $imagens[7],
            $questoes[8], $descricoes[8], $imagens[8],
            $questoes[9], $descricoes[9], $imagens[9],
            $questoes[10], $descricoes[10], $imagens[10],
            $curso_id
        );
        
        $stmtExisting->close();
    } else {
        // INSERIR
        $sql = "INSERT INTO atividades SET 
            tituloAtiv = ?, curso_id = ?,
            questao1 = ?, descricao1 = ?, imagem1 = ?,
            questao2 = ?, descricao2 = ?, imagem2 = ?,
            questao3 = ?, descricao3 = ?, imagem3 = ?,
            questao4 = ?, descricao4 = ?, imagem4 = ?,
            questao5 = ?, descricao5 = ?, imagem5 = ?,
            questao6 = ?, descricao6 = ?, imagem6 = ?,
            questao7 = ?, descricao7 = ?, imagem7 = ?,
            questao8 = ?, descricao8 = ?, imagem8 = ?,
            questao9 = ?, descricao9 = ?, imagem9 = ?,
            questao10 = ?, descricao10 = ?, imagem10 = ?";
            
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param(
            "sissssssssssssssssssssssssssssss",
            $tituloAtiv, $curso_id,
            $questoes[1], $descricoes[1], $imagens[1],
            $questoes[2], $descricoes[2], $imagens[2],
            $questoes[3], $descricoes[3], $imagens[3],
            $questoes[4], $descricoes[4], $imagens[4],
            $questoes[5], $descricoes[5], $imagens[5],
            $questoes[6], $descricoes[6], $imagens[6],
            $questoes[7], $descricoes[7], $imagens[7],
            $questoes[8], $descricoes[8], $imagens[8],
            $questoes[9], $descricoes[9], $imagens[9],
            $questoes[10], $descricoes[10], $imagens[10]
        );
    }

    if ($stmt->execute()) {
        echo "Dados salvos com sucesso!";
    } else {
        echo "Erro ao salvar os dados: " . $stmt->error;
    }

    $stmt->close();
    $checkStmt->close();

} else {
    // CÓDIGO PARA EXIBIR DADOS (GET)
    $curso_id = isset($_GET['curso_id']) ? (int)$_GET['curso_id'] : 0;
    
    if ($curso_id > 0) {
        $sql = "SELECT * FROM atividades WHERE curso_id = ? LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $curso_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $tituloAtiv = $row['tituloAtiv'] ?? '';
            for ($i = 1; $i <= 10; $i++) {
                ${"questao$i"} = $row["questao$i"] ?? '';
                ${"descricao$i"} = $row["descricao$i"] ?? '';
                ${"imagem$i"} = $row["imagem$i"] ?? '';
            }
        }
        $stmt->close();
    }
}

$mysqli->close();
?>