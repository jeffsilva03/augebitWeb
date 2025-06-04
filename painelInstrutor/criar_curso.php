<?php
// criar_curso.php
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../arquivosReuso/conexao.php';
$sucesso = false;

$instrutor_id = $_SESSION['usuario_id'] ?? null;
$errors = [];

// Verifica sessão
if (!$instrutor_id) {
    $errors[] = "Instrutor não autenticado.";
}

// Processa formulário se for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura e valida campos
    $titulo    = trim($_POST['titulo']    ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $category  = trim($_POST['category']  ?? '');
    $level     = trim($_POST['level']     ?? '');
    $duration  = trim($_POST['duration']  ?? '');
    $rating    = is_numeric($_POST['rating'] ?? null) ? floatval($_POST['rating']) : null;
    $students  = is_numeric($_POST['students'] ?? null) ? intval($_POST['students']) : null;
    $grad1     = trim($_POST['grad1']     ?? '');
    $grad2     = trim($_POST['grad2']     ?? '');
    $icon      = trim($_POST['icon']      ?? '');

    // Validações
    if (mb_strlen($titulo) < 3)     $errors[] = "Título muito curto (mínimo 3 caracteres).";
    if (mb_strlen($descricao) < 20) $errors[] = "Descrição muito curta (mínimo 20 caracteres).";
    if ($rating === null || $rating < 0 || $rating > 5) $errors[] = "Avaliação inválida.";
    if ($students === null || $students < 0)            $errors[] = "Número de estudantes inválido.";
    if (!$grad1 || !$grad2)                             $errors[] = "Escolha as duas cores do gradiente.";
    if (!$icon)                                         $errors[] = "Selecione um ícone para o curso.";

    if (empty($errors)) {
        // Insere no banco
        $gradient_json = json_encode([$grad1, $grad2]);
        $sql = "INSERT INTO cursos
          (instrutor_id, titulo, descricao, duration, level, category, gradient, icon, rating, students, criado_em)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql)
            or die("Prepare falhou: " . $conn->error);
        $stmt->bind_param(
            'issssssdis',
            $instrutor_id,
            $titulo,
            $descricao,
            $duration,
            $level,
            $category,
            $gradient_json,
            $icon,
            $rating,
            $students
        ) or die("Bind falhou: " . $stmt->error);
        $stmt->execute()
            or die("Execute falhou: " . $stmt->error);

        $sucesso = true;
    }
} else {
    // Valores padrão para GET
    $titulo = $descricao = $category = $level = $duration = '';
    $rating = 0;
    $students = 0;
    $grad1 = '#8a2be2';
    $grad2 = '#0e0b11';
    $icon = '';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Criar Novo Curso</title>
   <link rel="icon" href="../arquivosReuso/src/icone.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="criar_curso.css">
</head>
<body>
  <?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul>
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <!-- Container para notificações flutuantes -->
  <div class="notifications-container" id="notifications"></div>

  <!-- Header -->
  <header class="header">
    <div class="header-content">
      <h1 class="header-title">
        <div class="header-icon">
          <i class="fas fa-plus-circle"></i>
        </div>
        Criar Novo Curso
      </h1>
      <p class="header-subtitle">Configure todos os detalhes do seu curso para oferecer a melhor experiência aos alunos</p>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container fade-in">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
      <a href="painel_instrutor.php">
        <i class="fas fa-tachometer-alt"></i>
        Painel do Instrutor
      </a>
      <span class="breadcrumb-separator">
        <i class="fas fa-chevron-right"></i>
      </span>
      <span class="breadcrumb-current">
        <i class="fas fa-plus-circle"></i>
        Criar Curso
      </span>
    </nav>

    <!-- Main Form -->
    <form class="form" id="courseForm" method="POST" action="criar_curso.php">
      
      <!-- Informações Básicas -->
      <div class="card slide-up">
        <div class="card-header">
          <h2 class="card-title">
            <div class="card-title-icon">
              <i class="fas fa-info-circle"></i>
            </div>
            Informações Básicas
          </h2>
          <p class="card-subtitle">
            Configure as informações principais do seu curso
          </p>
        </div>
        <div class="card-body">
          <div class="form-section">
            <h3 class="form-section-title">
              <i class="fas fa-edit"></i>
              Dados Principais
            </h3>
            
            <!-- Título -->
            <div class="form-group">
              <label class="label label-required" for="titulo">
                <i class="fas fa-heading label-icon"></i>
                Título do Curso
              </label>
              <input 
                class="input" 
                type="text" 
                id="titulo" 
                name="titulo" 
                placeholder="Ex: Introdução ao Desenvolvimento Web"
                required
                maxlength="255"
                value="<?= htmlspecialchars($titulo) ?>"
              >
              <div class="input-hint">
                <i class="fas fa-info-circle"></i>
                Escolha um título claro e atrativo que descreva bem o conteúdo
              </div>
              <div class="char-counter" id="titleCounter">0/255</div>
            </div>

            <!-- Descrição -->
            <div class="form-group">
              <label class="label label-required" for="descricao">
                <i class="fas fa-align-left label-icon"></i>
                Descrição Detalhada
              </label>
              <textarea 
                class="textarea" 
                id="descricao" 
                name="descricao" 
                placeholder="Descreva detalhadamente o que os alunos aprenderão, pré-requisitos e resultados esperados..."
                required
              ><?= htmlspecialchars($descricao) ?></textarea>
              <div class="input-hint">
                <i class="fas fa-lightbulb"></i>
                Inclua objetivos de aprendizagem, conteúdo abordado e público-alvo
              </div>
              <div class="char-counter" id="descCounter">0 caracteres</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Configurações do Curso -->
      <div class="card slide-up">
        <div class="card-header">
          <h2 class="card-title">
            <div class="card-title-icon">
              <i class="fas fa-cogs"></i>
            </div>
            Configurações do Curso
          </h2>
          <p class="card-subtitle">
            Defina categoria, nível de dificuldade e outras especificações
          </p>
        </div>
        <div class="card-body">
          <div class="form-section">
            <h3 class="form-section-title">
              <i class="fas fa-sliders-h"></i>
              Especificações
            </h3>
            
            <div class="form-row-3">
              <!-- Categoria -->
              <div class="form-group">
                <label class="label" for="category">
                  <i class="fas fa-tags label-icon"></i>
                  Categoria
                </label>
                <select class="select" id="category" name="category">
                  <option value="">Selecione uma categoria</option>
                  <option value="tech" <?= $category === 'tech' ? 'selected' : '' ?>>Tecnologia</option>
                  <option value="design" <?= $category === 'design' ? 'selected' : '' ?>>Design</option>
                  <option value="marketing" <?= $category === 'marketing' ? 'selected' : '' ?>>Marketing</option>
                  <option value="business" <?= $category === 'business' ? 'selected' : '' ?>>Negócios</option>
                  <option value="languages" <?= $category === 'languages' ? 'selected' : '' ?>>Idiomas</option>
                  <option value="health" <?= $category === 'health' ? 'selected' : '' ?>>Saúde</option>
                  <option value="music" <?= $category === 'music' ? 'selected' : '' ?>>Música</option>
                  <option value="arts" <?= $category === 'arts' ? 'selected' : '' ?>>Artes</option>
                  <option value="other" <?= $category === 'other' ? 'selected' : '' ?>>Outro</option>
                </select>
              </div>

              <!-- Nível -->
              <div class="form-group">
                <label class="label" for="level">
                  <i class="fas fa-signal label-icon"></i>
                  Nível de Dificuldade
                </label>
                <select class="select" id="level" name="level">
                  <option value="">Selecione o nível</option>
                  <option value="beginner" <?= $level === 'Iniciante' ? 'selected' : '' ?>>Iniciante</option>
                  <option value="intermediate" <?= $level === 'Intermediário' ? 'selected' : '' ?>>Intermediário</option>
                  <option value="advanced" <?= $level === 'Avançado' ? 'selected' : '' ?>>Avançado</option>
                  <option value="expert" <?= $level === 'expert' ? 'selected' : '' ?>>Expert</option>
                </select>
              </div>

              <!-- Duração -->
              <div class="form-group">
                <label class="label" for="duration">
                  <i class="fas fa-clock label-icon"></i>
                  Duração Estimada
                </label>
                <input 
                  class="input" 
                  type="text" 
                  id="duration" 
                  name="duration" 
                  placeholder="Ex: 2 horas, 5 dias, 3 semanas"
                  value="<?= htmlspecialchars($duration) ?>"
                >
                <div class="input-hint">
                  <i class="fas fa-info-circle"></i>
                  Informe o tempo necessário para conclusão
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Estatísticas e Avaliação -->
      <div class="card slide-up">
        <div class="card-header">
          <h2 class="card-title">
            <div class="card-title-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            Estatísticas e Avaliação
          </h2>
          <p class="card-subtitle">
            Configure a avaliação inicial e número de estudantes
          </p>
        </div>
        <div class="card-body">
          <div class="form-section">
            <h3 class="form-section-title">
              <i class="fas fa-star"></i>
              Métricas Iniciais
            </h3>
            
            <div class="form-row">
              <!-- Rating -->
              <div class="form-group">
                <label class="label" for="rating">
                  <i class="fas fa-star label-icon"></i>
                  Avaliação (0-5)
                </label>
                <div style="display: flex; align-items: center; gap: 1rem;">
                  <input 
                    class="range-input" 
                    type="range" 
                    id="rating" 
                    name="rating" 
                    min="0" 
                    max="5" 
                    step="0.1" 
                    value="<?= $rating ?>"
                  >
                  <span class="range-value" id="ratingValue"><?= number_format($rating, 1) ?></span>
                </div>
                <div class="input-hint">
                  <i class="fas fa-info-circle"></i>
                  Avaliação inicial do curso (pode ser ajustada posteriormente)
                </div>
              </div>

              <!-- Número de Estudantes -->
              <div class="form-group">
                <label class="label" for="students">
                  <i class="fas fa-users label-icon"></i>
                  Número de Estudantes
                </label>
                <input 
                  class="input" 
                  type="number" 
                  id="students" 
                  name="students" 
                  min="0" 
                  value="<?= $students ?>"
                  placeholder="0"
                >
                <div class="input-hint">
                  <i class="fas fa-info-circle"></i>
                  Número inicial de estudantes inscritos
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Personalização Visual -->
      <div class="card slide-up">
        <div class="card-header">
          <h2 class="card-title">
            <div class="card-title-icon">
              <i class="fas fa-palette"></i>
            </div>
            Personalização Visual
          </h2>
          <p class="card-subtitle">
            Escolha cores e ícones para tornar seu curso mais atrativo
          </p>
        </div>
        <div class="card-body">
          <div class="form-section">
            <h3 class="form-section-title">
              <i class="fas fa-paint-brush"></i>
              Aparência
            </h3>
            
            <!-- Gradiente de Cores -->
            <div class="form-group">
              <label class="label" for="gradient">
                <i class="fas fa-fill-drip label-icon"></i>
                Gradiente de Cores
              </label>
              <div class="color-input-group">
                <input type="color" class="color-input" id="color1" name="grad1" value="<?= htmlspecialchars($grad1) ?>">
                <span>→</span>
                <input type="color" class="color-input" id="color2" name="grad2" value="<?= htmlspecialchars($grad2) ?>">
              </div>
              <div class="gradient-preview" id="gradientPreview"></div>
              <div class="input-hint">
                <i class="fas fa-info-circle"></i>
                Escolha duas cores para criar o gradiente do seu curso
              </div>
            </div>

            <!-- Seletor de Ícone -->
            <div class="form-group">
              <label class="label" for="icon">
                <i class="fas fa-icons label-icon"></i>
                Ícone do Curso
              </label>
              <div class="icon-selector" id="iconSelector">
                <div class="icon-option" data-icon="fas fa-graduation-cap">
                  <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-book">
                  <i class="fas fa-book"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-laptop-code">
                  <i class="fas fa-laptop-code"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-paint-brush">
                  <i class="fas fa-paint-brush"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-chart-line">
                  <i class="fas fa-chart-line"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-camera">
                  <i class="fas fa-camera"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-music">
                  <i class="fas fa-music"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-dumbbell">
                  <i class="fas fa-dumbbell"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-heartbeat">
                  <i class="fas fa-heartbeat"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-language">
                  <i class="fas fa-language"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-rocket">
                  <i class="fas fa-rocket"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-lightbulb">
                  <i class="fas fa-lightbulb"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-brain">
                  <i class="fas fa-brain"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-globe">
                  <i class="fas fa-globe"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-cog">
                  <i class="fas fa-cog"></i>
                </div>
                <div class="icon-option" data-icon="fas fa-bullseye">
                  <i class="fas fa-bullseye"></i>
                </div>
              </div>
              <input type="hidden" id="icon" name="icon" value="<?= htmlspecialchars($icon) ?>">
              <div class="input-hint">
                <i class="fas fa-info-circle"></i>
                Selecione um ícone que represente o tema do seu curso
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Botões de Ação -->
      <div class="card slide-up">
        <div class="card-body">
          <div class="button-group">
            <button type="button" class="btn btn-secondary" onclick="resetForm()">
              <i class="fas fa-undo"></i>
              Limpar Formulário
            </button>
            <button type="submit" class="btn btn-primary" id="submitBtn">
              <i class="fas fa-save"></i>
              Criar Curso
            </button>
          </div>
        </div>
      </div>
    </form>
  </main>

  <script>
    // Character counters
    const titleInput = document.getElementById('titulo');
    const titleCounter = document.getElementById('titleCounter');
    const descInput = document.getElementById('descricao');
    const descCounter = document.getElementById('descCounter');

    function updateTitleCounter() {
      const count = titleInput.value.length;
      titleCounter.textContent = `${count}/255`;
      titleCounter.style.color = count > 200 ? '#ec4e56' : '#666666';
    }

    function updateDescCounter() {
      const count = descInput.value.length;
      descCounter.textContent = `${count} caracteres`;
      descCounter.style.color = count < 20 ? '#ec4e56' : '#666666';
    }

    titleInput.addEventListener('input', updateTitleCounter);
    descInput.addEventListener('input', updateDescCounter);
    
    // Initialize counters
    updateTitleCounter();
    updateDescCounter();

    // Rating range slider
    const ratingSlider = document.getElementById('rating');
    const ratingValue = document.getElementById('ratingValue');

    ratingSlider.addEventListener('input', function() {
      ratingValue.textContent = parseFloat(this.value).toFixed(1);
    });

    // Color gradient picker
    const color1 = document.getElementById('color1');
    const color2 = document.getElementById('color2');
    const gradientPreview = document.getElementById('gradientPreview');

    function updateGradient() {
      const c1 = color1.value;
      const c2 = color2.value;
      const gradient = `linear-gradient(135deg, ${c1}, ${c2})`;
      gradientPreview.style.background = gradient;
    }

    color1.addEventListener('change', updateGradient);
    color2.addEventListener('change', updateGradient);
    updateGradient(); // Initialize

    // Icon selector
    const iconOptions = document.querySelectorAll('.icon-option');
    const iconInput = document.getElementById('icon');

    iconOptions.forEach(option => {
      // Check if this icon should be selected based on PHP value
      if (option.dataset.icon === iconInput.value) {
        option.classList.add('selected');
      }
      
      option.addEventListener('click', function() {
        // Remove selected class from all options
        iconOptions.forEach(opt => opt.classList.remove('selected'));
        
        // Add selected class to clicked option
        this.classList.add('selected');
        
        // Set the value
        iconInput.value = this.dataset.icon;
      });
    });

    function resetForm() {
      if (confirm('Tem certeza que deseja limpar todos os campos do formulário?')) {
        document.getElementById('courseForm').reset();
        iconOptions.forEach(opt => opt.classList.remove('selected'));
        ratingValue.textContent = '0.0';
        titleCounter.textContent = '0/255';
        descCounter.textContent = '0 caracteres';
        updateGradient();
        iconInput.value = '';
      }
    }

    // Add smooth animations on scroll
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.animationDelay = '0.1s';
          entry.target.classList.add('slide-up');
        }
      });
    });

    document.querySelectorAll('.card').forEach(card => {
      observer.observe(card);
    });

    // Sistema de notificações aprimorado
    function showNotification(type, title, description) {
      const notifications = {
        success: {
          icon: '✓',
          class: 'status-success'
        },
        error: {
          icon: '⚠',
          class: 'status-error'
        },
        warning: {
          icon: '⚡',
          class: 'status-warning'
        },
        info: {
          icon: 'ℹ',
          class: 'status-info'
        }
      };

      const notification = notifications[type];
      const container = document.getElementById('notifications');
      
      const notificationEl = document.createElement('div');
      notificationEl.className = `status-message ${notification.class}`;
      notificationEl.innerHTML = `
        <div class="status-icon">${notification.icon}</div>
        <div class="status-content">
          <div class="status-title">${title}</div>
          <div class="status-description">${description}</div>
        </div>
        <button class="status-close" onclick="removeNotification(this)">×</button>
      `;
      
      container.appendChild(notificationEl);
      
      // Auto remove após 5 segundos
      setTimeout(() => {
        if (notificationEl.parentElement) {
          removeNotification(notificationEl.querySelector('.status-close'));
        }
      }, 5000);
    }

    function removeNotification(button) {
      const notification = button.parentElement;
      notification.style.animation = 'slideOutRight 0.4s ease forwards';
      setTimeout(() => {
        if (notification.parentElement) {
          notification.remove();
        }
      }, 400);
    }

    // Função para mostrar sucesso após criação do curso
    function showSuccessAndReset() {
      showNotification(
        'success', 
        'Curso criado com sucesso!', 
        'Seu curso foi salvo e está disponível no painel do instrutor.'
      );
      
      // Limpa o formulário após 2 segundos
      setTimeout(() => {
        document.getElementById('courseForm').reset();
        iconOptions.forEach(opt => opt.classList.remove('selected'));
        ratingValue.textContent = '0.0';
        titleCounter.textContent = '0/255';
        descCounter.textContent = '0 caracteres';
        iconInput.value = '';
        updateGradient();
      }, 2000);
    }

    // Verifica se o curso foi criado com sucesso
    <?php if (isset($sucesso) && $sucesso): ?>
    showSuccessAndReset();
    <?php endif; ?>
  </script>
</body>
</html>