<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'city',
        'state',
        'address',
        'nearest_landmark',
        'video_verification_url'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'id',
        'user_id'
    ];
    
}
