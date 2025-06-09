<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'STORE Pedro Zanelato')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        
        main{
            min-height: 100vh;
        }

        #cart-count{
            display: none;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .product-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,.2);
        }

        .product-image {
            height: 250px;
            object-fit: contain;
            border-radius: 8px 8px 0 0;
        }

        .btn-add-cart {
            background: linear-gradient(45deg, var(--success-color), #20c997);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-add-cart:hover {
            background: linear-gradient(45deg, #20c997, var(--success-color));
            transform: scale(1.05);
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 30px 0;
        }

        .page-btn {
            padding: 8px 16px;
            border: 1px solid var(--primary-color);
            background: white;
            color: var(--primary-color);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .page-btn:hover:not(:disabled) {
            background: var(--primary-color);
            color: white;
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-btn.active {
            background: var(--primary-color);
            color: white;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cart-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
        }

        .payment-option {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            border-color: var(--primary-color);
            background-color: #f8f9ff;
        }

        .payment-option.selected {
            border-color: var(--primary-color);
            background-color: #e7f3ff;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .alert {
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .product-card {
                margin-bottom: 20px;
            }
            
            .cart-item {
                padding: 10px;
            }
            
            .payment-option {
                padding: 15px;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('web.products.index') }}">
                <i class="fas fa-store me-2"></i>Store Zanetec
            </a>
            <!-- <button class="navbar-toggler" type="button" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.products.index') ? 'active' : '' }}" href="{{ route('web.products.index') }}">
                            <i class="fas fa-home me-1"></i>Produtos
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex">
                    <button class="btn btn-outline-light position-relative" id="cart-btn" onclick="goToCart()">
                        <span class="cart-span" id="cart-span">Ver Carrinho</span>
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge" id="cart-count">0</span>
                    </button>
                </div>
            </div> -->
            
            <div class="" id="navbarNav">
                <div class="d-flex">
                    <button class="btn btn-outline-light position-relative" id="cart-btn" onclick="goToCart()">
                        <span class="cart-span" id="cart-span">Ver Carrinho</span>
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge" id="cart-count">0</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2025 STORE Pedro Zanelato. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function goToCart() {
            window.location.href = "{{ route('web.carts.index') }}";
        }

        // Atualizar contador do carrinho ao carregar a página
        $(document).ready(function() {
            updateCartCount();
        });

        // Função para atualizar contador do carrinho
        function updateCartCount() {
            $.get("{{ route('api.carts.totalItems') }}")
                .done(function(response) {
                    if(response.success === true){
                        $('#cart-count').text(response.data.count);
                        if (response.data.count > 0) {
                            $('#cart-count').show();
                        } else {
                            $('#cart-count').hide();
                        }
                    }
                })
                .fail(function() {
                    console.log('Erro ao buscar contagem do carrinho');
                });
        }

        // Função para adicionar produto ao carrinho
        function addToCart(productId) {
            $.post("{{ route('api.carts-items.store') }}", {
                product_id: productId
            })
            .done(function(response) {
                if (response.success) {
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    $('#cart-count').text(response.cart_count);
                    $('#cart-count').show();
                    
                    
                    $('#cart-btn').addClass('animate__animated animate__pulse');
                    setTimeout(function() {
                        $('#cart-btn').removeClass('animate__animated animate__pulse');
                    }, 1000);
                }
            })
            .fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao adicionar produto ao carrinho'
                });
            });
        }

        function showLoading() {
            $('.loading').show();
        }

        function hideLoading() {
            $('.loading').hide();
        }

        function formatPrice(price) {
            return 'R$ ' + parseFloat(price).toFixed(2).replace('.', ',');
        }

        function validateCreditCard(cardNumber) {
            // Remove espaços e caracteres não numéricos
            cardNumber = cardNumber.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            
            if (cardNumber.length !== 16) {
                return false;
            }
            
            let sum = 0;
            let shouldDouble = false;
            
            for (let i = cardNumber.length - 1; i >= 0; i--) {
                let digit = parseInt(cardNumber.charAt(i));
                
                if (shouldDouble) {
                    digit *= 2;
                    if (digit > 9) {
                        digit -= 9;
                    }
                }
                
                sum += digit;
                shouldDouble = !shouldDouble;
            }
            
            return (sum % 10) === 0;
        }

        function formatCardNumber(input) {
            let value = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            input.value = formattedValue;
        }

        function formatCardExpiry(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            input.value = value;
        }
    </script>

    @yield('scripts')
</body>
</html>

