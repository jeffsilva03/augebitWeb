<?php
session_start();
require '../conexaoBD/conexao.php';
if (!isset($_SESSION['perfil']) || $_SESSION['perfil']!=='adm') {
  header('Location: ../form_login.php');
  exit;
}
$result = $conexao->query("SELECT id,nome,email,perfil FROM cadastro ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Listar Usuários</title>
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
      overflow-x: hidden;
    }
    
    
    /* Main content */
    main.container {
      max-width: 1400px;
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
      content: '\f022';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      color: var(--clr-purple);
    }
    
    /* Actions bar */
    .actions-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }
    
    .search-box {
      display: flex;
      background-color: #f0f0f7;
      border-radius: var(--radius);
      padding: 0.5rem 1rem;
      width: 300px;
      transition: var(--transition);
      border: 1px solid transparent;
    }
    
    .search-box:focus-within {
      border-color: var(--clr-purple);
      box-shadow: 0 0 0 3px rgba(138, 43, 226, 0.1);
    }
    
    .search-box input {
      background: transparent;
      border: none;
      outline: none;
      width: 100%;
      padding: 0.25rem 0.5rem;
      font-family: var(--font-base);
    }
    
    .search-box i {
      color: var(--clr-gray);
      display: flex;
      align-items: center;
    }
    
    .add-button {
      background: linear-gradient(135deg, var(--clr-purple) 0%, var(--clr-purple-dark) 100%);
      color: var(--clr-white);
      border: none;
      border-radius: var(--radius);
      padding: 0.625rem 1.25rem;
      font-family: var(--font-base);
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
    }
    
    .add-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(138, 43, 226, 0.3);
    }
    
    /* Table styles */
    .table-container {
      overflow-x: auto;
      border-radius: var(--radius);
    }
    
    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      font-size: 0.95rem;
    }
    
    thead tr {
      background-color: #f9f9fd;
    }
    
    th {
      text-align: left;
      padding: 1rem;
      font-weight: 600;
      color: var(--clr-dark);
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.05rem;
      border-bottom: 1px solid #eaeaea;
    }
    
    tbody tr {
      transition: var(--transition);
    }
    
    tbody tr:hover {
      background-color: #f9f9fd;
    }
    
    td {
      padding: 1rem;
      border-bottom: 1px solid #eaeaea;
    }
    
    td:last-child {
      text-align: right;
    }
    
    .status {
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 500;
      display: inline-block;
    }
    
    .status-adm {
      background-color: rgba(138, 43, 226, 0.1);
      color: var(--clr-purple);
    }
    
    .status-user {
      background-color: rgba(79, 190, 135, 0.1);
      color: #4fbe87;
    }
    
    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }
    
    .btn {
      padding: 0.4rem 0.75rem;
      border-radius: var(--radius);
      font-size: 0.85rem;
      font-weight: 500;
      transition: var(--transition);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }
    
    .btn-edit {
      background-color: rgba(79, 190, 135, 0.1);
      color: #4fbe87;
    }
    
    .btn-delete {
      background-color: rgba(236, 78, 86, 0.1);
      color: #ec4e56;
    }
    
    .btn:hover {
      filter: brightness(0.9);
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
      .actions-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
      }
      
      .search-box {
        width: 100%;
      }
      
      .add-button {
        justify-content: center;
      }
      
      th, td {
        padding: 0.75rem 0.5rem;
      }
      
      .action-buttons {
        flex-direction: column;
      }

    
    }
  </style>
</head>
<body class="pagina-painel">
  <?php include 'header.php'; ?>

  <main class="container animar-fadein">
    <section class="card">
      <h2 class="card-titulo">Lista de Usuários</h2>
      
      <div class="actions-bar">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Buscar usuários...">
        </div>
        <a href="adicionar_usuario.php" class="add-button">
          <i class="fas fa-plus"></i>
          Adicionar Usuário
        </a>
      </div>
      
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Email</th>
              <th>Perfil</th>
              <th >Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php while($u=$result->fetch_assoc()): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nome']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                  <span class="status <?= $u['perfil'] === 'adm' ? 'status-adm' : 'status-user' ?>">
                    <?= $u['perfil'] ?>
                  </span>
                </td>
                <td>
                  <div class="action-buttons">
                    <a class="btn btn-edit" href="editar_usuario.php?id=<?= $u['id'] ?>">
                      <i class="fas fa-edit"></i> Editar
                    </a>
                    <a class="btn btn-delete" href="excluir_usuario.php?id=<?= $u['id'] ?>">
                      <i class="fas fa-trash"></i> Excluir
                    </a>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
      </div>
    </section>
  </main>

  <?php echo '</div></div>'; ?>
</body>
</html>
