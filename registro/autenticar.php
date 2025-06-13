<?php
session_start();
require '../conexaoBD/conexao.php';

if (isset($_POST['email'], $_POST['senha'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $stmt = $conexao->prepare(
        "SELECT id, nome, senha, perfil FROM cadastro WHERE email = ?"
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nome, $hash, $perfil);
        $stmt->fetch();

        if (password_verify($senha, $hash)) {
            $_SESSION['usuario_id']   = $id;
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['perfil']       = $perfil;

            switch ($perfil) {
                case 'adm':
                    header('Location: ../painelADM/painel_adm.php');
                    break;
                case 'instrutor':
                    header('Location: ../painelInstrutor/painel_instrutor.php');
                    break;
                case 'usuario':
                    header('Location: ../curso/usuario/paginaPerfil/pgnUser.php');
                    break;
                case 'usuarioGeral':
                default:
                    header('Location: ../listaCursos/listagemCursos.php');
            }
            exit();
        }
    }
}

$_SESSION['erro_login'] = 'Email ou senha inválidos.';
header('Location: form_login.php');
exit();
?>