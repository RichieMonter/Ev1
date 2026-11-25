<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;

// ==========================================
// 1. MÓDULO PÚBLICO: RASTREO DE ÓRDENES (VUE)
// ==========================================
Route::get('/', function () {
    return view('tracking');
});

Route::get('/track-order', function () {
    return view('tracking');
});

Route::get('/api/track-order', function (Request $request) {
    $customerNumber = $request->query('customer_number');
    $invoiceNumber = $request->query('invoice_number');

    $order = DB::table('orders')
        ->join('customers', 'orders.customer_number', '=', 'customers.id')
        ->where('orders.customer_number', $customerNumber)
        ->where('orders.invoice_number', $invoiceNumber)
        ->whereNull('orders.deleted_at')
        ->select(
            'customers.company_name as customerName',
            'orders.customer_number as customerNumber',
            'orders.invoice_number as invoiceNumber',
            'orders.current_status as status',
            'orders.order_date as date',
            'customers.delivery_address as address',
            'orders.notes'
        )
        ->first();

    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    return response()->json($order);
});

// ==========================================
// 2. MÓDULO ADMINISTRATIVO: LOGIN, USUARIOS Y ÓRDENES
// ==========================================
Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
Route::post('/login', [AdminController::class, 'login']);
Route::post('/logout', [AdminController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/{section?}', [AdminController::class, 'dashboard']);

    // Gestión de Usuarios (Administrator)
    Route::post('/admin/users', [AdminController::class, 'storeUser']);
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
    Route::patch('/admin/users/{id}/toggle', [AdminController::class, 'toggleStatus']);

    // Gestión de Órdenes (Create, Edit, Logical Delete, Restore)
    Route::post('/admin/orders', [AdminController::class, 'storeOrder']);
    Route::put('/admin/orders/{id}', [AdminController::class, 'updateOrder']);
    Route::delete('/admin/orders/{id}', [AdminController::class, 'softDeleteOrder']);
    Route::patch('/admin/orders/{id}/restore', [AdminController::class, 'restoreOrder']);
});
