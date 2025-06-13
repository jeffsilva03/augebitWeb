<?php
// Iniciar a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// caminho até o seu conexao.php
$pathConn = realpath('conexao.php');
if (!$pathConn || !file_exists($pathConn)) {
    $possible_paths = [
        '../../../conexaoBD/conexao.php',
        '../../../../conexaoBD/conexao.php',
        '../conexaoBD/conexao.php',
        'conexaoBD/conexao.php'
    ];
    $connection_found = false;
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $connection_found = true;
            break;
        }
    }
    if (!$connection_found) {
        die('Erro: não encontrou o arquivo de conexão (HEADER)');
    }
} else {
    require_once $pathConn;
}


if (!isset($conn) && isset($conexao)) {
    $conn = $conexao;
}
if (!isset($conn)) {
    die('Erro: a variável de conexão ($conn) não foi definida em conexao.php');
}

// 3) Busca ID do usuário e avatar
$userId        = $_SESSION['usuario_id'] ?? null;
$defaultAvatar = "curso/usuario/paginaPerfil/uploads/avatars/default-avatar.png";
$avatarUrl     = $defaultAvatar;

if ($userId) {
    $sql  = "SELECT avatar FROM cadastro WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        if (!empty($user['avatar']) && file_exists($user['avatar'])) {
            $avatarUrl = $user['avatar'];
        } elseif (!empty($user['avatar'])) {
            $try = dirname(__FILE__) . '/' . $user['avatar'];
            $avatarUrl = file_exists($try) ? $user['avatar'] : $defaultAvatar;
        }
    }
    $stmt->close();
}

// função para encontrar o caminho correto da logo
function findLogoPath() {
    $currentDir = dirname(__FILE__);
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    
    // possíveis caminhos da logo
    $possiblePaths = [
        'logo.png',              
        'src/logo.png',          
        '../logo.png',           
        '../src/logo.png',       
        '../../src/logo.png',    
        '../../../src/logo.png', 
        '../../../../src/logo.png', 
        '../../../../../src/logo.png' 
    ];
    
    foreach ($possiblePaths as $path) {
        $fullPath = $currentDir . '/' . $path;
        if (file_exists($fullPath)) {
            // Converte para caminho web relativo
            $webPath = str_replace($documentRoot, '', realpath($fullPath));
            $webPath = str_replace('\\', '/', $webPath); // Windows compatibility
            $webPath = ltrim($webPath, '/');
            
            // Se começar com o domínio, remove
            if (strpos($webPath, $_SERVER['HTTP_HOST']) !== false) {
                $webPath = substr($webPath, strpos($webPath, $_SERVER['HTTP_HOST']) + strlen($_SERVER['HTTP_HOST']));
                $webPath = ltrim($webPath, '/');
            }
            
            return $webPath;
        }
    }
    
  
    $scriptPath = $_SERVER['SCRIPT_NAME'];
    $pathParts = explode('/', trim($scriptPath, '/'));
    

    array_pop($pathParts);
    
   
    $backPath = '';
    $foundReuso = false;
    
    for ($i = count($pathParts) - 1; $i >= 0; $i--) {
        if ($pathParts[$i] === 'arquivosReuso') {
            $foundReuso = true;
            break;
        }
        $backPath .= '../';
    }
    
    if ($foundReuso) {
        return $backPath . 'src/logo.png';
    }
    
   
    return 'src/logo.png';
}


function calculateBasePath() {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $depth = substr_count($scriptName, '/') - 2;
    return str_repeat('../', max(0, $depth));
}

$logoPath = findLogoPath();
$basePath = calculateBasePath();

// função para encontrar avatar path
function findAvatarPath($avatarUrl, $basePath) {
    if (empty($avatarUrl) || $avatarUrl === "curso/usuario/paginaPerfil/uploads/avatars/default-avatar.png") {
        $possibleDefaultPaths = [
            $basePath . 'curso/usuario/paginaPerfil/uploads/avatars/default-avatar.png',
            '../' . $avatarUrl,
            '../../' . $avatarUrl,
            '../../../' . $avatarUrl,
            $avatarUrl
        ];
        
        foreach ($possibleDefaultPaths as $path) {
            $checkPath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($path, './');
            if (file_exists($checkPath)) {
                return $path;
            }
        }
    }
    
    // Se é um caminho personalizado
    if (strpos($avatarUrl, 'http') === 0) {
        return $avatarUrl; // URL completa
    }
    
    return $basePath . $avatarUrl;
}

$avatarPath = findAvatarPath($avatarUrl, $basePath);

