<?php
// footer.php
?>

<!-- Font Awesome (Ícones Sociais) -->
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
/>

<style>
  :root {
    --bg-color: #000;
    --text-main: #eee;
    --text-secondary: #777;
    --accent: #9b30ff;       /* Azul da sua logo */
    --hover-line: #9b30ff;   /* Roxo para a linha de hover */
  }

  .footer {
    background: var(--bg-color);
    color: var(--text-secondary);
    text-align: center;
    padding: 60px 0 20px;
    font-family: Arial, sans-serif;
  }

  /* Logo */
  .footer .logo img {
    max-width: 200px;
    height: auto;
    margin-bottom: 20px;
    display: inline-block;
  }

  /* NAV */
  .footer-nav {
    list-style: none;
    padding: 0;
    margin: 0 auto 20px;
    display: flex;
    gap: 30px;
    justify-content: center;
  }
  .footer-nav li a {
    position: relative;
    color: var(--text-secondary);
    text-decoration: none;
    text-transform: uppercase;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 1px;
    padding-bottom: 5px;
    transition: color 0.2s;
  }
  .footer-nav li a:hover {
    color: var(--text-main);
  }
  .footer-nav li a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    width: 0;
    height: 2px;
    background: var(--hover-line);
    transition: width 0.3s ease;
  }
  .footer-nav li a:hover::after {
    width: 100%;
  }

  /* Ícones sociais em linha separada */
  .social-icons {
    list-style: none;
    padding: 0;
    margin: 0 auto 40px;
    display: flex;
    gap: 15px;
    justify-content: center;
  }
  .social-icons li a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 1px solid var(--accent);
    border-radius: 50%;
    color: var(--accent);
    font-size: 16px;
    text-decoration: none;
    transition: background 0.3s, color 0.3s;
  }
  .social-icons li a:hover {
    background: var(--accent);
    color: var(--bg-color);
  }

  /* Rodapé inferior */
  .footer-bottom {
    font-size: 13px;
    color: var(--text-secondary);
    margin-top: 20px;
  }
  .footer-bottom a {
    color: var(--accent);
    text-decoration: none;
  }
  .footer-bottom a:hover {
    text-decoration: underline;
  }
</style>

<footer class="footer">
  <!-- Logo -->
  <div class="logo">
    <a href="/">
      <img src="src/logoBranca.png" alt="Augebit">
    </a>
  </div>

  <!-- Navegação -->
  <ul class="footer-nav">
    <li><a href="#">Home</a></li>
    <li><a href="#">Sobre</a></li>
    <li><a href="#">Blog</a></li>
    <li><a href="#">Contato</a></li>
  </ul>

  <!-- Ícones sociais (linha separada) -->
  <ul class="social-icons">
    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
  </ul>

  <!-- Rodapé inferior -->
  <div class="footer-bottom">
  Copyright ©2025 All rights reserved | Augebit Company

  </div>
</footer>
