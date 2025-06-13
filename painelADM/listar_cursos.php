<?php
session_start();
require '../conexaoBD/conexao.php';
if (!isset($_SESSION['perfil']) || $_SESSION['perfil']!=='adm') {
  header('Location: ../form_login.php'); exit;
}

// Buscar todos os cursos
$cursos = $conexao->query("SELECT id, titulo, descricao FROM cursos ORDER BY titulo");

// Verificar se o usuário clicou em um curso para ver os inscritos
$curso_selecionado = null;
$inscritos = null;
$total_inscritos = 0;

if (isset($_GET['curso_id']) && is_numeric($_GET['curso_id'])) {
  $curso_id = intval($_GET['curso_id']);
  
  // Buscar informações do curso
  $stmt_curso = $conexao->prepare("SELECT id, titulo, descricao FROM cursos WHERE id = ?");
  $stmt_curso->bind_param('i', $curso_id);
  $stmt_curso->execute();
  $resultado_curso = $stmt_curso->get_result();
  $curso_selecionado = $resultado_curso->fetch_assoc();
  
  if ($curso_selecionado) {
      // Buscar inscritos neste curso
      $stmt_inscritos = $conexao->prepare("
      SELECT c.id, c.nome, c.email, i.inscrito_em as data_inscricao 
      FROM cadastro c
      JOIN inscricoes i ON c.id = i.usuario_id
      WHERE i.curso_id = ?
      ORDER BY c.nome
    ");
    $stmt_inscritos->bind_param('i', $curso_id);
    $stmt_inscritos->execute();
    $inscritos = $stmt_inscritos->get_result();
    $total_inscritos = $inscritos->num_rows;
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Cursos e Inscritos • ADM</title>
  
  <link rel="icon" href="src/icone.ico" type="image/x-icon">
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

    
    .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem 1rem;
    }

   
    .cursos-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-top: 1.5rem;
    }

   
    .card {
      background: var(--white);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow-md);
      transition: transform var(--anim-duration) ease, box-shadow var(--anim-duration) ease;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
    }

    .card-header {
      padding: 1.25rem;
      border-bottom: 1px solid var(--gray-light);
      background-color: var(--primary-bg);
    }

    .card-body {
      padding: 1.25rem;
      flex-grow: 1;
    }

    .card-footer {
      padding: 1.25rem;
      border-top: 1px solid var(--gray-light);
      background-color: #fafafa;
    }

    .card-titulo {
      color: var(--primary-dark);
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
      font-weight: 600;
    }

    .card-subtitulo {
      color: var(--gray-dark);
      font-size: 0.875rem;
      margin-bottom: 1rem;
    }

    .card-descricao {
      color: var(--dark);
      font-size: 0.95rem;
      line-height: 1.5;
    }


    .badge {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      font-size: 0.75rem;
      font-weight: 600;
      border-radius: 9999px;
      gap: 0.25rem;
    }

    .badge-primary {
      background-color: var(--primary-bg);
      color: var(--primary-dark);
    }

    
    .inscritos-lista {
      margin-top: 2rem;
    }

    .inscritos-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .inscritos-titulo {
      color: var(--primary-dark);
      font-size: 1.5rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .tabela-container {
      overflow-x: auto;
      border-radius: var(--radius);
      box-shadow: var(--shadow-md);
    }

    .tabela {
      width: 100%;
      border-collapse: collapse;
      background-color: var(--white);
    }

    .tabela th, 
    .tabela td {
      padding: 0.75rem 1rem;
      text-align: left;
    }

    .tabela th {
      background-color: var(--primary-bg);
      color: var(--primary-dark);
      font-weight: 600;
      position: relative;
    }

    .tabela tr {
      border-bottom: 1px solid var(--gray-light);
    }

    .tabela tr:last-child {
      border-bottom: none;
    }

    .tabela tr:hover {
      background-color: #fafafa;
    }

    /* Voltar link */
    .voltar-link {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      color: var(--primary);
      font-weight: 500;
      text-decoration: none;
      padding: 0.5rem 0;
      transition: color var(--anim-duration) ease;
    }

    .voltar-link:hover {
      color: var(--primary-dark);
    }

    /* HEADER */
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--gray-light);
    }

    .page-title {
      color: var(--primary-dark);
      font-size: 1.75rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

   
    .btn {
      background: var(--primary);
      color: var(--white);
      border: none;
      border-radius: var(--radius);
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: all var(--anim-duration) ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      text-decoration: none;
    }

    .btn:hover {
      background: var(--primary-dark);
      box-shadow: var(--shadow-md);
      transform: translateY(-2px);
    }

    .btn-sm {
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
    }

    .btn-outline {
      background: transparent;
      border: 2px solid var(--primary);
      color: var(--primary);
    }

    .btn-outline:hover {
      background: var(--primary-bg);
    }

  
    .status-vazio {
      padding: 2rem;
      text-align: center;
      background-color: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--shadow-md);
    }

    .status-vazio-icon {
      font-size: 3rem;
      color: var(--gray);
      margin-bottom: 1rem;
    }

    .status-vazio-titulo {
      font-size: 1.25rem;
      color: var(--gray-dark);
      margin-bottom: 0.5rem;
    }

    .status-vazio-texto {
      color: var(--gray);
      margin-bottom: 1.5rem;
    }

    
    .animar-fadein {
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

  
    @media (max-width: 768px) {
      .cursos-grid {
        grid-template-columns: 1fr;
      }
      
      .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }
      
      .inscritos-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }
    }
  </style>
