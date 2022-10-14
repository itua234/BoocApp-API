<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChefProfile extends Model
{
    use HasFactory;
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'city',
        'state',
        'address',
        'nearest_landmark',
        'status',
        'rating',

        'id_card_url',
        'video_url',

        'is_certified',
        'certificate_url',

        'is_restaurant',
        'cac_reg_number',
        'restaurant_name',
        'restaurant_address'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
