<?php
session_start();
require '../conexaoBD/conexao.php';
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'adm') {
  header('Location: ../registro/form_login.php'); exit;
}

// Contadores
$totalUsuarios      = $conexao->query("SELECT COUNT(*) FROM cadastro")->fetch_row()[0];
$usuariosComuns     = $conexao->query("SELECT COUNT(*) FROM cadastro WHERE perfil='usuario'")->fetch_row()[0];
$instrutores        = $conexao->query("SELECT COUNT(*) FROM cadastro WHERE perfil='instrutor'")->fetch_row()[0];
$administradores    = $conexao->query("SELECT COUNT(*) FROM cadastro WHERE perfil='adm'")->fetch_row()[0];
$totalCursos        = $conexao->query("SELECT COUNT(*) FROM cursos")->fetch_row()[0];
$totalInscricoes    = $conexao->query("SELECT COUNT(*) FROM inscricoes")->fetch_row()[0];

// Dados para gráficos
$perfilsData = [
  'Usuários'      => $usuariosComuns,
  'Instrutores'   => $instrutores,
  'Administradores'=> $administradores,
];
$cursoData = [
  'Cursos'        => $totalCursos,
  'Inscrições'    => $totalInscricoes,
];

// Tendências de crescimento (simulado - você pode substituir por dados reais)
$crescimentoMensal = [
  'Jan' => 15, 'Fev' => 21, 'Mar' => 28, 'Abr' => 35, 
  'Mai' => 42, 'Jun' => 55, 'Jul' => 68, 'Ago' => 82, 
  'Set' => 96, 'Out' => 110, 'Nov' => 125, 'Dez' => $totalInscricoes
];

