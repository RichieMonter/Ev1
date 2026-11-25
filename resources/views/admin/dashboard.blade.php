<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HALCON - Administrative Dashboard</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { margin: 0; background: #f1f5f9; color: #1e293b; display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: #111827;
            color: #fff;
            display: flex;
            flex-direction: column;
            transition: width 0.2s ease;
            flex-shrink: 0;
            z-index: 50;
        }
        .sidebar.collapsed { width: 0; overflow: hidden; }
        .sidebar-brand { padding: 22px 24px; border-bottom: 3px solid #f59e0b; }
        .sidebar-brand span { font-size: 0.7rem; color: #f59e0b; letter-spacing: 2px; font-weight: 700; display: block; }
        .sidebar-brand h2 { margin: 4px 0 0; letter-spacing: 3px; font-size: 1.5rem; }
        .sidebar-nav { padding: 16px 12px; flex: 1; display: flex; flex-direction: column; gap: 6px; }
        .nav-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1.5px; color: #64748b; padding: 8px 12px 4px; font-weight: 700; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.92rem;
            font-weight: 500;
            transition: all 0.15s;
        }
        .nav-link:hover { background: #1e293b; color: #fff; }
        .nav-link.active { background: #f59e0b; color: #111827; font-weight: 700; }
        .sidebar-footer { padding: 16px; border-top: 1px solid #1e293b; }
        .public-link { display: block; text-align: center; color: #94a3b8; font-size: 0.82rem; text-decoration: none; }
        .public-link:hover { color: #f59e0b; }

        /* Main Area */
        .main-wrapper { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar {
            background: #ffffff;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .topbar-left { display: flex; align-items: center; gap: 14px; }
        .hamburger-btn {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 7px 11px;
            cursor: pointer;
            font-size: 1.1rem;
            color: #0f172a;
        }
        .hamburger-btn:hover { background: #e2e8f0; }
        .user-info { display: flex; align-items: center; gap: 14px; }
        .role-badge { background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; padding: 4px 12px; border-radius: 999px; font-weight: 700; font-size: 0.8rem; }
        .btn-logout { background: #ef4444; color: #fff; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem; }
        .btn-logout:hover { background: #dc2626; }

        /* Content */
        .container { padding: 28px; max-width: 1200px; width: 100%; margin: 0 auto; }
        .card { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #e2e8f0; }
        .card h2 { margin-top: 0; font-size: 1.18rem; color: #0f172a; }
        .card-header-flex { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }

        /* Module Cards */
        .nav-modules { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 16px; margin-top: 16px; }
        .module-box {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.15s, border-color 0.15s;
        }
        .module-box:hover { transform: translateY(-2px); border-color: #f59e0b; }
        .module-box h3 { margin: 0 0 8px; font-size: 1.02rem; color: #0f172a; }
        .module-box p { margin: 0 0 16px; font-size: 0.85rem; color: #64748b; line-height: 1.45; }
        .btn-module {
            display: inline-block;
            text-align: center;
            background: #0f172a;
            color: #fff;
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .btn-module:hover { background: #f59e0b; color: #111827; }

        /* Alerts & Forms */
        .alert-success { background: #dcfce7; border-left: 4px solid #16a34a; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-error { background: #fef2f2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; align-items: end; }
        .order-form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; align-items: end; }
        @media (max-width: 860px) { .order-form-grid { grid-template-columns: 1fr; } }

        .catalog-bar {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: center;
            justify-content: space-between;
        }
        .catalog-bar .catalog-select-wrap { flex: 1; min-width: 260px; }
        .catalog-hint { font-size: 0.8rem; color: #475569; margin: 0; }

        label { display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: #fff; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #f59e0b; box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.15); }
        .btn-primary { background: #f59e0b; color: #111827; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 700; text-decoration: none; display: inline-block; font-size: 0.9rem; }
        .btn-primary:hover { background: #d97706; }
        .btn-secondary { background: #0f172a; color: #fff; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }

        /* Status Pills */
        .pill { padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 0.76rem; display: inline-block; }
        .pill-ordered { background: #e0f2fe; color: #0369a1; }
        .pill-process { background: #fef3c7; color: #92400e; }
        .pill-route { background: #ede9fe; color: #5b21b6; }
        .pill-delivered { background: #dcfce7; color: #166534; }
        .pill-deleted { background: #fee2e2; color: #991b1b; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 0.88rem; }
        th, td { text-align: left; padding: 11px 12px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        th { background: #f8fafc; color: #475569; font-weight: 600; }
        .btn-sm { padding: 6px 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 0.78rem; font-weight: 600; }
        .btn-edit { background: #f59e0b; color: #111827; }
        .btn-edit:hover { background: #d97706; }
        .btn-delete { background: #fee2e2; color: #991b1b; }
        .btn-delete:hover { background: #ef4444; color: #fff; }
        .btn-restore { background: #dcfce7; color: #166534; }
        .btn-restore:hover { background: #16a34a; color: #fff; }
        .btn-toggle { background: #e2e8f0; color: #1e293b; }

        /* Modal */
        .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); align-items: center; justify-content: center; z-index: 100; padding: 16px; }
        .modal-backdrop.open { display: flex; }
        .modal-box { background: #fff; width: 100%; max-width: 620px; border-radius: 12px; padding: 24px; box-shadow: 0 20px 25px rgba(0,0,0,0.25); max-height: 92vh; overflow-y: auto; }
        .modal-box h3 { margin: 0 0 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; font-size: 1.15rem; color: #0f172a; }
        .modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width: 600px) { .modal-grid { grid-template-columns: 1fr; } }
        .modal-group { margin-bottom: 4px; }
        .modal-full { grid-column: 1 / -1; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 14px; }
    </style>
</head>
<body>

    <!-- Menú Lateral -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <span>LOGISTICS & SUPPLY</span>
            <h2>HALCON</h2>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Navigation</div>
            <a href="{{ url('/dashboard') }}" class="nav-link {{ $section === 'overview' ? 'active' : '' }}">
                Dashboard Overview
            </a>

            <a href="{{ url('/dashboard/orders') }}" class="nav-link {{ $section === 'orders' ? 'active' : '' }}">
                Order Management
            </a>

            @if (in_array($roleName, ['Administrator', 'Sales']))
                <a href="{{ url('/dashboard/deleted-orders') }}" class="nav-link {{ $section === 'deleted-orders' ? 'active' : '' }}">
                    Deleted Orders
                </a>
            @endif

            @if ($roleName === 'Administrator')
                <a href="{{ url('/dashboard/users') }}" class="nav-link {{ $section === 'users' ? 'active' : '' }}">
                    User & Role Management
                </a>
            @endif

            <div class="nav-label" style="margin-top:10px;">Departments</div>

            @if (in_array($roleName, ['Administrator', 'Sales']))
                <a href="{{ url('/dashboard/sales') }}" class="nav-link {{ $section === 'sales' ? 'active' : '' }}">
                    Sales Workspace
                </a>
            @endif

            @if (in_array($roleName, ['Administrator', 'Purchasing']))
                <a href="{{ url('/dashboard/purchasing') }}" class="nav-link {{ $section === 'purchasing' ? 'active' : '' }}">
                    Purchasing & Supply
                </a>
            @endif

            @if (in_array($roleName, ['Administrator', 'Warehouse']))
                <a href="{{ url('/dashboard/warehouse') }}" class="nav-link {{ $section === 'warehouse' ? 'active' : '' }}">
                    Warehouse Operations
                </a>
            @endif

            @if (in_array($roleName, ['Administrator', 'Route']))
                <a href="{{ url('/dashboard/route') }}" class="nav-link {{ $section === 'route' ? 'active' : '' }}">
                    Route & Delivery
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <a href="{{ url('/track-order') }}" class="public-link" target="_blank">↗ Open Customer Tracking</a>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar-left">
                <button class="hamburger-btn" onclick="toggleSidebar()" title="Toggle Navigation">☰</button>
                <strong style="font-size:1.05rem;">Administrative Portal</strong>
            </div>

            <div class="user-info">
                <span>Logged in as: <strong>{{ $user->username }}</strong></span>
                <span class="role-badge">{{ $roleName }}</span>
                <form method="POST" action="{{ url('/logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </header>

        <main class="container">
            @if (session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <!-- ========================================== -->
            <!-- VISTA 1: OVERVIEW                          -->
            <!-- ========================================== -->
            @if ($section === 'overview')
                <div class="card">
                    <h2>Welcome, {{ $user->username }} — Authorized Modules ({{ $roleName }})</h2>
                    <p style="color:#64748b; font-size:0.9rem; margin-top:-6px;">
                        Select a module below or use the sidebar menu to manage orders, users, and department workflows.
                    </p>

                    <div class="nav-modules">
                        <div class="module-box">
                            <div>
                                <h3>Active Orders & CRUD</h3>
                                <p>Create orders (starting as "Ordered"), filter by invoice, customer, date or status, edit details, and perform logical deletions.</p>
                            </div>
                            <a href="{{ url('/dashboard/orders') }}" class="btn-module">Open Order Management →</a>
                        </div>

                        @if (in_array($roleName, ['Administrator', 'Sales']))
                            <div class="module-box">
                                <div>
                                    <h3>Deleted Orders Archive</h3>
                                    <p>Inspect logically deleted orders, search records, edit details, and restore orders back to the active list.</p>
                                </div>
                                <a href="{{ url('/dashboard/deleted-orders') }}" class="btn-module">View Deleted Orders →</a>
                            </div>
                        @endif

                        @if ($roleName === 'Administrator')
                            <div class="module-box">
                                <div>
                                    <h3>User & Role Management</h3>
                                    <p>Register new employees, edit profiles, assign department roles, and activate or deactivate accounts.</p>
                                </div>
                                <a href="{{ url('/dashboard/users') }}" class="btn-module">Open User Management →</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- ========================================== -->
            <!-- VISTA 2: ORDER MANAGEMENT (CREATE + LIST + FILTERS) -->
            <!-- ========================================== -->
            @if (in_array($section, ['orders', 'sales', 'purchasing', 'warehouse', 'route']))
                @if (in_array($roleName, ['Administrator', 'Sales']))
                    <div class="card">
                        <h2>Create New Customer Order</h2>

                        <!-- Catálogo de Clientes Existentes o Nuevo -->
                        <div class="catalog-bar">
                            <div class="catalog-select-wrap">
                                <label for="customerCatalogSelect" style="color:#0f172a;">Customer Catalog (Select existing customer or register a new one)</label>
                                <select id="customerCatalogSelect" onchange="handleCustomerCatalogChange(this)">
                                    <option value="new" data-next-id="{{ $nextCustomerId }}">+ Register New Customer (ID #{{ $nextCustomerId }})</option>
                                    @foreach ($customers as $cust)
                                        @php
                                            $custFiscal = !empty($cust->fiscal_data) ? $cust->fiscal_data : ($cust->fiscal_info ?? ($cust->tax_id ?? ($cust->rfc ?? '')));
                                        @endphp
                                        <option value="{{ $cust->id }}"
                                            data-id="{{ $cust->id }}"
                                            data-company="{{ $cust->company_name }}"
                                            data-fiscal="{{ $custFiscal }}"
                                            data-address="{{ $cust->delivery_address }}">
                                            #{{ $cust->id }} — {{ $cust->company_name }} ({{ $cust->delivery_address }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="catalog-hint" id="catalogStatusHint">
                                Mode: <strong>New Customer</strong> — Fill in the customer details below and they will be saved to the database.
                            </p>
                        </div>

                        <form method="POST" action="{{ url('/admin/orders') }}" class="order-form-grid">
                            @csrf
                            <div>
                                <label>Consecutive Invoice Number</label>
                                <input type="text" name="invoice_number" value="{{ $nextInvoice }}" required>
                            </div>
                            <div>
                                <label>Unique Customer Number</label>
                                <input type="number" name="customer_number" id="createCustomerNumber" value="{{ $nextCustomerId }}" min="1" required>
                            </div>
                            <div>
                                <label>Initial Order Status</label>
                                <input type="text" value="Ordered (Automatic)" disabled style="background:#e0f2fe; color:#0369a1; font-weight:700; border-color:#bae6fd;">
                            </div>

                            <div>
                                <label>Customer / Company Name</label>
                                <input type="text" name="company_name" id="createCompanyName" placeholder="e.g., Constructora del Norte S.A." required>
                            </div>
                            <div>
                                <label>Customer Fiscal Information (RFC / Tax ID)</label>
                                <input type="text" name="fiscal_info" id="createFiscalInfo" placeholder="e.g., CNO980512ABC - General Regime">
                            </div>
                            <div>
                                <label>Order Date & Time</label>
                                <input type="datetime-local" name="order_date" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>

                            <div style="grid-column: span 2;">
                                <label>Delivery Address</label>
                                <input type="text" name="delivery_address" id="createDeliveryAddress" placeholder="Full street, city, and state delivery address" required>
                            </div>
                            <div>
                                <label>Notes / Additional Information</label>
                                <input type="text" name="notes" placeholder="Materials requested, instructions...">
                            </div>

                            <div style="grid-column: 1 / -1; display:flex; justify-content:flex-end; margin-top:4px;">
                                <button type="submit" class="btn-primary">+ Create Order</button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header-flex">
                        <h2 style="margin:0;">Active Orders List</h2>
                        @if (in_array($roleName, ['Administrator', 'Sales']))
                            <a href="{{ url('/dashboard/deleted-orders') }}" class="btn-secondary" style="font-size:0.82rem; padding:7px 12px;">
                                View Deleted Orders →
                            </a>
                        @endif
                    </div>

                    <!-- Barra de Búsqueda y Filtros -->
                    <form method="GET" action="{{ url('/dashboard/' . $section) }}" class="form-grid" style="background:#f8fafc; padding:14px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:14px;">
                        <div>
                            <label>Filter by Invoice Number</label>
                            <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" placeholder="INV-2026-...">
                        </div>
                        <div>
                            <label>Filter by Customer Number</label>
                            <input type="number" name="customer_number" value="{{ request('customer_number') }}" placeholder="e.g., 1">
                        </div>
                        <div>
                            <label>Filter by Date</label>
                            <input type="date" name="order_date" value="{{ request('order_date') }}">
                        </div>
                        <div>
                            <label>Filter by Status</label>
                            <select name="status">
                                <option value="">-- All Statuses --</option>
                                @foreach (['Ordered', 'In process', 'In route', 'Delivered'] as $st)
                                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button type="submit" class="btn-primary" style="flex:1;">Search</button>
                            <a href="{{ url('/dashboard/' . $section) }}" class="btn-toggle" style="padding:10px 12px; border-radius:6px; text-decoration:none; font-size:0.85rem; font-weight:600;">Clear</a>
                        </div>
                    </form>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer #</th>
                                    <th>Company Name</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Delivery Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $ord)
                                    @php
                                        $fiscal = !empty($ord->fiscal_data) ? $ord->fiscal_data : ($ord->fiscal_info ?? ($ord->tax_id ?? ($ord->rfc ?? '')));
                                        $formattedDate = $ord->order_date ? \Carbon\Carbon::parse($ord->order_date)->format('Y-m-d\TH:i') : '';
                                        $stLower = strtolower($ord->current_status);
                                        $pillClass = $stLower === 'delivered' ? 'pill-delivered' : ($stLower === 'in route' ? 'pill-route' : ($stLower === 'in process' ? 'pill-process' : 'pill-ordered'));
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $ord->invoice_number }}</strong></td>
                                        <td>#{{ $ord->customer_number }}</td>
                                        <td>{{ $ord->company_name ?? 'N/A' }}</td>
                                        <td>{{ $ord->order_date }}</td>
                                        <td><span class="pill {{ $pillClass }}">{{ $ord->current_status }}</span></td>
                                        <td>{{ $ord->delivery_address ?? 'N/A' }}</td>
                                        <td style="display:flex; gap:6px; flex-wrap:wrap;">
                                            <button type="button" class="btn-sm btn-edit btn-open-order"
                                                data-id="{{ $ord->order_id }}"
                                                data-invoice="{{ $ord->invoice_number }}"
                                                data-customer="{{ $ord->customer_number }}"
                                                data-company="{{ $ord->company_name ?? '' }}"
                                                data-fiscal="{{ $fiscal }}"
                                                data-date="{{ $formattedDate }}"
                                                data-address="{{ $ord->delivery_address ?? '' }}"
                                                data-status="{{ $ord->current_status }}"
                                                data-notes="{{ $ord->notes ?? '' }}">
                                                Details / Edit
                                            </button>

                                            @if (in_array($roleName, ['Administrator', 'Sales']))
                                                <form method="POST" action="{{ url('/admin/orders/' . $ord->order_id) }}" style="margin:0;" onsubmit="return confirm('Logically delete order {{ $ord->invoice_number }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-sm btn-delete">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align:center; color:#64748b; padding:20px;">No active orders matched your search criteria.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- ========================================== -->
            <!-- VISTA 3: DELETED ORDERS (LOGICAL DELETION & RESTORE) -->
            <!-- ========================================== -->
            @if ($section === 'deleted-orders' && in_array($roleName, ['Administrator', 'Sales']))
                <div class="card">
                    <div class="card-header-flex">
                        <div>
                            <h2 style="margin:0;">Deleted Orders (Logical Deletion Archive)</h2>
                            <p style="color:#64748b; font-size:0.88rem; margin:4px 0 0;">
                                These orders remain safely stored in MySQL (`deleted_at` timestamp set) and are hidden from customer tracking until restored.
                            </p>
                        </div>
                        <a href="{{ url('/dashboard/orders') }}" class="btn-secondary" style="font-size:0.82rem; padding:7px 12px;">
                            ← Back to Active Orders
                        </a>
                    </div>

                    <form method="GET" action="{{ url('/dashboard/deleted-orders') }}" class="form-grid" style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:14px;">
                        <div style="grid-column: span 3;">
                            <label>Search Deleted Orders (Invoice #, Customer #, or Company Name)</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by invoice, customer ID, or company name...">
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button type="submit" class="btn-primary" style="flex:1;">Search</button>
                            <a href="{{ url('/dashboard/deleted-orders') }}" class="btn-toggle" style="padding:10px 12px; border-radius:6px; text-decoration:none; font-size:0.85rem; font-weight:600;">Clear</a>
                        </div>
                    </form>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer #</th>
                                    <th>Company Name</th>
                                    <th>Last Status</th>
                                    <th>Deleted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($deletedOrders as $del)
                                    @php
                                        $fiscal = !empty($del->fiscal_data) ? $del->fiscal_data : ($del->fiscal_info ?? ($del->tax_id ?? ($del->rfc ?? '')));
                                        $formattedDelDate = $del->order_date ? \Carbon\Carbon::parse($del->order_date)->format('Y-m-d\TH:i') : '';
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $del->invoice_number }}</strong></td>
                                        <td>#{{ $del->customer_number }}</td>
                                        <td>{{ $del->company_name ?? 'N/A' }}</td>
                                        <td><span class="pill pill-deleted">{{ $del->current_status }} (Deleted)</span></td>
                                        <td>{{ $del->order_deleted_at }}</td>
                                        <td style="display:flex; gap:6px; flex-wrap:wrap;">
                                            <button type="button" class="btn-sm btn-edit btn-open-order"
                                                data-id="{{ $del->order_id }}"
                                                data-invoice="{{ $del->invoice_number }}"
                                                data-customer="{{ $del->customer_number }}"
                                                data-company="{{ $del->company_name ?? '' }}"
                                                data-fiscal="{{ $fiscal }}"
                                                data-date="{{ $formattedDelDate }}"
                                                data-address="{{ $del->delivery_address ?? '' }}"
                                                data-status="{{ $del->current_status }}"
                                                data-notes="{{ $del->notes ?? '' }}">
                                                Edit Info
                                            </button>

                                            <form method="POST" action="{{ url('/admin/orders/' . $del->order_id . '/restore') }}" style="margin:0;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-sm btn-restore">↺ Restore Order</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align:center; color:#64748b; padding:20px;">No logically deleted orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- ========================================== -->
            <!-- VISTA 4: USER MANAGEMENT (ADMINISTRATOR)   -->
            <!-- ========================================== -->
            @if ($section === 'users' && $roleName === 'Administrator')
                <div class="card">
                    <h2>Register New Employee</h2>
                    <form method="POST" action="{{ url('/admin/users') }}" class="form-grid">
                        @csrf
                        <div>
                            <label>Username</label>
                            <input type="text" name="username" placeholder="e.g., cmendoza" required>
                        </div>
                        <div>
                            <label>Corporate Email</label>
                            <input type="email" name="email" placeholder="carlos@halcon.com" required>
                        </div>
                        <div>
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Min 6 characters" required>
                        </div>
                        <div>
                            <label>Assigned Role</label>
                            <select name="role_id" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn-primary" style="width:100%;">+ Register Employee</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h2>Registered Company Employees</h2>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Assigned Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $emp)
                                    <tr>
                                        <td>#{{ $emp->id }}</td>
                                        <td><strong>{{ $emp->username }}</strong></td>
                                        <td>{{ $emp->email }}</td>
                                        <td>{{ $emp->role_name ?? 'Unassigned' }}</td>
                                        <td>
                                            @if ($emp->is_active)
                                                <span class="pill pill-delivered">Active</span>
                                            @else
                                                <span class="pill pill-deleted">Inactive</span>
                                            @endif
                                        </td>
                                        <td style="display:flex; gap:8px;">
                                            <button type="button" class="btn-sm btn-edit btn-open-user"
                                                data-id="{{ $emp->id }}"
                                                data-username="{{ $emp->username }}"
                                                data-email="{{ $emp->email }}"
                                                data-role="{{ $emp->role_id }}">
                                                Edit / Role
                                            </button>

                                            <form method="POST" action="{{ url('/admin/users/' . $emp->id . '/toggle') }}" style="margin:0;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-sm btn-toggle">
                                                    {{ $emp->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Modal: Detalles y Edición de Orden (con permisos según rol) -->
    <div class="modal-backdrop" id="orderModal">
        <div class="modal-box">
            <h3 id="orderModalTitle">Order Details & Edit</h3>
            <form method="POST" id="orderEditForm">
                @csrf
                @method('PUT')
                @php
                    $canEditCustomerFields = in_array($roleName, ['Administrator', 'Sales']);
                @endphp

                <div class="modal-grid">
                    <div class="modal-group">
                        <label>Invoice Number</label>
                        <input type="text" id="ordInvoiceDisplay" disabled style="background:#f8fafc; font-weight:700;">
                    </div>

                    <div class="modal-group">
                        <label>Unique Customer Number</label>
                        <input type="text" id="ordCustomerDisplay" disabled style="background:#f8fafc; font-weight:700;">
                    </div>

                    <div class="modal-group">
                        <label>Customer / Company Name</label>
                        <input type="text" name="company_name" id="ordCompanyName" {{ $canEditCustomerFields ? '' : 'readonly style=background:#f8fafc;' }}>
                    </div>

                    <div class="modal-group">
                        <label>Customer Fiscal Information (RFC / Tax ID)</label>
                        <input type="text" name="fiscal_info" id="ordFiscalInfo" {{ $canEditCustomerFields ? '' : 'readonly style=background:#f8fafc;' }}>
                    </div>

                    <div class="modal-group">
                        <label>Order Date & Time</label>
                        <input type="datetime-local" name="order_date" id="ordDate" {{ $canEditCustomerFields ? '' : 'readonly style=background:#f8fafc;' }}>
                    </div>

                    <div class="modal-group">
                        <label>Current Order Status</label>
                        <select name="current_status" id="ordStatus" required>
                            <option value="Ordered">Ordered</option>
                            <option value="In process">In process</option>
                            <option value="In route">In route</option>
                            <option value="Delivered">Delivered</option>
                        </select>
                    </div>

                    <div class="modal-group modal-full">
                        <label>Delivery Address</label>
                        <input type="text" name="delivery_address" id="ordAddress" {{ $canEditCustomerFields ? '' : 'readonly style=background:#f8fafc;' }}>
                    </div>

                    <div class="modal-group modal-full">
                        <label>Notes / Tracking Updates</label>
                        <textarea name="notes" id="ordNotes" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-sm btn-toggle" onclick="closeOrderModal()">Cancel</button>
                    <button type="submit" class="btn-sm btn-edit">Save Order Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Editar Empleado (Administrator) -->
    @if ($roleName === 'Administrator')
        <div class="modal-backdrop" id="editModal">
            <div class="modal-box" style="max-width:460px;">
                <h3>Edit Employee & Role</h3>
                <form method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-group" style="margin-bottom:12px;">
                        <label>Username</label>
                        <input type="text" name="username" id="editUsername" required>
                    </div>
                    <div class="modal-group" style="margin-bottom:12px;">
                        <label>Corporate Email</label>
                        <input type="email" name="email" id="editEmail" required>
                    </div>
                    <div class="modal-group" style="margin-bottom:12px;">
                        <label>Assigned Role</label>
                        <select name="role_id" id="editRole" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-group">
                        <label>New Password (leave blank to keep current)</label>
                        <input type="password" name="password" placeholder="Optional new password">
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-sm btn-toggle" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="btn-sm btn-edit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        function handleCustomerCatalogChange(selectEl) {
            const opt = selectEl.options[selectEl.selectedIndex];
            const numInput = document.getElementById('createCustomerNumber');
            const compInput = document.getElementById('createCompanyName');
            const fiscInput = document.getElementById('createFiscalInfo');
            const addrInput = document.getElementById('createDeliveryAddress');
            const hint = document.getElementById('catalogStatusHint');

            if (selectEl.value === 'new') {
                numInput.value = opt.dataset.nextId || '';
                compInput.value = '';
                fiscInput.value = '';
                addrInput.value = '';
                hint.innerHTML = 'Mode: <strong>New Customer</strong> — Fill in the customer details below and they will be saved to the database.';
            } else {
                numInput.value = opt.dataset.id || '';
                compInput.value = opt.dataset.company || '';
                fiscInput.value = opt.dataset.fiscal || '';
                addrInput.value = opt.dataset.address || '';
                hint.innerHTML = 'Mode: <strong>Existing Customer #' + opt.dataset.id + '</strong> — Customer info loaded from database (you can still edit if needed).';
            }
        }

        function openOrderModal(order) {
            document.getElementById('orderEditForm').action = '/admin/orders/' + order.id;
            document.getElementById('orderModalTitle').innerText = 'Order Details & Edit — ' + order.invoice_number;
            document.getElementById('ordInvoiceDisplay').value = order.invoice_number;
            document.getElementById('ordCustomerDisplay').value = '#' + order.customer_number;
            document.getElementById('ordCompanyName').value = order.company_name;
            document.getElementById('ordFiscalInfo').value = order.fiscal_info;
            document.getElementById('ordDate').value = order.order_date;
            document.getElementById('ordAddress').value = order.delivery_address;

            const statusSelect = document.getElementById('ordStatus');
            const validStatuses = ['Ordered', 'In process', 'In route', 'Delivered'];
            statusSelect.value = validStatuses.includes(order.current_status) ? order.current_status : 'Ordered';

            document.getElementById('ordNotes').value = order.notes;
            document.getElementById('orderModal').classList.add('open');
        }

        function closeOrderModal() {
            document.getElementById('orderModal').classList.remove('open');
        }

        function openEditModal(id, username, email, roleId) {
            document.getElementById('editForm').action = '/admin/users/' + id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = roleId;
            document.getElementById('editModal').classList.add('open');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('open');
        }

        document.querySelectorAll('.btn-open-order').forEach(btn => {
            btn.addEventListener('click', () => {
                openOrderModal({
                    id: btn.dataset.id,
                    invoice_number: btn.dataset.invoice,
                    customer_number: btn.dataset.customer,
                    company_name: btn.dataset.company,
                    fiscal_info: btn.dataset.fiscal,
                    order_date: btn.dataset.date,
                    delivery_address: btn.dataset.address,
                    current_status: btn.dataset.status,
                    notes: btn.dataset.notes
                });
            });
        });

        document.querySelectorAll('.btn-open-user').forEach(btn => {
            btn.addEventListener('click', () => {
                openEditModal(btn.dataset.id, btn.dataset.username, btn.dataset.email, btn.dataset.role);
            });
        });
    </script>
</body>
</html>
