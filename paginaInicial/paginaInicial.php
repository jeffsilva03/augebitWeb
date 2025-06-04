<?php
// paginaInicial.php
ob_start();
session_start();

include '../conexaoBD/conexao.php'; // fornece $conexao (mysqli)

// inicializa variáveis
$nome = $email = $mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');

    if ($nome && filter_var($email, FILTER_VALIDATE_EMAIL) && $mensagem) {
        $sql  = "INSERT INTO contate_nos (nome, email, mensagem) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('sss', $nome, $email, $mensagem);
            if ($stmt->execute()) {
                $_SESSION['mensagem_feedback'] = 'Obrigado! Sua mensagem foi enviada.';
            } else {
                $_SESSION['mensagem_feedback'] = 'Erro ao enviar. Tente novamente mais tarde.';
            }
            $stmt->close();
        } else {
            $_SESSION['mensagem_feedback'] = 'Falha na preparação da consulta.';
        }
    } else {
        $_SESSION['mensagem_feedback'] = 'Por favor, preencha todos os campos corretamente.';
    }

    // PRG: limpa o POST e evita reenvio
    header("Location: " . $_SERVER['PHP_SELF'] . "#faleConosco");
    exit;
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AUGEBIT</title>
  <link rel="icon" href="src/icone.ico" type="image/x-icon">


  <!-- Fonte moderna -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    /* Variáveis de tema */
    /* Variáveis de tema */
:root {
  --font-base: 'Poppins', sans-serif;
  --clr-bg: #f5f6fa;
  --clr-white: #ffffff;
  --clr-dark: #2d2d2d;
  --clr-gray: #666666;
  --clr-purple: #8a2be2;
  --clr-purple-dark: rgb(14,11,17);
  --clr-orange: #fb6f49;
  --radius: 12px;
  --spacing: 1.6rem;
  --transition: 0.4s ease;
}

/* Reset */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
  color: var(--clr-dark);
  line-height: 1.6;
  scroll-behavior: smooth;
}
a { font-family: 'Poppins'; text-decoration: none; color: inherit; }
img { display: block; max-width: 100%; }

/* Utility */
.container { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }


h2.section-title {
  position: relative; text-align: center; margin-bottom: var(--spacing);
  font-size: 2.5rem; color: #8a2be2;
  opacity: 0.9;
}
h2.section-title::after {
  content: '';
  width: 80px;
  height: 4px;
  background: var(--clr-purple-dark);
  position: absolute;
  left: 50%; transform: translateX(-50%);
  bottom: -0.6rem; border-radius: 2px;
}

/* Fade-up */
.fade-up { opacity: 0; transform: translateY(20px); transition: opacity .6s, transform .6s; }
.fade-up.visible { opacity: 1; transform: translateY(0); }

/* Hero Section Aprimorada */
.heroi {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    text-align: center;
    background: linear-gradient(135deg, var(--clr-purple) 0%, var(--clr-purple-dark) 100%);
    color: var(--clr-white);
    overflow: hidden;
}

/* Background decorativo */
.heroi::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(251, 111, 73, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(138, 43, 226, 0.4) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    animation: gradientShift 8s ease-in-out infinite;
}

/* Partículas flutuantes */
.hero-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 6s; }
.particle:nth-child(2) { left: 20%; animation-delay: 1s; animation-duration: 8s; }
.particle:nth-child(3) { left: 35%; animation-delay: 2s; animation-duration: 7s; }
.particle:nth-child(4) { left: 50%; animation-delay: 0.5s; animation-duration: 9s; }
.particle:nth-child(5) { left: 65%; animation-delay: 3s; animation-duration: 6s; }
.particle:nth-child(6) { left: 80%; animation-delay: 1.5s; animation-duration: 8s; }
.particle:nth-child(7) { left: 90%; animation-delay: 2.5s; animation-duration: 7s; }

/* Formas geométricas decorativas */
.hero-shapes {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    animation: rotate 20s linear infinite;
}

.shape:nth-child(1) {
    width: 200px;
    height: 200px;
    top: 10%;
    left: -5%;
    animation-duration: 25s;
}

.shape:nth-child(2) {
    width: 150px;
    height: 150px;
    top: 60%;
    right: -10%;
    animation-duration: 30s;
    animation-direction: reverse;
}

