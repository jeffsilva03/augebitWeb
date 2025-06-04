<?php 

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Acompanhamento - AUGEBIT</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            min-height: 100vh;
            padding: 30px 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }

        .logo {
            height: 40px;
        }

        .nav {
            display: flex;
            gap: 30px;
        }

        .nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 16px;
        }

        .nav a:hover {
            color: #6366f1;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
        }

        .student-card {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 15px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            color: white;
            max-width: 600px;
        }

        .student-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .student-info h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .student-info p {
            font-size: 14px;
            opacity: 0.9;
        }

        .stats-section {
            display: grid;
            grid-template-columns: 300px 300px 1fr;
            gap: 30px;
            margin-bottom: 40px;
            max-width: 1200px;
        }

        .stat-card {
        flex: 1;
        background: #fff;
        border-radius: 8px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-shadow: 0 1px 5px rgba(0,0,0,0.05);
        }


        .stat-card h4 {
            font-size: 16px;
            font-weight: 500;
            color: #666;
            margin-bottom: 10px;
        }

        .stat-percentage {
            font-size: 36px;
            font-weight: 700;
            color: #333;
        }

        .evaluation-card {
            position: relative;
        }

        .evaluation-chart {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            position: relative;
        }

        .evaluation-legend {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        .legend-excellent {
            background-color: #22c55e;
        }

        .legend-good {
            background-color: #94a3b8;
        }

        .section {
            background-color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
        }

        .table-container {
            overflow-x: auto;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .show-more {
            color: #6366f1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .table th {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .table td {
            color: #6b7280;
            font-size: 14px;
        }

        .table tr:hover {
            background-color: #f9fafb;
        }

        .grade {
            font-weight: 600;
            color: #059669;
        }

        .donut-chart {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: conic-gradient(#22c55e 0deg 252deg, #e5e7eb 252deg 360deg);
            position: relative;
        }

        .donut-chart::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
        }
    </style>
</head>
<body>
 
    <?php
    
    // Dados do aluno (normalmente viriam do banco de dados)
    $student = [
        'name' => 'Thaynara Oliveira',
        'course' => 'Curso: Excel',
        'period' => 'Período: Tarde'
    ];

    $stats = [
        'excel' => 86,
        'word' => 70
    ];

    $classes = [
        [
            'course' => 'Informática Básica - Excel',
            'date' => '11/11/2025 às 11:00 - 13:00',
            'grade' => '10,00'
        ],
        [
            'course' => 'Informática Básica - Excel',
            'date' => '11/11/2025 às 11:00 - 13:00',
            'grade' => '10,00'
        ],
        [
            'course' => 'Informática Básica - Excel',
            'date' => '11/11/2025 às 11:00 - 13:00',
            'grade' => '10,00'
        ],
        [
            'course' => 'Informática Básica - Excel',
            'date' => '11/11/2025 às 11:00 - 13:00',
            'grade' => '10,00'
        ],
        [
            'course' => 'Informática Básica - Excel',
            'date' => '11/11/2025 às 11:00 - 13:00',
            'grade' => '10,00'
        ]
    ];

    $makeup_classes = [
        [
            'course' => 'Informática Básica - Excel',
            'date' => '11/11/2025 às 11:00 - 13:00',
            'grade' => '10,00'
        ],
        [
            'course' => 'Informática Básica - Excel',
            'date' => '11/11/2025 às 11:00 - 13:00',
            'grade' => '10,00'
        ],
        [
            'course' => 'Informática Básica - Excel',
            'date' => '11/11/2025 às 11:00 - 13:00',
            'grade' => '10,00'
        ],
        [
            'course' => 'Informática Básica - Excel',
            'date' => '11/11/2025 às 11:00 - 13:00',
            'grade' => '10,00'
        ],
        [
            'course' => 'Informática Básica - Excel',
            'date' => '11/11/2025 às 11:00 - 13:00',
            'grade' => '10,00'
        ]
    ];
    ?>

    <div class="container">
        

        <h1 class="page-title">Ficha de Acompanhamento</h1>

        <div class="student-card">
            <div class="student-avatar">
                <img src="fotos/augebitt.png" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%;">
            </div>
            <div class="student-info">
                <h3><?php echo $student['name']; ?></h3>
                <p><?php echo $student['course']; ?></p>
                <p><?php echo $student['period']; ?></p>
            </div>
        </div>

        <div class="stats-section">
            <div class="stat-card">
                <h4>Excel</h4>
                <div class="stat-percentage"><?php echo $stats['excel']; ?>%</div>
            </div>
            <div class="stat-card">
                <h4>Word</h4>
                <div class="stat-percentage"><?php echo $stats['word']; ?>%</div>
            </div>
            <div class="stat-card evaluation-card">
                <h4>Avaliação do Aluno</h4>
                <div class="evaluation-chart">
                    <div class="donut-chart"></div>
                </div>
                <div class="evaluation-legend">
                    <div class="legend-item">
                        <div class="legend-color legend-excellent"></div>
                        <span>Excelente</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color legend-good"></div>
                        <span>Bom</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Históricos Aulas</h2>
                <a href="#" class="show-more">Mostrar mais ></a>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Data/Horário</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?php echo $class['course']; ?></td>
                            <td><?php echo $class['date']; ?></td>
                            <td class="grade"><?php echo $class['grade']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Históricos Reposição Aulas</h2>
                <a href="#" class="show-more">Mostrar mais ></a>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Data/Horário</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($makeup_classes as $class): ?>
                        <tr>
                            <td><?php echo $class['course']; ?></td>
                            <td><?php echo $class['date']; ?></td>
                            <td class="grade"><?php echo $class['grade']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<?php include '../arquivosReuso/footer.php'; ?>