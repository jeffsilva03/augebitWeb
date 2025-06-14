<?php
session_start();
require_once '../../arquivosReuso/conexao.php';

// Verificar se usuário está logado e é usuarioGeral
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'usuarioGeral') {
    header('Location: ../../registro/form_login.php');
    exit();
}

// Verificar se foi passado o ID do curso
if (!isset($_GET['curso_id'])) {
    header('Location: ../../listaCursos/listagemCursos.php');
    exit();
}

$curso_id = (int)$_GET['curso_id'];

// Buscar informações do curso
$sql = "SELECT c.*, ca.nome as instrutor_nome 
        FROM cursos c 
        INNER JOIN cadastro ca ON c.instrutor_id = ca.id 
        WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $curso_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ../../listaCursos/listagemCursos.php');
    exit();
}

$curso = $result->fetch_assoc();

// Verificar se o usuário já está inscrito no curso
$sql_check = "SELECT id FROM inscricoes WHERE usuario_id = ? AND curso_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param('ii', $_SESSION['usuario_id'], $curso_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Usuário já está inscrito, redirecionar para o curso
    header('Location: curso-detalhes.php?id=' . $curso_id);
    exit();
}

// Função para gerar preço fictício baseado na duração e nível
function gerarPreco($duration, $level) {
    $base_price = 99.90;
    
    // Ajustar preço baseado na duração
    $hours = (int)filter_var($duration, FILTER_SANITIZE_NUMBER_INT);
    if ($hours > 40) $base_price += 50;
    if ($hours > 80) $base_price += 100;
    
    // Ajustar preço baseado no nível
    switch($level) {
        case 'Iniciante':
            $base_price -= 20;
            break;
        case 'Avançado':
            $base_price += 50;
            break;
    }
    
    return number_format($base_price, 2, ',', '.');
}

$preco = gerarPreco($curso['duration'], $curso['level']);

include_once '../../arquivosReuso/header.php';
?>

<link rel="stylesheet" href="pagamento-curso.css">


