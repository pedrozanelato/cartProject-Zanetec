@extends('layouts.app')

@section('title', 'Carrinho - STORE Pedro Zanelato')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold text-primary">
                <i class="fas fa-shopping-cart me-2"></i>Carrinho
            </h1>
            <a href="{{ route('web.products.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Continuar Comprando
            </a>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div class="loading" id="loading">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
    <p class="mt-2 text-muted">Carregando carrinho...</p>
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

<!-- Cart Items -->
<div id="cart-items-container">
</div>

<div id="cart-summary" style="display: none;">
    <div class="row mt-4">
        <div class="col-lg-8"></div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Resumo do Pedido</h5>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">R$ 0,00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <span class="text-success">Grátis</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="total" class="text-primary">R$ 0,00</strong>
                    </div>
                    <button class="btn btn-success btn-lg w-100" onclick="proceedToPayment()">
                        <i class="fas fa-credit-card me-2"></i>Prosseguir para Pagamento
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="alert-container"></div>
@endsection

@section('scripts')
<script>
let cartItems = [];

$(document).ready(function() {
    loadCartItems();
});

function loadCartItems() {
    showLoading();
    
    $.post("{{ route('api.carts.list') }}")
    .done(function(response) {
        
        if(response.success === true){
            hideLoading();
            cartItems = response.data.items;
            
            if (cartItems.length === 0) {
                showEmptyCart();
            } else {
                displayCartItems(cartItems);
                calculateTotals();
                $('#cart-summary').show();
            }
        }
    })
    .fail(function() {
        hideLoading();
        showEmptyCart();
    });
}

function showEmptyCart() {
    $('#empty-cart').show();
    $('#cart-items-container').hide();
    $('#cart-summary').hide();
    $('#pagination-container').hide();
}

function displayCartItems(items) {
    const container = $('#cart-items-container');
    container.empty();
    container.show();
    
    items.forEach(function(item, index) {
        const itemHtml = `
            <div class="cart-item fade-in" style="animation-delay: ${index * 0.1}s" data-item-id="${item.id}">
                <div class="row align-items-center">
                    <div class="col-md-2 col-sm-3">
                        <img src="${item.product.file}" class="img-fluid rounded" alt="${item.product.name}">
                    </div>
                    <div class="col-md-4 col-sm-9">
                        <h6 class="fw-bold mb-1">${item.product.name}</h6>
                        <p class="text-muted mb-0">Preço unitário: ${formatPrice(item.product.unitPrice)}</p>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary btn-sm" id="decrease-btn-${item.id}" onclick="updateQuantity(${item.id}, ${-1})" ${item.quantity <= 1 ? 'disabled' : ''}>
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="mx-3 fw-bold quantity-${item.id}">${item.quantity}</span>
                            <button class="btn btn-outline-secondary btn-sm" id="increase-btn-${item.id}" onclick="updateQuantity(${item.id}, ${+1})">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <p class="fw-bold text-success mb-0 item-total-${item.id}">${formatPrice(item.product.unitPrice * item.quantity)}</p>
                    </div>
                    <div class="col-md-1 col-sm-2">
                        <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(${item.id})" title="Remover item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(itemHtml);
    });
}

function updateQuantity(itemId, newQuantity) {

    // Desabilita botões de alterar quantidade
    document.querySelector(`#decrease-btn-${itemId}`).disabled = true;
    document.querySelector(`#increase-btn-${itemId}`).disabled = true;
    
    const itemIndex = cartItems.findIndex(item => item.id === itemId);
    if (itemIndex !== -1) {

        newQuantity += cartItems[itemIndex].quantity;
        $.ajax({
            url: `/api/carts-items/${cartItems[itemIndex].id}`,
            method: 'PUT',
            data: {
                quantity: newQuantity
            },
            success: function(response) {
                if (response.success) {
                    console.log(response);
                    cartItems[itemIndex].quantity = newQuantity;

                    $(`.quantity-${itemId}`).text(newQuantity);
                    $(`.item-total-${itemId}`).text(formatPrice(cartItems[itemIndex].product.unitPrice * newQuantity));

                    calculateTotals();
                    updateCartCount();

                    $(`.cart-item[data-item-id="${itemId}"]`).addClass('animate__animated animate__pulse');
                    setTimeout(function() {
                        $(`.cart-item[data-item-id="${itemId}"]`).removeClass('animate__animated animate__pulse');
                    }, 1000);

                    showAlert('Quantidade atualizada!', 'success');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Ops!',
                    text: 'Não foi possível atualizar a quantidade do produto'
                });
            }
        });
    }
    
    // Habilita botões de alterar quantidade
    if (newQuantity > 1) {
        document.querySelector(`#decrease-btn-${itemId}`).disabled = false;
    }
    document.querySelector(`#increase-btn-${itemId}`).disabled = false;
}

function removeFromCart(itemId) {
    Swal.fire({
        title: 'Remover item?',
        text: 'Tem certeza que deseja remover este item do carrinho?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, remover',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            
            $(`.cart-item[data-item-id="${itemId}"]`).addClass('animate__animated animate__fadeOutLeft');
            
            setTimeout(function() {
                $.ajax({
                    url: `/api/carts-items/${itemId}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {

                            cartItems = cartItems.filter(item => item.id !== itemId);
                            $(`.cart-item[data-item-id="${itemId}"]`).remove();
                            
                            calculateTotals();
                            
                            updateCartCount();
                            
                            if (cartItems.length === 0) {
                                showEmptyCart();
                            }
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Removido!',
                                text: 'Item removido do carrinho.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Erro ao remover o produto do carrinho.'
                        });
                    }
                });
            }, 500);
        }
    });
}

function calculateTotals() {
    let subtotal = 0;
    
    cartItems.forEach(function(item) {
        console.log(item);
        subtotal += item.product.unitPrice * item.quantity;
    });
    
    $('#subtotal').text(formatPrice(subtotal));
    $('#total').text(formatPrice(subtotal));
}

function proceedToPayment() {
    if (cartItems.length === 0) {
        showAlert('Seu carrinho está vazio!', 'warning');
        return;
    }
    
    $('body').addClass('animate__animated animate__fadeOut');
    
    setTimeout(function() {
        window.location.href = "{{ route('web.orders.index') }}";
    }, 500);
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
    }, 3000);
}
</script>
@endsection

