<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // GET /api/orders (Listar órdenes de más recientes a más antiguas)
    public function index()
    {
        return response()->json(Order::orderBy('created_at', 'desc')->get(), 200);
    }

    // GET /api/orders/{id}
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order, 200);
    }

    // GET /api/orders/invoice/{invoice}
    public function searchByInvoice($invoice)
    {
        $order = Order::where('invoice_number', $invoice)->firstOrFail();
        return response()->json($order, 200);
    }

    // POST /api/orders
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:orders',
            'customer_number' => 'required|exists:customers,id',
            'created_by_user_id' => 'required|exists:users,id',
            'order_date' => 'required|date',
            'notes' => 'nullable|string',
            'current_status' => 'required|string'
        ]);

        $order = Order::create($validated);
        return response()->json($order, 201);
    }

    // PUT /api/orders/{id}
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->all());
        return response()->json($order, 200);
    }

    // DELETE /api/orders/{id} (Soft Delete / Archivar)
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['message' => 'Order archived successfully'], 200);
    }

    // GET /api/orders/archived
    public function archived()
    {
        return response()->json(Order::onlyTrashed()->get(), 200);
    }
}