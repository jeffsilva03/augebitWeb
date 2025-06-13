<?php
include('conecta.php');
date_default_timezone_set('America/Sao_Paulo');

// Mostrar erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão
$mysqli = new mysqli($host, $login, $password, $bd);
if ($mysqli->connect_error) {
    die("Erro na conexão com o banco de dados: " . $mysqli->connect_error);
}

// Buscar cursos da tabela cursos
$cursos = [];
$sqlCursos = "SELECT id, titulo FROM cursos ORDER BY titulo";
$resultCursos = $mysqli->query($sqlCursos);
if ($resultCursos) {
    while ($row = $resultCursos->fetch_assoc()) {
        $cursos[] = $row;
    }
}

// Buscar avaliações existentes para popular o select de edição
$avaliacoes_existentes = [];
$sqlAvaliacoesExistentes = "SELECT DISTINCT a.curso_id, c.titulo 
                           FROM avaliacao a 
                           INNER JOIN cursos c ON a.curso_id = c.id 
                           ORDER BY c.titulo";
$resultAvaliacoesExistentes = $mysqli->query($sqlAvaliacoesExistentes);
if ($resultAvaliacoesExistentes) {
    while ($row = $resultAvaliacoesExistentes->fetch_assoc()) {
        $avaliacoes_existentes[] = $row;
    }
}

// Variáveis para pré-preencher o formulário
$curso_id_selecionado = '';
$dados_existentes = [];

