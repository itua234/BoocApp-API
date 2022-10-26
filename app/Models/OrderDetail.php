<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
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
        'is_filled_gas',
        'burners'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function firstname(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => ucwords(strtolower($value)),
        );
    }

    protected function lastname(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => ucwords(strtolower($value)),
        );
    }

    protected function period(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => ucwords(strtolower($value)),
        );
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