.shape:nth-child(3) {
    width: 100px;
    height: 100px;
    top: 30%;
    right: 15%;
    animation-duration: 20s;
}

/* Conteúdo */
.hero-content {
    position: relative;
    z-index: 3;
    max-width: 800px;
    margin: 0 auto;
}

.heroi h1 {
    font-size: clamp(2.5rem, 5vw, 4.5rem);
    font-weight: 700;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.2;
    animation: slideInUp 1s ease-out 0.2s both;
}

.heroi .subtitle {
    font-size: clamp(1.1rem, 2.5vw, 1.4rem);
    margin-bottom: 0.5rem;
    font-weight: 300;
    opacity: 0.9;
    animation: slideInUp 1s ease-out 0.4s both;
}

.heroi p {
    font-size: clamp(1rem, 2vw, 1.25rem);
    margin-bottom: 3rem;
    opacity: 0.85;
    line-height: 1.6;
    animation: slideInUp 1s ease-out 0.6s both;
}

/* Botões */
.hero-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    animation: slideInUp 1s ease-out 0.8s both;
}

.botao-primario {
    position: relative;
    background: var(--clr-white);
    color: var(--clr-purple-dark);
    padding: 1rem 2.5rem;
    border-radius: var(--radius);
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    border: 2px solid transparent;
    overflow: hidden;
    z-index: 1;
    cursor: pointer;
}

.botao-primario::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s;
}

.botao-primario:hover::before {
    left: 100%;
}

.botao-primario:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0,0,0,0.25);
    background: linear-gradient(135deg, var(--clr-white) 0%, #f8f8f8 100%);
}

.botao-primario:active {
    transform: translateY(-2px) scale(1.01);
    transition: all 0.1s ease;
}

.botao-secundario {
    position: relative;
    background: transparent;
    color: var(--clr-white);
    padding: 1rem 2.5rem;
    border-radius: var(--radius);
    font-weight: 600;
    font-size: 1.1rem;
    border: 2px solid rgba(255,255,255,0.3);
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    backdrop-filter: blur(10px);
    cursor: pointer;
    overflow: hidden;
}

.botao-secundario::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    background: rgba(255,255,255,0.1);
    transition: width 0.3s ease;
    z-index: -1;
}

.botao-secundario:hover::before {
    width: 100%;
}

.botao-secundario:hover {
    transform: translateY(-4px) scale(1.02);
    border-color: var(--clr-white);
    background: rgba(255,255,255,0.15);
    box-shadow: 0 12px 30px rgba(0,0,0,0.2);
}

.botao-secundario:active {
    transform: translateY(-2px) scale(1.01);
    transition: all 0.1s ease;
}

/* Indicador de scroll */
.scroll-indicator {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    color: rgba(255,255,255,0.8);
    animation: bounce 2s infinite;
    z-index: 3;
}

.scroll-indicator span {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    font-weight: 300;
}

.scroll-arrow {
    width: 24px;
    height: 24px;
    border: 2px solid currentColor;
    border-left: none;
    border-top: none;
    transform: rotate(45deg);
}

/* Estatísticas flutuantes */
.hero-stats {
    position: absolute;
    top: 50%;
    left: 2rem;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 2rem;
    z-index: 3;
}

.stat-item {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    padding: 1rem;
    border-radius: var(--radius);
    text-align: center;
    min-width: 120px;
    border: 1px solid rgba(255,255,255,0.2);
    animation: fadeInLeft 1s ease-out 1s both;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--clr-orange);
    display: block;
}

.stat-label {
    font-size: 0.8rem;
    opacity: 0.8;
    font-weight: 300;
}

/* Animações */
@keyframes gradientShift {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.6; }
    50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes slideInUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
    40% { transform: translateX(-50%) translateY(-10px); }
    60% { transform: translateX(-50%) translateY(-5px); }
}

