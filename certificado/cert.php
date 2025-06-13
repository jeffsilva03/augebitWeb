<?php

//  Inicia sessão 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//  Verifica login usando a mesma chave de sessão do header
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../registro/form_login.php');
    exit;
}
$usuario_id = $_SESSION['usuario_id'];

// Conexão PDO
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=semestral_3b;charset=utf8",
        "root", "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Pega nome do aluno
$stmt = $pdo->prepare("SELECT nome FROM cadastro WHERE id = ?");
$stmt->execute([$usuario_id]);
$nome_aluno = $stmt->fetchColumn();

// Pega cursos 
$stmt = $pdo->prepare("
    SELECT c.id, c.titulo, c.duration
FROM inscricoes i  
JOIN cursos c ON i.curso_id = c.id
WHERE i.usuario_id = ?
");
$stmt->execute([$usuario_id]);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se não há inscrições, avisa e sai
if (empty($cursos)) {
    ?><!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>Certificados - AUGEBIT</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: 'Inter', sans-serif; 
                background: #ffffff;
                color: #1a202c;
                line-height: 1.6;
            }
            .container {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2rem;
            }
            .empty-state {
                text-align: center;
                max-width: 500px;
                padding: 3rem 2rem;
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                border: 2px solid #e2e8f0;
                border-radius: 24px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            }
            .empty-icon {
                width: 80px;
                height: 80px;
                margin: 0 auto 2rem;
                background: linear-gradient(135deg, #5b67d1, #7c3aed);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                color: white;
            }
            .empty-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1a202c;
                margin-bottom: 1rem;
            }
            .empty-message {
                color: #64748b;
                font-size: 1.1rem;
            }
        </style>
    </head>
    <body>
        <?php include __DIR__ . '/../arquivosReuso/header.php'; ?>
        <div class="container">
            <div class="empty-state">
                <div class="empty-icon">📜</div>
                <h2 class="empty-title">Nenhum Curso Encontrado</h2>
                <p class="empty-message">Você ainda não está inscrito em nenhum curso. Explore nossa plataforma e comece sua jornada de aprendizado!</p>
            </div>
        </div>
        <?php include __DIR__ . '/../arquivosReuso/footer.php'; ?>
    </body>
    </html><?php
    exit;
}

