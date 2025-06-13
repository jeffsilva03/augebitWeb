<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login – AUGEBIT</title>
  
  <link rel="icon" href="src/icone.ico" type="image/x-icon">
  <link rel="stylesheet" href="login.css">
</head>
<body>

  <!-- lado esquerdo com background e logo -->
  <div class="split left">
    <div class="logo-container">
    </div>
  </div>

  <!-- lado direito com form de login -->
  <div class="split right">
    <div class="login-box">
      <h2>Login</h2>
      <form action="autenticar.php" method="post">
        <label for="email">Usuário</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Entrar</button>

        <p class="aux-link">
        Não tem conta? <a href="form_cadastro.php">Cadastre-se</a>
      </p>

        <?php if (isset($_SESSION['erro_login'])): ?>
          <p class="error">
            <?php 
              echo $_SESSION['erro_login']; 
              unset($_SESSION['erro_login']); 
            ?>
          </p>
        <?php endif; ?>
      </form>
    </div>
  </div>

</body>
</html>