<div class="payment-container">
    <a href="../../listaCursos/listagemCursos.php" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Voltar aos cursos
    </a>

    <div class="payment-header">
        <h1 class="payment-title">
            <i class="fas fa-credit-card"></i>
            Finalizar Inscrição
        </h1>
        <p class="payment-subtitle">Complete seu pagamento para ter acesso total ao curso</p>
    </div>

    <div class="course-info">
        <h2 class="course-title"><?php echo htmlspecialchars($curso['titulo']); ?></h2>
        <p><?php echo htmlspecialchars($curso['descricao']); ?></p>
        <div class="course-details">
            <div class="course-detail">
                <i class="fas fa-user-tie"></i>
                <span><strong>Instrutor:</strong> <?php echo htmlspecialchars($curso['instrutor_nome']); ?></span>
            </div>
            <div class="course-detail">
                <i class="fas fa-clock"></i>
                <span><strong>Duração:</strong> <?php echo htmlspecialchars($curso['duration'] ?? '40h'); ?></span>
            </div>
            <div class="course-detail">
                <i class="fas fa-star"></i>
                <span><strong>Avaliação:</strong> <?php echo htmlspecialchars($curso['rating'] ?? '4.5'); ?> ⭐</span>
            </div>
        </div>
    </div>

    <div class="price-section">
        <div class="price-label">Valor do curso</div>
        <div class="price-value">R$ <?php echo $preco; ?></div>
        <div class="price-installments">ou 12x de R$ <?php echo number_format((float)str_replace(',', '.', $preco) / 12, 2, ',', '.'); ?> sem juros</div>
    </div>

    <div class="payment-methods">
        <h3 class="methods-title">Escolha a forma de pagamento</h3>
        <div class="methods-grid">
            <div class="method-card selected" data-method="credit">
                <i class="fas fa-credit-card method-icon"></i>
                <div class="method-name">Cartão de Crédito</div>
            </div>
            <div class="method-card" data-method="pix">
                <i class="fas fa-qrcode method-icon"></i>
                <div class="method-name">PIX</div>
            </div>
            <div class="method-card" data-method="boleto">
                <i class="fas fa-barcode method-icon"></i>
                <div class="method-name">Boleto</div>
            </div>
        </div>
    </div>

    <div class="payment-form" id="paymentForm">
        <div class="form-group">
            <label class="form-label">Nome no Cartão</label>
            <input type="text" class="form-input" placeholder="Digite o nome como está no cartão" required>
        </div>
        <div class="form-group">
            <label class="form-label">Número do Cartão</label>
            <input type="text" class="form-input" placeholder="0000 0000 0000 0000" maxlength="19" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Validade</label>
                <input type="text" class="form-input" placeholder="MM/AA" maxlength="5" required>
            </div>
            <div class="form-group">
                <label class="form-label">CVV</label>
                <input type="text" class="form-input" placeholder="123" maxlength="4" required>
            </div>
        </div>
    </div>

    <form id="confirmPaymentForm" method="POST" action="processar-pagamento.php">
        <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
        <input type="hidden" name="usuario_id" value="<?php echo $_SESSION['usuario_id']; ?>">
        <input type="hidden" name="valor" value="<?php echo str_replace(',', '.', $preco); ?>">
        
        <button type="submit" class="payment-button">
            <i class="fas fa-lock"></i>
            Finalizar Pagamento - R$ <?php echo $preco; ?>
        </button>
    </form>

    <div class="security-info">
        <i class="fas fa-shield-alt"></i>
        <strong>Pagamento 100% seguro.</strong> Seus dados estão protegidos com criptografia SSL.
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleção de método de pagamento
    const methodCards = document.querySelectorAll('.method-card');
    const paymentForm = document.getElementById('paymentForm');
    
    methodCards.forEach(card => {
        card.addEventListener('click', function() {
            methodCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            
            const method = this.dataset.method;
            updatePaymentForm(method);
        });
    });
    
    function updatePaymentForm(method) {
        let formHTML = '';
        
        switch(method) {
            case 'credit':
            case 'debit':
                formHTML = `
                    <div class="form-group">
                        <label class="form-label">Nome no Cartão</label>
                        <input type="text" class="form-input" placeholder="Digite o nome como está no cartão" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Número do Cartão</label>
                        <input type="text" class="form-input" placeholder="0000 0000 0000 0000" maxlength="19" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Validade</label>
                            <input type="text" class="form-input" placeholder="MM/AA" maxlength="5" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">CVV</label>
                            <input type="text" class="form-input" placeholder="123" maxlength="4" required>
                        </div>
                    </div>
                `;
                break;
            case 'pix':
                formHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-qrcode" style="font-size: 4rem; color: #059669; margin-bottom: 1rem;"></i>
                        <h3>PIX - Pagamento Instantâneo</h3>
                        <p>Após confirmar, você receberá o código PIX para pagamento.</p>
                    </div>
                `;
                break;
            case 'boleto':
                formHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-barcode" style="font-size: 4rem; color: #059669; margin-bottom: 1rem;"></i>
                        <h3>Boleto Bancário</h3>
                        <p>O boleto será gerado após a confirmação e pode ser pago em qualquer banco.</p>
                        <p><small><strong>Prazo:</strong> Vencimento em 3 dias úteis</small></p>
                    </div>
                `;
                break;
        }
        
        paymentForm.innerHTML = formHTML;
        
        // Adicionar máscara para cartão se necessário
        if (method === 'credit' || method === 'debit') {
            addCardMasks();
        }
    }
    
    function addCardMasks() {
        // Máscara para número do cartão
        const cardNumber = paymentForm.querySelector('input[placeholder="0000 0000 0000 0000"]');
        if (cardNumber) {
            cardNumber.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
                let matches = value.match(/\d{4,16}/g);
                let match = matches && matches[0] || '';
                let parts = [];
                for (let i = 0, len = match.length; i < len; i += 4) {
                    parts.push(match.substring(i, i + 4));
                }
                if (parts.length) {
                    e.target.value = parts.join(' ');
                } else {
                    e.target.value = value;
                }
            });
        }
        
        // Máscara para validade
        const expiry = paymentForm.querySelector('input[placeholder="MM/AA"]');
        if (expiry) {
            expiry.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            });
        }
        
        // Máscara para CVV
        const cvv = paymentForm.querySelector('input[placeholder="123"]');
        if (cvv) {
            cvv.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '');
            });
        }
    }
    
    // Inicializar com cartão de crédito
    addCardMasks();
    
    // Validação do formulário
    document.getElementById('confirmPaymentForm').addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('.method-card.selected').dataset.method;
        
        if (selectedMethod === 'credit' || selectedMethod === 'debit') {
            const inputs = paymentForm.querySelectorAll('input[required]');
            let valid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = '#ef4444';
                } else {
                    input.style.borderColor = '#10b981';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }
        }
        
        // Mostrar loading
        const button = e.target.querySelector('.payment-button');
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando pagamento...';
        button.disabled = true;
    });
});
</script>

<?php include_once '../../arquivosReuso/footer.php'; ?>