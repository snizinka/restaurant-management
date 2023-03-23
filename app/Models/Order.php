<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['general_orders_id', 'order_number', 'restaurant_id'];
    protected $dates = ['deleted_at'];

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }
}
