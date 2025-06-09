

# 🛍️ cartProject

**cartProject-bussula** é um sistema modular monolitico de arquitetura limpa em Laravel 10 que implementa produtos, carrinho e checkout com módulos independentes (`Product`, `Cart`, `Order`), testada com PHPUnit e usando MYSQL.

---

## 🖥️ Requisitos

- **PHP 8.1+**  
- **Composer 2.x**  
- **Laravel 10.x**  
- Banco de dados MySQL ou SQLite (para testes)   

---

## ⚙️ Instalação

```bash
# 1. Clone o repositório
git clone https://github.com/pedrozanelato/cartProject-bussula.git
cd cartProject-bussula

# 2. Instale dependências PHP
composer install

# 3. Copie .env e gere APP_KEY
cp .env.example .env
php artisan key:generate

#4. Crie o link simbólico do storage caso necessário
php artisan storage:link

```

## 🔐 Configuração de Ambiente

Edite o arquivo .env conforme seu ambiente:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=storetest_bussola_zanetec
DB_USERNAME=root
DB_PASSWORD=senha
```

# Crie o banco no MySQL
# e então:

```bash
php artisan migrate
php artisan module:seed Product
```

## ▶️ Executando a Aplicação
# Servidor Local

```bash
php artisan serve
# => http://127.0.0.1:8000
```

## ▶️ Chamadas via API
# Documentação e Collection Api

Postman: [Collection](https://myapisdev.postman.co/workspace/API's-Dev~ff4a4532-78d2-46cf-bde2-07f77c1557b7/collection/17224712-85874a7c-e078-432c-b8d8-27a59635ac77?action=share&creator=17224712)
