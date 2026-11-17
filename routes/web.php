<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
