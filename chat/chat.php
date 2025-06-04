<?php
// chat.php - componente de chat aprimorado
?>

<style>
  :root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --purple-gradient: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
    --glass-bg: rgba(255, 255, 255, 0.1);
    --glass-border: rgba(255, 255, 255, 0.2);
    --shadow-soft: 0 8px 32px rgba(31, 38, 135, 0.37);
    --shadow-strong: 0 12px 48px rgba(31, 38, 135, 0.5);
    --radius: 16px;
    --radius-small: 12px;
    --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    --blur: 16px;
  }

  * {
    box-sizing: border-box;
  }

  body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  /* Botão de toggle com design glassmorphism */
  #chat-toggle {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: var(--purple-gradient);
    color: white;
    border: 1px solid var(--glass-border);
    border-radius: 50px;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.95rem;
    box-shadow: var(--shadow-soft);
    backdrop-filter: blur(var(--blur));
    transition: var(--transition);
    z-index: 1000;
    user-select: none;
  }

  #chat-toggle:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: var(--shadow-strong);
    background: linear-gradient(135deg, #9333EA 0%, #8B5CF6 100%);
  }

  #chat-toggle:active {
    transform: translateY(-2px) scale(1.02);
  }

  #chat-toggle i {
    font-size: 1.3rem;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
  }

  /* Container principal com glassmorphism aprimorado */
  .chat-container {
    position: fixed;
    bottom: 7rem;
    right: 2rem;
    width: 380px;
    max-width: calc(100vw - 2rem);
    height: 65vh;
    max-height: 600px;
    background: var(--glass-bg);
    backdrop-filter: blur(var(--blur));
    border: 1px solid var(--glass-border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-strong);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: translateY(30px) scale(0.9);
    opacity: 0;
    visibility: hidden;
    transition: var(--transition);
    z-index: 999;
  }

  .chat-container.open {
    visibility: visible;
    transform: translateY(0) scale(1);
    opacity: 1;
  }

  /* Cabeçalho com gradiente animado */
  .chat-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 1.25rem 1.5rem;
    background: var(--primary-gradient);
    color: white;
    position: relative;
    overflow: hidden;
  }

  .chat-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    animation: shimmer 3s infinite;
  }

  @keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
  }

  .chat-header img.bot-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255,255,255,0.3);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }

  .chat-header .title {
    font-weight: 700;
    font-size: 1.1rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
  }

  .chat-header .subtitle {
    font-size: 0.8rem;
    opacity: 0.9;
    font-weight: 400;
  }

  /* Área de mensagens com scroll customizado */
  .messages {
    flex: 1;
    padding: 1.5rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
  }

  .messages::-webkit-scrollbar {
    width: 6px;
  }

  .messages::-webkit-scrollbar-track {
    background: transparent;
  }

  .messages::-webkit-scrollbar-thumb {
    background: var(--purple-gradient);
    border-radius: 3px;
  }

  .messages::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #9333EA 0%, #8B5CF6 100%);
  }

  /* Bolhas de mensagem aprimoradas */
  .message {
    max-width: 80%;
    padding: 1.25rem 1.5rem;
    border-radius: var(--radius);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    opacity: 0;
    transform: translateY(20px);
    animation: messageSlideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    position: relative;
    backdrop-filter: blur(8px);
  }

  @keyframes messageSlideIn {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .message.bot {
    align-self: flex-start;
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
    color: #2D3748;
    border-top-left-radius: 4px;
    border: 1px solid rgba(255,255,255,0.3);
  }

  .message.user {
    align-self: flex-end;
    background: var(--purple-gradient);
    color: white;
    border-top-right-radius: 4px;
    border: 1px solid rgba(255,255,255,0.2);
  }

  .bubble-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
  }

  .bubble-header img.bot-avatar-small {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid rgba(255,255,255,0.3);
  }

  .bubble-header i.user-icon {
    font-size: 1.1rem;
    background: var(--accent-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .bubble-header .name {
    font-weight: 600;
    font-size: 0.85rem;
    opacity: 0.9;
  }

  .bubble-content {
    font-size: 0.95rem;
    line-height: 1.5;
    font-weight: 400;
  }

  /* Indicador de digitação modernizado */
  .typing {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 1rem;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
    border-radius: var(--radius);
    border-top-left-radius: 4px;
    max-width: 80%;
    align-self: flex-start;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    animation: messageSlideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
  }

  .typing-text {
    font-size: 0.9rem;
    color: #4A5568;
    margin-right: 8px;
  }

  .typing-dots {
    display: flex;
    gap: 4px;
  }

  .typing .dot {
    width: 8px;
    height: 8px;
    background: var(--purple-gradient);
    border-radius: 50%;
    animation: typingBounce 1.4s infinite ease-in-out;
  }

  .typing .dot:nth-child(1) { animation-delay: -0.32s; }
  .typing .dot:nth-child(2) { animation-delay: -0.16s; }
  .typing .dot:nth-child(3) { animation-delay: 0s; }

  @keyframes typingBounce {
    0%, 80%, 100% {
      transform: scale(0.8);
      opacity: 0.5;
    }
    40% {
      transform: scale(1);
      opacity: 1;
    }
  }

  /* Botões de opção com hover effects aprimorados */
  .options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    backdrop-filter: blur(8px);
    border-top: 1px solid rgba(255,255,255,0.2);
  }

  .options button {
    padding: 1rem 1.25rem;
    background: var(--purple-gradient);
    color: white;
    border: none;
    border-radius: var(--radius-small);
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
    box-shadow: 0 4px 16px rgba(139, 92, 246, 0.3);
    text-align: center;
  }

  .options button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
  }

  .options button:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 24px rgba(139, 92, 246, 0.4);
    background: linear-gradient(135deg, #9333EA 0%, #8B5CF6 100%);
  }

  .options button:hover::before {
    left: 100%;
  }

  .options button:active {
    transform: translateY(-1px) scale(0.98);
  }

  /* Responsividade aprimorada */
  @media (max-width: 480px) {
    #chat-toggle {
      bottom: 1.5rem;
      right: 1.5rem;
      padding: 0.875rem 1.25rem;
    }

    .chat-container {
      width: calc(100vw - 1rem);
      right: 0.5rem;
      bottom: 6rem;
      height: 70vh;
    }

    .options {
      grid-template-columns: 1fr;
      gap: 0.5rem;
    }

    .options button {
      padding: 0.875rem 1rem;
    }
  }

  @media (max-width: 320px) {
    .chat-container {
      width: calc(100vw - 0.5rem);
      right: 0.25rem;
    }
  }
