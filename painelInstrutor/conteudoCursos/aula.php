<?php
include('conecta.php');
date_default_timezone_set('America/Sao_Paulo');

// Mostrar erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$mysqli = new mysqli($host, $login, $password, $bd);
if ($mysqli->connect_error) {
    die("Erro na conexão com o banco de dados: " . $mysqli->connect_error);
}

$mensagem = '';
$tipo_mensagem = '';

// Processar exclusão de vídeo
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $deleteSql = "DELETE FROM aula WHERE id = ?";
    $deleteStmt = $mysqli->prepare($deleteSql);
    if ($deleteStmt) {
        $deleteStmt->bind_param('i', $delete_id);
        if ($deleteStmt->execute()) {
            $mensagem = "Vídeo removido com sucesso!";
            $tipo_mensagem = 'success';
        } else {
            $mensagem = "Erro ao remover o vídeo: " . $deleteStmt->error;
            $tipo_mensagem = 'error';
        }
        $deleteStmt->close();
    }
    
   
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=" . urlencode($mensagem) . "&type=" . $tipo_mensagem);
    exit();
}


if (isset($_GET['msg']) && isset($_GET['type'])) {
    $mensagem = $_GET['msg'];
    $tipo_mensagem = $_GET['type'];
}

// Buscar cursos disponíveis
$cursos = [];
$sqlCursos = "SELECT id, titulo FROM cursos ORDER BY titulo";
$resultCursos = $mysqli->query($sqlCursos);
if ($resultCursos && $resultCursos->num_rows > 0) {
    while ($row = $resultCursos->fetch_assoc()) {
        $cursos[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $video_link = filter_var(trim($_POST['videoAula'] ?? ''), FILTER_SANITIZE_URL);
    $curso_id = (int)($_POST['curso_id'] ?? 0);
    
    if (!empty($video_link) && $curso_id > 0) {
        // Verificar se o curso existe
        $checkCursoSql = "SELECT id FROM cursos WHERE id = ? LIMIT 1";
        $checkCursoStmt = $mysqli->prepare($checkCursoSql);
        $checkCursoStmt->bind_param('i', $curso_id);
        $checkCursoStmt->execute();
        $cursosResult = $checkCursoStmt->get_result();
        
        if ($cursosResult && $cursosResult->num_rows > 0) {
            // Verificar se já existe um vídeo para este curso
            $checkSql = "SELECT id FROM aula WHERE curso_id = ? LIMIT 1";
            $checkStmt = $mysqli->prepare($checkSql);
            $checkStmt->bind_param('i', $curso_id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result && $result->num_rows > 0) {
                // Atualizar vídeo existente para este curso
                $sql = "UPDATE aula SET videoAula = ? WHERE curso_id = ?";
                $stmt = $mysqli->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('si', $video_link, $curso_id);
                    if ($stmt->execute()) {
                        $mensagem = "Vídeo atualizado com sucesso para o curso selecionado!";
                        $tipo_mensagem = 'success';
                    } else {
                        $mensagem = "Erro ao atualizar o vídeo: " . $stmt->error;
                        $tipo_mensagem = 'error';
                    }
                    $stmt->close();
                }
            } else {
                // Inserir novo vídeo para este curso
                $sql = "INSERT INTO aula (videoAula, curso_id) VALUES (?, ?)";
                $stmt = $mysqli->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('si', $video_link, $curso_id);
                    if ($stmt->execute()) {
                        $mensagem = "Vídeo adicionado com sucesso ao curso selecionado!";
                        $tipo_mensagem = 'success';
                    } else {
                        $mensagem = "Erro ao adicionar o vídeo: " . $stmt->error;
                        $tipo_mensagem = 'error';
                    }
                    $stmt->close();
                } else {
                    $mensagem = "Erro ao preparar a query: " . $mysqli->error;
                    $tipo_mensagem = 'error';
                }
            }
            $checkStmt->close();
        } else {
            $mensagem = "Curso não encontrado. Por favor, selecione um curso válido.";
            $tipo_mensagem = 'error';
        }
        $checkCursoStmt->close();
    } else {
        if (empty($video_link)) {
            $mensagem = "Por favor, insira um link de vídeo válido.";
        } else {
            $mensagem = "Por favor, selecione um curso.";
        }
        $tipo_mensagem = 'error';
    }
}

