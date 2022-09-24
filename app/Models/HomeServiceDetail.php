<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeServiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'date',
        'period',
        'firstname',
        'lastname',
        'phone',
        'address',
        'note',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
