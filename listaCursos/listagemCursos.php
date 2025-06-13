<?php
session_start();

if (!isset($_SESSION['usuario_id'], $_SESSION['perfil'])) {
    header('Location: ../login/form_login.php');
    exit;
}
$meuPerfil = $_SESSION['perfil'];

require_once '../arquivosReuso/conexao.php';

$sql = "SELECT c.*, ca.nome as instrutor_nome 
        FROM cursos c 
        INNER JOIN cadastro ca ON c.instrutor_id = ca.id 
        ORDER BY c.criado_em DESC";
$result = $conn->query($sql);
$cursos = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cursos[] = $row;
    }
}

function getCategoryIcon($category) {
    $icons = [
        'tech' => 'fas fa-microchip',
        'design' => 'fas fa-palette',
        'business' => 'fas fa-briefcase',
        'marketing' => 'fas fa-bullhorn',
        'data' => 'fas fa-chart-line',
        'mobile' => 'fas fa-mobile-alt',
        'programming' => 'fas fa-laptop-code'
    ];
    return isset($icons[$category]) ? $icons[$category] : 'fas fa-book';
}

function getCategoryName($category) {
    $names = [
        'tech' => 'Tecnologia',
        'design' => 'Design',
        'business' => 'Negócios',
        'marketing' => 'Marketing',
        'data' => 'Análise de Dados',
        'mobile' => 'Desenvolvimento Mobile',
        'programming' => 'Programação'
    ];
    return isset($names[$category]) ? $names[$category] : 'Geral';
}

function getGradientStyle($gradient) {
    if (!empty($gradient)) {
        $colors = json_decode($gradient, true);
        if (is_array($colors) && count($colors) >= 2) {
            return "background: linear-gradient(135deg, {$colors[0]}, {$colors[1]});";
        }
    }
    $defaultGradients = [
        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
        'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
    ];
    return 'background: ' . $defaultGradients[array_rand($defaultGradients)] . ';';
}

function formatDuration($duration) {
    if (!$duration) return '40h';
    return $duration;
}

function generateCourseLink($curso, $perfil) {
    // Verificar se o ID do curso existe
    if (!isset($curso['id']) || empty($curso['id'])) {
        return ['link' => '#', 'texto' => 'Indisponível', 'disabled' => true];
    }
    
    // Definir o link e texto baseado no perfil
    switch ($perfil) {
        case 'usuarioGeral':
            return [
                'link' => 'curso.php?id=' . urlencode($curso['id']),
                'texto' => 'Ver Detalhes',
                'disabled' => false
            ];
        case 'instrutor':
            return [
                'link' => 'curso.php?id=' . urlencode($curso['id']),
                'texto' => 'Gerenciar Curso',
                'disabled' => false
            ];
        case 'admin':
            return [
                'link' => 'curso.php?id=' . urlencode($curso['id']),
                'texto' => 'Acessar Curso',
                'disabled' => false
            ];
        default:
            return [
                'link' => 'curso.php?id=' . urlencode($curso['id']),
                'texto' => 'Ver Curso',
                'disabled' => false
            ];
    }
}

include_once '../arquivosReuso/header.php';
?>

<link rel="icon" href="src/icone.ico" type="image/x-icon">
<link rel="stylesheet" href="listagemCursos.css">

