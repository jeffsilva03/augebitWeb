<?php
// Verificar se o usuário está logado
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../registro/form_login.php');
    exit();
    
}



// Definir o diretório para uploads de imagens - caminho absoluto para garantir consistência
$upload_dir = dirname(__FILE__) . "/uploads/avatars/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
// Caminho relativo para armazenar no banco de dados e exibir na página
$upload_dir_rel = "uploads/avatars/";

// Buscar o caminho correto para o arquivo de conexão
$possible_paths = [
    '../../../conexaoBD/conexao.php',
    '../../../../conexaoBD/conexao.php',
    '../../../conexaoBD/conexao.php'
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
    die("Erro: não foi possível encontrar o arquivo de conexão.");
}

// Normalizar o nome da variável de conexão
if (!isset($conn) && isset($conexao)) {
    $conn = $conexao;
}

// Pegar o ID do usuário da sessão
$usuario_id = $_SESSION['usuario_id'];

// Variáveis para mensagens
$upload_error = null;
$upload_success = null;

// Processar o upload da foto apenas se o formulário for enviado
if (isset($_POST['submit_photo'])) {
    if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] == 0) {
        $file_extension = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
        $new_filename = "user_" . $usuario_id . "_" . time() . "." . $file_extension;
        $target_file = $upload_dir . $new_filename;
        $db_file_path = $upload_dir_rel . $new_filename; // Caminho relativo para armazenar no BD
        
        // Verificar se o arquivo é uma imagem
        $check = getimagesize($_FILES["avatar"]["tmp_name"]);
        if ($check !== false) {
            // Verificar tamanho (limite de 5MB)
            if ($_FILES["avatar"]["size"] > 5000000) {
                $upload_error = "Arquivo muito grande. Tamanho máximo: 5MB.";
            } else {
                // Verificar formato (apenas jpg, jpeg, png)
                if ($file_extension != "jpg" && $file_extension != "jpeg" && $file_extension != "png") {
                    $upload_error = "Apenas arquivos JPG, JPEG e PNG são permitidos.";
                } else {
                    // Tudo ok, tenta fazer o upload
                    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                        // Atualizar caminho da foto no banco de dados
                        $sql = "UPDATE cadastro SET avatar = ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("si", $db_file_path, $usuario_id);
                        
                        if ($stmt->execute()) {
                            $upload_success = "Foto atualizada com sucesso!";
                            // Atualizar a sessão para refletir a mudança imediatamente
                            $_SESSION['avatar'] = $db_file_path;
                        } else {
                            $upload_error = "Erro ao atualizar foto no banco de dados: " . $conn->error;
                        }
                        $stmt->close();
                    } else {
                        $upload_error = "Erro ao fazer upload do arquivo.";
                    }
                }
            }
        } else {
            $upload_error = "O arquivo não é uma imagem válida.";
        }
    } else if ($_FILES["avatar"]["error"] != 4) { // 4 = UPLOAD_ERR_NO_FILE
        $upload_error = "Erro no upload do arquivo.";
    } else {
        $upload_error = "Nenhum arquivo selecionado.";
    }
}

// Buscar informações do usuário
$sql = "SELECT c.*, 
        (SELECT COUNT(*) FROM inscricoes i WHERE i.usuario_id = c.id) as cursos_inscritos 
        FROM cadastro c WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
} else {
    die("Usuário não encontrado.");
}
$stmt->close();

// Obter informações do curso, se aplicável
$curso = null;
$sql_curso = "SELECT c.titulo, c.descricao, i.inscrito_em 
              FROM inscricoes i 
              JOIN cursos c ON i.curso_id = c.id 
              WHERE i.usuario_id = ? 
              ORDER BY i.inscrito_em DESC 
              LIMIT 1";
$stmt_curso = $conn->prepare($sql_curso);
$stmt_curso->bind_param("i", $usuario_id);
$stmt_curso->execute();
$result_curso = $stmt_curso->get_result();
if ($result_curso->num_rows > 0) {
    $curso = $result_curso->fetch_assoc();
}
$stmt_curso->close();

// Definir o caminho para o avatar
$default_avatar = "assets/images/default-avatar.png";

