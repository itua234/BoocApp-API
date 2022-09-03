<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChefProfile extends Model
{
    use HasFactory, BelongsToUser;

    protected $fillable = [
        'user_id',
        'nin',
        'bvn',
        'driving_license',
        'driving_license_number',
        'residential_address',
        'latitude',
        'longitude',
        'available',
        'status',
        'photo'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
