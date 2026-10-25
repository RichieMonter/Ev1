<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes - Sistema Halcón (Evidence 2)
|--------------------------------------------------------------------------
*/

// --- USERS ENDPOINTS ---
Route::get('/users', [UserController::class, 'index']);                 // GET /api/users (Listar usuarios)
Route::get('/users/{id}', [UserController::class, 'show']);             // GET /api/users/{id} (Obtener usuario)
Route::post('/users', [UserController::class, 'store']);                // POST /api/users (Crear usuario)
Route::put('/users/{id}', [UserController::class, 'update']);           // PUT /api/users/{id} (Actualizar usuario)
Route::patch('/users/{id}/deactivate', [UserController::class, 'deactivate']); // PATCH /api/users/{id}/deactivate (Desactivar usuario)

// --- ORDERS ENDPOINTS ---
Route::get('/orders', [OrderController::class, 'index']);               // GET /api/orders (Listar órdenes recientes primero)
Route::get('/orders/archived', [OrderController::class, 'archived']);   // GET /api/orders/archived (Listar ordenes archivadas)
Route::get('/orders/invoice/{invoice}', [OrderController::class, 'searchByInvoice']); // GET /api/orders/invoice/{invoice} (Buscar por factura)
Route::get('/orders/{id}', [OrderController::class, 'show']);           // GET /api/orders/{id} (Obtener orden específica)
Route::post('/orders', [OrderController::class, 'store']);              // POST /api/orders (Crear orden)
Route::put('/orders/{id}', [OrderController::class, 'update']);         // PUT /api/orders/{id} (Actualizar orden / estado)
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);     // DELETE /api/orders/{id} (Archivar/Soft delete orden)
