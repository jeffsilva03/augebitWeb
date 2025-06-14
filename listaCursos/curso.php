<?php
// curso.php - Versão corrigida com suporte completo a vídeos do YouTube/Vimeo
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'], $_SESSION['perfil'])) {
    header('Location: ../login/form_login.php');
    exit;
}

// Incluir conexão
require_once '../arquivosReuso/conexao.php';

// Pegar ID do curso da URL
$curso_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($curso_id <= 0) {
    die("Curso não encontrado. ID inválido.");
}

// Função para converter URLs do YouTube/Vimeo para embed
function convertToEmbedUrl($url) {
    if (empty($url)) return '';
    
    // Limpar a URL
    $url = trim($url);
    
    // YouTube
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0&modestbranding=1';
    }
    
    // Vimeo
    if (preg_match('/(?:vimeo\.com\/)([0-9]+)/', $url, $matches)) {
        return 'https://player.vimeo.com/video/' . $matches[1];
    }
    
    // Se já é uma URL de embed, retorna como está
    if (strpos($url, 'embed') !== false || strpos($url, 'player.vimeo') !== false) {
        return $url;
    }
    
    // Para outros tipos de vídeo (MP4, etc.)
    return $url;
}

// Função para verificar se é um vídeo embedável
function isEmbeddableVideo($url) {
    if (empty($url)) return false;
    
    return (
        strpos($url, 'youtube.com') !== false ||
        strpos($url, 'youtu.be') !== false ||
        strpos($url, 'vimeo.com') !== false ||
        strpos($url, 'embed') !== false ||
        strpos($url, 'player.vimeo') !== false
    );
}

// Função para verificar se é um arquivo de vídeo direto
function isDirectVideo($url) {
    if (empty($url)) return false;
    
    $video_extensions = ['.mp4', '.webm', '.ogg', '.mov', '.avi'];
    $url_lower = strtolower($url);
    
    foreach ($video_extensions as $ext) {
        if (strpos($url_lower, $ext) !== false) {
            return true;
        }
    }
    
    return false;
}

