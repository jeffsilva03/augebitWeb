<?php
session_start();

// incluir conexão
$pathConn = realpath( '../conexaoBD/conexao.php');
if (!$pathConn || !file_exists($pathConn)) {
    die('Erro: não encontrou o arquivo de conexão em ../conexaoBD/conexao.php');
}
require_once $pathConn;
if (!isset($conn) && isset($conexao)) {
    $conn = $conexao;
}
if (!isset($conn)) {
    die('Erro: a variável de conexão ($conn) não foi definida em conexao.php');
}

// buscar lista de cursos
$cursos = [];
$sql = "SELECT id, titulo, descricao FROM cursos";
if ($res = $conn->query($sql)) {
    while ($row = $res->fetch_assoc()) {
        $cursos[] = $row;
    }
    $res->free();
}

// processar submissão do formulário
$status = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $conf  = $_POST['confirmar_senha'] ?? '';
    $perfil = $_POST['perfil'] ?? '';
    $conta_ativa = isset($_POST['conta_ativa']) ? 1 : 0;
    $cursos_sel  = $_POST['cursos'] ?? [];

    // validações básicas
    if (!$nome || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status[] = ['type'=>'error','msg'=>'Preencha nome e email corretamente.'];
    } elseif (strlen($senha) < 6 || $senha !== $conf) {
        $status[] = ['type'=>'error','msg'=>'Senha inválida ou não confere.'];
    } elseif (!$perfil) {
        $status[] = ['type'=>'error','msg'=>'Selecione um perfil.'];
    } elseif (empty($cursos_sel)) {
        $status[] = ['type'=>'error','msg'=>'Selecione ao menos um curso.'];
    } else {
        // inserir usuário
        $hash = password_hash($senha, PASSWORD_BCRYPT);
        $stmt = $conn->prepare(
          "INSERT INTO cadastro (nome,email,senha,perfil) VALUES (?,?,?,?)"
        );
        $stmt->bind_param('ssss', $nome, $email, $hash, $perfil);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $stmt->close();
            // inserir inscrições
            $stmt2 = $conn->prepare(
              "INSERT INTO inscricoes (usuario_id,curso_id) VALUES (?,?)"
            );
            foreach ($cursos_sel as $cid) {
                $stmt2->bind_param('ii', $user_id, $cid);
                $stmt2->execute();
            }
            $stmt2->close();
            $status[] = ['type'=>'success','msg'=>'Usuário cadastrado com sucesso!'];
            // limpar POST para não reexibir form
            $_POST = [];
        } else {
            $status[] = ['type'=>'error','msg'=>'Erro ao cadastrar usuário.'];
            $stmt->close();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adicionar Novo Usuário</title>
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
      margin-bottom: 2rem;
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
      content: '\f234';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      color: var(--clr-purple);
    }
    
    .card-curso .card-titulo::before {
      content: '\f5da';
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
    
    /* Course selection */
    .curso-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-top: 0.5rem;
    }
    
    .curso-card {
      border: 1px solid #e2e8f0;
      border-radius: var(--radius);
      padding: 1rem;
      transition: var(--transition);
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }
    
    .curso-card:hover {
      border-color: var(--clr-purple);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(138, 43, 226, 0.1);
    }
    
    .curso-card input[type="checkbox"] {
      position: absolute;
      opacity: 0;
    }
    
    .curso-card input[type="checkbox"]:checked + .curso-content {
      border-color: var(--clr-purple);
    }
    
    .curso-card input[type="checkbox"]:checked + .curso-content::before {
      content: '\f00c';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      position: absolute;
      top: -8px;
      right: -8px;
      width: 25px;
      height: 25px;
      border-radius: 50%;
      background: var(--clr-purple);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
    }
    
    .curso-content {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      border: 1px solid transparent;
      border-radius: var(--radius);
      padding: 0.5rem;
      transition: var(--transition);
    }
    
    .curso-titulo {
      font-weight: 500;
      color: var(--clr-dark);
      margin-top: 0.5rem;
    }
    
    .curso-icon {
      width: 40px;
      height: 40px;
      background-color: rgba(138, 43, 226, 0.1);
      color: var(--clr-purple);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
    }
    
    .curso-descricao {
      font-size: 0.75rem;
      color: var(--clr-gray);
    }
    
    .curso-badge {
      font-size: 0.7rem;
      font-weight: 500;
      padding: 0.2rem 0.5rem;
      border-radius: 12px;
      background-color: rgba(138, 43, 226, 0.1);
      color: var(--clr-purple);
      display: inline-block;
    }
    
    /* Password strength indicator */
    .password-strength {
      height: 5px;
      width: 100%;
      background: #e2e8f0;
      border-radius: 10px;
      margin-top: 0.3rem;
      margin-bottom:1.5rem;
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
    
    /* Custom switch */
    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 48px;
      height: 24px;
      margin: 0.5rem 0;
    }
    
    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    
    .toggle-slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #e2e8f0;
      transition: var(--transition);
      border-radius: 34px;
    }
    
    .toggle-slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: var(--transition);
      border-radius: 50%;
    }
    
    input:checked + .toggle-slider {
      background-color: var(--clr-purple);
    }
    
    input:checked + .toggle-slider:before {
      transform: translateX(24px);
    }
    
    .toggle-label {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin: 0.5rem 0;
      cursor: pointer;
    }
    
    /* Tab system */
    .tabs {
      display: flex;
      border-bottom: 1px solid #e2e8f0;
      margin-bottom: 1.5rem;
    }
    
    .tab {
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      cursor: pointer;
      position: relative;
      color: var(--clr-gray);
      transition: var(--transition);
    }
    
    .tab.active {
      color: var(--clr-purple);
    }
    
    .tab.active::after {
      content: '';
      position: absolute;
      bottom: -1px;
      left: 0;
      width: 100%;
      height: 2px;
      background-color: var(--clr-purple);
    }
    
    .tab-content {
      display: none;
    }
    
    .tab-content.active {
      display: block;
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
      
      .curso-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <main class="container animar-fadein">
    <div class="breadcrumb">
      <a href="listar_usuarios.php"><i class="fas fa-users"></i> Usuários</a>
      <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
      <span class="breadcrumb-current">Adicionar Novo Usuário</span>
    </div>

    <?php foreach ($status as $s): ?>
      <div class="form-status <?= $s['type']==='success' ? 'status-success' : 'status-error' ?>">
        <i class="fas <?= $s['type']==='success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
        <?= htmlspecialchars($s['msg']) ?>
      </div>
    <?php endforeach; ?>

    <form method="post" class="formulario">
      <div class="tabs">
        <div class="tab active" data-tab="info-pessoal">1. Informações Pessoais</div>
        <div class="tab" data-tab="cursos">2. Cursos</div>
      </div>

      <!-- Tab 1 -->
      <div class="tab-content active" id="info-pessoal">
        <section class="card">
          <h2 class="card-titulo">Informações do Usuário</h2>
          <div class="form-grid">
            <div class="form-group">
              <label class="label" for="nome">Nome Completo</label>
              <input class="input" type="text" id="nome" name="nome"
                     value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="label" for="email">Email</label>
              <input class="input" type="email" id="email" name="email"
                     value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label class="label" for="senha">Senha</label>
              <input class="input" type="password" id="senha" name="senha" required>
              <div class="password-strength"><div id="password-bar" class="password-strength-bar"></div></div>
            </div>
            <div class="form-group">
              <label class="label" for="confirmar_senha">Confirmar Senha</label>
              <input class="input" type="password" id="confirmar_senha" name="confirmar_senha" required>
            </div>
          </div>
          <div class="form-group">
            <label class="label" for="perfil">Tipo de Perfil</label>
            <select class="select" id="perfil" name="perfil" required>
              <option value="">Selecione um perfil</option>
              <option value="usuario"   <?= (($_POST['perfil']??'')==='usuario')?'selected':'' ?>>Usuário</option>
              <option value="instrutor" <?= (($_POST['perfil']??'')==='instrutor')?'selected':'' ?>>Instrutor</option>
              <option value="adm"       <?= (($_POST['perfil']??'')==='adm')?'selected':'' ?>>Administrador</option>
            </select>
          </div>
         
          <div class="d-flex" style="justify-content: space-between; margin-top:1.5rem;">
            <a class="botao botao-secundario" href="listar_usuarios.php">
              <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <button type="button" class="botao botao-principal next-tab" data-next="cursos">
              Próximo: Cursos <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </section>
      </div>

      <!-- Tab 2 -->
      <div class="tab-content" id="cursos">
        <section class="card card-curso">
          <h2 class="card-titulo">Atribuir Cursos</h2>
          <div class="form-group">
            <label class="label">Selecione os cursos para este usuário</label>
            <div class="curso-grid">
              <?php foreach ($cursos as $curso): 
                $checked = in_array($curso['id'], $_POST['cursos'] ?? []) ? 'checked' : '';
              ?>
                <label class="curso-card">
                  <input type="checkbox" name="cursos[]" value="<?= $curso['id'] ?>" <?= $checked ?>>
                  <div class="curso-content">
                    <div class="curso-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h4 class="curso-titulo"><?= htmlspecialchars($curso['titulo']) ?></h4>
                    <p class="curso-descricao"><?= htmlspecialchars($curso['descricao']) ?></p>
                  </div>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="d-flex" style="justify-content: space-between; margin-top:1.5rem;">
            <button type="button" class="botao botao-secundario prev-tab" data-prev="info-pessoal">
              <i class="fas fa-arrow-left"></i> Voltar: Informações
            </button>
            <button type="submit" class="botao botao-principal">
              <i class="fas fa-user-plus"></i> Cadastrar Usuário
            </button>
          </div>
        </section>
      </div>
    </form>
  </main>

  <script>
    // navegação de abas
    document.addEventListener('DOMContentLoaded', function() {
      const tabs = document.querySelectorAll('.tab');
      const contents = document.querySelectorAll('.tab-content');
      const nextBtns = document.querySelectorAll('.next-tab');
      const prevBtns = document.querySelectorAll('.prev-tab');

      function showTab(id) {
        tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === id));
        contents.forEach(c => c.classList.toggle('active', c.id === id));
      }

      tabs.forEach(t => t.addEventListener('click', () => showTab(t.dataset.tab)));
      nextBtns.forEach(b => b.addEventListener('click', () => showTab(b.dataset.next)));
      prevBtns.forEach(b => b.addEventListener('click', () => showTab(b.dataset.prev)));

      // força de senha
      const pwd = document.getElementById('senha');
      const bar = document.getElementById('password-bar');
      pwd.addEventListener('input', function() {
        const v = this.value;
        bar.style.width = v.length > 0
          ? (v.length < 6 ? '33%' : v.length < 10 ? '66%' : '100%')
          : '0';
        bar.className = 'password-strength-bar ' +
          (v.length < 6 ? 'strength-weak' : v.length < 10 ? 'strength-medium' : 'strength-strong');
      });

      // confirmar senha
      const confirm = document.getElementById('confirmar_senha');
      confirm.addEventListener('input', function() {
        this.style.borderColor =
          this.value === pwd.value ? '#4fbe87' : '#ec4e56';
      });
    });
  </script>
</body>
</html>