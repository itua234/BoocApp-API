<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chef_id',
        'total',
        'subtotal',
        'shipping_cost',
        'subcharge',
        'type',
        'reference',
        'payment_status',
        'order_status',
        'payment_channel',
        'discount_code',
        'verified',
        'order_no',
        'reason_for_declining'
    ];

    protected $hidden = [
        'created_at',
        'verified'
    ];

    protected $with = ['contents', 'detail'];

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->toFormattedDateString(),
            set: fn ($value) => $value,
        );
    }

    public function detail()
    {
        return $this->hasOne(OrderDetail::class, 'order_id');
    }

    public function contents()
    {
        return $this->hasMany(OrderContent::class, 'order_id');
    }

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
