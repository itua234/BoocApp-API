<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DishCategory extends Model
{
    use HasFactory;
    use BelongsToUser;

    protected $fillable = [
        'name',
        'user_id',
        'slug',
        'description',
        'type'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function dishes()
    {
        return $this->hasMany(Dish::class, 'category_id');
    }
}