// define os itens do header-nav
$navItems = [
    ['href' => 'paginaInicial/paginaInicial.php',           'icon' => 'fa-home',         'text' => 'Home'],
    ['href' => 'paginaInicial/paginaInicial.php#sobre',     'icon' => 'fa-info-circle',  'text' => 'Sobre nós'],
    ['href' => 'paginaInicial/paginaInicial.php#cursos',    'icon' => 'fa-graduation-cap','text' => 'Nossos Cursos'],
    ['href' => 'paginaInicial/paginaInicial.php#faleConosco','icon' => 'fa-envelope',     'text' => 'Contato'],
];

//  itens do avatar dropdown
$menuItems = [
    ['href' => 'curso/usuario/paginaPerfil/pgnUser.php', 'label' => '<i class="fas fa-user"></i> Perfil'],
    ['href' => 'notas.php',                             'label' => '<i class="fas fa-chart-bar"></i> Notas'],
    ['href' => 'certificado/cert.php',                  'label' => '<i class="fas fa-file-alt"></i> Relatórios'],
    ['divider' => true],
    ['href' => 'registro/logout.php',                   'label' => '<i class="fas fa-sign-out-alt"></i> Sair'],
];
?>
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
      --dropdown-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
      --border-radius: 12px;
      --transition-speed: 0.3s;
    }
    
    .ico{
      width: 40px;
      height: 40px;
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

    /* Avatar + dropdown */
    .user-menu {
      position: relative;
      display: inline-block;
      cursor: pointer;
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
    
    .dropdown-menu {
      list-style: none;
      margin: 12px 0 0;
      padding: 10px 0;
      background: #fff;
      border: none;
      border-radius: var(--border-radius);
      position: absolute;
      right: 0;
      top: 100%;
      min-width: 200px;
      box-shadow: var(--dropdown-shadow);
      display: none;
      z-index: 10;
      animation: slideDown 0.3s ease;
      transform-origin: top right;
    }
    
    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
    
    .dropdown-menu li + li { 
      margin-top: 2px; 
    }
    
    .dropdown-menu a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 20px;
      color: var(--nav-text);
      text-decoration: none;
      font-size: 14px;
      transition: all var(--transition-speed);
    }
    
    .dropdown-menu a i {
      width: 20px;
      text-align: center;
      font-size: 16px;
      color: var(--accent-purple);
      transition: all var(--transition-speed);
    }
    
    .dropdown-menu a:hover {
      background: linear-gradient(to right, rgba(155, 48, 255, 0.1), rgba(155, 48, 255, 0.05));
      color: var(--accent-purple);
      padding-left: 24px;
    }
    
    .dropdown-menu a:hover i {
      transform: scale(1.2);
    }

    
    .dropdown-divider {
      height: 1px;
      margin: 8px 0;
      background: linear-gradient(to right, transparent, rgba(155, 48, 255, 0.2), transparent);
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
        box-shadow: var(--dropdown-shadow);
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
      
      .dropdown-menu {
        position: fixed;
        width: calc(100% - 24px);
        max-width: 300px;
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
        <?php foreach ($navItems as $item): ?>
          <li>
            <a href="<?= htmlspecialchars($basePath . $item['href']) ?>">
              <i class="fas <?= $item['icon'] ?>"></i> <?= $item['text'] ?>
            </a>
          </li>
        <?php endforeach ?>
      </ul>

      <div class="user-actions">
        <a href="<?= htmlspecialchars($basePath . 'registro/form_login.php') ?>" class="btn-cursos">
          <i class="fas fa-book-open"></i> Acessar Cursos
        </a>

        <div class="user-menu">
          <div class="avatar" id="avatarToggle">
            <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Avatar" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM5YjMwZmYiLz4KPHN2ZyB4PSI4IiB5PSI4IiB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSI+CjxwYXRoIGQ9Ik0xMiAxMkM3IDEyIDMgMTYgMyAyMVYyM0gyMVYyMUMxNyAxNiAxMyAxMiAxMiAxMloiIGZpbGw9IndoaXRlIi8+CjxjaXJjbGUgY3g9IjEyIiBjeT0iOCIgcj0iNCIgZmlsbD0id2hpdGUiLz4KPHN2Zz4KPHN2Zz4=';">
          </div>
          <ul class="dropdown-menu" id="avatarMenu">
            <?php foreach ($menuItems as $item): ?>
              <?php if (!empty($item['divider'])): ?>
                <div class="dropdown-divider"></div>
              <?php else: ?>
                <li>
                  <a href="<?= htmlspecialchars($basePath . $item['href']) ?>">
                    <?= $item['label'] ?>
                  </a>
                </li>
              <?php endif ?>
            <?php endforeach ?>
          </ul>
        </div>
      </div>
    </header>
  </div>

  <script>
    const toggle = document.getElementById('avatarToggle');
    const menu   = document.getElementById('avatarMenu');
    toggle.addEventListener('click', e => {
      e.stopPropagation();
      menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', () => menu.style.display = 'none');

    // Mobile menu toggle
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