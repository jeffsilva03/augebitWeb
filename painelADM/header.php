<?php 
if (session_status()===PHP_SESSION_NONE) session_start();
?>
<div class="wrapper">
  <aside class="sidebar">
    <div class="sidebar-logo">
      <a href="painel_adm.php"><span>Painel ADM</span></a>
    </div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="painel_adm.php" class="<?= ($_SERVER['PHP_SELF'] == '/adm/painel_adm.php') ? 'active' : '' ?>">
            <span class="icon"></span><span>Dashboard</span>
        </a></li>
        <li><a href="listar_usuarios.php" class="<?= ($_SERVER['PHP_SELF'] == '/adm/listar_usuarios.php') ? 'active' : '' ?>">
            <span class="icon"></span><span>Usuários</span>
        </a></li>
        <li><a href="listar_cursos.php" class="<?= ($_SERVER['PHP_SELF'] == '/adm/listar_cursos.php') ? 'active' : '' ?>">
            <span class="icon"></span><span>Cursos</span>
        </a></li>
      </ul>
    </nav>
    <!-- Botão para recolher/expandir sidebar -->
    <div class="btn-toggle-sidebar" id="toggle-sidebar"></div>
  </aside>

  <div class="main-content">
    <header class="topnav">
      <button type="button" class="btn-mobile-toggle" id="mobile-toggle">
        <span></span>
      </button>
      
      <div class="logo-center">
        <img src="src/logo.png" alt="Logo" class="logo-img">
      </div>
      <div class="topnav-user">
        <span>Bem‑vindo, <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?></strong></span>
        <a href="../paginaInicial/paginaInicial.php" class="btn-logout">Sair</a>
      </div>
    </header>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleSidebarBtn = document.getElementById('toggle-sidebar');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    

    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
    }
    
   
    toggleSidebarBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        
       
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    
   
    const mobileToggleBtn = document.getElementById('mobile-toggle');
    const body = document.body;
    
    mobileToggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('mobile-visible');
        body.classList.toggle('mobile-menu-open');
    });
    
    // Fechar menu ao clicar em um link (para mobile)
    const mobileMenuLinks = sidebar.querySelectorAll('a');
    
    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                sidebar.classList.remove('mobile-visible');
                body.classList.remove('mobile-menu-open');
            }
        });
    });
    
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('mobile-visible');
            body.classList.remove('mobile-menu-open');
        }
    });
});
    </script>