# Sistema Halcón - Evidencia CRM & Logística

## Descripción del Proyecto
Sistema de gestión de logística y seguimiento de entregas ("Sistema Halcón") diseñado para administrar clientes, roles de usuario, órdenes de compra, productos e inventario, adjuntando evidencia fotográfica y registro de estados de entrega.

## Diagrama Entidad-Relación (ERD)
```mermaid
erDiagram
    ROLE ||--o{ USER : "has many"
    USER ||--o{ ORDER : "creates"
    CUSTOMER ||--o{ ORDER : "places"
    ORDER ||--o{ ORDER_DETAIL : "contains"
    PRODUCT ||--o{ ORDER_DETAIL : "included in"
    ORDER ||--o{ EVIDENCE : "has"

    ROLE {
        int id PK
        string department_name
    }
    USER {
        int id PK
        int role_id FK
        string username
        string password_hash
    }
    CUSTOMER {
        int customer_number PK
        string company_name
        string fiscal_data
        string delivery_address
    }
    ORDER {
        int invoice_number PK
        int customer_number FK
        int created_by_user_id FK
        date order_date
        text notes
        string current_status
        boolean is_deleted
    }
    ORDER_DETAIL {
        int id PK
        int invoice_number FK
        int product_id FK
        int quantity
        decimal unit_price
    }
    PRODUCT {
        int id PK
        string name
        decimal price
        int stock
    }
    EVIDENCE {
        int id PK
        int invoice_number FK
        string photo_url
        datetime uploaded_at
    }
```

---

## Evidence 2 - REST API Implementation

Backend update for **Sistema Halcón** implementing REST API endpoints for Users and Orders management.

### Available API Endpoints

#### Users (`/api/users`)
- **GET `/api/users`**: Retrieves a list of all users, including active and inactive users.
- **GET `/api/users/{id}`**: Retrieves information for a specific user.
- **POST `/api/users`**: Creates a new user.
- **PUT `/api/users/{id}`**: Updates basic information for a user.
- **PATCH `/api/users/{id}/deactivate`**: Changes a user's status to inactive.

#### Orders (`/api/orders`)
- **GET `/api/orders`**: Retrieves all orders, ordered from newest to oldest.
- **GET `/api/orders/{id}`**: Retrieves a specific order by ID.
- **GET `/api/orders/invoice/{invoice}`**: Searches for an order using its invoice number.
- **POST `/api/orders`**: Creates a new order.
- **PUT `/api/orders/{id}`**: Updates an existing order's information or status.
- **DELETE `/api/orders/{id}`**: Logically deletes (archives) an order.
- **GET `/api/orders/archived`**: Retrieves a list of all archived orders.