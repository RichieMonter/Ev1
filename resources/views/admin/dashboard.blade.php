<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HALCON - Administrative Dashboard</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { margin: 0; background: #f1f5f9; color: #1e293b; display: flex; min-height: 100vh; }

        /* Sidebar / Menú Lateral */
        .sidebar {
            width: 260px;
            background: #111827;
            color: #fff;
            display: flex;
            flex-direction: column;
            transition: transform 0.25s ease;
            flex-shrink: 0;
            z-index: 50;
        }
        .sidebar.collapsed {
            width: 0;
            overflow: hidden;
        }
        .sidebar-brand {
            padding: 22px 24px;
            border-bottom: 3px solid #f59e0b;
        }
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
        .container { padding: 28px; max-width: 1150px; width: 100%; margin: 0 auto; }
        .card { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #e2e8f0; }
        .card h2 { margin-top: 0; font-size: 1.2rem; color: #0f172a; }

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
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px; align-items: end; }
        label { display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 5px; }
        input, select { width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; }
        .btn-primary { background: #f59e0b; color: #111827; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-weight: 700; }
        .btn-primary:hover { background: #d97706; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 0.9rem; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; color: #475569; font-weight: 600; }
        .status-active { color: #166534; background: #dcfce7; padding: 3px 9px; border-radius: 999px; font-weight: 700; font-size: 0.78rem; }
        .status-inactive { color: #991b1b; background: #fee2e2; padding: 3px 9px; border-radius: 999px; font-weight: 700; font-size: 0.78rem; }
        .btn-sm { padding: 6px 11px; border: none; border-radius: 5px; cursor: pointer; font-size: 0.8rem; font-weight: 600; }
        .btn-edit { background: #f59e0b; color: #111827; }
        .btn-toggle { background: #e2e8f0; color: #1e293b; }

        /* Modal */
        .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); align-items: center; justify-content: center; z-index: 100; }
        .modal-backdrop.open { display: flex; }
        .modal-box { background: #fff; width: 100%; max-width: 440px; border-radius: 10px; padding: 24px; box-shadow: 0 20px 25px rgba(0,0,0,0.25); }
        .modal-box h3 { margin-top: 0; }
        .modal-group { margin-bottom: 14px; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; }
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

            @if ($roleName === 'Administrator')
                <a href="{{ url('/dashboard/users') }}" class="nav-link {{ $section === 'users' ? 'active' : '' }}">
                    User & Role Management
                </a>
            @endif

            @if (in_array($roleName, ['Administrator', 'Sales']))
                <a href="{{ url('/dashboard/sales') }}" class="nav-link {{ $section === 'sales' ? 'active' : '' }}">
                    Sales & Orders
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
            <a href="{{ url('/track-order') }}" class="public-link">← Public Order Tracking</a>
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

            <!-- VISTA 1: OVERVIEW (Accesos directos a los módulos permitidos para este rol) -->
            @if ($section === 'overview')
                <div class="card">
                    <h2>Welcome, {{ $user->username }} — Authorized Modules ({{ $roleName }})</h2>
                    <p style="color:#64748b; font-size:0.9rem; margin-top:-6px;">
                        Select an operational module below or use the sidebar menu to navigate.
                    </p>

                    <div class="nav-modules">
                        @if ($roleName === 'Administrator')
                            <div class="module-box">
                                <div>
                                    <h3>User & Role Management</h3>
                                    <p>Register new employees, edit profiles, assign department roles, and activate or deactivate accounts.</p>
                                </div>
                                <a href="{{ url('/dashboard/users') }}" class="btn-module">Open User Management →</a>
                            </div>
                        @endif

                        @if (in_array($roleName, ['Administrator', 'Sales']))
                            <div class="module-box">
                                <div>
                                    <h3>Sales & Customer Orders</h3>
                                    <p>Create and manage incoming customer orders, invoices, and billing data.</p>
                                </div>
                                <a href="{{ url('/dashboard/sales') }}" class="btn-module">Open Sales Module →</a>
                            </div>
                        @endif

                        @if (in_array($roleName, ['Administrator', 'Purchasing']))
                            <div class="module-box">
                                <div>
                                    <h3>Purchasing & Supply</h3>
                                    <p>Support orders that require purchasing missing construction materials from suppliers.</p>
                                </div>
                                <a href="{{ url('/dashboard/purchasing') }}" class="btn-module">Open Purchasing Module →</a>
                            </div>
                        @endif

                        @if (in_array($roleName, ['Administrator', 'Warehouse']))
                            <div class="module-box">
                                <div>
                                    <h3>Warehouse Operations</h3>
                                    <p>Process, pick, verify inventory, and prepare customer orders for dispatch.</p>
                                </div>
                                <a href="{{ url('/dashboard/warehouse') }}" class="btn-module">Open Warehouse Module →</a>
                            </div>
                        @endif

                        @if (in_array($roleName, ['Administrator', 'Route']))
                            <div class="module-box">
                                <div>
                                    <h3>Route & Delivery Evidence</h3>
                                    <p>Manage routed shipments and register photographic proof of delivery.</p>
                                </div>
                                <a href="{{ url('/dashboard/route') }}" class="btn-module">Open Route Module →</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- VISTA 2: USER MANAGEMENT (Exclusiva para Administrator) -->
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
                                                <span class="status-active">Active</span>
                                            @else
                                                <span class="status-inactive">Inactive</span>
                                            @endif
                                        </td>
                                        <td style="display:flex; gap:8px;">
                                            <button type="button" class="btn-sm btn-edit"
                                                onclick="openEditModal({{ $emp->id }}, '{{ $emp->username }}', '{{ $emp->email }}', {{ $emp->role_id }})">
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

            <!-- VISTA 3: MÓDULOS OPERATIVOS (Sales, Purchasing, Warehouse, Route) -->
            @if (in_array($section, ['sales', 'purchasing', 'warehouse', 'route']))
                <div class="card">
                    <h2 style="text-transform: capitalize;">{{ $section }} Management Module</h2>
                    <p style="color:#64748b; font-size:0.9rem;">
                        Active orders connected to MySQL database (<strong>{{ $roleName }}</strong> workspace).
                    </p>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th>Order Date</th>
                                    <th>Delivery Address</th>
                                    <th>Current Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $ord)
                                    <tr>
                                        <td><strong>{{ $ord->invoice_number }}</strong></td>
                                        <td>{{ $ord->company_name ?? 'Customer #' . $ord->customer_number }}</td>
                                        <td>{{ $ord->order_date }}</td>
                                        <td>{{ $ord->delivery_address ?? 'N/A' }}</td>
                                        <td><span class="status-active">{{ strtoupper($ord->current_status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">No active orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Modal para Editar Empleado y Cambiar Rol -->
    @if ($roleName === 'Administrator')
        <div class="modal-backdrop" id="editModal">
            <div class="modal-box">
                <h3>Edit Employee & Role</h3>
                <form method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-group">
                        <label>Username</label>
                        <input type="text" name="username" id="editUsername" required>
                    </div>
                    <div class="modal-group">
                        <label>Corporate Email</label>
                        <input type="email" name="email" id="editEmail" required>
                    </div>
                    <div class="modal-group">
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
    </script>
</body>
</html>
