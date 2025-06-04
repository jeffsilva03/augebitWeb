<?php
// adm/editar_usuario.php
session_start();
require '../conexaoBD/conexao.php';
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'adm') {
    header('Location: ../form_login.php');
    exit();
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: listar_usuarios.php');
    exit();
}

// Busca dados atuais
$stmt = $conexao->prepare("SELECT nome, email, perfil FROM cadastro WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($nome, $email, $perfil_atual);
$stmt->fetch();
$stmt->close();

// Processa atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_nome   = trim($_POST['nome']);
    $novo_email  = trim($_POST['email']);
    $nova_senha  = $_POST['senha'];
    $novo_perfil = $_POST['perfil'];

    $hash = $nova_senha ? password_hash($nova_senha, PASSWORD_DEFAULT) : null;

    $sql = "UPDATE cadastro SET nome = ?, email = ?, perfil = ?";
    if ($hash) {
        $sql .= ", senha = ?";
    }
    $sql .= " WHERE id = ?";

    $stmt = $conexao->prepare($sql);
    if ($hash) {
        $stmt->bind_param('ssssi', $novo_nome, $novo_email, $novo_perfil, $hash, $id);
    } else {
        $stmt->bind_param('sssi', $novo_nome, $novo_email, $novo_perfil, $id);
    }
    $stmt->execute();
    $_SESSION['mensagem'] = "Usuário #{$id} atualizado com sucesso.";
    header('Location: listar_usuarios.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuário #<?= $id ?></title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --font-base: 'Poppins', sans-serif;
      --clr-bg: #f5f6fa;
      --clr-white: #ffffff;
      --clr-dark: #2d2d2d;
      --clr-gray: #666666;
      --clr-purple: #8a2be2;
      --clr-purple-dark: rgb(14,11,17);
      --radius: 12px;
      --spacing: 1.6rem;
      --transition: 0.4s ease;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: var(--font-base);
      background-color: var(--clr-bg);
      color: var(--clr-dark);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    /* Main content */
    main.container {
      max-width: 1000px;
      width: 100%;
      margin: 2rem auto;
      padding: 0 var(--spacing);
      flex: 1;
    }
    
    .card {
      background-color: var(--clr-white);
      border-radius: var(--radius);
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      padding: 2rem;
      position: relative;
      overflow: hidden;
    }
    
    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(to right, var(--clr-purple), var(--clr-purple-dark));
    }
    
    .card-titulo {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
      color: var(--clr-dark);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .card-titulo::before {
      content: '\f007';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      color: var(--clr-purple);
    }
    
    /* Breadcrumb */
    .breadcrumb {
      display: flex;
      align-items: center;
      margin-bottom: 1.5rem;
      font-size: 0.875rem;
    }
    
    .breadcrumb a {
      color: var(--clr-gray);
      text-decoration: none;
      transition: var(--transition);
    }
    
    .breadcrumb a:hover {
      color: var(--clr-purple);
    }
    
    .breadcrumb-separator {
      margin: 0 0.5rem;
      color: var(--clr-gray);
    }
    
    .breadcrumb-current {
      color: var(--clr-purple);
      font-weight: 500;
    }
    
    /* Form styles */
    .formulario {
      display: grid;
      gap: 1.5rem;
    }
    
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    
    .label {
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--clr-dark);
      display: block;
      margin-bottom: 0.25rem;
    }
    
    .input,
    .select {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #e2e8f0;
      border-radius: var(--radius);
      font-family: var(--font-base);
      font-size: 1rem;
      color: var(--clr-dark);
      transition: var(--transition);
      background-color: #f9fafc;
    }
    
    .input:focus,
    .select:focus {
      outline: none;
      border-color: var(--clr-purple);
      box-shadow: 0 0 0 3px rgba(138, 43, 226, 0.1);
      background-color: var(--clr-white);
    }
    
    .input::placeholder {
      color: #a0aec0;
    }
    
    .select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%238a2be2' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 1rem center;
      background-size: 16px;
      padding-right: 2.5rem;
    }
    
    .input-hint {
      font-size: 0.75rem;
      color: var(--clr-gray);
      margin-top: 0.25rem;
    }
    
    .d-flex {
      display: flex;
    }
    
    .botao {
      padding: 0.75rem 1.5rem;
      border-radius: var(--radius);
      font-family: var(--font-base);
      font-weight: 500;
      font-size: 0.9rem;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      transition: var(--transition);
      border: none;
    }
    
    .botao-principal {
      background: linear-gradient(135deg, var(--clr-purple) 0%, var(--clr-purple-dark) 100%);
      color: var(--clr-white);
    }
    
    .botao-principal:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(138, 43, 226, 0.3);
    }
    
    .botao-secundario {
      background: transparent;
      color: var(--clr-dark);
      border: 1px solid var(--clr-purple);
    }
    
    .botao-secundario:hover {
      background-color: rgba(138, 43, 226, 0.05);
    }
    
    /* Form grid for responsive layout */
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }
    
    /* Status messages */
    .form-status {
      padding: 1rem;
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .status-success {
      background-color: rgba(79, 190, 135, 0.1);
      color: #4fbe87;
      border-left: 4px solid #4fbe87;
    }
    
    .status-error {
      background-color: rgba(236, 78, 86, 0.1);
      color: #ec4e56;
      border-left: 4px solid #ec4e56;
    }
    
    /* Animations */
    .animar-fadein {
      animation: fadeIn 0.5s ease forwards;
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
      
      .d-flex {
        flex-direction: column;
        gap: 1rem;
      }
      
      .botao {
        width: 100%;
      }
    }
    
    /* Custom password strength indicator */
    .password-strength {
      height: 5px;
      width: 100%;
      background: #e2e8f0;
      border-radius: 10px;
      margin-top: 0.5rem;
      overflow: hidden;
      transition: var(--transition);
    }
    
    .password-strength-bar {
      height: 100%;
      width: 0;
      border-radius: 10px;
      transition: var(--transition);
    }
    
    .strength-weak {
      width: 33%;
      background-color: #ec4e56;
    }
    
    .strength-medium {
      width: 66%;
      background-color: #f7c32e;
    }
    
    .strength-strong {
      width: 100%;
      background-color: #4fbe87;
    }
  </style>
</head>
<body class="pagina-painel">
  <?php include 'header.php'; ?>

  <main class="container animar-fadein">
    <div class="breadcrumb">
      <a href="listar_usuarios.php"><i class="fas fa-users"></i> Usuários</a>
      <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
      <span class="breadcrumb-current">Editar Usuário #<?= $id ?></span>
    </div>
    
    <section class="card">
      <h2 class="card-titulo">Editar Usuário #<?= $id ?></h2>
      
      <form method="post" class="formulario">
        <div class="form-grid">
          <div class="form-group">
            <label class="label" for="nome">Nome</label>
            <input class="input" type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome) ?>" required>
          </div>
          
          <div class="form-group">
            <label class="label" for="email">Email</label>
            <input class="input" type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
          </div>
        </div>
        
        <div class="form-group">
          <label class="label" for="senha">Senha</label>
          <input class="input" type="password" id="senha" name="senha" placeholder="••••••••">
          <p class="input-hint"><i class="fas fa-info-circle"></i> Deixe em branco para manter a senha atual</p>
          <div class="password-strength">
            <div class="password-strength-bar" id="password-bar"></div>
          </div>
        </div>
        
        <div class="form-group">
          <label class="label" for="perfil">Perfil</label>
          <select class="select" id="perfil" name="perfil" required>
            <option value="usuario" <?= $perfil_atual==='usuario' ? 'selected' : '' ?>>Usuário</option>
            <option value="instrutor" <?= $perfil_atual==='instrutor' ? 'selected' : '' ?>>Instrutor</option>
            <option value="adm" <?= $perfil_atual==='adm' ? 'selected' : '' ?>>Administrador</option>
          </select>
        </div>
        
        <div class="d-flex" style="gap:1rem; margin-top:1.5rem;">
          <button class="botao botao-principal" type="submit">
            <i class="fas fa-save"></i> Salvar Alterações
          </button>
          <a class="botao botao-secundario" href="listar_usuarios.php">
            <i class="fas fa-times"></i> Cancelar
          </a>
        </div>
      </form>
    </section>
  </main>
  
  <script>
    // Script para indicador de força de senha (puramente visual)
    document.addEventListener('DOMContentLoaded', function() {
      const passwordInput = document.getElementById('senha');
      const passwordBar = document.getElementById('password-bar');
      
      passwordInput.addEventListener('input', function() {
        const value = this.value;
        
        if (value.length === 0) {
          passwordBar.className = 'password-strength-bar';
          passwordBar.style.width = '0';
          return;
        }
        
        if (value.length < 6) {
          passwordBar.className = 'password-strength-bar strength-weak';
        } else if (value.length < 10) {
          passwordBar.className = 'password-strength-bar strength-medium';
        } else {
          passwordBar.className = 'password-strength-bar strength-strong';
        }
      });
    });
  </script>


  <?php echo '</div></div>'; ?>
</body>
</html>