</style>

<!-- HTML do chat -->
<div id="chat-toggle">
  <i class="fas fa-robot"></i>
  <span>Assistente Virtual</span>
</div>

<div id="chat" class="chat-container">
  <div class="chat-header">
    <img src="src/chat.png" alt="Assistente Augebit" class="bot-avatar">
    <div>
      <div class="title">Assistente Augebit</div>
      <div class="subtitle">Online agora</div>
    </div>
  </div>
  <div id="messages" class="messages"></div>
  <div id="options" class="options"></div>
</div>

<script>
  const conversation = {
    "Começar": {
      reply: "Olá! Seja bem-vindo à Augebit! 👋\n\nSou seu assistente virtual e estou aqui para ajudá-lo. Como posso auxiliá-lo hoje?",
      next: ["Área de Atuação", "Serviços", "Quem Somos", "Contato"]
    },
    "Área de Atuação": {
      reply: "A Augebit atua principalmente na área de aprendizado técnica sobre:\n\n🔧 Desenvolvimento de Software\n💻 Consultoria em TI\n☁️ Soluções em Nuvem\n📱 Aplicações Mobile\n🔒 Segurança Digital\n\nTemos expertise em tecnologias modernas e metodologias ágeis para entregar soluções de alta qualidade.",
      next: ["Serviços", "Quem Somos", "Contato", "Voltar ao Menu"]
    },
    "Serviços": {
      reply: "Nossos principais serviços incluem os cursos referentes à:\n\n✨ Desenvolvimento de Sistemas Web\n📊 Business Intelligence\n🤖 Automação de Processos\n🛡️ Auditoria de Segurança\n📈 Consultoria Estratégica em TI\n🔧 Suporte Técnico Especializado\n\nOferecemos soluções personalizadas para cada necessidade do seu negócio!",
      next: ["Área de Atuação", "Quem Somos", "Contato", "Voltar ao Menu"]
    },
    "Quem Somos": {
      reply: "A Augebit é uma empresa de tecnologia focada em inovação e excelência! 🚀\n\n👥 Equipe especializada e experiente\n💡 Foco em soluções inovadoras\n🎯 Comprometimento com resultados\n⭐ Anos de experiência no mercado\n\nNossa missão é transformar desafios tecnológicos em oportunidades de crescimento para nossos clientes.",
      next: ["Área de Atuação", "Serviços", "Contato", "Voltar ao Menu"]
    },
    "Contato": {
      reply: "Entre em contato conosco:\n\n📧 Email: contato@augebit.com\n📱 WhatsApp: (11) 99999-9999\n🌐 Site: www.augebit.com\n\Estamos prontos para ajudá-lo!",
      next: ["Área de Atuação", "Serviços", "Quem Somos", "Voltar ao Menu"]
    },
    "Voltar ao Menu": {
      reply: "Como posso ajudá-lo hoje? Escolha uma das opções abaixo:",
      next: ["Área de Atuação", "Serviços", "Quem Somos", "Contato"]
    }
  };

  let currentOptions = ["Começar"];
  
  const toggleBtn = document.getElementById('chat-toggle');
  const chatBox = document.getElementById('chat');
  const messagesContainer = document.getElementById('messages');
  const optionsContainer = document.getElementById('options');

  // Toggle do chat
  toggleBtn.addEventListener('click', () => {
    chatBox.classList.toggle('open');
  });

  // Função para mostrar indicador de digitação
  function showTyping(callback) {
    const typingElement = document.createElement('div');
    typingElement.className = 'typing';
    
    const typingText = document.createElement('span');
    typingText.className = 'typing-text';
    typingText.textContent = 'Digitando';
    
    const dotsContainer = document.createElement('div');
    dotsContainer.className = 'typing-dots';
    
    for (let i = 0; i < 3; i++) {
      const dot = document.createElement('div');
      dot.className = 'dot';
      dotsContainer.appendChild(dot);
    }
    
    typingElement.appendChild(typingText);
    typingElement.appendChild(dotsContainer);
    messagesContainer.appendChild(typingElement);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    setTimeout(() => {
      typingElement.remove();
      callback();
    }, 1200);
  }

  // Função para adicionar mensagem
  function appendMessage(text, sender) {
    const messageElement = document.createElement('div');
    messageElement.className = `message ${sender}`;

    const header = document.createElement('div');
    header.className = 'bubble-header';

    if (sender === 'bot') {
      const avatar = document.createElement('img');
      avatar.src = 'src/chat.png';
      avatar.alt = 'Bot';
      avatar.className = 'bot-avatar-small';
      header.appendChild(avatar);
    } else {
      const icon = document.createElement('i');
      icon.className = 'fas fa-user user-icon';
      header.appendChild(icon);
    }

    const name = document.createElement('span');
    name.className = 'name';
    name.textContent = sender === 'bot' ? 'Assistente Augebit' : 'Você';
    header.appendChild(name);

    const content = document.createElement('div');
    content.className = 'bubble-content';
    content.style.whiteSpace = 'pre-line';
    content.textContent = text;

    messageElement.appendChild(header);
    messageElement.appendChild(content);
    messagesContainer.appendChild(messageElement);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  // Função para atualizar opções
  function updateOptions(optionsList) {
    optionsContainer.innerHTML = '';
    
    optionsList.forEach(option => {
      const button = document.createElement('button');
      button.textContent = option;
      button.addEventListener('click', () => handleUserChoice(option));
      optionsContainer.appendChild(button);
    });
  }

  // Função para lidar com escolha do usuário
  function handleUserChoice(choice) {
    appendMessage(choice, 'user');
    
    const conversationNode = conversation[choice];
    
    if (conversationNode) {
      showTyping(() => {
        appendMessage(conversationNode.reply, 'bot');
        currentOptions = conversationNode.next;
        updateOptions(currentOptions);
      });
    }
  }

  // Inicialização
  updateOptions(currentOptions);
  
  // Mensagem de boas-vindas automática após um pequeno delay
  setTimeout(() => {
    handleUserChoice('Começar');
  }, 800);
</script>