// Se não passou curso_id, mostra formulário de seleção
if (!isset($_GET['curso_id'])) {
    ?><!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>Selecionar Curso - AUGEBIT</title>
        <link rel="icon" href="src/icone.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            
            body { 
                font-family: 'Inter', sans-serif; 
                background: #ffffff;
                color: #1a202c;
                line-height: 1.6;
            }

            .main-wrapper {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .content-area {
                flex: 1;
                padding: 4rem 2rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                max-width: 800px;
                margin: 0 auto;
                width: 100%;
            }

            .page-header {
                text-align: center;
                margin-bottom: 3rem;
            }

            .page-title {
                font-size: 2.5rem;
                font-weight: 800;
                color: #1a202c;
                margin-bottom: 0.5rem;
                position: relative;
            }

            .page-title::after {
                content: '';
                position: absolute;
                bottom: -10px;
                left: 50%;
                transform: translateX(-50%);
                width: 60px;
                height: 4px;
                background: linear-gradient(90deg, #5b67d1, #7c3aed);
                border-radius: 2px;
            }

            .page-subtitle {
                font-size: 1.2rem;
                color: #64748b;
                font-weight: 500;
                margin-top: 1.5rem;
            }

            .selection-card {
                background: #ffffff;
                border: 2px solid #e2e8f0;
                border-radius: 24px;
                padding: 3rem;
                width: 100%;
                max-width: 600px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.08);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .selection-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #5b67d1, #7c3aed, #d946ef);
            }

            .selection-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 25px 80px rgba(0,0,0,0.12);
                border-color: #c7d2fe;
            }

            .form-group {
                margin-bottom: 2rem;
            }

            .form-label {
                display: block;
                font-size: 1.1rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 0.75rem;
            }

            .form-select {
                width: 100%;
                padding: 1rem 1.25rem;
                font-size: 1rem;
                border: 2px solid #e5e7eb;
                border-radius: 12px;
                background: #ffffff;
                color: #1f2937;
                transition: all 0.3s ease;
                font-family: 'Inter', sans-serif;
                appearance: none;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
                background-position: right 0.75rem center;
                background-repeat: no-repeat;
                background-size: 1.5em 1.5em;
                padding-right: 3rem;
            }

            .form-select:hover {
                border-color: #9ca3af;
            }

            .form-select:focus {
                outline: none;
                border-color: #5b67d1;
                box-shadow: 0 0 0 3px rgba(91, 103, 209, 0.1);
            }

            .form-select option {
                padding: 0.5rem;
                font-size: 1rem;
            }

            .btn-generate {
                width: 100%;
                padding: 1.25rem 2rem;
                background: linear-gradient(135deg, #5b67d1, #7c3aed);
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 1.1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Inter', sans-serif;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                position: relative;
                overflow: hidden;
            }

            .btn-generate::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s ease;
            }

            .btn-generate:hover {
                transform: translateY(-2px);
                box-shadow: 0 15px 40px rgba(91, 103, 209, 0.4);
            }

            .btn-generate:hover::before {
                left: 100%;
            }

            .btn-generate:active {
                transform: translateY(0);
            }

            .courses-info {
                margin-top: 2rem;
                padding: 1.5rem;
                background: linear-gradient(135deg, #f8fafc, #f1f5f9);
                border-radius: 12px;
                border-left: 4px solid #5b67d1;
            }

            .courses-count {
                font-size: 0.9rem;
                color: #64748b;
                font-weight: 500;
            }

            @media (max-width: 768px) {
                .content-area {
                    padding: 2rem 1rem;
                }
                
                .page-title {
                    font-size: 2rem;
                }
                
                .selection-card {
                    padding: 2rem 1.5rem;
                }
                
                .form-select,
                .btn-generate {
                    padding: 1rem;
                }
            }
        </style>
    </head>
    <body>
        <?php include __DIR__ . '/../arquivosReuso/header.php'; ?>
        
        <div class="main-wrapper">
            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">Gerar Certificado</h1>
                    <p class="page-subtitle">Selecione o curso para o qual deseja gerar seu certificado</p>
                </div>

                <div class="selection-card">
                    <form method="get">
                        <div class="form-group">
                            <label for="curso_id" class="form-label">Escolha seu curso concluído</label>
                            <select name="curso_id" id="curso_id" class="form-select" required>
                                <option value="">Selecione um curso...</option>
                                <?php foreach ($cursos as $c): ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo htmlspecialchars($c['titulo']); ?> 
                                        <?php echo htmlspecialchars($c['duration'] ?? 'N/A'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-generate">
                            Gerar Meu Certificado
                        </button>
                    </form>

                    <div class="courses-info">
                        <div class="courses-count">
                            📚 Você possui <?php echo count($cursos); ?> curso<?php echo count($cursos) > 1 ? 's' : ''; ?> concluído<?php echo count($cursos) > 1 ? 's' : ''; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include __DIR__ . '/../arquivosReuso/footer.php'; ?>
    </body>
    </html><?php
    exit;
}

// Valida curso selecionado
$curso_id = (int) $_GET['curso_id'];
$curso_arr = array_filter($cursos, fn($c) => $c['id'] === $curso_id);
if (empty($curso_arr)) {
    die("Curso inválido.");
}
$curso = array_shift($curso_arr);

// Prepara dados do certificado
$curso_titulo       = $curso['titulo'];
// extrai só números da duração
$carga_horaria = preg_replace('/\D/', '', $curso['duration'] ?? '0');
if (empty($carga_horaria)) {
    $carga_horaria = '0'; 
}
$data_conclusao     = date('d/m/Y');
$codigo_certificado = 'AUG-' . strtoupper(substr(md5($usuario_id . $curso_id), 0, 8));

//  HTML do certificado
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Certificado - AUGEBIT</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include __DIR__ . '/../arquivosReuso/header.php'; ?>

  <div class="certificate-page" id="certificado">
    <div class="main-content">
      <div class="page-intro">
        <h1 class="congratulations">Parabéns!</h1>
        <p class="intro-text">Seu certificado está pronto para ser visualizado e impresso</p>
      </div>

      <div class="certificate-wrapper" id="certificado">
        <div class="certificate-container">
          <div class="certificate-border"></div>
          <div class="corner-ornaments"></div>
          <div class="corner-ornaments"></div>
          <div class="corner-ornaments"></div>
          <div class="corner-ornaments"></div>

          <div class="institution-header">
            <div class="logo-container">
              <img src="src/logo.png" alt="AUGEBIT Logo">
            </div>
          </div>

          <h1 class="certificate-title">Certificado</h1>

          <div class="certificate-body">
            <p class="declaration-text">
              Certificamos que
            </p>
            
            <div class="student-name"><?php echo htmlspecialchars($nome_aluno); ?></div>
            
            <p class="completion-text">
              concluiu com aproveitamento o curso
            </p>
            
            <div class="course-name"><?php echo htmlspecialchars($curso_titulo); ?></div>
          </div>

          <div class="certificate-details">
            <div class="detail-item">
              <div class="detail-label">Data de Conclusão</div>
              <div class="detail-value"><?php echo htmlspecialchars($data_conclusao); ?></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Carga Horária</div>
              <div class="detail-value"><?php echo htmlspecialchars($carga_horaria); ?> horas</div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Código de Verificação</div>
              <div class="detail-value"><?php echo htmlspecialchars($codigo_certificado); ?></div>
            </div>
          </div>

          <div class="signature-section">
            <div class="signature-line"></div>
            <div class="signature-title">Coordenação Acadêmica</div>
          </div>
        </div>

        <div class="actions-section">
          <button class="btn-action btn-primary" onclick="window.print()">
            Imprimir Certificado
          </button>
          <button class="btn-action btn-secondary" onclick="window.history.back()">
            Voltar
          </button>
        </div>
      </div>
    </div>
  </div>

  <?php include __DIR__ . '/../arquivosReuso/footer.php'; ?>
</body>
</html>