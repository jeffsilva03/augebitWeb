<?php
session_start();
require '../conexaoBD/conexao.php';
if (!isset($_SESSION['perfil']) || $_SESSION['perfil']!=='adm') {
  header('Location: ../form_login.php');
  exit;
}

$id = intval($_GET['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['confirmar'])) {
  $stmt = $conexao->prepare("DELETE FROM cadastro WHERE id=?");
  $stmt->bind_param('i',$id);
  $stmt->execute();
  $_SESSION['mensagem'] = "Usuário #{$id} excluído.";
  header('Location: listar_usuarios.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Confirmar Exclusão</title>
    <link rel="icon" href="src/icone.ico" type="image/x-icon">
  <link rel="stylesheet" href="style.css">
</head>
<body class="pagina-painel">
  <?php include 'header.php'; ?>

  <main class="container animar-fadein">
    <section class="card">
      <h2 class="card-titulo">Excluir Usuário #<?= $id ?></h2>
      <p>Tem certeza que deseja <strong>excluir</strong> este usuário? Esta ação não pode ser desfeita.</p>
      <form method="post" style="display:flex; gap:1rem; margin-top:1.5rem;">
        <button name="confirmar" class="botao" type="submit">Sim, Excluir</button>
        <a class="botao" href="listar_usuarios.php" style="background:var(--fundo-cartao); color:var(--texto-principal); border:1px solid var(--destaque-roxo);">Cancelar</a>
      </form>
    </section>
  </main>

  <?php echo '</div></div>'; ?>
</body>
</html>