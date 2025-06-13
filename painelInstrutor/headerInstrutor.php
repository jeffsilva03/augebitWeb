<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Augebit</title>
  <link rel="icon" href="src/icone.ico" type="image/x-icon">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>

    :root {
      --bg-header: #FFFFFF;
      --nav-text: #333333;
      --accent-purple: #9b30ff;
      --accent-purple-light: #b66fff;
      --accent-purple-dark: #7a00cc;
      --avatar-border: #e5e7eb;
      --header-shadow: 0 4px 20px rgba(155, 48, 255, 0.1);
      --border-radius: 12px;
      --transition-speed: 0.3s;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body { 
      margin: 0;
      font-family: 'Poppins', Arial, sans-serif;
      background-color: #f9f9f9;
    }

    .header-wrapper {
      background: var(--bg-header);
      padding: 16px 24px;
      border-radius: var(--border-radius);
      max-width: 1200px;
      margin: 20px auto;
      box-shadow: var(--header-shadow);
      position: sticky;
      top: 20px;
      z-index: 100;
    }
    
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .header .logo img {
      height: 40px;
      display: block;
      transition: transform var(--transition-speed);
    }
    
    .header .logo img:hover {
      transform: scale(1.05);
    }
    
   
    .header .logo img:not([src]),
    .header .logo img[src=""],
    .header .logo img:broken {
      width: 120px;
      height: 40px;
      background: linear-gradient(135deg, var(--accent-purple), var(--accent-purple-light));
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 16px;
    }
    
    .header .logo img:not([src])::before,
    .header .logo img[src=""]::before,
    .header .logo img:broken::before {
      content: "AUGEBIT";
    }
    
    .header-nav {
      list-style: none;
      display: flex;
      gap: 32px;
      margin: 0;
      padding: 0;
    }
    
    .header-nav a {
      color: var(--nav-text);
      text-decoration: none;
      font-size: 15px;
      font-weight: 500;
      padding: 4px 0;
      position: relative;
      transition: color var(--transition-speed);
    }
    
    .header-nav a:hover {
      color: var(--accent-purple);
    }
    
    .header-nav a::after {
      content: "";
      position: absolute;
      left: 0; bottom: -4px;
      width: 0; height: 3px;
      background: linear-gradient(90deg, var(--accent-purple), var(--accent-purple-light));
      transition: width var(--transition-speed) ease;
      border-radius: 3px;
    }
    
    .header-nav a:hover::after {
      width: 100%;
    }
    
    .user-actions {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .btn-cursos {
      background: linear-gradient(135deg, var(--accent-purple), var(--accent-purple-dark));
      color: #fff;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 30px;
      font-size: 14px;
      font-weight: 600;
      transition: all var(--transition-speed);
      box-shadow: 0 4px 12px rgba(155, 48, 255, 0.25);
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .btn-cursos i {
      font-size: 16px;
    }
    
    .btn-cursos:hover {
      background: linear-gradient(135deg, var(--accent-purple-dark), var(--accent-purple));
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(155, 48, 255, 0.3);
    }
    
    .btn-cursos:active {
      transform: translateY(0);
      box-shadow: 0 4px 8px rgba(155, 48, 255, 0.2);
    }

    .avatar {
      width: 42px;
      height: 42px;
      border: 2px solid var(--accent-purple-light);
      border-radius: 50%;
      overflow: hidden;
      display: block;
      transition: all var(--transition-speed);
      box-shadow: 0 2px 8px rgba(155, 48, 255, 0.15);
    }
    
    .avatar:hover {
      border-color: var(--accent-purple);
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(155, 48, 255, 0.25);
    }
    
    .avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .mobile-menu-toggle {
      display: none;
      background: none;
      border: none;
      color: var(--nav-text);
      font-size: 24px;
      cursor: pointer;
      padding: 4px;
      transition: color var(--transition-speed);
    }
    
    .mobile-menu-toggle:hover {
      color: var(--accent-purple);
    }

    @media (max-width: 992px) {
      .header-nav {
        gap: 20px;
      }
      
      .header-nav a {
        font-size: 14px;
      }
      
      .btn-cursos {
        padding: 8px 16px;
      }
    }

    @media (max-width: 768px) {
      .header-wrapper {
        padding: 12px 16px;
        margin: 12px;
      }
      
      .mobile-menu-toggle {
        display: block;
      }
      
      .header-nav {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        flex-direction: column;
        background: white;
        padding: 16px;
        margin-top: 12px;
        border-radius: var(--border-radius);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        animation: fadeIn 0.3s ease;
      }
      
      @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
      }
      
      .header-nav.active {
        display: flex;
      }
      
      .header-nav li {
        width: 100%;
      }
      
      .header-nav a {
        display: block;
        padding: 12px 0;
        font-size: 16px;
      }
      
      .header-nav a::after {
        display: none;
      }
    }
    </style>
</head>
<body>
  <div class="header-wrapper">
    <header class="header">
      <div class="logo">
        <a href="/">
          <img src="src/logo.png" alt="Augebit Logo" id="logoImg">
        </a>
      </div>

      <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
      </button>

      <ul class="header-nav" id="mainNav">
        <li>
          <a href="../paginaInicial/paginaInicial.php">
            <i class="fas fa-home"></i> Página Inicial do Site
          </a>
        </li>
        <li>
          <a href="conteudoCursos/index.php">
            <i class="fas fa-info-circle"></i> Atividade
          </a>
        </li>
        <li>
          <a href="conteudoCursos/aula.php">
            <i class="fas fa-graduation-cap"></i> Aula
          </a>
        </li>
        <li>
          <a href="conteudoCursos/avaliacao.php">
            <i class="fas fa-envelope"></i> Avaliação
          </a>
        </li>
      </ul>

      

        <div class="avatar">
          <img src="src/default-avatar.png" alt="Avatar" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM5YjMwZmYiLz4KPHN2ZyB4PSI4IiB5PSI4IiB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSI+CjxwYXRoIGQ9Ik0xMiAxMkM3IDEyIDMgMTYgMyAyMVYyM0gyMVYyMUMxNyAxNiAxMyAxMiAxMiAxMloiIGZpbGw9IndoaXRlIi8+CjxjaXJjbGUgY3g9IjEyIiBjeT0iOCIgcj0iNCIgZmlsbD0id2hpdGUiLz4KPHN2Zz4KPHN2Zz4=';">
        </div>
      </div>
    </header>
  </div>

  <script>
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mainNav          = document.getElementById('mainNav');
    mobileMenuToggle.addEventListener('click', () => {
      mainNav.classList.toggle('active');
      const icon = mobileMenuToggle.querySelector('i');
      icon.classList.toggle('fa-bars');
      icon.classList.toggle('fa-times');
    });
    document.addEventListener('click', e => {
      if (!mobileMenuToggle.contains(e.target) && !mainNav.contains(e.target)) {
        mainNav.classList.remove('active');
        const icon = mobileMenuToggle.querySelector('i');
        icon.classList.add('fa-bars');
        icon.classList.remove('fa-times');
      }
    });
    mainNav.addEventListener('click', e => e.stopPropagation());
  </script>
</body>
</html>