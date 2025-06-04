<?php
session_start();
require '../conexaoBD/conexao.php';

if (isset($_POST['nome'], $_POST['email'], $_POST['senha'], $_POST['perfil'])) {
    $nome   = trim($_POST['nome']);
    $email  = trim($_POST['email']);
    $senha  = $_POST['senha'];
    $perfil = $_POST['perfil'];

    // Validações
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro'] = 'E-mail inválido.';
    } elseif (strlen($senha) < 6) {
        $_SESSION['erro'] = 'A senha deve ter no mínimo 6 caracteres.';
    } elseif (!in_array($perfil, ['usuario','usuarioGeral','instrutor','adm'])) {
        $_SESSION['erro'] = 'Perfil inválido.';
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conexao->prepare(
            "INSERT INTO cadastro (nome, email, senha, perfil) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('ssss', $nome, $email, $hash, $perfil);

        if ($stmt->execute()) {
            $_SESSION['mensagem'] = 'Cadastro realizado com sucesso!';

            // Se o cadastro foi de um usuário comum (gratuito ou pagante), sempre manda ao login
            if ($perfil === 'usuario' || $perfil === 'usuarioGeral') {
                header('Location: form_login.php');
                exit();
            }

            // Caso contrário (instrutor ou adm), quem decidirá o destino é o ADM que está logado
            if (isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'adm') {
                header('Location: adm/painel_adm.php');
                exit();
            }

            // Fallback geral
            header('Location: form_login.php');
            exit();
        } else {
            $_SESSION['erro'] = 'Erro ao cadastrar: ' . $stmt->error;
        }
    }
} else {
    $_SESSION['erro'] = 'Preencha todos os campos.';
}

// Em caso de erro, volta ao formulário público
header('Location: form_cadastro.php');
exit();
