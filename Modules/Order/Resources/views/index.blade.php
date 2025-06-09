@extends('layouts.app')

@section('title', 'Pagamento - STORE Pedro Zanelato')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold text-primary">
                <i class="fas fa-credit-card me-2"></i>Finalizar Pagamento
            </h1>
            <a href="{{ route('web.carts.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Voltar ao Carrinho
            </a>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div class="loading" id="loading">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
    <p class="mt-2 text-muted">Carregando checkout...</p>
</div>

<div id="empty-cart" style="display: none;">
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
        <h3 class="text-muted mb-3">Seu carrinho está vazio</h3>
        <p class="text-muted mb-4">Adicione alguns produtos para continuar</p>
        <a href="{{ route('web.products.index') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-shopping-bag me-2"></i>Ver Produtos
        </a>
    </div>
</div>

<div id="checkout-container" class="row">
    <!-- Payments -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-payment me-2"></i>Escolha a forma de pagamento
                </h5>
            </div>
            <div class="card-body">
                <!-- PIX -->
                <div class="payment-option" data-payment="pix" onclick="selectPayment('pix')">
                    <div class="d-flex align-items-center">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="pix" value="pix">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-qrcode fa-2x text-success me-3"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">PIX</h6>
                                    <p class="mb-0 text-muted">Pagamento instantâneo</p>
                                    <small class="text-success fw-bold">10% de desconto</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cartão de Crédito à Vista -->
                <div class="payment-option" data-payment="credito_1x" onclick="selectPayment('credito_1x')">
                    <div class="d-flex align-items-center">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="credito_1x" value="credito_1x">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-credit-card fa-2x text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Cartão de Crédito à Vista</h6>
                                    <p class="mb-0 text-muted">Pagamento em uma parcela</p>
                                    <small class="text-success fw-bold">10% de desconto</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cartão de Crédito Parcelado -->
                <div class="payment-option" data-payment="credito_parcelado" onclick="selectPayment('credito_parcelado')">
                    <div class="d-flex align-items-center">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="credito_parcelado" value="credito_parcelado">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar-alt fa-2x text-warning me-3"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Cartão de Crédito Parcelado</h6>
                                    <p class="mb-0 text-muted">Parcelamento de 2x a 12x</p>
                                    <small class="text-warning">Com juros</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="times-section" style="display: none;" class="mt-3 p-3 bg-light rounded">
                    <label for="times" class="form-label fw-bold">Número de parcelas:</label>
                    <select class="form-select" id="times" onchange="calculateInstallments()">
                        <option value="">Selecione o número de parcelas</option>
                        <option value="2">2x com juros de 2%</option>
                        <option value="3">3x com juros de 3%</option>
                        <option value="4">4x com juros de 4%</option>
                        <option value="5">5x com juros de 5%</option>
                        <option value="6">6x com juros de 6%</option>
                        <option value="7">7x com juros de 7%</option>
                        <option value="8">8x com juros de 8%</option>
                        <option value="9">9x com juros de 9%</option>
                        <option value="10">10x com juros de 10%</option>
                        <option value="11">11x com juros de 11%</option>
                        <option value="12">12x com juros de 12%</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Cartão de Crédito Form -->
        <div id="credit-card-form" style="display: none;" class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-credit-card me-2"></i>Dados do Cartão de Crédito
                </h5>
            </div>
            <div class="card-body">
                <form id="payment-form">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="card_holder_name" class="form-label fw-bold">Nome do Titular do Cartão *</label>
                            <input type="text" class="form-control" id="card_holder_name" name="card_holder_name" 
                                   placeholder="Nome completo como está no cartão" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="card_number" class="form-label fw-bold">Número do Cartão *</label>
                            <input type="text" class="form-control" id="card_number" name="card_number" 
                                   placeholder="0000 0000 0000 0000" maxlength="19" 
                                   oninput="formatCardNumber(this)" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="card_expiry" class="form-label fw-bold">Data de Validade *</label>
                            <input type="text" class="form-control" id="card_expiry" name="card_expiry" 
                                   placeholder="MM/AA" maxlength="5" 
                                   oninput="formatCardExpiry(this)" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="card_cvv" class="form-label fw-bold">CVV *</label>
                            <input type="text" class="form-control" id="card_cvv" name="card_cvv" 
                                   placeholder="123" maxlength="3" 
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success btn-lg w-100 mt-3" id="process-payment-btn">
                        <i class="fas fa-lock me-2"></i>Finalizar Pagamento
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-receipt me-2"></i>Resumo do Pedido
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span id="order-subtotal">R$ 0,00</span>
                </div>
                
                <div id="discount-section" style="display: none;">
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Desconto(10%):</span>
                        <span id="order-discount">- R$ 0,00</span>
                    </div>
                </div>
                
                <div id="interest-section" style="display: none;">
                    <div class="d-flex justify-content-between mb-2 text-warning">
                        <span>Juros:</span>
                        <span id="order-interest">+ R$ 0,00</span>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Frete:</span>
                    <span class="text-success">Grátis</span>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong id="order-total" class="text-primary fs-5">R$ 0,00</strong>
                </div>
                
                <div id="installment-info" style="display: none;" class="alert alert-info">
                    <small>
                        <strong id="installment-count">12</strong>x de 
                        <strong id="installment-value">R$ 416,66</strong>
                    </small>
                </div>
                
                <div class="text-center">
                    <i class="fas fa-shield-alt text-success me-2"></i>
                    <small class="text-muted">Pagamento 100% seguro</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="alert-container"></div>
