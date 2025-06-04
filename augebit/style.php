<?php

// Dados dos certificados
$certificates = [
    [
        'title' => 'Python',
        'subtitle' => 'Fundamentos da Linguagem',
        'description' => 'Certificado que comprova conhecimento em Python, desde conceitos básicos até programação avançada, incluindo estruturas de dados e frameworks.',
        'image' => 'fotos/python.png',
        'date' => '2024',
        'duration' => '40 horas'
    ],
    [
        'title' => 'Machine Learning',
        'subtitle' => 'Inteligência Artificial',
        'description' => 'Certificado em Machine Learning, abordando algoritmos de aprendizado, modelos preditivos e implementação prática de IA.',
        'image' => 'fotos/machinelearning.png',
        'date' => '2024',
        'duration' => '60 horas'
    ],
    [
        'title' => 'React',
        'subtitle' => 'Desenvolvimento Frontend',
        'description' => 'Certificado em React, cobrindo componentes, hooks, gerenciamento de estado e desenvolvimento de aplicações modernas.',
        'image' => 'fotos/react.png',
        'date' => '2024',
        'duration' => '45 horas'
    ],
    [
        'title' => 'Modern JavaScript',
        'subtitle' => 'ES6+ e Frameworks',
        'description' => 'Certificado em JavaScript moderno, incluindo ES6+, async/await, modules, APIs e desenvolvimento web avançado.',
        'image' => 'fotos/javascript.png',
        'date' => '2024',
        'duration' => '35 horas'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Certificados - AUGEBIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #1e293b;
        }

        .certificate-card {
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .certificate-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #cbd5e1;
        }

        .header-nav a {
            position: relative;
            padding-bottom: 4px;
            transition: all 0.3s ease;
        }

        .header-nav a:hover {
            color: #6366f1;
        }

        .header-nav a:hover::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 1px;
        }

        .stats-card {
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1),
                0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .progress-bar {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }

        .title-gradient {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-6 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img src="fotos/logoofc.png" alt="AUGEBIT" class="h-10" />
                </div>
                <nav class="header-nav hidden md:flex space-x-8">
                    <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Cursos</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Certificados</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Perfil</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-16">
        <!-- Title Section -->
        <div class="text-center mb-16">
            <h1 class="text-5xl font-semibold mb-4 title-gradient">Meus Certificados</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto font-normal">
                Explore os certificados conquistados e comprove suas habilidades em tecnologia
            </p>
        </div>

        <!-- Stats Section -->
        <section class="mb-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="stats-card rounded-xl p-6 text-center">
                    <div class="text-3xl font-semibold text-indigo-600 mb-1">
                        <?php echo count($certificates); ?>
                    </div>
                    <div class="text-gray-600 font-normal">Certificados</div>
                </div>
                <div class="stats-card rounded-xl p-6 text-center">
                    <div class="text-3xl font-semibold text-purple-600 mb-1">180</div>
                    <div class="text-gray-600 font-normal">Horas Totais</div>
                </div>
                <div class="stats-card rounded-xl p-6 text-center">
                    <div class="text-3xl font-semibold text-green-600 mb-1">100%</div>
                    <div class="text-gray-600 font-normal">Aproveitamento</div>
                </div>
                <div class="stats-card rounded-xl p-6 text-center">
                    <div class="text-3xl font-semibold text-blue-600 mb-1">2024</div>
                    <div class="text-gray-600 font-normal">Ano Atual</div>
                </div>
            </div>
        </section>

        <!-- Certificates Grid -->
        <section class="pb-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                <?php foreach ($certificates as $index => $cert): ?>
                <div class="certificate-card rounded-2xl p-8 group flex flex-col justify-between h-full">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-40 h-40 bg-gray-100 rounded-xl flex items-center justify-center overflow-hidden">
                            <img src="<?php echo $cert['image']; ?>" alt="<?php echo $cert['title']; ?>" class="w-full h-full object-cover" />
                        </div>
                        <div class="bg-gray-100 text-gray-700 px-4 py-2 rounded-full text-sm font-medium">
                            <?php echo $cert['date']; ?>
                        </div>
                    </div>

                    <h3 class="text-2xl font-semibold mb-2 text-gray-900 group-hover:text-indigo-600 transition">
                        <?php echo $cert['title']; ?>
                    </h3>

                    <p class="text-lg text-indigo-600 mb-4 font-medium">
                        <?php echo $cert['subtitle']; ?>
                    </p>

                    <p class="text-gray-600 mb-6 leading-relaxed font-normal">
                        <?php echo $cert['description']; ?>
                    </p>

                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <?php echo $cert['duration']; ?>
                            </span>
                        </div>

                        <button class="btn-primary text-white px-6 py-3 rounded-lg font-medium">
                            Visualizar
                        </button>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-6">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="progress-bar h-2 rounded-full" style="width: 100%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-2">
                            <span class="font-normal">Progresso</span>
                            <span class="font-medium">100%</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <script>
        // Animação de entrada dos cards
        window.addEventListener('load', function () {
            const cards = document.querySelectorAll('.certificate-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease';

                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });

        // Efeito nos botões
        document.querySelectorAll('.btn-primary').forEach(button => {
            button.addEventListener('click', function (e) {
                // Adicione aqui a lógica para visualizar o certificado
                console.log('Visualizar certificado');
            });
        });
    </script>
</body>
</html>
<?php include 'arquivosReuso/footer.php'; ?>
