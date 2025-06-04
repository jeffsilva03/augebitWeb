<?php
session_start();
// Verificar se usuário está logado e é usuarioGeral
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'usuarioGeral') {
    header('Location: ../../registro/form_login.php');
    exit();
}

$pagamento = $_SESSION['pagamento_sucesso'];
unset($_SESSION['pagamento_sucesso']); // Limpar da sessão
include_once '../../arquivosReuso/header.php';
?>
<style>
    .success-container {
        max-width: 600px;
        margin: 2rem auto;
        padding: 2rem;
        text-align: center;
    }
    .success-card {
        background: white;
        border-radius: 20px;
        padding: 3rem 2rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }
    .success-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .success-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        animation: successPulse 2s ease-in-out infinite;
    }
    .success-icon i {
        font-size: 2.5rem;
        color: white;
    }
    @keyframes successPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .success-title {
        color: #1f2937;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
    }
    .success-subtitle {
        color: #6b7280;
        font-size: 1.2rem;
        margin-bottom: 2rem;
        line-height: 1.6;
    }
    .payment-details {
        background: #f8fafc;
        border-radius: 12px;
        padding: 2rem;
        margin: 2rem 0;
        text-align: left;
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .detail-row:last-child {
        border-bottom: none;
        font-weight: 700;
        font-size: 1.1rem;
        color: #059669;
    }
    .detail-label {
        color: #6b7280;
        font-weight: 500;
    }
    .detail-value {
        color: #1f2937;
        font-weight: 600;
    }
    .success-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 2rem;
    }
    .btn {
        padding: 1rem 2rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        min-width: 160px;
    }
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 2px solid #e5e7eb;
    }
    .btn-secondary:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        background: #fbbf24;
        animation: confetti-fall 3s linear infinite;
    }
    .confetti:nth-child(1) { left: 10%; animation-delay: 0s; background: #ef4444; }
    .confetti:nth-child(2) { left: 20%; animation-delay: 0.2s; background: #10b981; }
    .confetti:nth-child(3) { left: 30%; animation-delay: 0.4s; background: #3b82f6; }
    .confetti:nth-child(4) { left: 40%; animation-delay: 0.6s; background: #8b5cf6; }
    .confetti:nth-child(5) { left: 50%; animation-delay: 0.8s; background: #f59e0b; }
    .confetti:nth-child(6) { left: 60%; animation-delay: 1s; background: #ef4444; }
    .confetti:nth-child(7) { left: 70%; animation-delay: 1.2s; background: #10b981; }
    .confetti:nth-child(8) { left: 80%; animation-delay: 1.4s; background: #3b82f6; }
    .confetti:nth-child(9) { left: 90%; animation-delay: 1.6s; background: #8b5cf6; }
    
    @keyframes confetti-fall {
        0% {
            transform: translateY(-100vh) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }
    .success-message {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        border: 2px solid #22c55e;
        border-radius: 12px;
        padding: 1.5rem;
        margin: 1.5rem 0;
        color: #166534;
        font-weight: 600;
    }
    @media (max-width: 768px) {
        .success-container {
            margin: 1rem;
            padding: 1rem;
        }
        .success-card {
            padding: 2rem 1.5rem;
        }
        .success-title {
            font-size: 2rem;
        }
        .success-actions {
            flex-direction: column;
        }
        .btn {
            width: 100%;
        }
    }
</style>

<div class="success-container">
    <!-- Confetti Animation -->
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>

    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1 class="success-title">Pagamento Confirmado!</h1>
        <p class="success-subtitle">
            Parabéns! Seu pagamento foi processado com sucesso e você já pode acessar o curso.
        </p>

        <div class="success-message">
            <i class="fas fa-graduation-cap"></i>
            Agora você tem acesso completo ao curso <strong><?php echo htmlspecialchars($pagamento['curso_nome']); ?></strong>
        </div>

        <div class="payment-details">
            <div class="detail-row">
                <span class="detail-label">Curso:</span>
                <span class="detail-value"><?php echo htmlspecialchars($pagamento['curso_nome']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Data do Pagamento:</span>
                <span class="detail-value"><?php echo date('d/m/Y H:i'); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Método de Pagamento:</span>
                <span class="detail-value">Cartão de Crédito</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">Aprovado</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Valor Pago:</span>
                <span class="detail-value">R$ <?php echo number_format($pagamento['valor'], 2, ',', '.'); ?></span>
            </div>
        </div>

        <div class="success-actions">
            <a href="<?php echo $pagamento['curso_url']; ?>" class="btn btn-primary">
                <i class="fas fa-play-circle"></i> Acessar Curso Agora
            </a>
            <a href="listagemCursos.php" class="btn btn-secondary">
                <i class="fas fa-list"></i> Ver Todos os Cursos
            </a>
        </div>

        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
            <p style="color: #6b7280; font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i>
                Um e-mail de confirmação foi enviado para seu endereço cadastrado com os detalhes da compra.
            </p>
        </div>
    </div>
</div>

<script>
// Auto-redirect para o curso após 10 segundos (opcional)
setTimeout(function() {
    if (confirm('Deseja ser redirecionado automaticamente para o curso?')) {
        window.location.href = '<?php echo $pagamento['curso_url']; ?>';
    }
}, 10000);

// Adicionar efeito de confetti mais dinâmico
document.addEventListener('DOMContentLoaded', function() {
    // Criar mais confetti dinamicamente
    const container = document.querySelector('.success-container');
    for (let i = 0; i < 20; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.animationDelay = Math.random() * 3 + 's';
        confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
        
        const colors = ['#ef4444', '#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899'];
        confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
        
        container.appendChild(confetti);
        
        // Remover confetti após a animação
        setTimeout(() => {
            if (confetti.parentNode) {
                confetti.parentNode.removeChild(confetti);
            }
        }, 5000);
    }
});
</script>

<?php include_once '../../arquivosReuso/footer.php'; ?>