@keyframes fadeInLeft {
    from {
        transform: translateX(-30px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsivo */
@media (max-width: 768px) {
    .heroi {
        min-height: 100vh;
        padding: 2rem 0;
    }

    .hero-buttons {
        flex-direction: column;
        align-items: center;
    }

    .botao-primario,
    .botao-secundario {
        width: 100%;
        max-width: 280px;
    }

    .hero-stats {
        position: static;
        transform: none;
        flex-direction: row;
        justify-content: center;
        margin-top: 3rem;
        left: auto;
    }

    .stat-item {
        min-width: 100px;
        padding: 0.8rem;
    }

    .shape {
        display: none;
    }
}

@media (max-width: 480px) {
    .heroi .subtitle {
        font-size: 1rem;
    }

    .hero-stats {
        flex-wrap: wrap;
        gap: 1rem;
    }
}

        /* Seção Sobre - Nova Identidade */
        .sobre {
            position: relative;
            padding: 6rem 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            overflow: hidden;
        }

        .sobre::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.03) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-20px, -20px) rotate(180deg); }
        }

        .section-title {
            text-align: center;
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            margin-bottom: 4rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -1rem;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .sobre-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .sobre-text {
            position: relative;
        }

        .sobre-text::before {
            content: '';
            position: absolute;
            top: -2rem;
            left: -2rem;
            width: 100px;
            height: 100px;
            background: var(--gradient-secondary);
            border-radius: 50%;
            opacity: 0.1;
            z-index: -1;
        }

        .sobre-intro {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--clr-dark);
            margin-bottom: 1.5rem;
            line-height: 1.8;
            position: relative;
        }

        .sobre-description {
            font-size: 1.1rem;
            color: var(--clr-gray);
            margin-bottom: 2.5rem;
            line-height: 1.8;
        }

        .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: var(--clr-white);
            border-radius: 12px;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            border: 1px solid rgba(99, 102, 241, 0.1);
        }

        .feature-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow);
            border-color: var(--clr-primary);
        }

        .feature-icon {
            width: 24px;
            height: 24px;
            margin-right: 0.75rem;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: black;
            font-size: 0.8rem;
            font-weight: bold;
            flex-shrink: 0;
        }

        .feature-text {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--clr-dark);
        }

        .sobre-visual {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-container {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-glow);
            transform: perspective(1000px) rotateY(-5deg);
            transition: all 0.5s ease;
        }

        .image-container:hover {
            transform: perspective(1000px) rotateY(0deg) scale(1.02);
        }

        .image-container img {
            width: 100%;
            height: auto;
            display: block;
            transition: all 0.5s ease;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-primary);
            opacity: 0.1;
            transition: opacity 0.3s ease;
        }

        .image-container:hover .image-overlay {
            opacity: 0;
        }

        /* Elementos decorativos */
        .decorative-elements {
            position: absolute;
            top: 20%;
            right: 10%;
            width: 200px;
            height: 200px;
            opacity: 0.05;
            z-index: 1;
        }

        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            background: var(--gradient-primary);
        }

        .decorative-circle:nth-child(1) {
            width: 60px;
            height: 60px;
            top: 0;
            left: 0;
            animation: pulse 4s ease-in-out infinite;
        }

        .decorative-circle:nth-child(2) {
            width: 40px;
            height: 40px;
            top: 30px;
            right: 20px;
            animation: pulse 4s ease-in-out infinite 1s;
        }

        .decorative-circle:nth-child(3) {
            width: 30px;
            height: 30px;
            bottom: 10px;
            left: 40px;
            animation: pulse 4s ease-in-out infinite 2s;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.05; transform: scale(1); }
            50% { opacity: 0.15; transform: scale(1.1); }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .sobre {
                padding: 4rem 0;
            }

            .sobre-content {
                grid-template-columns: 1fr;
                gap: 3rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .image-container {
                transform: none;
                margin: 0 auto;
                max-width: 400px;
            }

            .image-container:hover {
                transform: scale(1.02);
            }

            .decorative-elements {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 1rem;
            }

            .feature-item {
                padding: 0.75rem;
            }

            .feature-text {
                font-size: 0.9rem;
            }
        }

        /* Animações de entrada */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 0.8s ease forwards;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feature-item:nth-child(1) { animation-delay: 0.1s; }
        .feature-item:nth-child(2) { animation-delay: 0.2s; }
        .feature-item:nth-child(3) { animation-delay: 0.3s; }
        .feature-item:nth-child(4) { animation-delay: 0.4s; }

