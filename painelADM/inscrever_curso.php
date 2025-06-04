<?php
// adm/inscrever_curso.php
session_start();
require '../conexaoBD/conexao.php';
if (!isset($_SESSION['perfil']) || $_SESSION['perfil']!=='adm') {
  header('Location: ../form_login.php'); exit;
}

// Processa inscrição...
if ($_SERVER['REQUEST_METHOD']==='POST') {
  // Verifica se é uma submissão de confirmação
  if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'true') {
    $usuario_id = intval($_POST['usuario_id']);
    $curso_id = intval($_POST['curso_id']);
    
    // Verificar se já existe esta inscrição para evitar duplicatas
    $verificar = $conexao->prepare("SELECT id FROM inscricoes WHERE usuario_id = ? AND curso_id = ?");
    $verificar->bind_param('ii', $usuario_id, $curso_id);
    $verificar->execute();
    $verificar->store_result();
    
    if ($verificar->num_rows > 0) {
      $_SESSION['erro'] = "Este usuário já está inscrito neste curso.";
    } else {
      // Inserir a inscrição
      $stmt = $conexao->prepare("INSERT INTO inscricoes (usuario_id, curso_id) VALUES (?, ?)");
      $stmt->bind_param('ii', $usuario_id, $curso_id);
      $resultado = $stmt->execute();
      
      if ($resultado) {
        $_SESSION['mensagem'] = "Inscrição realizada com sucesso.";
      } else {
        $_SESSION['erro'] = "Erro ao inscrever: " . $stmt->error;
      }
    }
    
    // Permanece na mesma página em vez de redirecionar
    $mostrar_resultado = true;
  }
}

// Buscar dados para os selects
$usuarios = $conexao->query("SELECT id, nome FROM cadastro ORDER BY nome");
$cursos = $conexao->query("SELECT id, titulo FROM cursos ORDER BY titulo");

