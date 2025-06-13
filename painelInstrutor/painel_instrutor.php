<?php
include '../arquivosReuso/conexao.php';

// Definir o título da página
$page_title = 'Meus Cursos - Dashboard';

// Incluir cabeçalho
require_once 'headerInstrutor.php';
// Buscar estatísticas do banco
$query_usuarios = "SELECT COUNT(*) as total FROM cadastro WHERE perfil IN ('usuario', 'usuarioGeral')";
$result_usuarios = $conn->query($query_usuarios);
$total_usuarios = $result_usuarios->fetch_assoc()['total'];

$query_cursos = "SELECT COUNT(*) as total FROM cursos";
$result_cursos = $conn->query($query_cursos);
$total_cursos = $result_cursos->fetch_assoc()['total'];

$query_inscricoes = "SELECT COUNT(*) as total FROM inscricoes";
$result_inscricoes = $conn->query($query_inscricoes);
$total_inscricoes = $result_inscricoes->fetch_assoc()['total'];

$query_mensagens = "SELECT COUNT(*) as total FROM contate_nos";
$result_mensagens = $conn->query($query_mensagens);
$total_mensagens = $result_mensagens->fetch_assoc()['total'];

// Buscar cursos com informações do instrutor usando prepared statement
$query_cursos_lista = "
    SELECT c.*, ca.nome as instrutor_nome 
    FROM cursos c 
    JOIN cadastro ca ON c.instrutor_id = ca.id 
    ORDER BY c.criado_em DESC
";
$stmt = $conn->prepare($query_cursos_lista);
$stmt->execute();
$result_cursos_lista = $stmt->get_result();

// Buscar usuários recentes
$query_usuarios_lista = "
    SELECT nome, email, perfil, avatar
    FROM cadastro
    ORDER BY id DESC
    LIMIT 10
";

$result_usuarios_lista = $conn->query($query_usuarios_lista);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= $page_title ?></title>
    
  <link rel="icon" href="src/icone.ico" type="image/x-icon">
     <link rel="icon" href="/../arquivosReuso/src/icone.ico" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="stylesheet" href="style.css">
    
</head>