/* Recursos */
.recursos { padding: 4rem 0; }
.recursos .grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap: 1.5rem; }
.recursos .card { 
    padding: 2rem; 
    border-radius: var(--radius); 
    background: var(--clr-white); 
    text-align: center; 
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94); 
    cursor: pointer;
}
.recursos .card:hover { 
    transform: translateY(-8px) scale(1.02); 
    box-shadow: 0 20px 40px rgba(0,0,0,0.15); 
}
.recursos i { font-size: 2.5rem; color: var(--clr-purple-dark); margin-bottom: 1rem; }

/* Cursos em Destaque */
#cursos { background: var(--clr-bg); padding: 4rem 0; }
#cursos .descricao { text-align: center; color: var(--clr-gray); margin-bottom: 2rem; font-size: 1rem; }
#cursos .grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap: 1.5rem; }
#cursos .card {
  position: relative; background: var(--clr-white); border-radius: var(--radius);
  box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; display: flex; flex-direction: column;
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
#cursos .card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}
#cursos .badge {
  position: absolute; top: 1rem; left: 1rem; background: var(--clr-orange);
  color: #fff; padding: .25rem .75rem; border-radius: 8px; font-size: .75rem; font-weight: 600;
}
#cursos .card img { width: 100%; height: 180px; object-fit: cover; }
#cursos .conteudo { padding: 1.5rem; flex:1; display:flex; flex-direction:column; }
#cursos .categoria {
  font-size:.875rem; color:var(--clr-purple); margin-bottom:.5rem;
  display:flex; align-items:center; gap:.25rem;
}
#cursos .conteudo h4 {
  font-size:1.25rem; margin-bottom:.75rem; color:var(--clr-dark);
}
#cursos .conteudo p {
  flex:1; color:var(--clr-gray); margin-bottom:1rem; font-size:.95rem;
}
#cursos .detalhes {
  display:flex; justify-content:space-between; align-items:center;
  font-size:.875rem; color:var(--clr-gray); margin-bottom:1rem;
}
/* Preço em destaque */
#cursos .detalhes .preco {
  font-weight:700; font-size:1.1rem; color: var(--clr-purple);
}
#cursos .detalhes .info {
  display:flex; gap:1rem;
}
#cursos .detalhes .info span {
  display:flex; align-items:center; gap:.25rem;
  color: var(--clr-gray);
}
#cursos .detalhes .info i {
  font-size:1rem; color: var(--clr-purple);
}
#cursos .botao {
  background: var(--clr-purple); 
  color: #fff; 
  padding:.75rem;
  text-align:center; 
  border-radius:var(--radius); 
  font-weight:500;
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  margin-top:auto;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
#cursos .botao::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}
#cursos .botao:hover::before {
    left: 100%;
}
#cursos .botao:hover {
  background: var(--clr-purple-dark); 
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 8px 20px rgba(138, 43, 226, 0.3);
}
#cursos .botao:active {
    transform: translateY(-1px) scale(1.01);
    transition: all 0.1s ease;
}
#cursos .ver-todos {
  display:block; 
  max-width:240px; 
  margin:2rem auto 0;
  background:var(--clr-orange); 
  color:#fff; 
  padding:.75rem 1.5rem;
  border-radius:var(--radius); 
  font-weight:600; 
  text-align:center;
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
#cursos .ver-todos::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}
#cursos .ver-todos:hover::before {
    left: 100%;
}
#cursos .ver-todos:hover { 
    transform: translateY(-4px) scale(1.05); 
    box-shadow: 0 12px 25px rgba(251, 111, 73, 0.3);
}
#cursos .ver-todos:active {
    transform: translateY(-2px) scale(1.03);
    transition: all 0.1s ease;
}

/* Animação fade-up */
.fade-up {
    opacity: 1;
    transform: translateY(0);
    transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

/* Seção de depoimentos */
#depoimentos {
    padding: 120px 0;
    position: relative;
    overflow: hidden;
}

#depoimentos::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 80%, rgba(147, 51, 234, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(147, 51, 234, 0.08) 0%, transparent 50%);
    pointer-events: none;
}

#depoimentos::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 800px;
    height: 800px;
    background: conic-gradient(from 0deg, transparent, rgba(147, 51, 234, 0.03), transparent);
    border-radius: 50%;
    transform: translate(-50%, -50%) rotate(0deg);
    animation: rotate 60s linear infinite;
    pointer-events: none;
}