// Para preencher novamente o formulário após envio
$usuario_selecionado = isset($_POST['usuario_id']) ? intval($_POST['usuario_id']) : '';
$curso_selecionado = isset($_POST['curso_id']) ? intval($_POST['curso_id']) : '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Inscrever em Curso • ADM</title>
  <link rel="stylesheet" href="style.css">
  <style>
    :root {
      --primary: #6F24B6;
      --primary-dark: #6F24B6;
      --primary-light: #a5b4fc;
      --primary-bg: #eef2ff;
      --success: #10b981;
      --error: #ef4444;
      --dark: #1e293b;
      --gray-dark: #64748b;
      --gray: #94a3b8;
      --gray-light: #e2e8f0;
      --white: #ffffff;
      --radius: 12px;
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
      --anim-duration: 0.3s;
    }

    /* Card base styles */
    .card {
      background: var(--white);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow-lg);
      margin-bottom: 2rem;
      transition: transform var(--anim-duration) ease, box-shadow var(--anim-duration) ease;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .form-card {
      position: relative;
      padding: 2rem;
      border-left: 6px solid var(--primary);
    }

    .card-titulo {
      color: var(--primary-dark);
      font-size: 1.75rem;
      margin-bottom: 2rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .card-titulo svg {
      color: var(--primary);
    }

    /* Form elements styling */
    .formulario {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .select-group {
      position: relative;
      margin-bottom: 1rem;
    }

    .select-group select {
      width: 100%;
      padding: 1rem 1rem;
      background: var(--primary-bg);
      border: 2px solid transparent;
      border-radius: var(--radius);
      font-size: 1rem;
      color: var(--dark);
      transition: all var(--anim-duration) ease;
      -webkit-appearance: none;
      appearance: none;
      cursor: pointer;
    }

    .select-group select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
    }

    .select-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--dark);
    }

    .select-group::after {
      content: "▼";
      position: absolute;
      right: 1rem;
      bottom: 1rem;
      font-size: 0.875rem;
      color: var(--primary);
      pointer-events: none;
    }

    /* Buttons */
    .btn {
      background: var(--primary);
      color: var(--white);
      border: none;
      border-radius: var(--radius);
      padding: 1rem 1.5rem;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: all var(--anim-duration) ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      box-shadow: var(--shadow-md);
    }

    .btn:hover {
      background: var(--primary-dark);
      box-shadow: var(--shadow-lg);
      transform: translateY(-2px);
    }
    
    .btn-secondary {
      background: transparent;
      color: var(--primary);
      border: 2px solid var(--primary);
    }
    
    .btn-secondary:hover {
      background: var(--primary-bg);
      color: var(--primary-dark);
    }

    .btn-large {
      font-size: 1.125rem;
      padding: 1.25rem 2rem;
    }

    /* Ripple effect */
    .btn-ripple {
      position: relative;
      overflow: hidden;
    }

    .btn-ripple::after {
      content: "";
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      pointer-events: none;
      background-image: radial-gradient(circle, rgba(255, 255, 255, 0.4) 10%, transparent 10.01%);
      background-repeat: no-repeat;
      background-position: 50%;
      transform: scale(10, 10);
      opacity: 0;
      transition: transform 0.5s, opacity 1s;
    }

    .btn-ripple:active::after {
      transform: scale(0, 0);
      opacity: 0.3;
      transition: 0s;
    }
    
    /* Modal styling */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(15, 23, 42, 0.7);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 100;
      opacity: 0;
      visibility: hidden;
      transition: all var(--anim-duration);
      backdrop-filter: blur(4px);
    }
    
    .modal-overlay.ativo {
      opacity: 1;
      visibility: visible;
    }
    
    .modal {
      background: var(--white);
      width: 90%;
      max-width: 500px;
      border-radius: var(--radius);
      padding: 2rem;
      position: relative;
      transform: translateY(-20px);
      transition: all var(--anim-duration);
      box-shadow: var(--shadow-lg);
    }
    
    .modal-overlay.ativo .modal {
      transform: translateY(0);
    }
    
    .modal-titulo {
      color: var(--primary-dark);
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 1.5rem;
    }
    
    .modal-conteudo {
      margin-bottom: 1.5rem;
      color: var(--gray-dark);
      line-height: 1.6;
    }
    
    .modal-acoes {
      display: flex;
      gap: 1rem;
      justify-content: flex-end;
    }
    
    /* Alert and notification styling */
    .alerta {
      padding: 1rem;
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      animation: fadeInDown 0.5s ease forwards;
    }
    
    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .alerta-sucesso {
      background-color: rgba(16, 185, 129, 0.1);
      border-left: 4px solid var(--success);
      color: var(--success);
    }
    
    .alerta-erro {
      background-color: rgba(239, 68, 68, 0.1);
      border-left: 4px solid var(--error);
      color: var(--error);
    }

    /* Results card styling */
    .resultado-card {
      padding: 2rem;
      border-radius: var(--radius);
      background-color: var(--white);
      box-shadow: var(--shadow-md);
      margin-bottom: 2rem;
      border-top: 4px solid var(--primary);
      animation: slideIn 0.5s ease forwards;
    }
    
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .resultado-card h3 {
      font-size: 1.25rem;
      margin-bottom: 1.5rem;
      color: var(--primary-dark);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .resultado-acoes {
      margin-top: 1.5rem;
    }

    /* Container layout */
    .container {
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      padding: 2rem 1rem;
    }

    /* Animation */
    .animar-fadein {
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .form-card {
        padding: 1.5rem;
      }
      
      .card-titulo {
        font-size: 1.5rem;
      }
      
      .modal-acoes {
        flex-direction: column;
      }
      
      .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body class="pagina-painel">
  <?php include 'header.php'; ?>

  <main class="container animar-fadein">
    <?php if(isset($mostrar_resultado) && $mostrar_resultado): ?>
      <section class="resultado-card">
        <h3>
          <?php if(!empty($_SESSION['mensagem'])): ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Inscrição Realizada
          <?php elseif(!empty($_SESSION['erro'])): ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-circle"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Não Foi Possível Realizar a Inscrição
          <?php endif; ?>
        </h3>
        
        <?php if(!empty($_SESSION['mensagem'])): ?>
          <div class="alerta alerta-sucesso"><?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?></div>
        <?php endif;?>
        <?php if(!empty($_SESSION['erro'])): ?>
          <div class="alerta alerta-erro"><?= $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
        <?php endif;?>
        
        <div class="resultado-acoes">
          <a href="listar_usuarios.php" class="btn btn-ripple">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Ver Lista de Usuários
          </a>
        </div>
      </section>
    <?php else: ?>
      <?php if(!empty($_SESSION['mensagem'])): ?>
        <div class="alerta alerta-sucesso">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check"><polyline points="20 6 9 17 4 12"/></svg>
          <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
        </div>
      <?php endif;?>
      <?php if(!empty($_SESSION['erro'])): ?>
        <div class="alerta alerta-erro">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-triangle"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          <?= $_SESSION['erro']; unset($_SESSION['erro']); ?>
        </div>
      <?php endif;?>
    <?php endif; ?>

    <section class="card form-card">
      <h2 class="card-titulo">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
        Inscrever em Curso
      </h2>
      <form id="form-inscricao" method="post" class="formulario">
        <input type="hidden" name="action" value="inscrever">
        <input type="hidden" name="confirmar" value="false" id="campo-confirmar">

        <div class="select-group">
          <label for="usuario_id">Usuário</label>
          <select name="usuario_id" id="usuario_id" required>
            <option value="" disabled selected>Selecione o usuário</option>
            <?php while($u = $usuarios->fetch_assoc()): ?>
              <option value="<?=$u['id']?>" <?= ($usuario_selecionado == $u['id']) ? 'selected' : '' ?>>
                <?=htmlspecialchars($u['nome'])?>
              </option>
            <?php endwhile;?>
          </select>
        </div>

        <div class="select-group">
          <label for="curso_id">Curso</label>
          <select name="curso_id" id="curso_id" required>
            <option value="" disabled selected>Selecione o curso</option>
            <?php while($c = $cursos->fetch_assoc()): ?>
              <option value="<?=$c['id']?>" <?= ($curso_selecionado == $c['id']) ? 'selected' : '' ?>>
                <?=htmlspecialchars($c['titulo'])?>
              </option>
            <?php endwhile;?>
          </select>
        </div>

        <button class="btn btn-large btn-ripple" type="button" id="btn-inscrever">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          Confirmar Inscrição
        </button>
      </form>
    </section>
  </main>
  
  <!-- Modal de confirmação -->
  <div class="modal-overlay" id="modal-confirmacao">
    <div class="modal">
      <h3 class="modal-titulo">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        Confirmar Inscrição
      </h3>
      <div class="modal-conteudo">
        <p>Você está prestes a inscrever <strong id="nome-usuario"></strong> no curso <strong id="nome-curso"></strong>.</p>
        <p>Deseja confirmar esta inscrição?</p>
      </div>
      <div class="modal-acoes">
        <button class="btn btn-secondary btn-ripple" id="btn-cancelar">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          Cancelar
        </button>
        <button class="btn btn-ripple" id="btn-confirmar">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check"><polyline points="20 6 9 17 4 12"/></svg>
          Confirmar
        </button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Configurar o modal e funcionalidades
      const btnInscrever = document.getElementById('btn-inscrever');
      const modalConfirmacao = document.getElementById('modal-confirmacao');
      const btnCancelar = document.getElementById('btn-cancelar');
      const btnConfirmar = document.getElementById('btn-confirmar');
      const formInscricao = document.getElementById('form-inscricao');
      const campoConfirmar = document.getElementById('campo-confirmar');
      const nomeUsuario = document.getElementById('nome-usuario');
      const nomeCurso = document.getElementById('nome-curso');
      
      btnInscrever.addEventListener('click', function() {
        const usuarioSelect = document.getElementById('usuario_id');
        const cursoSelect = document.getElementById('curso_id');
        
        // Verificar se os campos foram preenchidos
        if (!usuarioSelect.value || !cursoSelect.value) {
          alert('Por favor, selecione o usuário e o curso.');
          return;
        }
        
        // Preencher os detalhes no modal
        nomeUsuario.textContent = usuarioSelect.options[usuarioSelect.selectedIndex].text;
        nomeCurso.textContent = cursoSelect.options[cursoSelect.selectedIndex].text;
        
        // Mostrar o modal
        modalConfirmacao.classList.add('ativo');
      });
      
      btnCancelar.addEventListener('click', function() {
        modalConfirmacao.classList.remove('ativo');
      });
      
      btnConfirmar.addEventListener('click', function() {
        // Definir o campo de confirmação como true
        campoConfirmar.value = 'true';
        
        // Enviar o formulário
        formInscricao.submit();
      });
      
      // Fechar o modal ao clicar fora dele
      modalConfirmacao.addEventListener('click', function(e) {
        if (e.target === modalConfirmacao) {
          modalConfirmacao.classList.remove('ativo');
        }
      });

      // Inicializar os ícones Lucide caso estejam presentes
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });
  </script>
</body>
</html>