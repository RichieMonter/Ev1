<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /**
     * Relación con el usuario que creó la orden.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Relación con el cliente asignado a la orden.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_number');
    }
}