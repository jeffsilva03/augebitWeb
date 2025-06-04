<?php
// certificado/cert.php

// 1) Inicia sessão (já lida no header, mas garantimos aqui)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Verifica login usando a mesma chave de sessão do header
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../registro/form_login.php');
    exit;
}
$usuario_id = $_SESSION['usuario_id'];

// 3) Conexão PDO
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=semestral_3b;charset=utf8",
        "root", "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// 4) Recupera nome do aluno
$stmt = $pdo->prepare("SELECT nome FROM cadastro WHERE id = ?");
$stmt->execute([$usuario_id]);
$nome_aluno = $stmt->fetchColumn();

// 5) Recupera cursos inscritos
$stmt = $pdo->prepare("
    SELECT c.id, c.titulo, c.duration
FROM inscricoes i  
JOIN cursos c ON i.curso_id = c.id
WHERE i.usuario_id = ?
");
$stmt->execute([$usuario_id]);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 6) Se não há inscrições, avisa e sai
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

// 7) Se não passou curso_id, mostra formulário de seleção
if (!isset($_GET['curso_id'])) {
    ?><!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>Selecionar Curso - AUGEBIT</title>
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

// 8) Valida curso selecionado
$curso_id = (int) $_GET['curso_id'];
$curso_arr = array_filter($cursos, fn($c) => $c['id'] === $curso_id);
if (empty($curso_arr)) {
    die("Curso inválido.");
}
$curso = array_shift($curso_arr);

// 9) Prepara dados do certificado
$curso_titulo       = $curso['titulo'];
// extrai só números da duração (ex: "20h" → "20")
$carga_horaria = preg_replace('/\D/', '', $curso['duration'] ?? '0');
if (empty($carga_horaria)) {
    $carga_horaria = '0'; // Fallback se não houver números na duration
}
$data_conclusao     = date('d/m/Y');
$codigo_certificado = 'AUG-' . strtoupper(substr(md5($usuario_id . $curso_id), 0, 8));

// 10) Exibe HTML do certificado
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Certificado - AUGEBIT</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: #1a202c;
            line-height: 1.6;
        }

        .certificate-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-intro {
            text-align: center;
            margin-bottom: 3rem;
        }

        .congratulations {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 0.5rem;
            font-family: 'Playfair Display', serif;
        }

        .intro-text {
            font-size: 1.2rem;
            color: #64748b;
            font-weight: 500;
        }

        .certificate-wrapper {
            position: relative;
            width: 100%;
            max-width: 1000px;
        }

        .certificate-container {
            background: #ffffff;
            border: 3px solid #1a202c;
            border-radius: 0;
            padding: 4rem 3rem;
            text-align: center;
            position: relative;
            box-shadow: 0 25px 50px rgba(0,0,0,0.1);
        }

        .certificate-border {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            right: 1.5rem;
            bottom: 1.5rem;
            border: 2px solid #5b67d1;
            pointer-events: none;
        }

        .corner-ornaments {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 2px solid #7c3aed;
        }

        .corner-ornaments:nth-child(1) { top: 1rem; left: 1rem; border-right: none; border-bottom: none; }
        .corner-ornaments:nth-child(2) { top: 1rem; right: 1rem; border-left: none; border-bottom: none; }
        .corner-ornaments:nth-child(3) { bottom: 1rem; left: 1rem; border-right: none; border-top: none; }
        .corner-ornaments:nth-child(4) { bottom: 1rem; right: 1rem; border-left: none; border-top: none; }

        .institution-header {
            margin-bottom: 2rem;
        }

        .logo-container {
            margin-bottom: 1.5rem;
        }

        .logo-container img {
            max-width: 200px;
            height: auto;
        }

        .institution-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 0.5rem;
        }

        .institution-tagline {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .certificate-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: #1a202c;
            margin: 2.5rem 0;
            font-family: 'Playfair Display', serif;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .certificate-body {
            margin: 2.5rem 0;
            font-size: 1.3rem;
            line-height: 1.8;
        }

        .declaration-text {
            color: #374151;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        .student-name {
            font-size: 2.2rem;
            font-weight: 700;
            color: #5b67d1;
            margin: 1.5rem 0;
            font-family: 'Playfair Display', serif;
            position: relative;
        }

        .student-name::before,
        .student-name::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 60px;
            height: 2px;
            background: #7c3aed;
        }

        .student-name::before { left: -80px; }
        .student-name::after { right: -80px; }

        .completion-text {
            color: #374151;
            font-weight: 500;
            margin: 1.5rem 0;
        }

        .course-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1a202c;
            margin: 1.5rem 0;
            font-family: 'Playfair Display', serif;
            font-style: italic;
        }

        .certificate-details {
            margin-top: 3rem;
            border-top: 2px solid #e5e7eb;
            padding-top: 3rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            text-align: center;
        }

        .detail-item {
            padding: 1rem;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            font-size: 1.1rem;
            color: #1a202c;
            font-weight: 700;
        }

        .signature-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }

        .signature-line {
            width: 200px;
            height: 1px;
            background: #1a202c;
            margin: 2rem auto 0.5rem;
        }

        .signature-title {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .actions-section {
            margin-top: 3rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .btn-action {
            padding: 1rem 2.5rem;
            border: 2px solid #5b67d1;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: #5b67d1;
            color: white;
        }

        .btn-primary:hover {
            background: #4c51bf;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(91, 103, 209, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: #5b67d1;
        }

        .btn-secondary:hover {
            background: #5b67d1;
            color: white;
            transform: translateY(-2px);
        }

       @media print {
  body * { visibility: hidden; }
  #certificado, #certificado * { visibility: visible; }
  #certificado { position: absolute; top: 0; left: 0; width: 100%; }
}


        @media (max-width: 768px) {
            .main-content {
                padding: 2rem 1rem;
            }
            
            .congratulations {
                font-size: 2rem;
            }
            
            .certificate-container {
                padding: 2.5rem 2rem;
            }
            
            .certificate-title {
                font-size: 2.5rem;
            }
            
            .student-name {
                font-size: 1.8rem;
            }
            
            .student-name::before,
            .student-name::after {
                display: none;
            }
            
            .course-name {
                font-size: 1.5rem;
            }
            
            .certificate-details {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .certificate-body {
                font-size: 1.1rem;
            }
            
            .actions-section {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-action {
                width: 100%;
                max-width: 300px;
            }
        }

        @media (max-width: 480px) {
            .certificate-container {
                padding: 2rem 1.5rem;
            }
            
            .certificate-title {
                font-size: 2rem;
                letter-spacing: 1px;
            }
            
            .student-name {
                font-size: 1.5rem;
            }
            
            .course-name {
                font-size: 1.3rem;
            }
            
            .corner-ornaments {
                width: 30px;
                height: 30px;
            }
        }
   </style>
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