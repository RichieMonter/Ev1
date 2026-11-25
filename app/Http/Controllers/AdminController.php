<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$fieldType => $request->login, 'password' => $request->password])) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['login' => 'Your employee account is deactivated. Contact an Administrator.']);
            }

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login' => 'Invalid credentials. Only registered company employees can access.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function dashboard(Request $request, $section = 'overview')
    {
        $this->ensureFiscalColumn();

        $user = Auth::user();
        $roleName = DB::table('roles')->where('id', $user->role_id)->value('department_name') ?? 'Employee';

        $permissions = [
            'overview'       => ['Administrator', 'Sales', 'Purchasing', 'Warehouse', 'Route'],
            'users'          => ['Administrator'],
            'orders'         => ['Administrator', 'Sales', 'Purchasing', 'Warehouse', 'Route'],
            'deleted-orders' => ['Administrator', 'Sales'],
            'sales'          => ['Administrator', 'Sales'],
            'purchasing'     => ['Administrator', 'Purchasing'],
            'warehouse'      => ['Administrator', 'Warehouse'],
            'route'          => ['Administrator', 'Route'],
        ];

        if (!isset($permissions[$section]) || !in_array($roleName, $permissions[$section])) {
            abort(403, 'Unauthorized Access: Your assigned role (' . $roleName . ') does not have permission to access this module.');
        }

        $users = [];
        $roles = [];
        $orders = [];
        $deletedOrders = [];
        $customers = [];
        $nextInvoice = $this->generateNextInvoiceNumber();
        $nextCustomerId = (DB::table('customers')->max('id') ?? 0) + 1;

        if ($roleName === 'Administrator') {
            $users = DB::table('users')
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->select('users.*', 'roles.department_name as role_name')
                ->orderBy('users.id')
                ->get();
            $roles = DB::table('roles')->get();
        }

        if (in_array($section, ['orders', 'sales', 'purchasing', 'warehouse', 'route'])) {
            $customers = DB::table('customers')->orderBy('id')->get();

            $query = DB::table('orders')
                ->leftJoin('customers', 'orders.customer_number', '=', 'customers.id')
                ->whereNull('orders.deleted_at')
                ->select('orders.*', 'customers.*')
                ->addSelect(
                    'orders.id as order_id',
                    'orders.created_at as order_created_at',
                    'orders.updated_at as order_updated_at'
                );

            if ($request->filled('invoice_number')) {
                $query->where('orders.invoice_number', 'like', '%' . $request->invoice_number . '%');
            }
            if ($request->filled('customer_number')) {
                $query->where('orders.customer_number', $request->customer_number);
            }
            if ($request->filled('order_date')) {
                $query->whereDate('orders.order_date', $request->order_date);
            }
            if ($request->filled('status')) {
                $query->where('orders.current_status', $request->status);
            }

            $orders = $query->orderByDesc('orders.id')->get();
        }

        if ($section === 'deleted-orders') {
            $delQuery = DB::table('orders')
                ->leftJoin('customers', 'orders.customer_number', '=', 'customers.id')
                ->whereNotNull('orders.deleted_at')
                ->select('orders.*', 'customers.*')
                ->addSelect(
                    'orders.id as order_id',
                    'orders.deleted_at as order_deleted_at'
                );

            if ($request->filled('search')) {
                $term = '%' . $request->search . '%';
                $delQuery->where(function ($q) use ($term, $request) {
                    $q->where('orders.invoice_number', 'like', $term)
                      ->orWhere('orders.customer_number', $request->search)
                      ->orWhere('customers.company_name', 'like', $term);
                });
            }

            $deletedOrders = $delQuery->orderByDesc('orders.deleted_at')->get();
        }

        return view('admin.dashboard', compact(
            'user',
            'roleName',
            'section',
            'users',
            'roles',
            'orders',
            'deletedOrders',
            'customers',
            'nextInvoice',
            'nextCustomerId'
        ));
    }

    // ==========================================
    // GESTIÓN DE ÓRDENES (CREATE, UPDATE, SOFT DELETE, RESTORE)
    // ==========================================
    public function storeOrder(Request $request)
    {
        $this->ensureFiscalColumn();

        $roleName = $this->getRoleName();
        if (!in_array($roleName, ['Administrator', 'Sales'])) {
            abort(403, 'Only Administrator or Sales can create orders.');
        }

        $request->validate([
            'invoice_number'   => 'required|string|max:100|unique:orders,invoice_number',
            'customer_number'  => 'required|integer|min:1',
            'company_name'     => 'required|string|max:255',
            'fiscal_info'      => 'nullable|string|max:255',
            'order_date'       => 'required|date',
            'delivery_address' => 'required|string|max:500',
            'notes'            => 'nullable|string',
        ]);

        $customerData = [
            'company_name'     => $request->company_name,
            'delivery_address' => $request->delivery_address,
            'updated_at'       => now(),
        ];

        // Guarda el dato fiscal en todas las columnas fiscales disponibles en customers
        foreach (['fiscal_data', 'fiscal_info', 'tax_id', 'rfc'] as $col) {
            if (Schema::hasColumn('customers', $col)) {
                $customerData[$col] = $request->fiscal_info;
            }
        }

        $existingCustomer = DB::table('customers')->where('id', $request->customer_number)->first();
        if ($existingCustomer) {
            DB::table('customers')->where('id', $request->customer_number)->update($customerData);
        } else {
            $customerData['id'] = $request->customer_number;
            $customerData['created_at'] = now();
            DB::table('customers')->insert($customerData);
        }

        $orderData = [
            'customer_number' => $request->customer_number,
            'invoice_number'  => $request->invoice_number,
            'current_status'  => 'Ordered',
            'order_date'      => Carbon::parse($request->order_date)->format('Y-m-d H:i:s'),
            'notes'           => $request->notes,
            'created_at'      => now(),
            'updated_at'      => now(),
        ];

        if (Schema::hasColumn('orders', 'created_by_user_id')) {
            $orderData['created_by_user_id'] = Auth::id();
        }

        if (Schema::hasColumn('orders', 'fiscal_info')) {
            $orderData['fiscal_info'] = $request->fiscal_info;
        }

        DB::table('orders')->insert($orderData);

        return redirect('/dashboard/orders')->with('success', 'Order ' . $request->invoice_number . ' created with status "Ordered" and customer saved in catalog.');
    }

    public function updateOrder(Request $request, $id)
    {
        $this->ensureFiscalColumn();

        $roleName = $this->getRoleName();
        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order) {
            abort(404, 'Order not found.');
        }

        $request->validate([
            'current_status'   => 'required|in:Ordered,In process,In route,Delivered',
            'notes'            => 'nullable|string',
            'company_name'     => 'nullable|string|max:255',
            'delivery_address' => 'nullable|string|max:500',
            'fiscal_info'      => 'nullable|string|max:255',
            'order_date'       => 'nullable|date',
        ]);

        $orderUpdate = [
            'current_status' => $request->current_status,
            'notes'          => $request->notes,
            'updated_at'     => now(),
        ];

        if (in_array($roleName, ['Administrator', 'Sales'])) {
            if ($request->filled('order_date')) {
                $orderUpdate['order_date'] = Carbon::parse($request->order_date)->format('Y-m-d H:i:s');
            }
            if (Schema::hasColumn('orders', 'fiscal_info') && $request->has('fiscal_info')) {
                $orderUpdate['fiscal_info'] = $request->fiscal_info;
            }

            $custUpdate = ['updated_at' => now()];
            if ($request->filled('company_name')) {
                $custUpdate['company_name'] = $request->company_name;
            }
            if ($request->filled('delivery_address')) {
                $custUpdate['delivery_address'] = $request->delivery_address;
            }

            if ($request->has('fiscal_info')) {
                foreach (['fiscal_data', 'fiscal_info', 'tax_id', 'rfc'] as $col) {
                    if (Schema::hasColumn('customers', $col)) {
                        $custUpdate[$col] = $request->fiscal_info;
                    }
                }
            }

            DB::table('customers')->where('id', $order->customer_number)->update($custUpdate);
        }

        DB::table('orders')->where('id', $id)->update($orderUpdate);

        $redirectTarget = $order->deleted_at ? '/dashboard/deleted-orders' : '/dashboard/orders';
        return redirect($redirectTarget)->with('success', 'Order ' . $order->invoice_number . ' updated successfully.');
    }

    public function softDeleteOrder($id)
    {
        $roleName = $this->getRoleName();
        if (!in_array($roleName, ['Administrator', 'Sales'])) {
            abort(403, 'Only Administrator or Sales can delete orders.');
        }

        $order = DB::table('orders')->where('id', $id)->first();
        if ($order) {
            DB::table('orders')->where('id', $id)->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect('/dashboard/orders')->with('success', 'Order ' . ($order->invoice_number ?? '') . ' logically deleted (moved to Deleted Orders).');
    }

    public function restoreOrder($id)
    {
        $roleName = $this->getRoleName();
        if (!in_array($roleName, ['Administrator', 'Sales'])) {
            abort(403, 'Only Administrator or Sales can restore orders.');
        }

        $order = DB::table('orders')->where('id', $id)->first();
        if ($order) {
            DB::table('orders')->where('id', $id)->update([
                'deleted_at' => null,
                'updated_at' => now(),
            ]);
        }

        return redirect('/dashboard/deleted-orders')->with('success', 'Order ' . ($order->invoice_number ?? '') . ' restored to active orders.');
    }

    private function generateNextInvoiceNumber()
    {
        $lastId = DB::table('orders')->max('id') ?? 0;
        $nextNumber = str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        return 'INV-' . date('Y') . '-' . $nextNumber;
    }

    private function getFiscalColumnName()
    {
        foreach (['fiscal_data', 'fiscal_info', 'tax_id', 'rfc'] as $col) {
            if (Schema::hasColumn('customers', $col)) {
                return $col;
            }
        }
        return null;
    }

    private function ensureFiscalColumn()
    {
        if (Schema::hasTable('customers') && !$this->getFiscalColumnName()) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('fiscal_data', 255)->nullable()->after('company_name');
            });
        }
    }

    // ==========================================
    // GESTIÓN DE USUARIOS
    // ==========================================
    public function storeUser(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        DB::table('users')->insert([
            'username' => $request->username,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/dashboard/users')->with('success', 'Employee registered successfully.');
    }

    public function updateUser(Request $request, $id)
    {
        $this->authorizeAdmin();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->update($data);

        return redirect('/dashboard/users')->with('success', 'Employee updated successfully.');
    }

    public function toggleStatus($id)
    {
        $this->authorizeAdmin();

        $user = DB::table('users')->where('id', $id)->first();
        if ($user) {
            DB::table('users')->where('id', $id)->update([
                'is_active' => !$user->is_active,
                'updated_at' => now(),
            ]);
        }

        return redirect('/dashboard/users')->with('success', 'Employee status updated.');
    }

    private function getRoleName()
    {
        return DB::table('roles')->where('id', Auth::user()->role_id)->value('department_name') ?? 'Employee';
    }

    private function authorizeAdmin()
    {
        if ($this->getRoleName() !== 'Administrator') {
            abort(403, 'Unauthorized action. Administrator role required.');
        }
    }
}
