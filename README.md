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
    