// Cálculo de taxas para KPIs
$taxaConclusao = 78; // Simulado - substituir por cálculo real
$taxaEngajamento = 85; // Simulado - substituir por cálculo real
$cursosAtivos = intval($totalCursos * 0.75); // Simulado
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Painel de ADM</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- ApexCharts para gráficos avançados -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="pagina-painel theme-light" id="app">
  <?php include 'header.php'; ?>

  <main class="container dashboard-container animate__animated animate__fadeIn">
    <div class="dashboard-header">
      <h1 class="titulo-painel">Dashboard</h1>
      
      <div class="dashboard-actions">
        <button class="btn-theme" id="themeToggle" aria-label="Alternar tema">
          <i data-lucide="moon" class="icon-theme"></i>
        </button>
        
        <button class="btn-outline">
          <i data-lucide="download" class="icon-btn"></i>
          Exportar dados
        </button>
      </div>
    </div>
    
    <div class="info-banner">
      <i data-lucide="info" class="icon-info"></i>
      <p>Bem-vindo ao dashboard! Explore os dados atualizados em tempo real.</p>
      <button class="btn-close-banner" aria-label="Fechar">
        <i data-lucide="x" class="icon-close"></i>
      </button>
    </div>

    <div class="dashboard-overview">
      <div class="card-kpi">
        <div class="kpi-icon-wrapper bg-roxo-gradient">
          <i data-lucide="zap" class="kpi-icon"></i>
        </div>
        <div class="kpi-data">
          <h3 class="kpi-value"><?= $taxaEngajamento ?>%</h3>
          <p class="kpi-label">Engajamento</p>
          <div class="kpi-trend positive">
            <i data-lucide="trending-up" class="trend-icon"></i>
            <span>+5.2%</span>
          </div>
        </div>
      </div>
      
      <div class="card-kpi">
        <div class="kpi-icon-wrapper bg-azul-gradient">
          <i data-lucide="check-circle" class="kpi-icon"></i>
        </div>
        <div class="kpi-data">
          <h3 class="kpi-value"><?= $taxaConclusao ?>%</h3>
          <p class="kpi-label">Taxa de conclusão</p>
          <div class="kpi-trend positive">
            <i data-lucide="trending-up" class="trend-icon"></i>
            <span>+2.7%</span>
          </div>
        </div>
      </div>
      
      <div class="card-kpi">
        <div class="kpi-icon-wrapper bg-verde-gradient">
          <i data-lucide="book-open" class="kpi-icon"></i>
        </div>
        <div class="kpi-data">
          <h3 class="kpi-value"><?= $cursosAtivos ?></h3>
          <p class="kpi-label">Cursos ativos</p>
          <div class="kpi-trend positive">
            <i data-lucide="trending-up" class="trend-icon"></i>
            <span>+3</span>
          </div>
        </div>
      </div>
      
      <div class="card-kpi">
        <div class="kpi-icon-wrapper bg-laranja-gradient">
          <i data-lucide="users" class="kpi-icon"></i>
        </div>
        <div class="kpi-data">
          <h3 class="kpi-value"><?= $totalUsuarios ?></h3>
          <p class="kpi-label">Total usuários</p>
          <div class="kpi-trend positive">
            <i data-lucide="trending-up" class="trend-icon"></i>
            <span>+12</span>
          </div>
        </div>
      </div>
    </div>

    <div class="dashboard-grid">
      <div class="card-dashboard bg-azul-gradient">
        <div class="card-header">
          <i data-lucide="users" class="icon-dashboard"></i>
          <div class="card-actions">
            <i data-lucide="more-horizontal" class="icon-more"></i>
          </div>
        </div>
        <div class="card-content">
          <h2 class="counter" data-target="<?= $totalUsuarios ?>"><?= $totalUsuarios ?></h2>
          <p>Total de Usuários</p>
        </div>
        <div class="card-footer">
          <span class="badge badge-light">+8.5% este mês</span>
        </div>
      </div>
      
      <div class="card-dashboard bg-roxo-gradient">
        <div class="card-header">
          <i data-lucide="award" class="icon-dashboard"></i>
          <div class="card-actions">
            <i data-lucide="more-horizontal" class="icon-more"></i>
          </div>
        </div>
        <div class="card-content">
          <h2 class="counter" data-target="<?= $instrutores ?>"><?= $instrutores ?></h2>
          <p>Instrutores</p>
        </div>
        <div class="card-footer">
          <span class="badge badge-light">+2 este mês</span>
        </div>
      </div>
      <div class="card-dashboard bg-amarelo-gradient">
        <div class="card-header">
          <i data-lucide="shield" class="icon-dashboard"></i>
          <div class="card-actions">
            <i data-lucide="more-horizontal" class="icon-more"></i>
          </div>
        </div>
        <div class="card-content">
          <h2 class="counter" data-target="<?= $administradores ?>"><?= $administradores ?></h2>
          <p>Administradores</p>
        </div>
        <div class="card-footer">
          <span class="badge badge-light">Sem alteração</span>
        </div>
      </div>
      <div class="card-dashboard bg-ciano-gradient">
        <div class="card-header">
          <i data-lucide="book-open" class="icon-dashboard"></i>
          <div class="card-actions">
            <i data-lucide="more-horizontal" class="icon-more"></i>
          </div>
        </div>
        <div class="card-content">
          <h2 class="counter" data-target="<?= $totalCursos ?>"><?= $totalCursos ?></h2>
          <p>Cursos Disponíveis</p>
        </div>
        <div class="card-footer">
          <span class="badge badge-light">+3 este mês</span>
        </div>
      </div>
      <div class="card-dashboard bg-vermelho-gradient">
        <div class="card-header">
          <i data-lucide="clipboard-list" class="icon-dashboard"></i>
          <div class="card-actions">
            <i data-lucide="more-horizontal" class="icon-more"></i>
          </div>
        </div>
        <div class="card-content">
          <h2 class="counter" data-target="<?= $totalInscricoes ?>"><?= $totalInscricoes ?></h2>
          <p>Inscrições</p>
        </div>
        <div class="card-footer">
          <span class="badge badge-light">+15 este mês</span>
        </div>
      </div>
    </div>

   
    
    <div class="dashboard-footer">
      <p>Última atualização: <strong><?= date('d/m/Y H:i') ?></strong></p>
      <button class="btn-primary">
        <i data-lucide="refresh-cw" class="icon-btn"></i>
        Atualizar dados
      </button>
    </div>
  </main>

  <script>
    // Ativa Lucide
    document.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();
      
      // Inicializar contadores com animação
      const counters = document.querySelectorAll('.counter');
      counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 1500;
        const increment = target / (duration / 16);
        let current = 0;
        
        const updateCounter = () => {
          current += increment;
          if (current < target) {
            counter.innerText = Math.ceil(current);
            requestAnimationFrame(updateCounter);
          } else {
            counter.innerText = target;
          }
        };
        
        updateCounter();
      });
      
      // Toggle tema claro/escuro
      const themeToggle = document.getElementById('themeToggle');
      const app = document.getElementById('app');
      const themeIcon = themeToggle.querySelector('.icon-theme');
      
      themeToggle.addEventListener('click', () => {
        app.classList.toggle('theme-dark');
        
        if (app.classList.contains('theme-dark')) {
          themeIcon.setAttribute('name', 'sun');
        } else {
          themeIcon.setAttribute('name', 'moon');
        }
        
        lucide.createIcons();
      });
      
      // Fechar banner
      const infoBanner = document.querySelector('.info-banner');
      const closeBtn = document.querySelector('.btn-close-banner');
      
      if (closeBtn && infoBanner) {
        closeBtn.addEventListener('click', () => {
          infoBanner.classList.add('fade-out');
          setTimeout(() => {
            infoBanner.style.display = 'none';
          }, 300);
        });
      }
    });

    // Dados para gráficos
    const perfisLabels = <?= json_encode(array_keys($perfilsData)) ?>;
    const perfisValues = <?= json_encode(array_values($perfilsData)) ?>;
    const cursoLabels = <?= json_encode(array_keys($cursoData)) ?>;
    const cursoValues = <?= json_encode(array_values($cursoData)) ?>;
    const crescimentoLabels = <?= json_encode(array_keys($crescimentoMensal)) ?>;
    const crescimentoValues = <?= json_encode(array_values($crescimentoMensal)) ?>;

    // Pie chart de perfis (aprimorado)
    new Chart(document.getElementById('perfilChart'), {
      type: 'doughnut',
      data: {
        labels: perfisLabels,
        datasets: [{
          data: perfisValues,
          backgroundColor: [
            'rgba(90, 97, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(139, 92, 246, 0.8)'
          ],
          borderColor: [
            'rgba(90, 97, 246, 1)',
            'rgba(16, 185, 129, 1)',
            'rgba(139, 92, 246, 1)'
          ],
          borderWidth: 2,
          hoverOffset: 15
        }]
      },
      options: {
        responsive: true,
        cutout: '65%',
        plugins: {
          legend: { 
            position: 'bottom',
            labels: {
              padding: 20,
              usePointStyle: true,
              pointStyle: 'circle'
            }
          },
          tooltip: {
            backgroundColor: 'rgba(17, 24, 39, 0.9)',
            padding: 12,
            bodyFont: {
              family: "'Poppins', sans-serif",
              size: 14
            },
            titleFont: {
              family: "'Poppins', sans-serif",
              size: 16,
              weight: 'bold'
            },
            boxPadding: 8,
            cornerRadius: 8
          }
        }
      }
    });

    // Bar chart de cursos vs inscrições (aprimorado)
    new Chart(document.getElementById('cursoChart'), {
      type: 'bar',
      data: {
        labels: cursoLabels,
        datasets: [{
          label: 'Total',
          data: cursoValues,
          backgroundColor: [
            'rgba(16, 185, 129, 0.9)',
            'rgba(59, 130, 246, 0.9)'
          ],
          borderColor: [
            'rgba(16, 185, 129, 1)',
            'rgba(59, 130, 246, 1)'
          ],
          borderWidth: 2,
          borderRadius: 8,
          maxBarThickness: 50
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { 
            beginAtZero: true,
            grid: {
              drawBorder: false,
              color: 'rgba(107, 114, 128, 0.1)'
            },
            ticks: {
              font: {
                family: "'Poppins', sans-serif"
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                family: "'Poppins', sans-serif"
              }
            }
          }
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(17, 24, 39, 0.9)',
            padding: 12,
            bodyFont: {
              family: "'Poppins', sans-serif"
            },
            titleFont: {
              family: "'Poppins', sans-serif",
              weight: 'bold'
            },
            cornerRadius: 8
          }
        }
      }
    });
    
    // ApexCharts - Gráfico de crescimento (novo)
    const options = {
      series: [{
        name: 'Inscrições',
        data: crescimentoValues
      }],
      chart: {
        height: 350,
        type: 'area',
        fontFamily: 'Poppins, sans-serif',
        toolbar: {
          show: false
        },
        dropShadow: {
          enabled: true,
          top: 0,
          left: 0,
          blur: 3,
          color: '#000',
          opacity: 0.1
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 3
      },
      colors: ['#8b5cf6'],
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.7,
          opacityTo: 0.2,
          stops: [0, 90, 100]
        }
      },
      xaxis: {
        categories: crescimentoLabels,
        labels: {
          style: {
            fontFamily: 'Poppins, sans-serif'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            fontFamily: 'Poppins, sans-serif'
          }
        }
      },
      tooltip: {
        theme: 'dark',
        x: {
          show: true
        },
        y: {
          title: {
            formatter: function () {
              return 'Inscrições:'
            }
          }
        }
      },
      grid: {
        borderColor: 'rgba(107, 114, 128, 0.1)',
        strokeDashArray: 5
      },
      markers: {
        size: 5,
        colors: ['#8b5cf6'],
        strokeColors: '#ffffff',
        strokeWidth: 2,
        hover: {
          size: 7
        }
      }
    };

    const chart = new ApexCharts(document.getElementById("crescimentoChart"), options);
    chart.render();
    
    // Alternância dos modos de visualização do gráfico
    document.querySelectorAll('.btn-toggle').forEach(button => {
      button.addEventListener('click', () => {
        document.querySelectorAll('.btn-toggle').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        
        // Aqui você pode adicionar lógica para mudar os dados do gráfico
        const view = button.getAttribute('data-view');
        if (view === 'semanal') {
          // Atualizar para dados semanais (simulado)
          chart.updateOptions({
            xaxis: {
              categories: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom']
            }
          });
          chart.updateSeries([{
            name: 'Inscrições',
            data: [12, 15, 8, 10, 25, 18, 32]
          }]);
        } else {
          // Voltar para dados mensais
          chart.updateOptions({
            xaxis: {
              categories: crescimentoLabels
            }
          });
          chart.updateSeries([{
            name: 'Inscrições',
            data: crescimentoValues
          }]);
        }
      });
    });
  </script>
</body>
</html>