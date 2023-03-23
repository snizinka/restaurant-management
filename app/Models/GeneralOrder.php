<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'order_number', 'driver_id', 'status', 'address', 'phone', 'username'];
    protected $dates = ['deleted_at'];


    public function orders() {
        return $this->hasMany(Order::class, 'general_orders_id', 'id');
    }

    public function driver() {
        return $this->belongsTo(Driver::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
