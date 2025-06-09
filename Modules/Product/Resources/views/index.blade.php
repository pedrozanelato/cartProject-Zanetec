@extends('layouts.app')

@section('title', 'Produtos - STORE Pedro Zanelato')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold text-primary">
                <i class="fas fa-shopping-bag me-2"></i>Produtos
            </h1>
            <div class="text-muted">
                <span id="products-info">Carregando produtos...</span>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div class="loading" id="loading">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
    <p class="mt-2 text-muted">Carregando produtos...</p>
</div>

<!-- Products Grid -->
<div class="row" id="products-grid">
</div>

<div class="pagination-container" id="pagination-container" style="display: none;">
    <button class="page-btn" id="prev-btn" onclick="changePage('prev')">
        <i class="fas fa-chevron-left me-1"></i>Anterior
    </button>
    
    <div id="page-numbers">
    </div>
    
    <button class="page-btn" id="next-btn" onclick="changePage('next')">
        Próxima<i class="fas fa-chevron-right ms-1"></i>
    </button>
</div>

<div id="alert-container"></div>
@endsection

@section('scripts')
<script>
let currentPage = 1;
let totalPages = 1;

$(document).ready(function() {
    initCart();
    loadProducts(1);
});

// Função para iniciar o carrinho
function initCart() {
    $.get("{{ route('api.carts.init') }}")
        .done(function(response) {
        })
        .fail(function() {
            console.log('Erro ao buscar contagem do carrinho');
    });
}

function loadProducts(page) {
    showLoading();
    
    $.post("{{ route('api.products.list') }}", {
        withPaginate: true,
        page: page,
        perPage: 12
    })
    .done(function(response) {
        if(response.success === true){
            hideLoading();
            displayProducts(response.data);
            updatePagination(response.pages.current, response.pages.last);
            updateProductsInfo(response.pages);
            
            currentPage = response.pages.current;
            totalPages = response.pages.last;
        }
    })
    .fail(function() {
        hideLoading();
        showAlert('Erro ao carregar produtos. Tente novamente.', 'danger');
    });
}

function displayProducts(products) {
    const grid = $('#products-grid');
    grid.empty();
    
    if (products.length === 0) {
        grid.html(`
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">Nenhum produto encontrado</h3>
            </div>
        `);
        return;
    }
    
    products.forEach(function(product, index) {
        const productCard = `
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card product-card h-100 fade-in" style="animation-delay: ${index * 0.1}s">
                    <img src="${product.file}" class="card-img-top product-image" alt="${product.name}">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold">${product.name}</h5>
                        <p class="card-text text-success fs-4 fw-bold mt-auto">${formatPrice(product.unitPrice)}</p>
                        <button class="btn btn-add-cart text-white fw-bold" onclick="addToCart(${product.id})">
                            <i class="fas fa-cart-plus me-2"></i>Adicionar ao Carrinho
                        </button>
                    </div>
                </div>
            </div>
        `;
        grid.append(productCard);
    });
    
    $('.product-card').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
}

function updatePagination(currentPage, totalPages) {
    if (totalPages <= 1) {
        $('#pagination-container').hide();
        return;
    }
    
    $('#pagination-container').show();
    
    $('#prev-btn').prop('disabled', currentPage === 1);
    $('#next-btn').prop('disabled', currentPage === totalPages);
    
    const pageNumbers = $('#page-numbers');
    pageNumbers.empty();
    
    for (let i = 1; i <= totalPages; i++) {
        const isActive = i === currentPage ? 'active' : '';
        const pageBtn = `
            <button class="page-btn ${isActive}" onclick="changePage(${i})">
                ${i}
            </button>
        `;
        pageNumbers.append(pageBtn);
    }
}

function updateProductsInfo(response) {
    const start = ((response.position.first - 1)) + 1;
    const end = Math.min(response.position.last * 12, response.position.last);
    const info = `Mostrando ${start}-${end} de ${response.total} produtos`;
    $('#products-info').text(info);
}

function changePage(page) {
    if (page === 'prev') {
        page = Math.max(1, currentPage - 1);
    } else if (page === 'next') {
        page = Math.min(totalPages, currentPage + 1);
    }
    
    if (page !== currentPage) {
        $('html, body').animate({
            scrollTop: 0
        }, 500);
        
        setTimeout(function() {
            loadProducts(page);
        }, 300);
    }
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

// Adiciona produto ao carrinho
function addToCart(productId) {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adicionando...';
    button.disabled = true;
    
    $.post("{{ route('api.carts-items.store') }}", {
        productId: productId,
        quantity: 1
    })
    .done(function(response) {
        if (response.success) {
            button.innerHTML = '<i class="fas fa-check me-2"></i>Adicionado!';
            button.classList.remove('btn-add-cart');
            button.classList.add('btn-success');
            
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: response.message,
                timer: 1500,
                showConfirmButton: false
            });
            
            // Contador Items do Carrinho
            $totalItems = parseInt($('#cart-count').text()?.trim() || '0', 10);
            $totalItems++;
            $('#cart-count').text($totalItems);
            $('#cart-count').show();
            
            $('#cart-btn').addClass('animate__animated animate__pulse');
            
            setTimeout(function() {
                button.innerHTML = originalText;
                button.classList.remove('btn-success');
                button.classList.add('btn-add-cart');
                button.disabled = false;
                $('#cart-btn').removeClass('animate__animated animate__pulse');
            }, 2000);
        }
    })
    .fail(function() {
        button.innerHTML = originalText;
        button.disabled = false;
        
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro ao adicionar produto ao carrinho'
        });
    });
}
</script>
@endsection

