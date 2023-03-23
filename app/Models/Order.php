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

    public function generalOrder() {
        return $this->belongsTo(GeneralOrder::class, 'general_orders_id', 'id');
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }
}