@endsection

@section('scripts')
<script>
let selectedPayment = null;
let orderTotal = 0.00;

$(document).ready(function() {
    
    setupFormValidation();
});

$(document).ready(function() {
    $('#checkout-container').hide();
    loadCartCheckout();
});

function loadCartCheckout() {
    showLoading();
    
    $.post("{{ route('api.carts.list') }}")
    .done(function(response) {
        
        if(response.success === true){
            
            if (response.data.items.length === 0) {
                showEmptyCart();
            } else {
                
                hideLoading();
                orderTotal = response.data.totalValue;
                $('#checkout-container').show();
                $('#order-subtotal').text(formatPrice(orderTotal));
                $('#order-total').text(formatPrice(orderTotal));
            }
        }
        else{
            
            showEmptyCart();
        }
    })
    .fail(function() {
        hideLoading();
        showEmptyCart();
    });
}

function showEmptyCart() {
    $('#empty-cart').show();
}

function selectPayment(paymentMethod) {
    selectedPayment = paymentMethod;
    
    // Remover seleção anterior
    $('.payment-option').removeClass('selected');
    $('input[name="payment_method"]').prop('checked', false);
    
    // Selecionar nova opção
    $(`.payment-option[data-payment="${paymentMethod}"]`).addClass('selected');
    $(`#${paymentMethod}`).prop('checked', true);
    
    // Esconder seções
    $('#times-section').hide();
    $('#credit-card-form').hide();
    $('#discount-section').hide();
    $('#interest-section').hide();
    $('#installment-info').hide();
    
    if (paymentMethod === 'pix') {
        calculatePayment('pix');
    } else if (paymentMethod === 'credito_1x') {
        $('#credit-card-form').show();
        calculatePayment('credito_1x');
    } else if (paymentMethod === 'credito_parcelado') {
        resetInstallmentsSelection();
        $('#times-section').show();
    }
    
    $(`.payment-option[data-payment="${paymentMethod}"]`).addClass('animate__animated animate__pulse');
    setTimeout(function() {
        $(`.payment-option[data-payment="${paymentMethod}"]`).removeClass('animate__animated animate__pulse');
    }, 1000);
}
function resetInstallmentsSelection() {
    $('#order-total').text(formatPrice(orderTotal));
    const select = document.getElementById('times');
    const defaultOption = select.querySelector('option[value=""]');

    defaultOption.disabled = false;
    select.value = "";
}

function calculateInstallments() {
    const select = document.getElementById('times');
    const defaultOption = select.querySelector('option[value=""]');
    if (select.value !== "") {
        calculatePayment('credito_parcelado', select.value);
        defaultOption.disabled = true;
    }
}

function calculatePayment(paymentMethod, times = null) {
    const data = {
        paymentMethod: paymentMethod
    };
    
    if (times) {
        data.times = times;
    }
    
    $.post("{{ route('api.orders.order') }}", data)
    .done(function(response) {
        
        if(response.success === true){
            updateOrderSummary(response.data);
        }
        else{
            
            showAlert('Não foi possível calcular pagamento', 'danger');
        }
    })
    .fail(function() {
        showAlert('Erro ao calcular pagamento', 'danger');
    });
}

