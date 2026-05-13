# Ecommerce API

REST API sederhana untuk mengelola katalog produk ecommerce. Project ini menggunakan Laravel 11 dan menyediakan endpoint CRUD `products` dengan validasi request, API Resource, pagination, unique product identifiers, enum status, soft delete, dan feature tests.

## Technology Stack

| Area | Technology |
| --- | --- |
| Backend | PHP 8.2+, Laravel 11 |
| Database | Laravel migrations, SQLite default in `.env.example`, MySQL supported via `.env` |
| API | Laravel API routes, FormRequest validation, JsonResource responses |
| Testing | PHPUnit 11, Laravel Feature Tests |
| Code Style | Laravel Pint |
| Frontend Tooling | Vite, Tailwind CSS dependencies available |

## Key Features

- Product CRUD API at `/api/products`.
- Paginated product listing with `data`, `links`, and `meta` response sections.
- Product fields include `sku`, `name`, `slug`, `description`, `price`, `stock`, and `status`.
- Product status enum values: `draft`, `active`, `archived`.
- Unique `sku` and `slug` constraints.
- Automatic slug generation from `name` during create when `slug` is not provided.
- Soft delete for products.
- Request validation separated into `StoreProductRequest` and `UpdateProductRequest`.
- API response transformation through `ProductResource`.
- Feature tests for listing, pagination cap, create, show, update, patch, delete, validation, and unique identifiers.

## Project Architecture

The product API follows standard Laravel layering:

```text
HTTP Request
  -> routes/api.php
  -> ProductController
  -> StoreProductRequest / UpdateProductRequest
  -> Product model
  -> products table
  -> ProductResource
  -> JSON response
```

Important files:

| Path | Purpose |
| --- | --- |
| `routes/api.php` | API route registration for `products` |
| `app/Http/Controllers/ProductController.php` | Product CRUD controller |
| `app/Http/Requests/StoreProductRequest.php` | Validation for create product |
| `app/Http/Requests/UpdateProductRequest.php` | Validation for update product |
| `app/Http/Resources/ProductResource.php` | Product API response shape |
| `app/Models/Product.php` | Product Eloquent model, casts, fillable fields, soft delete |
| `app/Enums/ProductStatus.php` | Product status enum |
| `database/migrations/*products*.php` | Product table schema and hardening migrations |
| `database/factories/ProductFactory.php` | Product factory for tests |
| `tests/Feature/ProductApiTest.php` | Product API feature tests |

## Getting Started

### Prerequisites

- PHP 8.2 or newer
- Composer
- Node.js and npm, only needed for Vite/frontend assets
- SQLite or MySQL

### Install Dependencies

```bash
composer install
npm install
```

### Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

For SQLite, keep the default database settings from `.env.example` and create the database file if needed:

```powershell
New-Item -ItemType File -Path database/database.sqlite
```

For MySQL, update these values in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_ecommerce
DB_USERNAME=db_ecommerce
DB_PASSWORD=your_password
```

### Run Migrations

```bash
php artisan migrate
```

### Start Development Server

```bash
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000/api/products
```

## API Reference

### Product Object

```json
{
  "id": 1,
  "sku": "KB-RGB-001",
  "name": "Gaming Keyboard",
  "slug": "gaming-keyboard",
  "description": "Mechanical keyboard with RGB lighting.",
  "price": "1250000.00",
  "stock": 15,
  "status": "active",
  "created_at": "2026-05-14T10:00:00+00:00",
  "updated_at": "2026-05-14T10:00:00+00:00"
}
```

`price` is returned as a decimal string because Laravel's `decimal:2` cast preserves money precision.

### Endpoints

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/products` | List products with pagination |
| `POST` | `/api/products` | Create a product |
| `GET` | `/api/products/{product}` | Show a product |
| `PUT` | `/api/products/{product}` | Update product fields |
| `PATCH` | `/api/products/{product}` | Partially update product fields |
| `DELETE` | `/api/products/{product}` | Soft delete a product |

### List Products

```http
GET /api/products?per_page=15
Accept: application/json
```

Response type: paginated collection response.

```json
{
  "data": [],
  "links": {
    "first": "http://127.0.0.1:8000/api/products?page=1",
    "last": "http://127.0.0.1:8000/api/products?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": null,
    "last_page": 1,
    "per_page": 15,
    "to": null,
    "total": 0
  }
}
```

Pagination notes:

- Default `per_page` is `15`.
- Maximum `per_page` is capped at `100`.

### Create Product

```http
POST /api/products
Accept: application/json
Content-Type: application/json
```

```json
{
  "sku": "KB-RGB-001",
  "name": "Gaming Keyboard",
  "description": "Mechanical keyboard with RGB lighting.",
  "price": 1250000,
  "stock": 15,
  "status": "active"
}
```

Response status: `201 Created`.

If `slug` is omitted, it is generated from `name`. For example, `Gaming Keyboard` becomes `gaming-keyboard`.

### Update Product

```http
PATCH /api/products/1
Accept: application/json
Content-Type: application/json
```

```json
{
  "stock": 25
}
```

Response status: `200 OK`.

### Delete Product

```http
DELETE /api/products/1
Accept: application/json
```

Response status: `204 No Content`.

Products are soft deleted, so the row remains in the database with `deleted_at` set.

### Validation Errors

Invalid requests return `422 Unprocessable Entity`:

```json
{
  "message": "The sku field is required. (and 3 more errors)",
  "errors": {
    "sku": ["The sku field is required."],
    "name": ["The name field is required."],
    "price": ["The price field must be at least 0."],
    "stock": ["The stock field must be at least 0."]
  }
}
```

## Product Validation Rules

| Field | Create | Update | Notes |
| --- | --- | --- | --- |
| `sku` | Required | Optional | Unique, max 100 characters |
| `name` | Required | Optional | Max 255 characters |
| `slug` | Auto-generated or provided | Optional | Unique, lowercase URL format |
| `description` | Optional | Optional | Nullable string |
| `price` | Required | Optional | Numeric, min 0, max `9999999999.99` |
| `stock` | Required | Optional | Integer, min 0 |
| `status` | Optional | Optional | Must be `draft`, `active`, or `archived` |

## Development Workflow

Useful commands:

```bash
php artisan route:list --path=api
php artisan migrate:status
php artisan test
vendor\bin\pint --test
```

Run Pint to format PHP files:

```bash
vendor\bin\pint
```

Run the Laravel development stack defined in Composer:

```bash
composer run dev
```

## Testing

The product API is covered by Laravel feature tests in `tests/Feature/ProductApiTest.php`.

Current coverage areas:

- Product listing response.
- Pagination size cap.
- Product creation with generated slug.
- Product show endpoint.
- Full and partial product updates.
- Soft delete behavior.
- Required field validation.
- Unique `sku` and `slug` validation.

Run tests:

```bash
php artisan test
```

## Coding Standards

- Use `declare(strict_types=1);` in PHP source files.
- Keep validation in FormRequest classes.
- Return API responses through JsonResource classes.
- Use Eloquent casts for domain values like money, integer stock, and enum status.
- Keep controllers focused on request orchestration.
- Run `vendor\bin\pint --test` before submitting changes.

## API Client Testing

You can test the API with Hoppscotch, Postman, Insomnia, cURL, or any HTTP client.

Example cURL request:

```bash
curl -X POST http://127.0.0.1:8000/api/products \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d "{\"sku\":\"KB-RGB-001\",\"name\":\"Gaming Keyboard\",\"price\":1250000,\"stock\":15}"
```

## License

This project is based on the Laravel application skeleton, which is open-sourced under the MIT license.
