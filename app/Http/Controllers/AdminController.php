<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    public function dashboard($section = 'overview')
    {
        $user = Auth::user();
        $roleName = DB::table('roles')->where('id', $user->role_id)->value('department_name') ?? 'Employee';

        // Control estricto de acceso por rol según la sección solicitada
        $permissions = [
            'overview'   => ['Administrator', 'Sales', 'Purchasing', 'Warehouse', 'Route'],
            'users'      => ['Administrator'],
            'sales'      => ['Administrator', 'Sales'],
            'purchasing' => ['Administrator', 'Purchasing'],
            'warehouse'  => ['Administrator', 'Warehouse'],
            'route'      => ['Administrator', 'Route'],
        ];

        if (!isset($permissions[$section]) || !in_array($roleName, $permissions[$section])) {
            abort(403, 'Unauthorized Access: Your assigned role (' . $roleName . ') does not have permission to access this module.');
        }

        $users = [];
        $roles = [];
        $orders = [];

        if ($roleName === 'Administrator') {
            $users = DB::table('users')
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->select('users.*', 'roles.department_name as role_name')
                ->orderBy('users.id')
                ->get();
            $roles = DB::table('roles')->get();
        }

        if (in_array($section, ['sales', 'purchasing', 'warehouse', 'route'])) {
            $orders = DB::table('orders')
                ->leftJoin('customers', 'orders.customer_number', '=', 'customers.id')
                ->whereNull('orders.deleted_at')
                ->select('orders.*', 'customers.company_name', 'customers.delivery_address')
                ->get();
        }

        return view('admin.dashboard', compact('user', 'roleName', 'section', 'users', 'roles', 'orders'));
    }

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

    private function authorizeAdmin()
    {
        $roleName = DB::table('roles')->where('id', Auth::user()->role_id)->value('department_name');
        if ($roleName !== 'Administrator') {
            abort(403, 'Unauthorized action. Administrator role required.');
        }
    }
}