<body class="animate__animated animate__fadeIn">
    <div class="container">
        <!-- Cabeçalho da Página -->
        <header class="page-header animate-fade-in-up">
            <div>
                <h1 class="page-title">
                    <i class="capa" data-lucide="graduation-cap"></i>
                    Dashboard de Cursos
                </h1>
                
            </div>
            <div class="header-actions">
                <a href="criar_curso.php" class="btn btn-primary btn-lg">
                    <i data-lucide="plus"></i>
                    Novo Curso
                </a>
                <a href="conteudoCursos/index.php" class="btn btn-outline">
                    <i data-lucide="bar-chart-3"></i>
                    Módulos
                </a>
            </div>
        </header>

        

        <!-- Cards de Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card animate-fade-in-up animate-delay-1">
                <div class="stat-content">
                    <div class="stat-icon">
                        <i data-lucide="book"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="counter" data-target="<?= $total_cursos ?>"><?= $total_cursos ?></h3>
                        <p>Total de Cursos</p>
                    </div>
                </div>
            </div>
            
            
        </div>

        <!-- Card Principal com Tabela -->
        <div class="main-card animate-fade-in-up">
            <div class="card-header">
                <div class="card-header-left">
                    <h2>Gerenciamento de Cursos</h2>
                </div>
                
            </div>
            <div class="card-body">
                <?php if ($total_cursos > 0): ?>
                    <div class="table-container">
                        <table id="coursesTable">
                            <thead>
                                <tr>
                                    <th style="width: 6%">ID</th>
                                    <th style="width: 30%">Curso</th>
                                    <th style="width: 12%">Status</th>
                                    <th style="width: 12%">Data Criação</th>
                                    <th style="width: 10%">Módulos</th>
                                    <th style="width: 10%">Alunos</th>
                                    <th style="width: 20%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Usar o resultado já obtido
                                while($curso = $result_cursos_lista->fetch_assoc()): 
                                    // Contar módulos do curso
                                    $mod_count = $conn->query("SELECT COUNT(*) as total FROM atividades WHERE curso_id = {$curso['id']}")->fetch_assoc()['total'];
                                    
                                    // Contar alunos inscritos no curso
                                    $alunos_count = $conn->query("SELECT COUNT(*) as total FROM inscricoes WHERE curso_id = {$curso['id']}")->fetch_assoc()['total'];
                                    
                                    // Determinar status (assumindo que existe uma coluna status na tabela cursos)
                                    $status = isset($curso['status']) ? $curso['status'] : 'ativo';
                                ?>
                                <tr data-course-id="<?= $curso['id'] ?>" data-status="<?= $status ?>">
                                    <td>
                                        <span style="font-weight: 700; color: #fbbf24; font-size: 1rem;">#<?= $curso['id'] ?></span>
                                    </td>
                                    <td>
                                        <div class="course-title"><?= htmlspecialchars($curso['titulo']) ?></div>
                                        <?php if (strlen($curso['descricao']) > 0): ?>
                                            <div class="course-description">
                                                <?= substr(htmlspecialchars($curso['descricao']), 0, 100) . (strlen($curso['descricao']) > 100 ? '...' : '') ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($status === 'ativo'): ?>
                                            <span class="status-badge status-active">
                                                <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i>
                                                Ativo
                                            </span>
                                        <?php elseif ($status === 'rascunho'): ?>
                                            <span class="status-badge status-draft">
                                                <i data-lucide="edit" style="width: 12px; height: 12px;"></i>
                                                Rascunho
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge" style="background: var(--info-light); color: var(--info); border: 1px solid var(--info);">
                                                <i data-lucide="pause-circle" style="width: 12px; height: 12px;"></i>
                                                Pausado
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.5rem; color: rgba(0, 0, 0, 0.8);">
                                            <i data-lucide="calendar" style="width: 16px; height: 16px;"></i>
                                            <?= date('d/m/Y', strtotime($curso['criado_em'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge">
                                            <i data-lucide="layers" style="width: 14px; height: 14px;"></i>
                                            <?= $mod_count ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: var(--gradient-secondary);">
                                            <i data-lucide="users" style="width: 14px; height: 14px;"></i>
                                            <?= $alunos_count ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                          
                                            <a href="editar_curso.php?id=<?= $curso['id'] ?>" class="btn btn-sm btn-secondary" title="Editar Curso">
                                                <i data-lucide="edit-3"></i>
                                                Editar
                                            </a>
                                            <div class="action-dropdown">
                                               
                                                <div class="dropdown-menu">
                                                    <a href="conteudos.php?curso_id=<?= $curso['id'] ?>" class="dropdown-item">
                                                        <i data-lucide="settings"></i>
                                                        Gerenciar Conteúdo
                                                    </a>
                                                    <a href="modulos.php?curso_id=<?= $curso['id'] ?>" class="dropdown-item">
                                                        <i data-lucide="layers"></i>
                                                        Gerenciar Módulos
                                                    </a>
                                                    <a href="alunos_curso.php?curso_id=<?= $curso['id'] ?>" class="dropdown-item">
                                                        <i data-lucide="users"></i>
                                                        Ver Alunos (<?= $alunos_count ?>)
                                                    </a>
                                                    <a href="estatisticas_curso.php?id=<?= $curso['id'] ?>" class="dropdown-item">
                                                        <i data-lucide="bar-chart"></i>
                                                        Estatísticas
                                                    </a>
                                                    <a href="duplicar_curso.php?id=<?= $curso['id'] ?>" class="dropdown-item">
                                                        <i data-lucide="copy"></i>
                                                        Duplicar Curso
                                                    </a>
                                                    <a href="exportar_curso.php?id=<?= $curso['id'] ?>" class="dropdown-item">
                                                        <i data-lucide="download"></i>
                                                        Exportar
                                                    </a>
                                                    <div style="border-top: 1px solid var(--gray-200); margin: 0.5rem 0;"></div>
                                                    <a href="arquivar_curso.php?id=<?= $curso['id'] ?>" class="dropdown-item" style="color: var(--warning);">
                                                        <i data-lucide="archive"></i>
                                                        Arquivar
                                                    </a>
                                                    <a href="excluir_curso.php?id=<?= $curso['id'] ?>" class="dropdown-item" 
                                                       onclick="return confirm('⚠️ ATENÇÃO!\n\nTem certeza que deseja excluir este curso?\n\nEsta ação irá remover PERMANENTEMENTE:\n• O curso completo\n• Todos os módulos (<?= $mod_count ?>)\n• Todos os conteúdos\n• Todas as inscrições (<?= $alunos_count ?> alunos)\n• Todos os dados relacionados\n\n🚨 ESTA AÇÃO NÃO PODE SER DESFEITA!\n\nDigite CONFIRMAR para prosseguir:')" 
                                                       style="color: var(--danger);">
                                                        <i data-lucide="trash-2"></i>
                                                        Excluir Definitivamente
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert">
                        <i data-lucide="lightbulb"></i>
                        <div class="alert-content">
                            <h3>Comece sua jornada como instrutor!</h3>
                            <p>Você ainda não possui cursos criados. É hora de compartilhar seu conhecimento com o mundo!</p>
                            <p style="margin-top: 1rem;">
                                <a href="criar_curso.php" class="btn btn-primary" style="margin-right: 1rem;">
                                    <i data-lucide="plus"></i>
                                    Criar Primeiro Curso
                                </a>
                                <a href="guia_instrutor.php" class="btn btn-outline">
                                    <i data-lucide="help-circle"></i>
                                    Guia do Instrutor
                                </a>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card de Ações em Massa (apenas se houver cursos) -->
        <?php if ($total_cursos > 0): ?>
       
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Inicializar Lucide Icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            
            // Animação dos contadores
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2500;
                const increment = target / (duration / 16);
                let current = 0;
                
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.ceil(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };
                
                // Iniciar animação após um pequeno delay
                setTimeout(updateCounter, 600);
            });

            // Funcionalidade de busca
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const tableRows = document.querySelectorAll('#coursesTable tbody tr');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusFilter_value = statusFilter.value.toLowerCase();

                tableRows.forEach(row => {
                    const courseTitle = row.querySelector('.course-title')?.textContent.toLowerCase() || '';
                    const courseDescription = row.querySelector('.course-description')?.textContent.toLowerCase() || '';
                    const courseStatus = row.getAttribute('data-status').toLowerCase();
                    
                    const matchesSearch = courseTitle.includes(searchTerm) || courseDescription.includes(searchTerm);
                    const matchesStatus = statusFilter_value === '' || courseStatus === statusFilter_value;
                    
                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                        row.style.animation = 'fadeInUp 0.3s ease-out';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterTable);
            }
            
            if (statusFilter) {
                statusFilter.addEventListener('change', filterTable);
            }

            // Animação de entrada escalonada para os cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(40px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 200 * (index + 1));
            });

            // Efeitos hover avançados para as linhas da tabela
            const allTableRows = document.querySelectorAll('tbody tr');
            allTableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.01) translateZ(0)';
                    this.style.zIndex = '10';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1) translateZ(0)';
                    this.style.zIndex = '1';
                });
            });

            // Funcionalidade de seleção em massa
            const selectAllCheckbox = document.getElementById('selectAll');
            const massActionButtons = document.querySelectorAll('#exportSelected, #archiveSelected, #publishSelected, #deleteSelected');
            
            if (selectAllCheckbox) {
                // Adicionar checkboxes individuais às linhas da tabela
                allTableRows.forEach(row => {
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.className = 'row-checkbox';
                    checkbox.style.marginRight = '0.5rem';
                    
                    const firstCell = row.querySelector('td');
                    if (firstCell) {
                        firstCell.insertBefore(checkbox, firstCell.firstChild);
                    }
                });

                const rowCheckboxes = document.querySelectorAll('.row-checkbox');

                selectAllCheckbox.addEventListener('change', function() {
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateMassActionButtons();
                });

                rowCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateMassActionButtons);
                });

                function updateMassActionButtons() {
                    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                    const hasSelection = checkedBoxes.length > 0;
                    
                    massActionButtons.forEach(button => {
                        button.disabled = !hasSelection;
                        if (hasSelection) {
                            button.style.opacity = '1';
                            button.style.cursor = 'pointer';
                        } else {
                            button.style.opacity = '0.5';
                            button.style.cursor = 'not-allowed';
                        }
                    });
                }
            }

            // Efeito de loading para botões
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    // Adicionar efeito ripple
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Animação de pulso para estatísticas quando atualizam
            setInterval(() => {
                const now = new Date();
                if (now.getSeconds() === 0) { // A cada minuto
                    counters.forEach(counter => {
                        counter.style.animation = 'pulse 1s ease-in-out';
                        setTimeout(() => {
                            counter.style.animation = '';
                        }, 1000);
                    });
                }
            }, 1000);
        });

        // CSS para animação ripple
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>

<?php
require_once '../arquivosReuso/footer.php';
?>