// Buscar vídeos existentes por curso
$videos_por_curso = [];
$sqlVideos = "SELECT a.*, c.titulo as curso_titulo FROM aula a 
              INNER JOIN cursos c ON a.curso_id = c.id 
              WHERE a.videoAula IS NOT NULL AND a.videoAula != '' AND a.videoAula != '0'
              ORDER BY c.titulo";
$resultVideos = $mysqli->query($sqlVideos);
if ($resultVideos && $resultVideos->num_rows > 0) {
    while ($row = $resultVideos->fetch_assoc()) {
        $videos_por_curso[] = $row;
    }
}


if (file_exists('headerInstrutor.php')) {
    include_once('headerInstrutor.php');
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Vídeo - Augebit</title>
    <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="text/javascript" src="DataTables/datatables.min.js"></script>
    <link rel="stylesheet" href="aula.css">
    <style>
        * {
            box-sizing: border-box;
        }

       

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }
        
        .page-title h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .page-title p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .video-upload-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            backdrop-filter: blur(10px);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 12px;
            color: #2d3748;
            font-size: 1rem;
        }
        
        .form-control, .form-select {
            width: 100%;
            padding: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f7fafc;
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 32px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }
        
        .success {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            margin: 20px 0;
            border-left: none;
            box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
        }
        
        .error {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            margin: 20px 0;
            border-left: none;
            box-shadow: 0 4px 15px rgba(245, 101, 101, 0.3);
        }
        
        .video-preview {
            display: none;
            margin: 25px 0;
            background: #f7fafc;
            border-radius: 16px;
            padding: 25px;
            border: 2px dashed #cbd5e0;
        }
        
        .video-preview.show {
            display: block;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .video-preview iframe {
            width: 100%;
            height: 315px;
            border: none;
            border-radius: 12px;
        }
        
        .videos-list {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .video-item {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .video-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .video-course-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .video-link {
            color: #667eea;
            text-decoration: none;
            word-break: break-all;
            font-weight: 500;
        }
        
        .video-link:hover {
            text-decoration: underline;
        }
        
        .video-actions {
            margin-top: 20px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .btn-small {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
            color: white;
        }
        
        .btn-edit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(237, 137, 54, 0.4);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
        }
        
        .btn-delete:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 101, 101, 0.4);
        }
        
        .btn-view {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }
        
        .btn-view:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
        }
        
        .no-videos {
            text-align: center;
            color: #718096;
            padding: 60px;
            font-style: italic;
            font-size: 1.1rem;
        }
        
        .help-text {
            font-size: 14px;
            color: #718096;
            margin-top: 8px;
            font-style: italic;
        }
        
        .upload-icon {
            font-size: 64px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .section-title {
            text-align: center;
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .section-description {
            text-align: center;
            color: #718096;
            margin-bottom: 40px;
            line-height: 1.6;
            font-size: 1rem;
        }

        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .modal-overlay.show {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: scale(0.8) translateY(-50px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .modal-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .modal-body {
            text-align: center;
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .modal-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 100px;
        }

        .modal-btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .modal-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .modal-btn-danger {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
        }

        .modal-btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(245, 101, 101, 0.4);
        }

        .modal-btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .modal-btn-secondary:hover {
            background: #cbd5e0;
            transform: translateY(-1px);
        }

        .modal-info {
            background: #e6fffa;
            border: 1px solid #b2f5ea;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            font-size: 14px;
            color: #234e52;
        }

        .modal-info strong {
            color: #1a202c;
        }

      
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            max-width: 400px;
            transform: translateX(400px);
            transition: all 0.3s ease;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            border-left: 4px solid #48bb78;
        }

        .notification.error {
            border-left: 4px solid #f56565;
        }

        .notification-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .notification-icon {
            font-size: 24px;
        }

        .notification-title {
            font-weight: 600;
            color: #2d3748;
        }

        .notification-body {
            color: #4a5568;
            font-size: 14px;
        }

        .notification-close {
            position: absolute;
            top: 10px;
            right: 15px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #a0aec0;
            padding: 0;
        }

        .notification-close:hover {
            color: #4a5568;
        }

        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .video-upload-section,
            .videos-list {
                padding: 20px;
            }
            
            .page-title h1 {
                font-size: 2rem;
            }
            
            .modal {
                margin: 20px;
                padding: 20px;
            }
            
            .modal-actions {
                flex-direction: column;
            }
            
            .video-actions {
                flex-direction: column;
            }
            
            .btn-small {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-title">
            <h1>Upload de Vídeo Aula</h1>
            <p>Adicione o link do seu vídeo para compartilhar com os alunos de um curso específico</p>
        </div>

        <div class="video-upload-section">
            <div class="upload-icon">
                🎥
            </div>
            
            <h2 class="section-title">Adicionar Vídeo</h2>
            <p class="section-description">
                Selecione o curso e cole o link do seu vídeo do YouTube, Vimeo ou qualquer plataforma de vídeo. 
                O vídeo será incorporado automaticamente para visualização.
            </p>

            <form method="POST" id="video-form">
                <div class="form-group">
                    <label class="form-label" for="curso-select">Selecionar Curso *</label>
                    <select id="curso-select" name="curso_id" class="form-select" required>
                        <option value="">Selecione um curso...</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?php echo htmlspecialchars($curso['id']); ?>">
                                <?php echo htmlspecialchars($curso['titulo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="help-text">Escolha o curso ao qual este vídeo pertence</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="video-link">Link do Vídeo *</label>
                    <input 
                        type="url" 
                        id="video-link" 
                        name="videoAula" 
                        class="form-control" 
                        placeholder="https://www.youtube.com/watch?v=..." 
                        required
                    >
                    <div class="help-text">
                        Suportamos links do YouTube, Vimeo, Google Drive e outras plataformas
                    </div>
                </div>

                <div class="video-preview" id="video-preview">
                    <h3 style="margin-bottom: 15px; color: #374151;">Pré-visualização do Vídeo</h3>
                    <div id="video-container"></div>
                </div>

                <button type="submit" class="btn-primary" id="submit-btn">
                    💾 Salvar Vídeo
                </button>
            </form>

            <?php if (!empty($mensagem)): ?>
                <div id="mensagem" class="<?php echo htmlspecialchars($tipo_mensagem); ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
        </div>

      
        <div class="videos-list">
            <h2>📚 Vídeos Cadastrados (<?php echo count($videos_por_curso); ?>)</h2>
            
            <?php if (!empty($videos_por_curso)): ?>
                <?php foreach ($videos_por_curso as $video): ?>
                    <div class="video-item">
                        <div class="video-course-title">
                            📚 <?php echo htmlspecialchars($video['curso_titulo']); ?>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>🔗 Link:</strong> 
                            <a href="<?php echo htmlspecialchars($video['videoAula']); ?>" 
                               target="_blank" 
                               class="video-link"
                               title="Abrir vídeo em nova aba">
                                <?php 
                                $link = $video['videoAula'];
                                echo htmlspecialchars(strlen($link) > 60 ? substr($link, 0, 60) . '...' : $link); 
                                ?>
                            </a>
                        </div>
                        <div style="font-size: 14px; color: #6b7280; margin-bottom: 15px;">
                            <strong>🆔 ID do Curso:</strong> <?php echo htmlspecialchars($video['curso_id']); ?> | 
                            <strong>🆔 ID do Vídeo:</strong> <?php echo htmlspecialchars($video['id']); ?>
                        </div>
                        <div class="video-actions">
                            <button class="btn-small btn-edit" 
                                    onclick="showEditModal(<?php echo htmlspecialchars($video['id']); ?>, '<?php echo htmlspecialchars(addslashes($video['videoAula'])); ?>', <?php echo htmlspecialchars($video['curso_id']); ?>, '<?php echo htmlspecialchars(addslashes($video['curso_titulo'])); ?>')"
                                    title="Editar este vídeo">
                                ✏️ Editar
                            </button>
                            <button class="btn-small btn-delete" 
                                    onclick="showDeleteModal(<?php echo htmlspecialchars($video['id']); ?>, '<?php echo htmlspecialchars(addslashes($video['curso_titulo'])); ?>')"
                                    title="Remover este vídeo">
                                🗑️ Remover
                            </button>
                            <a href="<?php echo htmlspecialchars($video['videoAula']); ?>" 
                               target="_blank" 
                               class="btn-small btn-view"
                               title="Visualizar vídeo em nova aba">
                                👁️ Visualizar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-videos">
                    📹 Nenhum vídeo cadastrado ainda.<br>
                    Adicione o primeiro vídeo usando o formulário acima.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal-overlay" id="modal-overlay">
        <div class="modal" id="modal">
            <div class="modal-header">
                <div class="modal-icon" id="modal-icon">💾</div>
                <div class="modal-title" id="modal-title">Confirmar Salvamento</div>
            </div>
            <div class="modal-body" id="modal-body">
                <div class="modal-info" id="modal-info"></div>
            </div>
            <div class="modal-actions" id="modal-actions">
                <button class="modal-btn modal-btn-secondary" onclick="closeModal()">Cancelar</button>
                <button class="modal-btn modal-btn-primary" id="modal-confirm-btn">Confirmar</button>
            </div>
        </div>
    </div>

    
    <div class="notification" id="notification">
        <button class="notification-close" onclick="closeNotification()">&times;</button>
        <div class="notification-header">
            <div class="notification-icon" id="notification-icon">✅</div>
            <div class="notification-title" id="notification-title">Sucesso!</div>
        </div>
        <div class="notification-body" id="notification-body">Operação realizada com sucesso.</div>
    </div>

    <script>
        const videoLinkInput = document.getElementById('video-link');
        const videoPreview = document.getElementById('video-preview');
        const videoContainer = document.getElementById('video-container');
        const modalOverlay = document.getElementById('modal-overlay');
        const modal = document.getElementById('modal');

        // Função para extrair ID do YouTube
        function getYouTubeVideoId(url) {
            const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
            const match = url.match(regExp);
            return (match && match[7].length == 11) ? match[7] : false;
        }

        // Função para extrair ID do Vimeo
        function getVimeoVideoId(url) {
            const regExp = /vimeo\.com\/(\d+)/;
            const match = url.match(regExp);
            return match ? match[1] : false;
        }

        
        function showVideoPreview(url) {
            let embedHtml = '';
            
            const youtubeId = getYouTubeVideoId(url);
            const vimeoId = getVimeoVideoId(url);
            
            if (youtubeId) {
                embedHtml = `<iframe src="https://www.youtube.com/embed/${youtubeId}" 
                            allowfullscreen 
                            style="width: 100%; height: 315px; border: none; border-radius: 8px;"></iframe>`;
            } else if (vimeoId) {
                embedHtml = `<iframe src="https://player.vimeo.com/video/${vimeoId}" 
                            allowfullscreen 
                            style="width: 100%; height: 315px; border: none; border-radius: 8px;"></iframe>`;
            } else {
                embedHtml = `<div style="padding: 40px; text-align: center; color: #6b7280; background: #f3f4f6; border-radius: 8px;">
                    <p style="margin: 0; font-size: 16px;">🎬 Preview não disponível para este link</p>
                    <p style="font-size: 14px; margin: 10px 0 0 0; color: #9ca3af;">
                        Link: ${url.length > 50 ? url.substring(0, 50) + '...' : url}
                    </p>
                    <p style="font-size: 14px; margin: 5px 0 0 0;">O vídeo será salvo normalmente.</p>
                </div>`;
            }
            
            videoContainer.innerHTML = embedHtml;
            videoPreview.classList.add('show');
        }

       
        videoLinkInput.addEventListener('input', function() {
            const url = this.value.trim();
            
            if (url && (url.includes('youtube.com') || url.includes('youtu.be') || url.includes('vimeo.com'))) {
                showVideoPreview(url);
            } else if (url === '') {
                videoPreview.classList.remove('show');
            } else if (url.length > 10) {
                videoContainer.innerHTML = `<div style="padding: 40px; text-align: center; color: #6b7280; background: #f3f4f6; border-radius: 8px;">
                    <p style="margin: 0; font-size: 16px;">🔗 Link detectado</p>
                    <p style="font-size: 14px; margin: 10px 0 0 0; color: #9ca3af; word-break: break-all;">
                        ${url.length > 60 ? url.substring(0, 60) + '...' : url}
                    </p>
                    <p style="font-size: 14px; margin: 5px 0 0 0;">Preview não disponível, mas o link será salvo.</p>
                </div>`;
                videoPreview.classList.add('show');
            }
        });

        // Funções do Modal
        function showModal(icon, title, body, actions) {
            document.getElementById('modal-icon').textContent = icon;
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-body').innerHTML = body;
            document.getElementById('modal-actions').innerHTML = actions;
            modalOverlay.classList.add('show');
        }

        function closeModal() {
            modalOverlay.classList.remove('show');
        }

        // Modal de confirmação de salvamento
        function showSaveConfirmation(cursoTexto, videoLink) {
            const body = `
                <p>Tem certeza que deseja salvar este vídeo?</p>
                <div class="modal-info">
                    <strong>Curso:</strong> ${cursoTexto}<br>
                    <strong>Link:</strong> ${videoLink.length > 50 ? videoLink.substring(0, 50) + '...' : videoLink}
                </div>
            `;
            
            const actions = `
                <button class="modal-btn modal-btn-secondary" onclick="closeModal()">Cancelar</button>
                <button class="modal-btn modal-btn-primary" onclick="confirmSave()">
                    💾 Salvar Vídeo
                </button>
            `;
            
            showModal('💾', 'Confirmar Salvamento', body, actions);
        }

        // Modal de confirmação de exclusão
        function showDeleteModal(id, cursoTitulo) {
            const body = `
                <p>Tem certeza que deseja remover este vídeo?</p>
                <div class="modal-info">
                    <strong>Curso:</strong> ${cursoTitulo}<br>
                    <strong>⚠️ Atenção:</strong> Esta ação não pode ser desfeita.
                </div>
            `;
            
            const actions = `
                <button class="modal-btn modal-btn-secondary" onclick="closeModal()">Cancelar</button>
                <button class="modal-btn modal-btn-danger" onclick="confirmDelete(${id})">
                    🗑️ Remover Vídeo
                </button>
            `;
            
            showModal('🗑️', 'Confirmar Exclusão', body, actions);
        }

        // Modal de edição
        function showEditModal(id, videoUrl, cursoId, cursoTitulo) {
            const body = `
                <p>Deseja editar este vídeo?</p>
                <div class="modal-info">
                    <strong>Curso:</strong> ${cursoTitulo}<br>
                    <strong>Link atual:</strong> ${videoUrl.length > 50 ? videoUrl.substring(0, 50) + '...' : videoUrl}
                </div>
                <p>O formulário será preenchido com os dados atuais para edição.</p>
            `;
            
            const actions = `
                <button class="modal-btn modal-btn-secondary" onclick="closeModal()">Cancelar</button>
                <button class="modal-btn modal-btn-primary" onclick="confirmEdit(${id}, '${videoUrl.replace(/'/g, "\\'")}', ${cursoId})">
                    ✏️ Editar Vídeo
                </button>
            `;
            
            showModal('✏️', 'Editar Vídeo', body, actions);
        }

        // Função para mostrar notificação
        function showNotification(type, title, message) {
            const notification = document.getElementById('notification');
            const icon = document.getElementById('notification-icon');
            const titleEl = document.getElementById('notification-title');
            const body = document.getElementById('notification-body');
            
            // Definir ícone e classe baseado no tipo
            if (type === 'success') {
                icon.textContent = '✅';
                notification.className = 'notification success';
            } else if (type === 'error') {
                icon.textContent = '❌';
                notification.className = 'notification error';
            } else if (type === 'info') {
                icon.textContent = 'ℹ️';
                notification.className = 'notification';
            }
            
            titleEl.textContent = title;
            body.textContent = message;
            
            // Mostrar notificação
            notification.classList.add('show');
            
            // Auto esconder após 5 segundos
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }

        function closeNotification() {
            document.getElementById('notification').classList.remove('show');
        }

       
        function confirmSave() {
            closeModal();
            
            
            const submitBtn = document.getElementById('submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading"></span> Salvando...';
            submitBtn.disabled = true;
            
            
            document.getElementById('video-form').submit();
        }

        function confirmDelete(id) {
            closeModal();
            showNotification('info', 'Processando...', 'Removendo vídeo, aguarde...');
            
        
            setTimeout(() => {
                window.location.href = `?delete=${id}`;
            }, 1000);
        }

        function confirmEdit(id, videoUrl, cursoId) {
            closeModal();
            
            // Preencher o formulário com os dados existentes
            document.getElementById('video-link').value = videoUrl;
            document.getElementById('curso-select').value = cursoId;
            
            // Mostrar preview
            if (videoUrl) {
                showVideoPreview(videoUrl);
            }
            
            // Scroll para o formulário
            document.getElementById('video-form').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
            
            // Destacar o formulário
            const form = document.querySelector('.video-upload-section');
            const originalBg = form.style.background;
            form.style.background = 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)';
            form.style.border = '2px solid #3b82f6';
            form.style.transition = 'all 0.3s ease';
            
            setTimeout(() => {
                form.style.background = originalBg;
                form.style.border = '';
            }, 3000);
            
            // Mostrar notificação
            showNotification('info', 'Modo de Edição', 'Formulário preenchido com os dados atuais. Faça as alterações e salve.');
        }

        // Validação e submissão do formulário
        document.getElementById('video-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const cursoSelect = document.getElementById('curso-select');
            const videoLink = document.getElementById('video-link');
            
            // Validações
            if (!cursoSelect.value) {
                showNotification('error', 'Erro de Validação', 'Por favor, selecione um curso antes de continuar.');
                cursoSelect.focus();
                return false;
            }
            
            if (!videoLink.value.trim()) {
                showNotification('error', 'Erro de Validação', 'Por favor, insira o link do vídeo antes de continuar.');
                videoLink.focus();
                return false;
            }
            
         
            try {
                new URL(videoLink.value.trim());
            } catch (error) {
                showNotification('error', 'Link Inválido', 'Por favor, insira um link válido (deve começar com http:// ou https://).');
                videoLink.focus();
                return false;
            }
            
            // Mostrar modal de confirmação
            const cursoTexto = cursoSelect.options[cursoSelect.selectedIndex].text;
            showSaveConfirmation(cursoTexto, videoLink.value);
        });

        // Fechar modal ao clicar fora
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });

        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalOverlay.classList.contains('show')) {
                closeModal();
            }
        });

       
        <?php if (!empty($mensagem)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const mensagemDiv = document.getElementById('mensagem');
            if (mensagemDiv) {
                <?php if ($tipo_mensagem === 'success'): ?>
                showNotification('success', 'Sucesso!', '<?php echo addslashes($mensagem); ?>');
                <?php else: ?>
                showNotification('error', 'Erro!', '<?php echo addslashes($mensagem); ?>');
                <?php endif; ?>
                
                
                mensagemDiv.style.display = 'none';
            }
        });
        <?php endif; ?>

      
      
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ Página carregada completamente');
            
           
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('debug') === '1') {
                console.log('🔍 Modo debug ativado');
                console.log('📋 Dados completos dos vídeos:', <?php echo json_encode($videos_por_curso); ?>);
            }

       
            <?php if (empty($mensagem)): ?>
            setTimeout(() => {
                showNotification('info', 'Sistema Carregado', 'Sistema de upload de vídeos pronto para uso!');
            }, 1000);
            <?php endif; ?>
        });

       
        document.querySelectorAll('.video-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px) scale(1.02)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

  
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.video-item, .video-upload-section, .videos-list').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>