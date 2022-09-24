<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OccasionServiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'occasion_type',
        'expected_guests',
        'date',
        'period',
        'firstname',
        'lastname',
        'phone',
        'address',
        'note',
        'budget'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
