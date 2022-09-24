<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DishExtra extends Model
{
    use HasFactory;

    protected $fillable = [
        'chef_id',
        'name',
        'measurement',
        'price',
        'profit',
        'description'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function chef(){
        return $this->belongsTo(User::class);
    }
}
