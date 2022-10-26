<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCodeUsage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'redeemer_id',
        'owner_id',
    ];
}
