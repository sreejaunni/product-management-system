# 🛒 Product Management System

A Laravel-based RESTful API with built-in authentication, product inventory, and order management features. Built for performance, scalability, and clean architecture.

---

## 🚀 Key Features

### 🔐 Authentication
- User registration & login with **Laravel Sanctum**
- Role-based access: Admin & Customer
- Token-based API authentication
- CSRF protection, password hashing, rate limiting

### 🛍️ Product Management
- CRUD operations on products (Admin-only)
- Product image upload support
- List & filter products by categories
- **Caching** for enhanced performance

### 📦 Order Management
- Create new orders
- View order history & order details
- Pivot table to manage product quantity & pricing in orders

### 📦 Inventory Management
- Product inventory management (decreasing stock on orders)

### 📘 API Documentation
- Fully documented via **Postman**
### 🧪 Testing
- Feature & unit tests with assertions and mocking
- Supports `.env.testing` for isolated test database

---

## 🧰 Tech Stack

- **Framework:** Laravel 12.x
- **Language:** PHP 8.x
- **Authentication:** Laravel Sanctum
- **Database:** MySQL
- **ORM:** Eloquent
- **Testing:** PHPUnit
- **API Docs:** Postman
- **Package Manager:** Composer
- **Caching:** Laravel default
- **Environment Management:** Dotenv
- **Dev Tools:** Laravel Artisan, Postman

## 📚 API Endpoints

### 🔐 Authentication

| Endpoint        | Method | Description          |
|----------------|--------|----------------------|
| `/api/register`| POST   | Register new user    |
| `/api/login`   | POST   | User login/token     |
| `/api/logout`  | POST   | Logout current user  |

---

### 🛍️ Product Management

| Endpoint                                | Method | Description                        |
|-----------------------------------------|--------|------------------------------------|
| `/api/products`                         | GET    | List all products                  |
| `/api/products?category_id[]=1&...`     | GET    | Filter products by category        |
| `/api/products/{id}`                    | GET    | View product details               |
| `/api/products`                         | POST   | Create product (Admin only)        |
| `/api/products/{id}`                    | PUT    | Update product (Admin only)        |
| `/api/products/{id}`                    | DELETE | Delete product (Admin only)        |

---

### 📦 Order Management

| Endpoint              | Method | Description               |
|-----------------------|--------|---------------------------|
| `/api/orders`         | POST   | Create new order          |
| `/api/orders/history` | GET    | View all order history    |
| `/api/orders/{id}`    | GET    | View a specific order     |

---

## 🛠️ Setup Instructions- Local

### 1. Clone the Repository

```bash
git clone https://github.com/sreejaunni/product-management-system.git
cd product-management-system
```
### 2.  Install Dependencies

```bash
composer install
```
### 3. Set Up Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Set Up Database

```bash
CREATE DATABASE product_management_system;
```
Update the .env file with your local database credentials:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_management_system
DB_USERNAME=root
DB_PASSWORD=
```
### 5. Run Migrations & Seeders

```bash
php artisan migrate --seed
```
### 6. Run the Application

```bash
php artisan serve
```

## ✅ Running Tests

### 1. Set Up test Database
Create a new test database:
```bash
CREATE DATABASE product_management_system_test;
```
Update the .env.testing file with the test database credentials:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_management_system_test
DB_USERNAME=root
DB_PASSWORD=
```
### 2. Run All Tests

```bash
php artisan test --env=testing
```

## 🛠️ Setup Instructions - Docker

### 1. Build and start containers:
```bash
docker-compose up --build -d
```
### 2. Update the .env file for db connection:
```bash
 cp .env.example .env

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=product_management_system
DB_USERNAME=root
DB_PASSWORD=root
```
### 1. Run setup inside the container:
```bash
docker exec -it laravel_app php artisan key:generate
```

## 🧪 Postman Collection

This project includes a Postman collection and environment setup to help you quickly test and interact with the API.

### 🔗 Files
- [Product Management API Collection](https://github.com/sreejaunni/product-management-api-collection/blob/main/product-management-collection.json
  )
- [Product Management Environment](https://github.com/sreejaunni/product-management-api-collection/blob/main/product-management-environment.json
  )

### 🚀 How to Use

1. Open Postman.
2. Import the collection:
    - Click **Import** → **Upload Files** → Select `product-management-collection.json`.
3. Import the environment:
    - Click **Environments** (⚙️ icon) → **Import** → Select `product-management-environment.json`.
4. Select the imported environment from the top-right dropdown in Postman.
5. Use the preconfigured requests to test the API endpoints.