@keyframes rotate {
    to {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

@keyframes gradientShift {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

/* Descrição */
.descricao {
    font-size: 1.25rem;
    color: #b3b3b3;
    text-align: center;
    margin-bottom: 80px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    font-weight: 400;
    line-height: 1.7;
    position: relative;
    z-index: 2;
}

/* Container dos cartões */
.cartoes-depoimento {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: 40px;
    margin-top: 60px;
    position: relative;
    z-index: 2;
}

/* Cartão individual de depoimento */
.depoimento {
  background: linear-gradient(135deg, #0a0a0a 10%, #1a0d26 50%, #0a0a0a 100%);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(132, 12, 244, 0.2);
  border-radius: 24px;
  padding: 40px 32px;
  position: relative;
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3),
              0 0 0 1px rgba(147, 51, 234, 0.1) inset;
  cursor: pointer;
}

.depoimento::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

.depoimento:hover {
    transform: translateY(-12px) scale(1.02);
    border-color: rgba(147, 51, 234, 0.4);
    box-shadow: 0 32px 64px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(147, 51, 234, 0.3) inset,
                0 0 60px rgba(147, 51, 234, 0.2);
}

.depoimento:hover::before {
    opacity: 1;
}

/* Badge de categoria */
.badge-cat {
    display: inline-block;
    background: linear-gradient(135deg, #9333ea, #7c3aed);
    color: white;
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 20px rgba(147, 51, 234, 0.3);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.badge-cat::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s ease;
}

.depoimento:hover .badge-cat::before {
    left: 100%;
}

.badge-cat:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 24px rgba(147, 51, 234, 0.4);
}

/* Texto do depoimento */
.depoimento p {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #e5e5e5;
    margin-bottom: 28px;
    font-weight: 400;
    position: relative;
    z-index: 1;
}

.depoimento p::before {
    content: '"';
    font-size: 4rem;
    color: rgba(147, 51, 234, 0.3);
    position: absolute;
    top: -20px;
    left: -20px;
    font-family: serif;
    line-height: 1;
    z-index: -1;
}

/* Autor do depoimento */
.autor {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    color: #ffffff;
    font-size: 1rem;
    padding-top: 20px;
    border-top: 1px solid rgba(147, 51, 234, 0.2);
    position: relative;
}

.autor i {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #9333ea, #7c3aed);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    box-shadow: 0 4px 16px rgba(147, 51, 234, 0.3);
    transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.depoimento:hover .autor i {
    transform: rotate(360deg) scale(1.1);
}

/* Responsividade para tablets */
@media (max-width: 1024px) {
    #depoimentos {
        padding: 100px 0;
    }

    .cartoes-depoimento {
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
    }

    .depoimento {
        padding: 32px 24px;
    }
}

/* Responsividade para mobile */
@media (max-width: 768px) {
    #depoimentos {
        padding: 80px 0;
    }

    .container {
        padding: 0 16px;
    }

    .cartoes-depoimento {
        grid-template-columns: 1fr;
        gap: 24px;
        margin-top: 40px;
    }

    .depoimento {
        padding: 28px 20px;
        border-radius: 20px;
    }

    .depoimento:hover {
        transform: translateY(-8px) scale(1.01);
    }

    .descricao {
        font-size: 1.125rem;
        margin-bottom: 60px;
    }

    .depoimento p {
        font-size: 1rem;
        line-height: 1.7;
    }

    .badge-cat {
        font-size: 0.8rem;
        padding: 6px 16px;
    }

    .autor {
        font-size: 0.9rem;
    }

    .autor i {
        width: 36px;
        height: 36px;
        font-size: 0.9rem;
    }
  }

        /* Animações adicionais */
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .depoimento:nth-child(2) {
            animation: float 6s ease-in-out infinite;
            animation-delay: 2s;
        }

        .depoimento:nth-child(3) {
            animation: float 6s ease-in-out infinite;
            animation-delay: 4s;
        }

        /* Efeitos de glassmorphism aprimorados */
        .depoimento {
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
        }

        /* Micro-interações */
        .badge-cat {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .badge-cat:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 24px rgba(147, 51, 234, 0.4);
        }

        /* Estados de foco para acessibilidade */
        .depoimento:focus-within {
            outline: 2px solid #9333ea;
            outline-offset: 4px;
        }

        

    /* Fale Conosco */
    #faleConosco { background:#f5f6fa; padding:4rem 0; }
    #faleConosco .descricao { text-align:center; color:var(--clr-gray); margin-bottom:3rem; }
    #faleConosco .container { display:flex; max-width:1200px; margin:0 auto; gap:2rem; }
    #faleConosco .info-contato { flex:1; padding-right:2rem; }
    #faleConosco .formulario { flex:1; background:#fff; padding:2rem; border-radius:var(--radius); box-shadow:0 5px 15px rgba(0,0,0,0.05); }
    #faleConosco h2.section-title { margin-bottom:1rem; }
    #faleConosco .item-contato { display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem; }
    #faleConosco .icone-contato {
      width:48px; height:48px; border-radius:50%; background:var(--clr-purple);
      display:flex; align-items:center; justify-content:center; color:#fff;
    }
    #faleConosco .info-texto span { color:var(--clr-gray); }
    #faleConosco .form-group { margin-bottom:1.5rem; }
    #faleConosco .form-group label { display:block; margin-bottom:.5rem; font-weight:600; color:var(--clr-dark); }
    #faleConosco .form-group input,
    #faleConosco .form-group textarea {
      width:100%; padding:.75rem; border:1px solid #e0e0e0; border-radius:8px; font-family:var(--font-base); font-size:1rem;
    }
    #faleConosco .form-group input:focus,
    #faleConosco .form-group textarea:focus {
      outline:none; border-color:var(--clr-purple); box-shadow:0 0 0 2px rgba(138,43,226,0.1);
    }
    #faleConosco button[type="submit"] {
      background:var(--clr-purple); color:#fff; border:none; border-radius:8px; padding:.75rem 2rem;
      font-weight:600; cursor:pointer; transition:background .3s, transform .3s; width:100%; font-size:1rem;
    }
    #faleConosco button[type="submit"]:hover {
      background:var(--clr-purple-dark); transform:translateY(-2px);
    }
    #faleConosco .alert-success,
    #faleConosco .alert-error { padding:1rem; border-radius:8px; margin-bottom:1.5rem; }
    #faleConosco .alert-success { background:#e6f7e8; color:#2e7d32; border:1px solid #c8e6c9; }
    #faleConosco .alert-error { background:#ffebee; color:#c62828; border:1px solid #ffcdd2; }

    /* Responsividade */
    @media (max-width: 768px) {
      #faleConosco .container { flex-direction:column; }
      #faleConosco .info-contato { padding-right:0; margin-bottom:2rem; }
    }
  </style>
