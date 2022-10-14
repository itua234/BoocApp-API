<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = [
        'chef_id',
        'category_id',
        'name',
        'description',
        'image',
        'measurement',
        'price',
        'profit'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function category()
    {
        return $this->belongsTo(DishCategory::class);
    }

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

}
