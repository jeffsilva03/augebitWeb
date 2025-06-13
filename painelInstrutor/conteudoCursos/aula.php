<?php
include('conecta.php');
date_default_timezone_set('America/Sao_Paulo');

// Mostrar erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão
$mysqli = new mysqli($host, $login, $password, $bd);
if ($mysqli->connect_error) {
    die("Erro na conexão com o banco de dados: " . $mysqli->connect_error);
}

$mensagem = '';
$tipo_mensagem = '';

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
        if (empty($video_link)) {
            $mensagem = "Por favor, insira um link de vídeo.";
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
              ORDER BY c.titulo";
$resultVideos = $mysqli->query($sqlVideos);
if ($resultVideos && $resultVideos->num_rows > 0) {
    while ($row = $resultVideos->fetch_assoc()) {
        $videos_por_curso[] = $row;
    }

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
        /* Estilos adicionais para o select e lista de vídeos */
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            background-color: white;
            color: #374151;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .form-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .videos-list {
            margin-top: 40px;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .videos-list h2 {
            color: #1f2937;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        .video-item {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: #f9fafb;
            transition: all 0.3s ease;
        }

        .video-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .video-course-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .video-link {
            color: #3b82f6;
            text-decoration: none;
            word-break: break-all;
            font-size: 14px;
        }

        .video-link:hover {
            text-decoration: underline;
        }

        .no-videos {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 40px;
        }

        .video-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
        }

        .btn-edit:hover {
            background: #d97706;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
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
                    <label class="form-label" for="curso-select">Selecionar Curso</label>
                    <select id="curso-select" name="curso_id" class="form-select" required>
                        <option value="">Selecione um curso...</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?php echo $curso['id']; ?>">
                                <?php echo htmlspecialchars($curso['titulo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="video-link">Link do Vídeo</label>
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

                <button type="submit" class="btn-primary">
                    Salvar Vídeo
                </button>
            </form>

            <?php if (!empty($mensagem)): ?>
                <div id="mensagem" class="<?php echo $tipo_mensagem; ?>">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Lista de vídeos existentes -->
        <div class="videos-list">
            <h2>Vídeos Cadastrados</h2>
            
            <?php if (!empty($videos_por_curso)): ?>
                <?php foreach ($videos_por_curso as $video): ?>
                    <div class="video-item">
                        <div class="video-course-title">
                            📚 <?php echo htmlspecialchars($video['curso_titulo']); ?>
                        </div>
                        <div>
                            <strong>Link:</strong> 
                            <a href="<?php echo htmlspecialchars($video['videoAula']); ?>" 
                               target="_blank" 
                               class="video-link">
                                <?php echo htmlspecialchars($video['videoAula']); ?>
                            </a>
                        </div>
                        <div class="video-actions">
                            <button class="btn-small btn-edit" onclick="editVideo(<?php echo $video['id']; ?>, '<?php echo htmlspecialchars($video['videoAula']); ?>', <?php echo $video['curso_id']; ?>)">
                                ✏️ Editar
                            </button>
                            <a href="?delete=<?php echo $video['id']; ?>" 
                               class="btn-small btn-delete" 
                               onclick="return confirm('Tem certeza que deseja remover este vídeo?')">
                                🗑️ Remover
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-videos">
                    Nenhum vídeo cadastrado ainda. Adicione o primeiro vídeo usando o formulário acima.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const videoLinkInput = document.getElementById('video-link');
        const videoPreview = document.getElementById('video-preview');
        const videoContainer = document.getElementById('video-container');

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

        // Função para mostrar preview do vídeo
        function showVideoPreview(url) {
            let embedHtml = '';
            
            const youtubeId = getYouTubeVideoId(url);
            const vimeoId = getVimeoVideoId(url);
            
            if (youtubeId) {
                embedHtml = `<iframe src="https://www.youtube.com/embed/${youtubeId}" allowfullscreen></iframe>`;
            } else if (vimeoId) {
                embedHtml = `<iframe src="https://player.vimeo.com/video/${vimeoId}" allowfullscreen></iframe>`;
            } else {
                embedHtml = `<div style="padding: 40px; text-align: center; color: #6b7280;">
                    <p>Preview não disponível para este link.</p>
                    <p style="font-size: 14px; margin-top: 10px;">O vídeo será salvo normalmente.</p>
                </div>`;
            }
            
            videoContainer.innerHTML = embedHtml;
            videoPreview.classList.add('show');
        }

        // Event listener para o input de link
        videoLinkInput.addEventListener('input', function() {
            const url = this.value.trim();
            
            if (url && (url.includes('youtube.com') || url.includes('youtu.be') || url.includes('vimeo.com'))) {
                showVideoPreview(url);
            } else if (url === '') {
                videoPreview.classList.remove('show');
            } else if (url.length > 10) {
                // Para outros tipos de link, mostrar mensagem genérica
                videoContainer.innerHTML = `<div style="padding: 40px; text-align: center; color: #6b7280;">
                    <p>Link detectado: ${url.substring(0, 50)}${url.length > 50 ? '...' : ''}</p>
                    <p style="font-size: 14px; margin-top: 10px;">Preview não disponível, mas o link será salvo.</p>
                </div>`;
                videoPreview.classList.add('show');
            }
        });

        // Função para editar vídeo
        function editVideo(id, videoUrl, cursoId) {
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
            const form = document.getElementById('video-form');
            form.style.border = '2px solid #3b82f6';
            setTimeout(() => {
                form.style.border = '';
            }, 3000);
        }

        // Ocultar mensagem após 5 segundos
        <?php if (!empty($mensagem)): ?>
        setTimeout(function() {
            const mensagemDiv = document.getElementById('mensagem');
            if (mensagemDiv) {
                mensagemDiv.style.display = 'none';
            }
        }, 5000);
        <?php endif; ?>

        // Validação do formulário
        document.getElementById('video-form').addEventListener('submit', function(e) {
            const cursoSelect = document.getElementById('curso-select');
            const videoLink = document.getElementById('video-link');
            
            if (!cursoSelect.value) {
                e.preventDefault();
                alert('Por favor, selecione um curso.');
                cursoSelect.focus();
                return false;
            }
            
            if (!videoLink.value.trim()) {
                e.preventDefault();
                alert('Por favor, insira o link do vídeo.');
                videoLink.focus();
                return false;
            }
        });
    </script>
</body>
</html>