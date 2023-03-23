<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dish extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'price', 'ingredients', 'category_id', 'restaurant_id'
    ];
    protected $dates = ['deleted_at'];

    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }

    public function category() {
        return $this->belongsTo(DishCategory::class);
    }
}
