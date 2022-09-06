<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DishExtra extends Model
{
    use HasFactory;

    public function dish(){
        return $this->belongsTo(Dish::class);
    }
}