</head>
<body class="pagina-painel">
  <?php include 'header.php'; ?>

  <main class="container animar-fadein">
    <?php if(!empty($_SESSION['mensagem'])): ?>
      <div class="alerta alerta-sucesso">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
      </div>
    <?php endif;?>
    <?php if(!empty($_SESSION['erro'])): ?>
      <div class="alerta alerta-erro">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <?= $_SESSION['erro']; unset($_SESSION['erro']); ?>
      </div>
    <?php endif;?>

    <?php if($curso_selecionado): ?>
      <div class="page-header">
        <a href="listar_cursos.php" class="voltar-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          Voltar para a lista de cursos
        </a>
        
        <a href="inscrever_curso.php" class="btn btn-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
          Inscrever novo aluno
        </a>
      </div>

      <div class="card">
        <div class="card-header">
          <h2 class="card-titulo"><?= htmlspecialchars($curso_selecionado['titulo']) ?></h2>
          <div class="card-subtitulo">
            <span class="badge badge-primary">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              <?= $total_inscritos ?> inscritos
            </span>
          </div>
        </div>
        <div class="card-body">
          <p class="card-descricao"><?= htmlspecialchars($curso_selecionado['descricao'] ?? 'Sem descrição disponível.') ?></p>
        </div>
      </div>

      <div class="inscritos-lista">
        <div class="inscritos-header">
          <h3 class="inscritos-titulo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Alunos Inscritos
          </h3>
        </div>

        <?php if($total_inscritos > 0): ?>
          <div class="tabela-container">
            <table class="tabela">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>E-mail</th>
                  <th>Telefone</th>
                  <th>Data de Inscrição</th>
                </tr>
              </thead>
              <tbody>
                <?php while($inscrito = $inscritos->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($inscrito['nome']) ?></td>
                    <td><?= htmlspecialchars($inscrito['email']) ?></td>
                    <td><?= htmlspecialchars($inscrito['telefone'] ?? 'Não informado') ?></td>
                    <td>
                      <?php 
                        $data = new DateTime($inscrito['data_inscricao']);
                        echo $data->format('d/m/Y H:i');
                      ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="status-vazio">
            <div class="status-vazio-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            </div>
            <h3 class="status-vazio-titulo">Nenhum aluno inscrito</h3>
            <p class="status-vazio-texto">Este curso ainda não possui alunos inscritos.</p>
            <a href="inscrever_curso.php" class="btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
              Inscrever aluno
            </a>
          </div>
        <?php endif; ?>
      </div>

    <?php else: ?>
      <div class="page-header">
        <h1 class="page-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
          Cursos
        </h1>
        <a href="inscrever_curso.php" class="btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
          Inscrever aluno
        </a>
      </div>

      <div class="cursos-grid">
        <?php if($cursos->num_rows > 0): ?>
          <?php while($curso = $cursos->fetch_assoc()): 
            $stmt_count = $conexao->prepare("SELECT COUNT(*) as total FROM inscricoes WHERE curso_id = ?");
            $stmt_count->bind_param('i', $curso['id']);
            $stmt_count->execute();
            $resultado_count = $stmt_count->get_result();
            $count = $resultado_count->fetch_assoc()['total'];
          ?>
            <div class="card">
              <div class="card-header">
                <h2 class="card-titulo"><?= htmlspecialchars($curso['titulo']) ?></h2>
                <div class="card-subtitulo">
                  <span class="badge badge-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <?= $count ?> inscritos
                  </span>
                </div>
              </div>
              <div class="card-body">
                <p class="card-descricao">
                  <?= htmlspecialchars(substr($curso['descricao'] ?? 'Sem descrição disponível.', 0, 150)) ?>
                  <?= strlen($curso['descricao'] ?? '') > 150 ? '...' : '' ?>
                </p>
              </div>
              <div class="card-footer">
                <a href="listar_cursos.php?curso_id=<?= $curso['id'] ?>" class="btn btn-outline btn-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                  Ver Inscritos
                </a>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="status-vazio" style="grid-column: 1 / -1;">
            <div class="status-vazio-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <h3 class="status-vazio-titulo">Nenhum curso cadastrado</h3>
            <p class="status-vazio-texto">Não há cursos cadastrados no sistema.</p>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });
  </script>
</body>
</html>