// Processar envio de avaliação
$mensagem_avaliacao = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_avaliacao'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $respostas = $_POST['resposta'] ?? [];
    
    // Buscar avaliação para verificar gabaritos
    $sqlAvaliacao = "SELECT * FROM avaliacao WHERE curso_id = ?";
    $stmtAvaliacao = $conn->prepare($sqlAvaliacao);
    $stmtAvaliacao->bind_param("i", $curso_id);
    $stmtAvaliacao->execute();
    $resultAvaliacao = $stmtAvaliacao->get_result();
    
    if ($resultAvaliacao->num_rows > 0) {
        $avaliacao_data = $resultAvaliacao->fetch_assoc();
        
        $total_questoes = 0;
        $acertos = 0;
        
        // Contar questões e verificar acertos
        for ($i = 1; $i <= 10; $i++) {
            if (!empty($avaliacao_data["pergunta$i"])) {
                $total_questoes++;
                $gabarito = $avaliacao_data["resposta_correta$i"] ?? '';
                $resposta_usuario = $respostas[$i] ?? '';
                
                if (strtoupper($gabarito) === strtoupper($resposta_usuario)) {
                    $acertos++;
                }
            }
        }
        
        $nota = $total_questoes > 0 ? round(($acertos / $total_questoes) * 10, 2) : 0;
        $aprovado = $nota >= 6.0 ? 1 : 0;
        
        // Verificar se o usuário já fez esta avaliação
        $sqlVerificar = "SELECT id FROM respostas_avaliacao WHERE usuario_id = ? AND curso_id = ?";
        $stmtVerificar = $conn->prepare($sqlVerificar);
        $stmtVerificar->bind_param("ii", $usuario_id, $curso_id);
        $stmtVerificar->execute();
        $resultVerificar = $stmtVerificar->get_result();
        
        if ($resultVerificar->num_rows > 0) {
            // Atualizar resposta existente
            $sqlUpdate = "UPDATE respostas_avaliacao SET 
                         resposta1=?, resposta2=?, resposta3=?, resposta4=?, resposta5=?,
                         resposta6=?, resposta7=?, resposta8=?, resposta9=?, resposta10=?,
                         nota=?, aprovado=?, data_realizacao=NOW()
                         WHERE usuario_id=? AND curso_id=?";
            
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ssssssssssdiiii", 
                $respostas[1] ?? '', $respostas[2] ?? '', $respostas[3] ?? '', 
                $respostas[4] ?? '', $respostas[5] ?? '', $respostas[6] ?? '', 
                $respostas[7] ?? '', $respostas[8] ?? '', $respostas[9] ?? '', 
                $respostas[10] ?? '', $nota, $aprovado, $usuario_id, $curso_id
            );
            
            if ($stmtUpdate->execute()) {
                $status_aprovacao = $aprovado ? "APROVADO" : "REPROVADO";
                $mensagem_avaliacao = "success|Avaliação concluída! Nota: $nota/10 - $status_aprovacao ($acertos/$total_questoes acertos)";
            } else {
                $mensagem_avaliacao = "error|Erro ao atualizar avaliação.";
            }
        } else {
            // Inserir nova resposta
            $sqlInsert = "INSERT INTO respostas_avaliacao 
                         (usuario_id, curso_id, resposta1, resposta2, resposta3, resposta4, resposta5,
                          resposta6, resposta7, resposta8, resposta9, resposta10, nota, aprovado, data_realizacao)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmtInsert = $conn->prepare($sqlInsert);
            $resp1 = $respostas[1] ?? '';
            $resp2 = $respostas[2] ?? '';
            $resp3 = $respostas[3] ?? '';
            $resp4 = $respostas[4] ?? '';
            $resp5 = $respostas[5] ?? '';
            $resp6 = $respostas[6] ?? '';
            $resp7 = $respostas[7] ?? '';
            $resp8 = $respostas[8] ?? '';
            $resp9 = $respostas[9] ?? '';
            $resp10 = $respostas[10] ?? '';

            $stmtInsert->bind_param("iissssssssssdi", 
                $usuario_id, $curso_id, $resp1, $resp2, 
                $resp3, $resp4, $resp5, 
                $resp6, $resp7, $resp8, 
                $resp9, $resp10, $nota, $aprovado
            );
            
            if ($stmtInsert->execute()) {
                $mensagem_avaliacao = "success|Avaliação enviada! Em breve sua avaliação será corrigida";
            } else {
                $mensagem_avaliacao = "error|Erro ao salvar avaliação.";
            }
        }
    }
}

// Buscar informações do curso
$sqlCurso = "SELECT c.*, cad.nome as instrutor_nome 
            FROM cursos c 
            LEFT JOIN cadastro cad ON c.instrutor_id = cad.id 
            WHERE c.id = ?";

