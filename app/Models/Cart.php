<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'dish_id', 'count'];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function dish() {
        return $this->belongsTo(Dish::class);
    }

}