<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">
                <i class="fas fa-graduation-cap"></i>
                Catálogo de Cursos
            </h1>
            <p class="hero-subtitle">
                Descubra conhecimentos que transformam carreiras e abrem novas oportunidades
            </p>
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number"><?php echo count($cursos); ?>+</span>
                    <span class="hero-stat-label">Cursos Disponíveis</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">50k+</span>
                    <span class="hero-stat-label">Estudantes Ativos</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">95%</span>
                    <span class="hero-stat-label">Taxa de Satisfação</span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <section class="filters-section">
        <div class="filters-container">
            <div class="filters-header">
                <i class="fas fa-sliders-h"></i>
                <h3>Filtros de Busca</h3>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="search">
                        <i class="fas fa-search"></i> Buscar Curso
                    </label>
                    <input type="text" id="search" placeholder="Digite o nome do curso...">
                </div>
                <div class="filter-group">
                    <label for="category">
                        <i class="fas fa-tags"></i> Categoria
                    </label>
                    <select id="category">
                        <option value="">Todas as categorias</option>
                        <option value="tech">Tecnologia</option>
                        <option value="design">Design</option>
                        <option value="business">Negócios</option>
                        <option value="marketing">Marketing</option>
                        <option value="data">Análise de Dados</option>
                        <option value="mobile">Desenvolvimento Mobile</option>
                        <option value="programming">Programação</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="level">
                        <i class="fas fa-layer-group"></i> Nível
                    </label>
                    <select id="level">
                        <option value="">Todos os níveis</option>
                        <option value="Iniciante">Iniciante</option>
                        <option value="Intermediário">Intermediário</option>
                        <option value="Avançado">Avançado</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="sort">
                        <i class="fas fa-sort"></i> Ordenar por
                    </label>
                    <select id="sort">
                        <option value="newest">Mais recentes</option>
                        <option value="oldest">Mais antigos</option>
                        <option value="rating">Melhor avaliados</option>
                        <option value="students">Mais populares</option>
                        <option value="alphabetical">Ordem alfabética</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <section class="courses-section">
        <div class="section-header">
            <div class="results-info">
                <span class="results-count" id="resultsCount"><?php echo count($cursos); ?></span>
                <span class="results-text">cursos encontrados</span>
            </div>
            <div class="view-toggle">
                <button class="view-btn active" data-view="grid">
                    <i class="fas fa-th"></i>
                </button>
                <button class="view-btn" data-view="list">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        <div class="loading" id="loadingState">
            <div class="spinner"></div>
            <p>Carregando cursos...</p>
        </div>

        <div class="cursos-grid" id="cursosGrid">
            <?php if (empty($cursos)): ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h3>Nenhum curso encontrado</h3>
                    <p>Não há cursos cadastrados no momento. Volte em breve para novidades!</p>
                </div>
            <?php else: ?>
                <?php foreach ($cursos as $index => $curso): ?>
                    <?php $linkInfo = generateCourseLink($curso, $meuPerfil); ?>
                    <article class="course-card" 
                             data-title="<?php echo strtolower(htmlspecialchars($curso['titulo'])); ?>"
                             data-category="<?php echo htmlspecialchars($curso['category'] ?? ''); ?>"
                             data-level="<?php echo htmlspecialchars($curso['level'] ?? ''); ?>"
                             data-rating="<?php echo htmlspecialchars($curso['rating'] ?? '4.5'); ?>"
                             data-students="<?php echo htmlspecialchars($curso['students'] ?? '0'); ?>"
                             data-created="<?php echo htmlspecialchars($curso['criado_em']); ?>"
                             data-course-id="<?php echo htmlspecialchars($curso['id']); ?>">
                        
                        <div class="course-badges">
                            <?php if ($index === 0): ?>
                                <span class="course-badge popular">Mais Popular</span>
                            <?php elseif ($index === 1): ?>
                                <span class="course-badge">Recomendado</span>
                            <?php elseif ($index === 2): ?>
                                <span class="course-badge new">Novo</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="course-image">
                            <div class="gradient-bg" style="<?php echo getGradientStyle($curso['gradient'] ?? ''); ?>">
                                <i class="<?php echo !empty($curso['icon']) ? str_replace('-outline', '', htmlspecialchars($curso['icon'])) : 'fas fa-book'; ?>"></i>
                            </div>
                        </div>
                        
                        <div class="course-content">
                            <div class="course-category">
                                <i class="<?php echo getCategoryIcon($curso['category'] ?? ''); ?>"></i>
                                <?php echo getCategoryName($curso['category'] ?? ''); ?>
                            </div>
                            
                            <h2 class="course-title" data-original="<?php echo htmlspecialchars($curso['titulo']); ?>">
                                <?php echo htmlspecialchars($curso['titulo']); ?>
                            </h2>
                            
                            <p class="course-description">
                                <?php echo htmlspecialchars(!empty($curso['descricao']) ? $curso['descricao'] : 'Aprenda com os melhores profissionais do mercado e desenvolva habilidades essenciais para sua carreira.'); ?>
                            </p>
                            
                            <div class="course-instructor">
                                <i class="fas fa-user-tie"></i>
                                <strong>Instrutor:</strong> <?php echo htmlspecialchars($curso['instrutor_nome']); ?>
                            </div>
                            
                            <div class="course-details">
                                <div class="course-info">
                                    <span>
                                        <i class="fas fa-clock"></i> 
                                        <?php echo formatDuration($curso['duration'] ?? ''); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-star"></i> 
                                        <?php echo htmlspecialchars($curso['rating'] ?? '4.5'); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-users"></i> 
                                        <?php echo number_format($curso['students'] ?? 0); ?>
                                    </span>
                                </div>
                                <div class="course-level"><?php echo htmlspecialchars($curso['level'] ?? 'Intermediário'); ?></div>
                            </div>
                            
                            <a href="<?php echo htmlspecialchars($linkInfo['link']); ?>" 
                               class="course-button <?php echo $linkInfo['disabled'] ? 'disabled' : ''; ?>"
                               <?php echo $linkInfo['disabled'] ? 'onclick="return false;"' : ''; ?>
                               data-course-id="<?php echo htmlspecialchars($curso['id']); ?>">
                                <i class="fas fa-play"></i>
                                <?php echo htmlspecialchars($linkInfo['texto']); ?>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const levelSelect = document.getElementById('level');
    const sortSelect = document.getElementById('sort');
    const cursosGrid = document.getElementById('cursosGrid');
    const resultsCount = document.getElementById('resultsCount');
    const loadingState = document.getElementById('loadingState');
    const viewButtons = document.querySelectorAll('.view-btn');
    
    const allCards = Array.from(cursosGrid.children).filter(card => 
        !card.classList.contains('empty-state') && !card.classList.contains('loading')
    );

    // Debug: Adicionar logs para verificar se os links estão funcionando
    document.querySelectorAll('.course-button').forEach(button => {
        button.addEventListener('click', function(e) {
            const courseId = this.getAttribute('data-course-id');
            const href = this.getAttribute('href');
            
            console.log('Clique no botão do curso:', {
                courseId: courseId,
                href: href,
                isDisabled: this.classList.contains('disabled')
            });
            
            if (this.classList.contains('disabled')) {
                e.preventDefault();
                alert('Este curso não está disponível no momento.');
                return false;
            }
            
            if (href === '#' || !href) {
                e.preventDefault();
                alert('Link do curso não está configurado corretamente.');
                return false;
            }
        });
    });

    function showLoading() {
        loadingState.classList.add('show');
        cursosGrid.style.display = 'none';
    }

    function hideLoading() {
        loadingState.classList.remove('show');
        cursosGrid.style.display = 'grid';
    }

    function filterAndSort() {
        showLoading();
        
        setTimeout(() => {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const selectedCategory = categorySelect.value;
            const selectedLevel = levelSelect.value;
            const sortBy = sortSelect.value;

            let filteredCards = allCards.filter(card => {
                const title = card.dataset.title || '';
                const category = card.dataset.category || '';
                const level = card.dataset.level || '';

                const matchesSearch = !searchTerm || title.includes(searchTerm);
                const matchesCategory = !selectedCategory || category === selectedCategory;
                const matchesLevel = !selectedLevel || level === selectedLevel;

                return matchesSearch && matchesCategory && matchesLevel;
            });

            filteredCards.sort((a, b) => {
                switch(sortBy) {
                    case 'oldest':
                        return new Date(a.dataset.created) - new Date(b.dataset.created);
                    case 'rating':
                        return parseFloat(b.dataset.rating || 0) - parseFloat(a.dataset.rating || 0);
                    case 'students':
                        return parseInt(b.dataset.students || 0) - parseInt(a.dataset.students || 0);
                    case 'alphabetical':
                        return (a.dataset.title || '').localeCompare(b.dataset.title || '');
                    case 'newest':
                    default:
                        return new Date(b.dataset.created) - new Date(a.dataset.created);
                }
            });

            cursosGrid.innerHTML = '';

            if (filteredCards.length > 0) {
                filteredCards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    cursosGrid.appendChild(card);
                    
                    setTimeout(() => {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 50);
                });
            } else {
                cursosGrid.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>Nenhum curso encontrado</h3>
                        <p>Tente ajustar os filtros de busca ou explore outras categorias.</p>
                    </div>
                `;
            }

            resultsCount.textContent = filteredCards.length;
            hideLoading();
        }, 300);
    }

    searchInput.addEventListener('input', debounce(filterAndSort, 300));
    categorySelect.addEventListener('change', filterAndSort);
    levelSelect.addEventListener('change', filterAndSort);
    sortSelect.addEventListener('change', filterAndSort);

    viewButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            viewButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const view = btn.dataset.view;
            if (view === 'list') {
                cursosGrid.style.gridTemplateColumns = '1fr';
                cursosGrid.querySelectorAll('.course-card').forEach(card => {
                    card.style.flexDirection = 'row';
                    card.style.height = 'auto';
                    const img = card.querySelector('.course-image');
                    if (img) img.style.width = '200px';
                });
            } else {
                cursosGrid.style.gridTemplateColumns = 'repeat(auto-fit, minmax(350px, 1fr))';
                cursosGrid.querySelectorAll('.course-card').forEach(card => {
                    card.style.flexDirection = 'column';
                    card.style.height = 'auto';
                    const img = card.querySelector('.course-image');
                    if (img) img.style.width = '100%';
                });
            }
        });
    });

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const courseCards = document.querySelectorAll('.course-card');
    courseCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
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
    });

    courseCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    const coursesTitles = allCards.map(card => 
        card.querySelector('.course-title')?.textContent || ''
    ).filter(Boolean);

    searchInput.addEventListener('focus', function() {
        this.setAttribute('list', 'course-suggestions');
        
        if (!document.getElementById('course-suggestions')) {
            const datalist = document.createElement('datalist');
            datalist.id = 'course-suggestions';
            coursesTitles.forEach(title => {
                const option = document.createElement('option');
                option.value = title;
                datalist.appendChild(option);
            });
            document.body.appendChild(datalist);
        }
    });

    function highlightSearchTerm(text, term) {
        if (!term) return text;
        const regex = new RegExp(`(${term})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    searchInput.addEventListener('input', function() {
        const term = this.value.trim();
        setTimeout(() => {
            cursosGrid.querySelectorAll('.course-title').forEach(title => {
                const originalText = title.getAttribute('data-original') || title.textContent;
                if (!title.getAttribute('data-original')) {
                    title.setAttribute('data-original', originalText);
                }
                title.innerHTML = highlightSearchTerm(originalText, term);
            });
        }, 350);
    });
});

// Adicionar estilos dinâmicos para o highlight
const style = document.createElement('style');
style.textContent = `
    mark {
        background: linear-gradient(135deg, #fef3c7, #fbbf24);
        color: #92400e;
        padding: 0.125rem 0.25rem;
        border-radius: 4px;
        font-weight: 600;
    }
    
    .course-card:hover mark {
        background: linear-gradient(135deg, #ddd6fe, #8b5cf6);
        color: white;
    }
    
    .course-button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #6b7280;
        pointer-events: none;
    }
    
    .course-button.disabled:hover {
        background: #6b7280;
        transform: none;
    }
`;
document.head.appendChild(style);
</script>

<?php
include_once '../arquivosReuso/footer.php';
?>