$stmtCurso = $conn->prepare($sqlCurso);
if (!$stmtCurso) {
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmtCurso->bind_param("i", $curso_id);
$stmtCurso->execute();
$resultCurso = $stmtCurso->get_result();

if ($resultCurso->num_rows == 0) {
    die("Curso não encontrado no banco de dados.");
}

$curso = $resultCurso->fetch_assoc();

// CONTROLE DE ACESSO - VERIFICAR SE O USUÁRIO PODE ACESSAR O CURSO
$tem_acesso = false;
$precisa_pagar = false;
$ja_inscrito = false;

// Verificar o perfil do usuário e se tem acesso ao curso
switch($_SESSION['perfil']) {
    case 'adm':
        // Administrador tem acesso total
        $tem_acesso = true;
        break;
        
    case 'instrutor':
        // Instrutor pode acessar todos os cursos (ou implementar lógica específica)
        $tem_acesso = true;
        break;
        
    case 'usuario':
        // Usuário premium tem acesso gratuito
        $tem_acesso = true;
        break;
        
    case 'usuarioGeral':
        // Usuário geral precisa pagar para acessar
        // Verificar se já está inscrito no curso
        $sql_inscricao = "SELECT id, inscrito_em FROM inscricoes WHERE usuario_id = ? AND curso_id = ?";
        $stmt_inscricao = $conn->prepare($sql_inscricao);
        
        if ($stmt_inscricao) {
            $stmt_inscricao->bind_param('ii', $_SESSION['usuario_id'], $curso_id);
            $stmt_inscricao->execute();
            $result_inscricao = $stmt_inscricao->get_result();
            
            if ($result_inscricao->num_rows > 0) {
                // Usuário já está inscrito (já pagou)
                $tem_acesso = true;
                $ja_inscrito = true;
                $inscricao_data = $result_inscricao->fetch_assoc();
            } else {
                // Usuário não está inscrito, precisa pagar
                $tem_acesso = false;
                $precisa_pagar = true;
            }
            $stmt_inscricao->close();
        }
        break;
        
    default:
        // Perfil não reconhecido
        $tem_acesso = false;
        break;
}

// Se o usuário não tem acesso e precisa pagar, redirecionar para página de pagamento
if (!$tem_acesso && $precisa_pagar) {
    // Ajustar o caminho conforme sua estrutura de pastas
    header('Location: ../curso/usuarioGeral/pagamento-curso.php?curso_id=' . $curso_id);
    exit;
}

// Se o usuário não tem acesso e não é caso de pagamento, negar acesso
if (!$tem_acesso) {
    die("Acesso negado. Você não tem permissão para acessar este curso.");
}

// Verificar se há mensagem de sucesso do pagamento
$pagamento_recente = false;
if (isset($_SESSION['pagamento_sucesso']) && $_SESSION['pagamento_sucesso']['curso_id'] == $curso_id) {
    $pagamento_recente = true;
    // Limpar a mensagem após exibir
    unset($_SESSION['pagamento_sucesso']);
}

// Se chegou até aqui, o usuário tem acesso ao curso
// Continuar com o código normal para buscar dados do curso

// Buscar atividades do curso
$sqlAtividades = "SELECT * FROM atividades WHERE curso_id = ?";
$stmtAtividades = $conn->prepare($sqlAtividades);
$atividades = [];
if ($stmtAtividades) {
    $stmtAtividades->bind_param("i", $curso_id);
    $stmtAtividades->execute();
    $resultAtividades = $stmtAtividades->get_result();
    if ($resultAtividades->num_rows > 0) {
        $atividades = $resultAtividades->fetch_assoc();
    }
}

// Buscar aulas do curso - CONSULTA CORRIGIDA
$sqlAulas = "SELECT * FROM aula WHERE curso_id = ? ORDER BY id ASC";
$stmtAulas = $conn->prepare($sqlAulas);
$aulas = [];
if ($stmtAulas) {
    $stmtAulas->bind_param("i", $curso_id);
    $stmtAulas->execute();
    $resultAulas = $stmtAulas->get_result();
    while ($row = $resultAulas->fetch_assoc()) {
        $aulas[] = $row;
    }
}

// Buscar objetivos do curso
$sqlObjetivos = "SELECT * FROM objetivo WHERE curso_id = ?";
$stmtObjetivos = $conn->prepare($sqlObjetivos);
$objetivos = [];
if ($stmtObjetivos) {
    $stmtObjetivos->bind_param("i", $curso_id);
    $stmtObjetivos->execute();
    $resultObjetivos = $stmtObjetivos->get_result();
    if ($resultObjetivos->num_rows > 0) {
        $objetivos = $resultObjetivos->fetch_assoc();
    }
}

// Buscar avaliações do curso
$sqlAvaliacoes = "SELECT * FROM avaliacao WHERE curso_id = ?";
$stmtAvaliacoes = $conn->prepare($sqlAvaliacoes);
$avaliacoes = [];
if ($stmtAvaliacoes) {
    $stmtAvaliacoes->bind_param("i", $curso_id);
    $stmtAvaliacoes->execute();
    $resultAvaliacoes = $stmtAvaliacoes->get_result();
    if ($resultAvaliacoes->num_rows > 0) {
        $avaliacoes = $resultAvaliacoes->fetch_assoc();
    }
}

// Buscar respostas anteriores do usuário (se existirem)
$respostas_anteriores = [];
$nota_anterior = null;
$aprovado_anterior = null;
$data_realizacao = null;

$sqlRespostas = "SELECT * FROM respostas_avaliacao WHERE usuario_id = ? AND curso_id = ?";
$stmtRespostas = $conn->prepare($sqlRespostas);
if ($stmtRespostas) {
    $stmtRespostas->bind_param("ii", $_SESSION['usuario_id'], $curso_id);
    $stmtRespostas->execute();
    $resultRespostas = $stmtRespostas->get_result();
    
    if ($resultRespostas->num_rows > 0) {
        $resposta_data = $resultRespostas->fetch_assoc();
        for ($i = 1; $i <= 10; $i++) {
            $respostas_anteriores[$i] = $resposta_data["resposta$i"];
        }
        $nota_anterior = $resposta_data['nota'];
        $aprovado_anterior = $resposta_data['aprovado'];
        $data_realizacao = $resposta_data['data_realizacao'];
    }
}

require_once '../arquivosReuso/header.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($curso['titulo']); ?> - Augebit</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="curso.css">
    <style>
        /* Estilos específicos para vídeos */
        .video-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .video-container h4 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 1.1em;
            font-weight: 600;
        }

        .video-wrapper {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            overflow: hidden;
            border-radius: 8px;
            background: #000;
        }

        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .video-player {
            width: 100%;
            height: auto;
            max-height: 400px;
            border-radius: 8px;
        }

        .video-info {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 0.9em;
            color: #666;
        }

        .no-videos-message {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 12px;
            color: #666;
        }

        .no-videos-message i {
            font-size: 3em;
            margin-bottom: 15px;
            color: #ddd;
        }

        @media (max-width: 768px) {
            .video-container {
                padding: 15px;
            }
            
            .video-wrapper {
                padding-bottom: 56.25%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Botão Voltar -->
        <a href="listagemCursos.php" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Voltar para Cursos
        </a>

        <!-- Mensagem de Sucesso do Pagamento -->
        <?php if ($pagamento_recente): ?>
        <div class="success-alert">
            <h3><i class="fas fa-check-circle"></i> Pagamento Confirmado!</h3>
            <p>Parabéns! Sua inscrição foi realizada com sucesso. Agora você tem acesso completo ao curso.</p>
        </div>
        <?php endif; ?>

        <!-- Badge de Acesso -->
        <?php
        $badge_class = '';
        $badge_text = '';
        $badge_icon = '';
        
        switch($_SESSION['perfil']) {
            case 'adm':
                $badge_class = 'admin';
                $badge_text = 'Acesso Administrativo';
                $badge_icon = 'fa-crown';
                break;
            case 'instrutor':
                $badge_class = 'instructor';
                $badge_text = 'Acesso Instrutor';
                $badge_icon = 'fa-chalkboard-teacher';
                break;
            case 'usuario':
                $badge_class = 'premium';
                $badge_text = 'Usuário Premium';
                $badge_icon = 'fa-star';
                break;
            case 'usuarioGeral':
                if ($ja_inscrito) {
                    $badge_class = 'purchased';
                    $badge_text = 'Curso Adquirido';
                    $badge_icon = 'fa-check-circle';
                } else {
                    $badge_class = 'premium';
                    $badge_text = 'Acesso Liberado';
                    $badge_icon = 'fa-unlock';
                }
                break;
        }
        ?>
        
        <div class="access-badge <?php echo $badge_class; ?>">
            <i class="fas <?php echo $badge_icon; ?>"></i>
            <?php echo $badge_text; ?>
            <?php if ($ja_inscrito && isset($inscricao_data)): ?>
            <small style="margin-left: 10px; opacity: 0.8;">
                (Desde <?php echo date('d/m/Y', strtotime($inscricao_data['inscrito_em'])); ?>)
            </small>
            <?php endif; ?>
        </div>

        <!-- Cabeçalho do Curso -->
        <div class="course-header">
            <h1 class="course-title"><?php echo htmlspecialchars($curso['titulo']); ?></h1>
            <p><?php echo htmlspecialchars($curso['descricao'] ?? ''); ?></p>
            
            <div class="course-meta">
                <div class="meta-item">
                    <i class="fas fa-user-tie"></i>
                    <span><?php echo htmlspecialchars($curso['instrutor_nome'] ?? 'Instrutor'); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span><?php echo htmlspecialchars($curso['duration'] ?? 'N/A'); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-layer-group"></i>
                    <span><?php echo htmlspecialchars($curso['level'] ?? 'N/A'); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-star"></i>
                    <span><?php echo htmlspecialchars($curso['rating'] ?? 'N/A'); ?></span>
                </div>
            </div>
        </div>

        <!-- Objetivos do Curso -->
        <?php if (!empty($objetivos)): ?>
        <div class="content-section">
            <h2 class="section-title"><i class="fas fa-bullseye"></i> Objetivos do Curso</h2>
            <div class="objectives-grid">
                <?php if (!empty($objetivos['objetivoCurso'])): ?>
                <div class="objective-card">
                    <h3><i class="fas fa-target"></i> Objetivos</h3>
                    <p><?php echo nl2br(htmlspecialchars($objetivos['objetivoCurso'])); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($objetivos['conteudoCurso'])): ?>
                <div class="objective-card">
                    <h3><i class="fas fa-list-ul"></i> Conteúdo</h3>
                    <p><?php echo nl2br(htmlspecialchars($objetivos['conteudoCurso'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Aulas - SEÇÃO CORRIGIDA -->
        <?php if (!empty($aulas)): ?>
        <div class="content-section">
            <h2 class="section-title"><i class="fas fa-play-circle"></i> Aulas do Curso</h2>
            <?php 
            $aulas_com_video = 0;
            foreach ($aulas as $index => $aula): 
                // Verificar se existe vídeo na aula
                $video_url = '';
                if (!empty($aula['videoAula']) && $aula['videoAula'] !== '0') {
                    $video_url = $aula['videoAula'];
                    $aulas_com_video++;
                }
                
                if (!empty($video_url)):
            ?>
                <div class="video-container">
                    <h4>
                        <i class="fas fa-play-circle"></i> 
                        Aula <?php echo $index + 1; ?>
                        <?php if (!empty($aula['titulo'])): ?>
                            - <?php echo htmlspecialchars($aula['titulo']); ?>
                        <?php endif; ?>
                    </h4>
                    
                    <?php if (isEmbeddableVideo($video_url)): ?>
                        <div class="video-wrapper">
                            <iframe 
                                src="<?php echo convertToEmbedUrl($video_url); ?>" 
                                title="Aula <?php echo $index + 1; ?>"
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    <?php elseif (isDirectVideo($video_url)): ?>
                        <video class="video-player" controls>
                            <source src="<?php echo htmlspecialchars($video_url); ?>" type="video/mp4">
                            Seu navegador não suporta o elemento de vídeo.
                        </video>
                    <?php else: ?>
                        <div class="video-info">
                            <p><strong>Link do vídeo:</strong> <a href="<?php echo htmlspecialchars($video_url); ?>" target="_blank"><?php echo htmlspecialchars($video_url); ?></a></p>
                            <p><em>Clique no link acima para assistir ao vídeo</em></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($aula['descricao'])): ?>
                    <div class="video-info">
                        <strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($aula['descricao'])); ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php 
                endif;
            endforeach; 
            
            // Se não há aulas com vídeo, mostrar mensagem
            if ($aulas_com_video === 0):
            ?>
            <div class="no-videos-message">
                <i class="fas fa-video-slash"></i>
                <h3>Nenhum vídeo disponível</h3>
                <p>As aulas para este curso ainda não foram adicionadas ou não possuem vídeos.</p>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="content-section">
            <h2 class="section-title"><i class="fas fa-play-circle"></i> Aulas do Curso</h2>
            <div class="no-videos-message">
                <i class="fas fa-video-slash"></i>
                <h3>Nenhuma aula disponível</h3>
                <p>As aulas para este curso ainda não foram criadas.</p>
            </div>
        </div>
        <?php endif; ?>

        

        <!-- Avaliações - SEÇÃO INTERATIVA -->
        <?php if (!empty($avaliacoes)): ?>
        <div class="content-section">
            <h2 class="section-title"><i class="fas fa-clipboard-check"></i> Avaliação do Curso</h2>
            
            <!-- Mensagem de resultado -->
            <?php if (!empty($mensagem_avaliacao)): ?>
                <?php 
                $msg_parts = explode('|', $mensagem_avaliacao);
                $msg_type = $msg_parts[0];
                $msg_text = $msg_parts[1];
                ?>
                <div class="alert alert-<?php echo $msg_type; ?>">
                    <i class="fas <?php echo $msg_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                    <?php echo htmlspecialchars($msg_text); ?>
                </div>
            <?php endif; ?>

           

            <form method="POST" class="evaluation-form" id="evaluation-form">
                <div class="progress-indicator">
                    <strong>Progresso da Avaliação</strong>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill"></div>
                    </div>
                    <small id="progress-text">0 questões respondidas</small>
                </div>

                <?php 
                $questoes_count = 0;
                for ($i = 1; $i <= 10; $i++) {
                    if (!empty($avaliacoes["pergunta$i"])) {
                        $questoes_count++;
                    }
                }
                ?>

                <input type="hidden" name="total_questoes" value="<?php echo $questoes_count; ?>">

                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <?php if (!empty($avaliacoes["pergunta$i"])): ?>
                    <div class="question-card" data-question="<?php echo $i; ?>">
                        <div class="question-text">
                            <strong>Questão <?php echo $i; ?>:</strong> 
                            <?php echo htmlspecialchars($avaliacoes["pergunta$i"]); ?>
                        </div>
                        <div class="alternatives">
                            <?php 
                            $alternativas = ['A', 'B', 'C', 'D'];
                            foreach ($alternativas as $alt): 
                                if (!empty($avaliacoes["alternativa{$alt}$i"])):
                            ?>
                            <label class="alternative" for="q<?php echo $i; ?>_<?php echo $alt; ?>">
                                <input type="radio" 
                                       id="q<?php echo $i; ?>_<?php echo $alt; ?>" 
                                       name="resposta[<?php echo $i; ?>]" 
                                       value="<?php echo $alt; ?>"
                                       <?php echo (isset($respostas_anteriores[$i]) && $respostas_anteriores[$i] === $alt) ? 'checked' : ''; ?>
                                       onchange="updateProgress()">
                                <strong><?php echo $alt; ?>)</strong> <?php echo htmlspecialchars($avaliacoes["alternativa{$alt}$i"]); ?>
                            </label>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endfor; ?>

                <div class="submit-section">
                    <p><strong>Instruções:</strong></p>
                    <ul style="text-align: left; margin: 10px 0;">
                        <li>Responda todas as questões antes de enviar</li>
                        <li>Você precisa de pelo menos 6.0 pontos para ser aprovado</li>
                        <li>Você pode refazer a avaliação quantas vezes quiser</li>
                    </ul>
                    
                    <button type="submit" name="enviar_avaliacao" class="submit-btn" id="submit-btn" disabled>
                        <i class="fas fa-paper-plane"></i> Enviar Avaliação
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>


    <!-- Seção de Resultados da Avaliação -->
<?php if ($nota_anterior !== null): ?>
<div class="content-section evaluation-result">
    <h2 class="section-title">
        <i class="fas <?php echo $aprovado_anterior ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i> 
        Resultado da Avaliação
    </h2>
    
    <div class="result-card <?php echo $aprovado_anterior ? 'approved' : 'failed'; ?>">
        <div class="result-header">
            <div class="grade">
                <span class="grade-number"><?php echo $nota_anterior; ?></span>
                <span class="grade-total">/10</span>
            </div>
            <div class="status">
                <h3><?php echo $aprovado_anterior ? 'APROVADO' : 'REPROVADO'; ?></h3>
                <p>Realizada em: <?php echo date('d/m/Y H:i', strtotime($data_realizacao)); ?></p>
            </div>
        </div>
        
        <!-- Gabarito Detalhado -->
        <div class="detailed-answers">
            <h4>Gabarito Detalhado:</h4>
            <?php 
            $acertos_detalhados = 0;
            for ($i = 1; $i <= 10; $i++): 
                if (!empty($avaliacoes["pergunta$i"])): 
                    $gabarito_correto = $avaliacoes["resposta_correta$i"] ?? $avaliacoes["resposta_correta"] ?? '';
                    $resposta_usuario = $respostas_anteriores[$i] ?? '';
                    $acertou = strtoupper($gabarito_correto) === strtoupper($resposta_usuario);
                    if ($acertou) $acertos_detalhados++;
            ?>
            <div class="answer-review <?php echo $acertou ? 'correct' : 'incorrect'; ?>">
                <span class="question-num">Q<?php echo $i; ?>:</span>
                <span class="user-answer">Sua resposta: <?php echo $resposta_usuario ?: 'Não respondida'; ?></span>
                <span class="correct-answer">Correta: <?php echo $gabarito_correto; ?></span>
                <i class="fas <?php echo $acertou ? 'fa-check' : 'fa-times'; ?>"></i>
            </div>
            <?php 
                endif;
            endfor; 
            ?>
            <div class="summary">
                <strong>Total: <?php echo $acertos_detalhados; ?>/<?php echo $questoes_count; ?> acertos</strong>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


    <script>
        // JavaScript para funcionalidade da avaliação
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar progresso
            updateProgress();
            
            // Adicionar event listeners para todas as alternativas
            const alternatives = document.querySelectorAll('.alternative');
            alternatives.forEach(alt => {
                alt.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        updateProgress();
                        
                        // Remover seleção anterior da mesma questão
                        const questionCard = this.closest('.question-card');
                        const allAlts = questionCard.querySelectorAll('.alternative');
                        allAlts.forEach(a => a.classList.remove('selected'));
                        
                        // Adicionar classe selected
                        this.classList.add('selected');
                    }
                });
            });
            
            // Marcar alternativas já selecionadas
            const selectedRadios = document.querySelectorAll('input[type="radio"]:checked');
            selectedRadios.forEach(radio => {
                radio.closest('.alternative').classList.add('selected');
            });
        });

        function updateProgress() {
            const totalQuestions = <?php echo $questoes_count; ?>;
            const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
            const progressPercent = (answeredQuestions / totalQuestions) * 100;
            
            // Atualizar barra de progresso
            const progressFill = document.getElementById('progress-fill');
            const progressText = document.getElementById('progress-text');
            const submitBtn = document.getElementById('submit-btn');
            
            if (progressFill) {
                progressFill.style.width = progressPercent + '%';
            }
            
            if (progressText) {
                progressText.textContent = `${answeredQuestions} de ${totalQuestions} questões respondidas`;
            }
            
            // Habilitar/desabilitar botão de envio
            if (submitBtn) {
                if (answeredQuestions === totalQuestions) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Avaliação';
                } else {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<i class="fas fa-hourglass-half"></i> Responda todas as questões (${answeredQuestions}/${totalQuestions})`;
                }
            }
        }

        // Confirmação antes de enviar
        document.getElementById('evaluation-form').addEventListener('submit', function(e) {
            const totalQuestions = <?php echo $questoes_count; ?>;
            const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
            
            if (answeredQuestions < totalQuestions) {
                e.preventDefault();
                alert('Por favor, responda todas as questões antes de enviar a avaliação.');
                return false;
            }
            
            if (!confirm('Tem certeza que deseja enviar sua avaliação? Você pode refazer quantas vezes quiser.')) {
                e.preventDefault();
                return false;
            }
        });

        // Smooth scroll para seções
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Auto-save das respostas no localStorage (opcional - para não perder progresso)
        function saveProgress() {
            const formData = new FormData(document.getElementById('evaluation-form'));
            const responses = {};
            
            for (let [key, value] of formData.entries()) {
                if (key.startsWith('resposta[')) {
                    responses[key] = value;
                }
            }
            
            localStorage.setItem('evaluation_progress_<?php echo $curso_id; ?>', JSON.stringify(responses));
        }

        // Carregar progresso salvo (opcional)
        function loadProgress() {
            const saved = localStorage.getItem('evaluation_progress_<?php echo $curso_id; ?>');
            if (saved) {
                try {
                    const responses = JSON.parse(saved);
                    for (let [key, value] of Object.entries(responses)) {
                        const input = document.querySelector(`input[name="${key}"][value="${value}"]`);
                        if (input) {
                            input.checked = true;
                            input.closest('.alternative').classList.add('selected');
                        }
                    }
                    updateProgress();
                } catch (e) {
                    console.log('Erro ao carregar progresso salvo:', e);
                }
            }
        }

        // Salvar progresso quando uma resposta for selecionada
        document.addEventListener('change', function(e) {
            if (e.target.type === 'radio' && e.target.name.startsWith('resposta[')) {
                saveProgress();
            }
        });

        // Limpar progresso salvo após envio bem-sucedido
        <?php if (!empty($mensagem_avaliacao) && strpos($mensagem_avaliacao, 'success') === 0): ?>
        localStorage.removeItem('evaluation_progress_<?php echo $curso_id; ?>');
        <?php endif; ?>

        // Carregar progresso ao inicializar (se não houver respostas anteriores do banco)
        <?php if (empty($respostas_anteriores)): ?>
        // loadProgress(); // Descomente se quiser usar o auto-save
        <?php endif; ?>

        // Adicionar indicador de tempo (opcional)
        let startTime = new Date().getTime();
        
        window.addEventListener('beforeunload', function() {
            const endTime = new Date().getTime();
            const timeSpent = Math.floor((endTime - startTime) / 1000);
            console.log(`Tempo gasto na avaliação: ${timeSpent} segundos`);
        });

        // Adicionar efeitos visuais
        function addVisualEffects() {
            // Animação de entrada para as questões
            const questions = document.querySelectorAll('.question-card');
            questions.forEach((question, index) => {
                question.style.opacity = '0';
                question.style.transform = 'translateY(20px)';
                question.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    question.style.opacity = '1';
                    question.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }

        // Inicializar efeitos visuais
        addVisualEffects();

        // Função para destacar questões não respondidas
        function highlightUnanswered() {
            const questions = document.querySelectorAll('.question-card');
            questions.forEach(question => {
                const radios = question.querySelectorAll('input[type="radio"]');
                const isAnswered = Array.from(radios).some(radio => radio.checked);
                
                if (!isAnswered) {
                    question.style.borderColor = '#ffc107';
                    question.style.backgroundColor = '#fff3cd';
                } else {
                    question.style.borderColor = '#28a745';
                    question.style.backgroundColor = '#d4edda';
                }
            });
        }

        // Aplicar destaque após mudanças
        document.addEventListener('change', function(e) {
            if (e.target.type === 'radio') {
                setTimeout(highlightUnanswered, 100);
            }
        });

        // Aplicar destaque inicial
        setTimeout(highlightUnanswered, 500);

        // Scroll para resultado após envio
<?php if (!empty($mensagem_avaliacao) && strpos($mensagem_avaliacao, 'success') === 0): ?>
setTimeout(function() {
    const resultSection = document.querySelector('.evaluation-result');
    if (resultSection) {
        resultSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}, 1000);
<?php endif; ?>
    </script>

    
    

</body>
</html>

<?php
// Fechar conexões
if (isset($stmtCurso)) $stmtCurso->close();
if (isset($stmtAtividades)) $stmtAtividades->close();
if (isset($stmtAulas)) $stmtAulas->close();
if (isset($stmtObjetivos)) $stmtObjetivos->close();
if (isset($stmtAvaliacoes)) $stmtAvaliacoes->close();
if (isset($stmtRespostas)) $stmtRespostas->close();
if (isset($conn)) $conn->close();
?>