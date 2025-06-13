<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Cadastro</title>
  
  <link rel="icon" href="src/icone.ico" type="image/x-icon">
  <link rel="stylesheet" href="cadastro.css">
</head>
<body>
  <div class="split left">
    <div class="logo-container">
    </div>
  </div>

  <div class="split right">
    <div class="login-box">
      <h2>Cadastre-se</h2>

      <?php if (!empty($_SESSION['mensagem'])): ?>
        <p class="success"><?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?></p>
      <?php endif; ?>

      <?php if (!empty($_SESSION['erro'])): ?>
        <p class="error"><?= $_SESSION['erro']; unset($_SESSION['erro']); ?></p>
      <?php endif; ?>

      <form action="cadastrar.php" method="post">
        <label for="nome">Nome:</label>
        <input id="nome" type="text" name="nome" required>

        <label for="email">Email:</label>
        <input id="email" type="email" name="email" required>

        <label for="senha">Senha:</label>
        <input id="senha" type="password" name="senha" required>

        <input type="hidden" name="perfil" value="usuarioGeral">

        <button type="submit">Cadastrar</button>
      </form>

      <p class="aux-link">
        Já tem conta? <a href="form_login.php">Faça login</a>
      </p>
    </div>
  </div>
</body>
</html>