// Verificar se o usuário tem avatar no banco de dados
if (!empty($usuario['avatar']) && file_exists($usuario['avatar'])) {
    $avatar_path = $usuario['avatar'];
} elseif (!empty($usuario['avatar'])) {
    // Se o caminho existe no BD mas o arquivo não, tenta com caminho relativo
    if (file_exists(dirname(__FILE__) . '/' . $usuario['avatar'])) {
        $avatar_path = $usuario['avatar'];
    } else {
        $avatar_path = $default_avatar;
    }
} else {
    $avatar_path = $default_avatar;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário - Augebit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <style>
        :root {
            --primary-color: #9b30ff;
            --secondary-color: #7a00cc;
            --bg-color: #f8f9fa;
            --text-color: #333;
            --border-color: #e5e7eb;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text-color);
        }
        
        .profile-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .avatar-container {
            margin-bottom: 20px;
        }
        
        .avatar-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .change-photo-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .change-photo-btn i {
            color: var(--primary-color);
            font-size: 20px;
        }
        
        .profile-name {
            margin-top: 10px;
            font-size: 24px;
            font-weight: bold;
        }
        
        .profile-content {
            display: flex;
            flex-wrap: wrap;
            padding: 20px;
        }
        
        .profile-section {
            flex: 1;
            min-width: 300px;
            margin: 10px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .profile-section h2 {
            margin-top: 0;
            color: var(--primary-color);
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
            font-size: 18px;
        }
        
        .info-item {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
        }
        
        .info-value {
            text-align: right;
        }
        
        .course-info h3 {
            margin-top: 0;
            color: var(--text-color);
            font-size: 16px;
        }
        
        .course-desc {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 12px;
        }
        
        .form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .form-buttons button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
        }
        
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        @media (max-width: 768px) {
            .profile-content {
                flex-direction: column;
            }
            
            .profile-section {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>

<?php
// Incluir o header da página
require_once '../../../arquivosReuso/header.php';
?>

    <div class="profile-container">
        <div class="profile-header">
            <div class="avatar-container">
                <div class="avatar-wrapper">
                    <img src="<?= htmlspecialchars($avatar_path) ?>" alt="Foto de perfil" class="profile-avatar">
                    <button type="button" class="change-photo-btn" onclick="openPhotoModal()">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
            </div>
            <h1 class="profile-name"><?= htmlspecialchars($usuario['nome']) ?></h1>
        </div>
        
        <div class="profile-content">
            <div class="profile-section info-section">
                <h2>INFORMAÇÕES</h2>
                <div class="info-item">
                    <span class="info-label">E-mail</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['email']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Telefone</span>
                    <span class="info-value">(00) 00000-0000</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Endereço</span>
                    <span class="info-value">Av. Abílio Francisco Figueiredo, 481</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Perfil</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['perfil']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Cursos Inscritos</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['cursos_inscritos']) ?></span>
                </div>
            </div>
            
            <div class="profile-section course-section">
                <h2>CURSO</h2>
                <?php if($curso): ?>
                    <div class="course-info">
                        <h3><?= htmlspecialchars($curso['titulo']) ?></h3>
                        <p class="course-desc"><?= htmlspecialchars($curso['descricao']) ?></p>
                        
                        <div class="info-item">
                            <span class="info-label">Período</span>
                            <span class="info-value">Noturno</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Inscrito em</span>
                            <span class="info-value"><?= date('d/m/Y', strtotime($curso['inscrito_em'])) ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="course-info">
                        <p>Nenhum curso registrado.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para upload de foto -->
    <div id="photo-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePhotoModal()">&times;</span>
            <h2>Alterar foto de perfil</h2>
            
            <?php if(isset($upload_error)): ?>
                <div class="alert alert-danger"><?= $upload_error ?></div>
            <?php endif; ?>
            
            <?php if(isset($upload_success)): ?>
                <div class="alert alert-success"><?= $upload_success ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data" id="photo-form">
                <div class="form-group">
                    <label for="avatar">Escolha uma nova foto</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" required>
                    <small>Formatos aceitos: JPG, JPEG, PNG. Tamanho máximo: 5MB</small>
                </div>
                <div class="form-buttons">
                    <button type="button" onclick="closePhotoModal()">Cancelar</button>
                    <button type="submit" name="submit_photo" class="btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funções para abrir e fechar o modal
        function openPhotoModal() {
            document.getElementById('photo-modal').style.display = 'block';
        }
        
        function closePhotoModal() {
            document.getElementById('photo-modal').style.display = 'none';
        }
        
        // Fechar o modal quando clicar fora dele
        window.onclick = function(event) {
            var modal = document.getElementById('photo-modal');
            if (event.target == modal) {
                closePhotoModal();
            }
        }
        
        // Exibir o nome do arquivo selecionado
        document.getElementById("avatar").addEventListener("change", function() {
            var fileName = this.value.split("\\").pop();
            if (fileName) {
                this.nextElementSibling.innerHTML = "Arquivo selecionado: " + fileName;
            }
        });
        
        // Se houver uma mensagem de sucesso, fechar o modal após 3 segundos
        <?php if(isset($upload_success)): ?>
        setTimeout(function() {
            closePhotoModal();
            // Não recarregamos a página automaticamente - o usuário terá que recarregar manualmente
            // para ver a nova imagem ou podemos atualizar a imagem via JavaScript
            updateAvatarImage('<?= htmlspecialchars($avatar_path) ?>');
        }, 3000);
        <?php endif; ?>
        
        // Função para atualizar a imagem do avatar sem recarregar a página
        function updateAvatarImage(newSrc) {
            // Adiciona um parâmetro de consulta aleatório para evitar o cache do navegador
            var timestamp = new Date().getTime();
            document.querySelector('.profile-avatar').src = newSrc + '?t=' + timestamp;
        }
    </script>
    
    <?php
    // Fechar conexão com o banco de dados após terminar de usar
    if (isset($conn) && $conn) {
        $conn->close();
    }
    ?>
</body>
</html>