// Se foi selecionado um curso para editar
if (isset($_GET['curso_id']) && !empty($_GET['curso_id'])) {
    $curso_id_selecionado = (int)$_GET['curso_id'];
    $sqlBuscar = "SELECT * FROM avaliacao WHERE curso_id = ? LIMIT 1";
    $stmtBuscar = $mysqli->prepare($sqlBuscar);
    $stmtBuscar->bind_param("i", $curso_id_selecionado);
    $stmtBuscar->execute();
    $resultBuscar = $stmtBuscar->get_result();
    
    if ($resultBuscar->num_rows > 0) {
        $dados_existentes = $resultBuscar->fetch_assoc();
    }
    $stmtBuscar->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [];

    // Adiciona o curso_id
    $curso_id = isset($_POST['curso_id']) ? (int)$_POST['curso_id'] : 0;
    if ($curso_id <= 0) {
        echo "<div class='alert alert-danger'>Erro: Por favor, selecione um curso válido.</div>";
        exit;
    }
    $dados[] = $curso_id;

    // Adiciona as perguntas, alternativas e respostas corretas
    for ($i = 1; $i <= 10; $i++) {
        $dados[] = $_POST["pergunta$i"] ?? '';
        $dados[] = $_POST["alternativaA$i"] ?? '';
        $dados[] = $_POST["alternativaB$i"] ?? '';
        $dados[] = $_POST["alternativaC$i"] ?? '';
        $dados[] = $_POST["alternativaD$i"] ?? '';
        $dados[] = $_POST["resposta_correta$i"] ?? ''; // Nova linha para resposta correta
    }

    // Verifica se todos os dados estão presentes (agora são 61 campos: 1 curso_id + 60 campos das questões)
    if (count($dados) !== 61) {
        echo "<div class='alert alert-danger'>Erro: número incorreto de dados recebidos. Esperado: 61, Recebido: " . count($dados) . "</div>";
        echo "<pre>"; print_r($dados); echo "</pre>";
        exit;
    }

    // Verifica se já existe um registro para este curso
    $checkSql = "SELECT id FROM avaliacao WHERE curso_id = ? LIMIT 1";
    $checkStmt = $mysqli->prepare($checkSql);
    $checkStmt->bind_param("i", $curso_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result && $result->num_rows > 0) {
        // Atualiza
        $sql = "UPDATE avaliacao SET 
            pergunta1 = ?, alternativaA1 = ?, alternativaB1 = ?, alternativaC1 = ?, alternativaD1 = ?, resposta_correta1 = ?,
            pergunta2 = ?, alternativaA2 = ?, alternativaB2 = ?, alternativaC2 = ?, alternativaD2 = ?, resposta_correta2 = ?,
            pergunta3 = ?, alternativaA3 = ?, alternativaB3 = ?, alternativaC3 = ?, alternativaD3 = ?, resposta_correta3 = ?,
            pergunta4 = ?, alternativaA4 = ?, alternativaB4 = ?, alternativaC4 = ?, alternativaD4 = ?, resposta_correta4 = ?,
            pergunta5 = ?, alternativaA5 = ?, alternativaB5 = ?, alternativaC5 = ?, alternativaD5 = ?, resposta_correta5 = ?,
            pergunta6 = ?, alternativaA6 = ?, alternativaB6 = ?, alternativaC6 = ?, alternativaD6 = ?, resposta_correta6 = ?,
            pergunta7 = ?, alternativaA7 = ?, alternativaB7 = ?, alternativaC7 = ?, alternativaD7 = ?, resposta_correta7 = ?,
            pergunta8 = ?, alternativaA8 = ?, alternativaB8 = ?, alternativaC8 = ?, alternativaD8 = ?, resposta_correta8 = ?,
            pergunta9 = ?, alternativaA9 = ?, alternativaB9 = ?, alternativaC9 = ?, alternativaD9 = ?, resposta_correta9 = ?,
            pergunta10 = ?, alternativaA10 = ?, alternativaB10 = ?, alternativaC10 = ?, alternativaD10 = ?, resposta_correta10 = ?
            WHERE curso_id = ?";
            
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            die("Erro ao preparar a query: " . $mysqli->error);
        }
        
        // Remove o curso_id do array de dados para o bind_param do UPDATE
        $dadosUpdate = array_slice($dados, 1); // Remove o primeiro elemento (curso_id)
        $dadosUpdate[] = $curso_id; // Adiciona o curso_id no final para o WHERE
        $tipos = str_repeat('s', count($dadosUpdate) - 1) . 'i'; // Todos string exceto o último (curso_id) que é int
        $stmt->bind_param($tipos, ...$dadosUpdate);
        
        $operacao = "atualizada";
        
    } else {
        // Insere novo registro
        $sql = "INSERT INTO avaliacao (
            curso_id,
            pergunta1, alternativaA1, alternativaB1, alternativaC1, alternativaD1, resposta_correta1,
            pergunta2, alternativaA2, alternativaB2, alternativaC2, alternativaD2, resposta_correta2,
            pergunta3, alternativaA3, alternativaB3, alternativaC3, alternativaD3, resposta_correta3,
            pergunta4, alternativaA4, alternativaB4, alternativaC4, alternativaD4, resposta_correta4,
            pergunta5, alternativaA5, alternativaB5, alternativaC5, alternativaD5, resposta_correta5,
            pergunta6, alternativaA6, alternativaB6, alternativaC6, alternativaD6, resposta_correta6,
            pergunta7, alternativaA7, alternativaB7, alternativaC7, alternativaD7, resposta_correta7,
            pergunta8, alternativaA8, alternativaB8, alternativaC8, alternativaD8, resposta_correta8,
            pergunta9, alternativaA9, alternativaB9, alternativaC9, alternativaD9, resposta_correta9,
            pergunta10, alternativaA10, alternativaB10, alternativaC10, alternativaD10, resposta_correta10
        ) VALUES (" . str_repeat('?, ', 60) . "?)";
        
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            die("Erro ao preparar a query: " . $mysqli->error);
        }
        
        $tipos = 'i' . str_repeat('s', 60); // Primeiro int (curso_id), resto string
        $stmt->bind_param($tipos, ...$dados);
        
        $operacao = "cadastrada";
    }

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Avaliação $operacao com sucesso!</div>";
        
        // Debug: Vamos verificar se os dados foram salvos corretamente
        echo "<div class='alert alert-info'>Debug: Verificando dados salvos...</div>";
        $sqlVerificar = "SELECT * FROM avaliacao WHERE curso_id = ? LIMIT 1";
        $stmtVerificar = $mysqli->prepare($sqlVerificar);
        $stmtVerificar->bind_param("i", $curso_id);
        $stmtVerificar->execute();
        $resultVerificar = $stmtVerificar->get_result();
        
        if ($resultVerificar->num_rows > 0) {
            $dadosSalvos = $resultVerificar->fetch_assoc();
            echo "<div class='alert alert-success'>✅ Dados salvos com sucesso! ID do registro: " . $dadosSalvos['id'] . "</div>";
            
            // Mostrar algumas perguntas salvas para confirmar
            echo "<div class='alert alert-info'>";
            echo "<strong>Primeira pergunta salva:</strong> " . htmlspecialchars($dadosSalvos['pergunta1']) . "<br>";
            echo "<strong>Resposta correta 1:</strong> " . htmlspecialchars($dadosSalvos['resposta_correta1']) . "<br>";
            echo "<strong>Segunda pergunta salva:</strong> " . htmlspecialchars($dadosSalvos['pergunta2']) . "<br>";
            echo "<strong>Resposta correta 2:</strong> " . htmlspecialchars($dadosSalvos['resposta_correta2']) . "<br>";
            echo "</div>";
        }
        $stmtVerificar->close();
        
        // Redireciona para evitar reenvio do formulário
        echo "<script>
                setTimeout(function(){ 
                    window.location.href = 'avaliacao.php?curso_id=" . $curso_id . "&success=1'; 
                }, 4000);
              </script>";
    } else {
        echo "<div class='alert alert-danger'>Erro ao salvar os dados: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $checkStmt->close();
}

