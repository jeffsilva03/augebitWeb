<?php
// 1) Chamamos session_start() o mais cedo possível
session_start();

// 2) Verificamos se o usuário está logado e puxamos o perfil
if (!isset($_SESSION['usuario_id'], $_SESSION['perfil'])) {
    header('Location: ../login/form_login.php');
    exit;
}
$meuPerfil = $_SESSION['perfil']; //

// Incluir arquivo de conexão
require_once '../arquivosReuso/conexao.php';

// Buscar todos os cursos com informações do instrutor
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

// Função para mapear categoria para ícone
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

// Função para mapear categoria para nome em português
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

// Função para gerar imagem padrão baseada no gradiente
function getGradientStyle($gradient) {
    if (!empty($gradient)) {
        $colors = json_decode($gradient, true);
        if (is_array($colors) && count($colors) >= 2) {
            return "background: linear-gradient(135deg, {$colors[0]}, {$colors[1]});";
        }
    }
    // Gradientes mais variados baseados na categoria
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

// Função para formatar duração
function formatDuration($duration) {
    if (!$duration) return '40h';
    return $duration;
}

// Incluir header
include_once '../arquivosReuso/header.php';
?>

<link rel="stylesheet" href="listagemCursos.css">

<!-- Hero Section -->
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
    <!-- Seção de Filtros -->
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

    <!-- Seção de Cursos -->
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

        <!-- Loading State -->
        <div class="loading" id="loadingState">
            <div class="spinner"></div>
            <p>Carregando cursos...</p>
        </div>

        <!-- Grid de Cursos -->
        <div class="cursos-grid" id="cursosGrid">
            <?php if (empty($cursos)): ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h3>Nenhum curso encontrado</h3>
                    <p>Não há cursos cadastrados no momento. Volte em breve para novidades!</p>
                </div>
            <?php else: ?>
                <?php foreach ($cursos as $index => $curso): ?>
                    <article class="course-card" 
                             data-title="<?php echo strtolower(htmlspecialchars($curso['titulo'])); ?>"
                             data-category="<?php echo htmlspecialchars($curso['category'] ?? ''); ?>"
                             data-level="<?php echo htmlspecialchars($curso['level'] ?? ''); ?>"
                             data-rating="<?php echo htmlspecialchars($curso['rating'] ?? '4.5'); ?>"
                             data-students="<?php echo htmlspecialchars($curso['students'] ?? '0'); ?>"
                             data-created="<?php echo htmlspecialchars($curso['criado_em']); ?>">
                        
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
                            
                            <h2 class="course-title"><?php echo htmlspecialchars($curso['titulo']); ?></h2>
                            
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
                            
                          <?php
// Determinar o link baseado no perfil do usuário
$linkCurso = ($meuPerfil === 'usuarioGeral') 
    ? "../curso/usuarioGeral/pagamento-curso.php?id=" . $curso['id']
    : "curso-detalhes.php?id=" . $curso['id'];
?>
<a href="<?php echo $linkCurso; ?>" class="course-button">
    <i class="fas fa-play"></i>
    <?php echo ($meuPerfil === 'usuarioGeral') ? 'Ver Detalhes' : 'Acessar Curso'; ?>
</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
    // Funcionalidade de filtros e busca
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

        // Função para mostrar loading
        function showLoading() {
            loadingState.classList.add('show');
            cursosGrid.style.display = 'none';
        }

        // Função para esconder loading
        function hideLoading() {
            loadingState.classList.remove('show');
            cursosGrid.style.display = 'grid';
        }

        // Função principal de filtro e ordenação
        function filterAndSort() {
            showLoading();
            
            // Simular delay de loading para UX
            setTimeout(() => {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const selectedCategory = categorySelect.value;
                const selectedLevel = levelSelect.value;
                const sortBy = sortSelect.value;

                // Filtrar cards
                let filteredCards = allCards.filter(card => {
                    const title = card.dataset.title || '';
                    const category = card.dataset.category || '';
                    const level = card.dataset.level || '';

                    const matchesSearch = !searchTerm || title.includes(searchTerm);
                    const matchesCategory = !selectedCategory || category === selectedCategory;
                    const matchesLevel = !selectedLevel || level === selectedLevel;

                    return matchesSearch && matchesCategory && matchesLevel;
                });

                // Ordenar cards
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

                // Limpar grid
                cursosGrid.innerHTML = '';

                // Adicionar cards filtrados e ordenados
                if (filteredCards.length > 0) {
                    filteredCards.forEach((card, index) => {
                        // Adicionar animação de entrada
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        cursosGrid.appendChild(card);
                        
                        // Animar entrada
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

                // Atualizar contador
                resultsCount.textContent = filteredCards.length;
                
                hideLoading();
            }, 300);
        }

        // Event listeners para filtros
        searchInput.addEventListener('input', debounce(filterAndSort, 300));
        categorySelect.addEventListener('change', filterAndSort);
        levelSelect.addEventListener('change', filterAndSort);
        sortSelect.addEventListener('change', filterAndSort);

        // Toggle de visualização
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

        // Função debounce para otimizar performance
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

        // Animação de hover nos cards
        const courseCards = document.querySelectorAll('.course-card');
        courseCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Scroll suave para filtros ao carregar
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        // Observar cards para animação de scroll
        courseCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // Auto-complete para busca (opcional)
        const coursesTitles = allCards.map(card => 
            card.querySelector('.course-title')?.textContent || ''
        ).filter(Boolean);

        searchInput.addEventListener('focus', function() {
            this.setAttribute('list', 'course-suggestions');
            
            // Criar datalist se não existir
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

        // Destacar termo de busca nos resultados
        function highlightSearchTerm(text, term) {
            if (!term) return text;
            const regex = new RegExp(`(${term})`, 'gi');
            return text.replace(regex, '<mark>$1</mark>');
        }

        // Aplicar destaque quando houver busca
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
    `;
    document.head.appendChild(style);
</script>

<?php
// Incluir footer
include_once '../arquivosReuso/footer.php';
?>