<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceUser extends Model
{
    use HasFactory;

    protected $table = 'service_users';

    protected $fillable = [
        'user_id',
        'service_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