</head>
<body>

<main>
    <!-- HEROI APRIMORADO -->
    <section class="heroi fade-up">
        <!-- Partículas flutuantes -->
        <div class="hero-particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>

        <!-- Formas geométricas -->
        <div class="hero-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>

        

        <div class="container">
            <div class="hero-content">
                <div class="subtitle">Educação de Excelência</div>
                <h1>Transformamos Conhecimento em Oportunidades</h1>
                <p>Descubra cursos e soluções inovadoras que impulsionam sua carreira. Aprenda com especialistas e construa o futuro que você merece.</p>
                
                <div class="hero-buttons">
                    <a href="#cursos" class="botao-primario">Explore nossos Cursos</a>
                    <a href="#sobre" class="botao-secundario">Saiba Mais</a>
                </div>
            </div>
        </div>

        <!-- Indicador de scroll -->
        <div class="scroll-indicator">
            <span>Role para baixo</span>
            <div class="scroll-arrow"></div>
        </div>
    </section>

   <!-- SOBRE -->
    <section class="sobre fade-up">
        <div class="container">
            <h2 class="section-title">Quem Somos</h2>
            <div class="sobre-content">
                <div class="sobre-text">
                    <p class="sobre-intro">Transformamos carreiras através da educação online de excelência</p>
                    <p class="sobre-description">A Augebit é referência em educação online, combinando tecnologia moderna e metodologias ativas para oferecer cursos que realmente fazem a diferença na sua jornada profissional.</p>
                    <div class="features-grid">
                        <div class="feature-item fade-up">
                            <div class="feature-icon">✓</div>
                            <span class="feature-text">Metodologia prática e hands-on</span>
                        </div>
                        <div class="feature-item fade-up">
                            <div class="feature-icon">★</div>
                            <span class="feature-text">Suporte e mentoria personalizada</span>
                        </div>
                        <div class="feature-item fade-up">
                            <div class="feature-icon">♦</div>
                            <span class="feature-text">Comunidade ativa de aprendizado</span>
                        </div>
                        <div class="feature-item fade-up">
                            <div class="feature-icon">●</div>
                            <span class="feature-text">Certificação reconhecida pelo mercado</span>
                        </div>
                    </div>
                </div>
                <div class="sobre-visual">
                    <div class="image-container">
                        <img src="src/fundo.png" alt="Equipe Augebit - Educação Online de Qualidade">
                        <div class="image-overlay"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Elementos decorativos -->
        <div class="decorative-elements">
            <div class="decorative-circle"></div>
            <div class="decorative-circle"></div>
            <div class="decorative-circle"></div>
        </div>
    </section>


  <!-- RECURSOS -->
  <section class="recursos fade-up">
    <div class="container">
      <h2 class="section-title">Por que escolher a Augebit?</h2>
      <div class="grid">
        <div class="card recursos"><i class="fas fa-laptop-code"></i><h3>Ambiente Virtual</h3><p>Plataforma responsiva e sempre atualizada.</p></div>
        <div class="card recursos"><i class="fas fa-chalkboard-teacher"></i><h3>Mentoria ao Vivo</h3><p>Aulas interativas com feedback imediato.</p></div>
        <div class="card recursos"><i class="fas fa-certificate"></i><h3>Certificação Profissional</h3><p>Certificados reconhecidos no mercado.</p></div>
        <div class="card recursos"><i class="fas fa-users"></i><h3>Comunidade Exclusiva</h3><p>Networking e grupos de estudo.</p></div>
      </div>
    </div>
  </section>

  <!-- CURSOS EM DESTAQUE -->
  <section id="cursos" class="fade-up">
    <div class="container">
      <h2 class="section-title">Nossos Cursos em Destaque</h2>
      <p class="descricao">Conheça alguns dos nossos cursos mais populares e inicie sua jornada de aprendizado</p>
      <div class="grid">
        <!-- Curso 1 -->
        <div class="card">
          <div class="badge">Mais Popular</div>
          <img src="src/desenvolvimentoWeb.png" alt="Desenvolvimento Web Completo">
          <div class="conteudo">
            <div class="categoria"><i class="fas fa-code"></i>Sistemas Web</div>
            <h4>Desenvolvimento Web</h4>
            <p>Aprenda a criar sites e aplicações web completas com HTML5, CSS3, JavaScript, PHP, MySQL e mais.</p>
            <div class="detalhes">
              <div class="preco">R$ 499,90</div>
              <div class="info">
                <span><i class="fas fa-clock"></i> 60h</span>
                <span><i class="fas fa-star"></i> 4.9</span>
              </div>
            </div>
            <a href="#" class="botao">Saiba Mais</a>
          </div>
        </div>
        <!-- Curso 2 -->
        <div class="card">
          <img src="src/react.png" alt="Marketing Digital Estratégico">
          <div class="conteudo">
            <div class="categoria"><i class="fas fa-bullhorn"></i> Aplicativos</div>
            <h4>React Native</h4>
            <p>Faça aplicativos móveis completos para Android e iOS com React Native, JavaScript, Expo, APIs, banco de dados e muito mais."</p>
            <div class="detalhes">
              <div class="preco">R$ 449,90</div>
              <div class="info">
                <span><i class="fas fa-clock"></i> 45h</span>
                <span><i class="fas fa-star"></i> 4.8</span>
              </div>
            </div>
            <a href="#" class="botao">Saiba Mais</a>
          </div>
        </div>
        <!-- Curso 3 -->
        <div class="card">
          <img src="src/python.jpg" alt="Data Science e Inteligência Artificial">
          <div class="conteudo">
            <div class="categoria"><i class="fas fa-chart-line"></i>Programação</div>
            <h4>Python</h4>
            <p>Aprenda a programar com Python do zero e desenvolva aplicações, automações, análise de dados, APIs, inteligência artificial e muito mais.</p>
            <div class="detalhes">
              <div class="preco">R$ 599,90</div>
              <div class="info">
                <span><i class="fas fa-clock"></i> 70h</span>
                <span><i class="fas fa-star"></i> 4.9</span>
              </div>
            </div>
            <a href="#" class="botao">Saiba Mais</a>
          </div>
        </div>
      </div>
      <a href="#cursos" class="ver-todos">Ver Todos os Cursos</a>
    </div>
  </section>

  <!-- DEPOIMENTOS -->
   <section id="depoimentos" class="fade-up">
        <div class="container">
            <h2 class="section-title">O que nossos alunos dizem</h2>
            <p class="descricao">Confira as histórias de transformação e sucesso de nossos estudantes</p>
            <div class="cartoes-depoimento">
                <div class="depoimento">
                    <div class="badge-cat">React Native</div>
                    <p>"O curso de React Native da Augebit me proporcionou conhecimento prático que aplico diariamente. Os projetos com datasets reais me deram confiança para migrar de carreira e hoje trabalho na área em uma multinacional."</p>
                    <div class="autor"><i class="fas fa-user"></i> Pedro Santos – Cientista de Dados</div>
                </div>
                <div class="depoimento">
                    <div class="badge-cat">Desenvolvimento Web</div>
                    <p>"Após concluir o curso de Desenvolvimento Web da Augebit, consegui uma vaga em uma startup. O conteúdo prático e os projetos reais foram essenciais na minha carreira."</p>
                    <div class="autor"><i class="fas fa-user"></i> João Silva – Desenvolvedor Front-end</div>
                </div>
               
            </div>
        </div>
    </section>

  <!-- FALE CONOSCO -->
  <section id="faleConosco" class="fade-up">
    <h2 class="section-title">Fale Conosco</h2>
    <p class="descricao">Estamos aqui para ajudar! Entre em contato conosco ou visite nosso escritório</p>
    <div class="container">
      <div class="info-contato">
        <h3>Entre em Contato</h3>
        <?php if (isset($_SESSION['mensagem_feedback'])): ?>
          <div class="<?= strpos($_SESSION['mensagem_feedback'],'Obrigado')===0?'alert-success':'alert-error' ?>">
            <?= htmlspecialchars($_SESSION['mensagem_feedback']) ?>
          </div>
          <?php unset($_SESSION['mensagem_feedback']); ?>
        <?php endif; ?>
        <div class="item-contato">
          <div class="icone-contato"><i class="fas fa-map-marker-alt"></i></div>
          <div class="info-texto"><span>Localização</span><p>Av. Paulista, 1000 - São Paulo, SP</p></div>
        </div>
        <div class="item-contato">
          <div class="icone-contato"><i class="fas fa-phone-alt"></i></div>
          <div class="info-texto"><span>Telefone</span><p>(11) 3456-7890</p></div>
        </div>
        <div class="item-contato">
          <div class="icone-contato"><i class="fas fa-envelope"></i></div>
          <div class="info-texto"><span>E-mail</span><p>contato@augebit.com.br</p></div>
        </div>
        <div class="item-contato">
          <div class="icone-contato"><i class="fas fa-clock"></i></div>
          <div class="info-texto"><span>Horário de Atendimento</span><p>Segunda a Sexta: 9h às 18h</p></div>
        </div>
      </div>
      <div class="formulario">
        <h3>Envie uma Mensagem</h3>
        <form method="post" action="#faleConosco" novalidate>
          <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" placeholder="Seu nome completo" value="<?= htmlspecialchars($nome) ?>" required>
          </div>
          <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="Seu e-mail" value="<?= htmlspecialchars($email) ?>" required>
          </div>
          <div class="form-group">
            <label for="mensagem">Mensagem</label>
            <textarea id="mensagem" name="mensagem" rows="5" placeholder="Digite sua mensagem aqui..."><?= htmlspecialchars($mensagem) ?></textarea>
          </div>
          <button type="submit">Enviar Mensagem</button>
        </form>
      </div>
    </div>
  </section>

  <?php include '../chat/chat.php'; ?>
</main>

<?php include '../arquivosReuso/footer.php'; ?>

<script>
  // Reveal on scroll
  document.querySelectorAll('.fade-up').forEach(el => {
    const io = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          io.unobserve(e.target);
        }
      });
    }, { threshold: .2 });
    io.observe(el);
  });
</script>
</body>
</html>
<?php
ob_end_flush();
?>