// Buscar nome do curso para exibição
$nome_curso_atual = '';
if ($curso_id_selecionado > 0) {
    $sqlNomeCurso = "SELECT titulo FROM cursos WHERE id = ?";
    $stmtNomeCurso = $mysqli->prepare($sqlNomeCurso);
    $stmtNomeCurso->bind_param("i", $curso_id_selecionado);
    $stmtNomeCurso->execute();
    $resultNomeCurso = $stmtNomeCurso->get_result();
    if ($resultNomeCurso->num_rows > 0) {
        $rowNomeCurso = $resultNomeCurso->fetch_assoc();
        $nome_curso_atual = $rowNomeCurso['titulo'];
    }
    $stmtNomeCurso->close();
}

include_once('headerInstrutor.php');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Avaliação</title>
    <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="avaliacao.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script type="text/javascript" src="DataTables/datatables.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    

</head>
<body>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1><i class="fas fa-clipboard-check"></i> Cadastro de Avaliação</h1>
            
            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> Operação realizada com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Debug: Mostrar informações da tabela -->
           
            
            <!-- Avaliações Existentes -->
            <?php if (!empty($avaliacoes_existentes)): ?>
            <div class="existing-evaluations">
                <h5><i class="fas fa-list"></i> Avaliações Existentes</h5>
                <p><strong>Editar avaliação existente:</strong></p>
                <div class="row">
                    <?php foreach ($avaliacoes_existentes as $aval): ?>
                    <div class="col-md-4 mb-2">
                        <a href="?curso_id=<?php echo $aval['curso_id']; ?>" 
                           class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-edit"></i> <?php echo htmlspecialchars($aval['titulo']); ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <form method="POST" id="form-avaliacao">
        <!-- Seletor de Curso -->
        <div class="course-selector">
            <h2><i class="fas fa-graduation-cap"></i> Selecione o Curso</h2>
            
            <?php if ($nome_curso_atual): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                Editando avaliação do curso: <strong><?php echo htmlspecialchars($nome_curso_atual); ?></strong>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label required-field"><i class="fas fa-book"></i> Curso</label>
                        <select name="curso_id" class="form-control" required>
                            <option value="">Selecione um curso...</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?php echo $curso['id']; ?>" 
                                        <?php echo ($curso_id_selecionado == $curso['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($curso['titulo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-light">
                            <i class="fas fa-lightbulb"></i> Selecione o curso para criar ou editar sua avaliação
                        </small>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="mb-3">
                       
                    </div>
                </div>
            </div>
        </div>

        <!-- Questões -->
        <?php for ($i = 1; $i <= 10; $i++): ?>
        <div class="form-section">
            <h4>
                <span class="question-number"><?php echo $i; ?></span>
                Questão <?php echo $i; ?>
            </h4>
            
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label required-field"><i class="fas fa-question-circle"></i> Enunciado da Questão</label>
                        <textarea name="pergunta<?php echo $i; ?>" class="form-control" rows="3" 
                                placeholder="Digite o enunciado da questão <?php echo $i; ?>..." 
                                required><?php echo htmlspecialchars($dados_existentes["pergunta$i"] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label required-field"><i class="fas fa-check-circle text-success"></i> Alternativa A</label>
                        <input name="alternativaA<?php echo $i; ?>" type="text" class="form-control" 
                               placeholder="Digite a alternativa A" 
                               value="<?php echo htmlspecialchars($dados_existentes["alternativaA$i"] ?? ''); ?>" 
                               required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label required-field"><i class="fas fa-check-circle text-info"></i> Alternativa B</label>
                        <input name="alternativaB<?php echo $i; ?>" type="text" class="form-control" 
                               placeholder="Digite a alternativa B" 
                               value="<?php echo htmlspecialchars($dados_existentes["alternativaB$i"] ?? ''); ?>" 
                               required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label required-field"><i class="fas fa-check-circle text-warning"></i> Alternativa C</label>
                        <input name="alternativaC<?php echo $i; ?>" type="text" class="form-control" 
                               placeholder="Digite a alternativa C" 
                               value="<?php echo htmlspecialchars($dados_existentes["alternativaC$i"] ?? ''); ?>" 
                               required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label required-field"><i class="fas fa-check-circle text-danger"></i> Alternativa D</label>
                        <input name="alternativaD<?php echo $i; ?>" type="text" class="form-control" 
                               placeholder="Digite a alternativa D" 
                               value="<?php echo htmlspecialchars($dados_existentes["alternativaD$i"] ?? ''); ?>" 
                               required>
                    </div>
                </div>
            </div>
            
            <!-- Seção para selecionar a resposta correta -->
            <div class="correct-answer-section">
                <h6><i class="fas fa-award"></i> Qual é a resposta correta?</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="answer-option">
                            <input type="radio" name="resposta_correta<?php echo $i; ?>" value="A" id="resposta_A_<?php echo $i; ?>" 
                                   <?php echo (isset($dados_existentes["resposta_correta$i"]) && $dados_existentes["resposta_correta$i"] == 'A') ? 'checked' : ''; ?>
                                   required>
                            <label for="resposta_A_<?php echo $i; ?>">
                                <span class="answer-letter">A</span> Alternativa A
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="answer-option">
                            <input type="radio" name="resposta_correta<?php echo $i; ?>" value="B" id="resposta_B_<?php echo $i; ?>"
                                   <?php echo (isset($dados_existentes["resposta_correta$i"]) && $dados_existentes["resposta_correta$i"] == 'B') ? 'checked' : ''; ?>
                                   required>
                            <label for="resposta_B_<?php echo $i; ?>">
                                <span class="answer-letter">B</span> Alternativa B
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="answer-option">
                            <input type="radio" name="resposta_correta<?php echo $i; ?>" value="C" id="resposta_C_<?php echo $i; ?>"
                                   <?php echo (isset($dados_existentes["resposta_correta$i"]) && $dados_existentes["resposta_correta$i"] == 'C') ? 'checked' : ''; ?>
                                   required>
                            <label for="resposta_C_<?php echo $i; ?>">
                                <span class="answer-letter">C</span> Alternativa C
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="answer-option">
                            <input type="radio" name="resposta_correta<?php echo $i; ?>" value="D" id="resposta_D_<?php echo $i; ?>"
                                   <?php echo (isset($dados_existentes["resposta_correta$i"]) && $dados_existentes["resposta_correta$i"] == 'D') ? 'checked' : ''; ?>
                                   required>
                            <label for="resposta_D_<?php echo $i; ?>">
                                <span class="answer-letter">D</span> Alternativa D
                            </label>
                        </div>
                    </div>
                </div>
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Selecione qual alternativa é a resposta correta para esta questão
                </small>
            </div>
        </div>
        <?php endfor; ?>

        <!-- Botões de Ação -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex gap-3 mb-4">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="fas fa-save"></i> 
                        <?php echo !empty($dados_existentes) ? 'Atualizar Avaliação' : 'Salvar Avaliação'; ?>
                    </button>
                    <a href="../painel_instrutor.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-list"></i> Ver Cursos
                    </a>
                    <?php if ($curso_id_selecionado > 0): ?>
                    <a href="avaliacao.php" class="btn btn-info btn-lg">
                        <i class="fas fa-plus"></i> Nova Avaliação
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>

    <div id="mensagem" class="mt-3"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Adiciona confirmação antes de sair da página se houver dados não salvos
    let formChanged = false;
    
    $('#form-avaliacao input, #form-avaliacao textarea, #form-avaliacao select').on('change input', function() {
        formChanged = true;
    });
    
    // Validação do formulário antes do envio
    $('#form-avaliacao').on('submit', function(e) {
        let isValid = true;
        let missingFields = [];
        
        // Verificar se todas as perguntas têm resposta correta selecionada
        for (let i = 1; i <= 10; i++) {
            const pergunta = $(`textarea[name="pergunta${i}"]`).val().trim();
            const alternativaA = $(`input[name="alternativaA${i}"]`).val().trim();
            const alternativaB = $(`input[name="alternativaB${i}"]`).val().trim();
            const alternativaC = $(`input[name="alternativaC${i}"]`).val().trim();
            const alternativaD = $(`input[name="alternativaD${i}"]`).val().trim();
            const respostaCorreta = $(`input[name="resposta_correta${i}"]:checked`).val();
            
            // Verificar se todos os campos da questão estão preenchidos
            if (!pergunta) {
                $(`textarea[name="pergunta${i}"]`).addClass('validation-error');
                missingFields.push(`Pergunta ${i}`);
                isValid = false;
            } else {
                $(`textarea[name="pergunta${i}"]`).removeClass('validation-error');
            }
            
            if (!alternativaA) {
                $(`input[name="alternativaA${i}"]`).addClass('validation-error');
                missingFields.push(`Alternativa A da questão ${i}`);
                isValid = false;
            } else {
                $(`input[name="alternativaA${i}"]`).removeClass('validation-error');
            }
            
            if (!alternativaB) {
                $(`input[name="alternativaB${i}"]`).addClass('validation-error');
                missingFields.push(`Alternativa B da questão ${i}`);
                isValid = false;
            } else {
                $(`input[name="alternativaB${i}"]`).removeClass('validation-error');
            }
            
            if (!alternativaC) {
                $(`input[name="alternativaC${i}"]`).addClass('validation-error');
                missingFields.push(`Alternativa C da questão ${i}`);
                isValid = false;
            } else {
                $(`input[name="alternativaC${i}"]`).removeClass('validation-error');
            }
            
            if (!alternativaD) {
                $(`input[name="alternativaD${i}"]`).addClass('validation-error');
                missingFields.push(`Alternativa D da questão ${i}`);
                isValid = false;
            } else {
                $(`input[name="alternativaD${i}"]`).removeClass('validation-error');
            }
            
            if (!respostaCorreta) {
                // Destacar a seção de resposta correta
                $(`input[name="resposta_correta${i}"]`).closest('.correct-answer-section').css('border-color', '#dc3545');
                missingFields.push(`Resposta correta da questão ${i}`);
                isValid = false;
            } else {
                $(`input[name="resposta_correta${i}"]`).closest('.correct-answer-section').css('border-color', '#28a745');
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios:\n\n' + missingFields.join('\n'));
            
            // Rolar para o primeiro campo com erro
            const firstError = $('.validation-error').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
                firstError.focus();
            }
            
            return false;
        }
        
        formChanged = false;
        
        // Adicionar loading no botão
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Salvando...').prop('disabled', true);
        
        // Restaurar botão após 3 segundos (caso não redirecione)
        setTimeout(function() {
            btn.html(originalText).prop('disabled', false);
        }, 3000);
    });
    
    $(window).on('beforeunload', function() {
        if (formChanged) {
            return 'Você tem alterações não salvas. Deseja realmente sair?';
        }
    });
    
    // Auto-resize para textareas
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Contador de caracteres para perguntas
    $('textarea[name*="pergunta"]').each(function() {
        const maxLength = 500;
        const textarea = $(this);
        const counter = $('<small class="form-text text-muted char-counter"></small>');
        textarea.after(counter);
        
        function updateCounter() {
            const remaining = maxLength - textarea.val().length;
            counter.text(remaining + ' caracteres restantes');
            if (remaining < 50) {
                counter.addClass('text-warning');
            } else {
                counter.removeClass('text-warning');
            }
        }
        
        textarea.on('input', updateCounter);
        updateCounter();
    });
    
    // Destacar alternativas quando a resposta correta é selecionada
    $('input[name*="resposta_correta"]').on('change', function() {
        const questionNumber = this.name.match(/\d+/)[0];
        const selectedAnswer = this.value;
        
        // Remover destaque de todas as alternativas desta questão
        $(`input[name*="alternativa"][name*="${questionNumber}"]`).removeClass('correct-answer-highlight');
        
        // Destacar a alternativa selecionada como correta
        $(`input[name="alternativa${selectedAnswer}${questionNumber}"]`).addClass('correct-answer-highlight');
        
        // Remover erro visual da seção de resposta correta
        $(this).closest('.correct-answer-section').css('border-color', '#28a745');
    });
    
    // Validação em tempo real
    $('input[required], textarea[required]').on('blur', function() {
        if ($(this).val().trim() === '') {
            $(this).addClass('validation-error');
        } else {
            $(this).removeClass('validation-error');
        }
    });
    
    // Melhorar UX dos radio buttons
    $('.answer-option').on('click', function() {
        const radio = $(this).find('input[type="radio"]');
        if (!radio.prop('checked')) {
            radio.prop('checked', true).trigger('change');
        }
    });
    
    // Adicionar efeito visual quando hover nos radio buttons
    $('.answer-option').on('mouseenter', function() {
        $(this).css('background-color', 'rgba(40, 167, 69, 0.1)');
    }).on('mouseleave', function() {
        if (!$(this).find('input[type="radio"]').prop('checked')) {
            $(this).css('background-color', '');
        }
    });
    
    // Destacar opção selecionada
    $('input[name*="resposta_correta"]').on('change', function() {
        const container = $(this).closest('.correct-answer-section');
        container.find('.answer-option').css('background-color', '');
        $(this).closest('.answer-option').css('background-color', 'rgba(40, 167, 69, 0.15)');
    });
    
    // Aplicar destaque inicial para respostas já selecionadas
    $('input[name*="resposta_correta"]:checked').each(function() {
        $(this).closest('.answer-option').css('background-color', 'rgba(40, 167, 69, 0.15)');
    });
});
</script>



</body>
</html>

<?php
$mysqli->close();
?>