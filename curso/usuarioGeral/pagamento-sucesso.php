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
<link rel="stylesheet" href="pagamento-sucesso.css">

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
            <a href="../../listaCursos/listagemCursos.php" class="btn btn-primary">
                <i class="fas fa-play-circle"></i> Acessar Curso Agora
            </a>
        </div>

        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
            <p style="color: #6b7280; font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i>
             A liberação do curso adquirido pode levar até 10 minutos
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