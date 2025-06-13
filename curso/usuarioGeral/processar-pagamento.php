<?php
// processar-pagamento.php - Versão corrigida
session_start();
require_once '../../arquivosReuso/conexao.php'; // Ajustar caminho conforme sua estrutura

// Verificar se usuário está logado e é usuarioGeral
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'usuarioGeral') {
    header('Location: ../../registro/form_login.php');
    exit();
}

// Verificar se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['curso_id'], $_POST['usuario_id'], $_POST['valor'])) {
    header('Location: ../../listaCursos/listagemCursos.php');
    exit();
}

$curso_id = (int)$_POST['curso_id'];
$usuario_id = (int)$_POST['usuario_id'];
$valor = (float)$_POST['valor'];

// Verificar se o usuário da sessão é o mesmo do formulário
if ($usuario_id !== $_SESSION['usuario_id']) {
    header('Location: ../../listaCursos/listagemCursos.php');
    exit();
}

// Verificar se o curso existe
$sql_curso = "SELECT titulo FROM cursos WHERE id = ?";
$stmt_curso = $conn->prepare($sql_curso);
$stmt_curso->bind_param('i', $curso_id);
$stmt_curso->execute();
$result_curso = $stmt_curso->get_result();

if ($result_curso->num_rows === 0) {
    header('Location: ../../listaCursos/listagemCursos.php');
    exit();
}

$curso = $result_curso->fetch_assoc();

// Verificar se o usuário já está inscrito
$sql_check = "SELECT id FROM inscricoes WHERE usuario_id = ? AND curso_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param('ii', $usuario_id, $curso_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Usuário já está inscrito, redirecionar para o curso
    header('Location: ../../listaCursos/curso.php?id=' . $curso_id);
    exit();
}

// Simular processamento de pagamento (sempre será aprovado)
sleep(2); // Simular delay do processamento

// Inserir inscrição na base de dados
$sql_inscricao = "INSERT INTO inscricoes (usuario_id, curso_id, inscrito_em) VALUES (?, ?, NOW())";
$stmt_inscricao = $conn->prepare($sql_inscricao);
$stmt_inscricao->bind_param('ii', $usuario_id, $curso_id);

if ($stmt_inscricao->execute()) {
    // Pagamento processado com sucesso
    $_SESSION['pagamento_sucesso'] = [
        'curso_nome' => $curso['titulo'],
        'curso_id' => $curso_id,
        'valor' => $valor,
        'data_pagamento' => date('Y-m-d H:i:s')
    ];
    header('Location: pagamento-sucesso.php');
} else {
    // Erro ao processar pagamento
    $_SESSION['erro_pagamento'] = 'Erro ao processar pagamento. Tente novamente.';
    header('Location: pagamento-curso.php?curso_id=' . $curso_id);
}

exit();
?>