function updateOrderSummary(paymentData) {
    
    $('#order-subtotal').text(formatPrice(orderTotal));
    
    if (paymentData.paymentMethod === 'pix' || paymentData.paymentMethod === 'credito_1x') {
        $('#discount-section').show();
        $('#order-discount').text('- ' + formatPrice(orderTotal - paymentData.totalValue));
        $('#order-total').text(formatPrice(paymentData.totalValue));
        if(paymentData.paymentMethod === 'credito_1x'){
            $('#credit-card-form').show();
        }
    } else if (paymentData.paymentMethod === 'credito_parcelado') {
        $('#interest-section').show();
        const interest = paymentData.totalValue - orderTotal;

        $('#order-interest').text('+ ' + formatPrice(interest));
        $('#order-total').text(formatPrice(paymentData.totalValue));
        $('#installment-info').show();
        $('#installment-count').text(paymentData.times);
        $('#installment-value').text(formatPrice(paymentData.totalValue / paymentData.times));
        $('#credit-card-form').show();
    }
}

function setupFormValidation() {
    $('#payment-form').on('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            processPayment();
        }
    });
    
    $('#card_holder_name').on('blur', function() {
        validateCardHolderName();
    });
    
    $('#card_number').on('blur', function() {
        validateCardNumber();
    });
    
    $('#card_expiry').on('blur', function() {
        validateCardExpiry();
    });
    
    $('#card_cvv').on('blur', function() {
        validateCardCVV();
    });
}

function validateForm() {
    let isValid = true;
    
    if (!validateCardHolderName()) isValid = false;
    if (!validateCardNumber()) isValid = false;
    if (!validateCardExpiry()) isValid = false;
    if (!validateCardCVV()) isValid = false;
    
    return isValid;
}

function validateCardHolderName() {
    const name = $('#card_holder_name').val().trim();
    const field = $('#card_holder_name');
    
    if (name.length < 3) {
        showFieldError(field, 'Nome deve ter pelo menos 3 caracteres');
        return false;
    }
    
    if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(name)) {
        showFieldError(field, 'Nome deve conter apenas letras');
        return false;
    }
    
    showFieldSuccess(field);
    return true;
}

function validateCardNumber() {
    const cardNumber = $('#card_number').val().replace(/\s/g, '');
    const field = $('#card_number');
    
    if (cardNumber.length !== 16) {
        showFieldError(field, 'Número do cartão deve ter 16 dígitos');
        return false;
    }
    
    if (!validateCreditCard(cardNumber)) {
        showFieldError(field, 'Número do cartão inválido');
        return false;
    }
    
    showFieldSuccess(field);
    return true;
}

function validateCardExpiry() {
    const expiry = $('#card_expiry').val();
    const field = $('#card_expiry');
    
    if (!/^\d{2}\/\d{2}$/.test(expiry)) {
        showFieldError(field, 'Formato inválido (MM/AA)');
        return false;
    }
    
    const [month, year] = expiry.split('/');
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear() % 100;
    const currentMonth = currentDate.getMonth() + 1;
    
    if (parseInt(month) < 1 || parseInt(month) > 12) {
        showFieldError(field, 'Mês inválido');
        return false;
    }
    
    if (parseInt(year) < currentYear || 
        (parseInt(year) === currentYear && parseInt(month) < currentMonth)) {
        showFieldError(field, 'Cartão expirado');
        return false;
    }
    
    showFieldSuccess(field);
    return true;
}

function validateCardCVV() {
    const cvv = $('#card_cvv').val();
    const field = $('#card_cvv');
    
    if (!/^\d{3}$/.test(cvv)) {
        showFieldError(field, 'CVV deve ter 3 dígitos');
        return false;
    }
    
    showFieldSuccess(field);
    return true;
}

function showFieldError(field, message) {
    field.removeClass('is-valid').addClass('is-invalid');
    field.siblings('.invalid-feedback').text(message);
}

function showFieldSuccess(field) {
    field.removeClass('is-invalid').addClass('is-valid');
}

function processPayment() {
    const btn = $('#process-payment-btn');
    const originalText = btn.html();
    
    btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Processando...');
    btn.prop('disabled', true);
    
    const formData = {
        card_holder_name: $('#card_holder_name').val(),
        card_number: $('#card_number').val(),
        card_expiry: $('#card_expiry').val(),
        card_cvv: $('#card_cvv').val()
    };
    
    Swal.fire({
        icon: 'success',
        title: 'Compra realizada com sucesso!',
        confirmButtonText: 'Continuar'
    }).then(() => {
        window.location.href = "{{ route('web.products.index') }}";
    });
}

function showAlert(message, type = 'info') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('#alert-container').html(alertHtml);
    
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
}
</script>
@endsection

