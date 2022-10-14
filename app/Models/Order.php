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
        'order_no'
    ];

    protected $hidden = [
        'updated_at',
        'verified'
    ];

    protected $with = ['contents'];

    protected $appends = ['detail'];

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->toFormattedDateString(),
            set: fn ($value) => $value,
        );
    }

    public function detail()
    {
        if($this->type == "HOME SERVICE"):
            return $this->homeService()->first();
        elseif($this->type == "DELIVERY SERVICE"):
            return $this->deliveryService()->first();
        elseif($this->type == "OCCASION SERVICE"):
            return $this->occasionService()->first();
        else:
            return null;
        endif;
    }

    public function getDetailAttribute()
    {
        return $this->detail();
    }

    public function homeService()
    {
        return $this->hasOne(HomeServiceDetail::class, 'order_id');
    } 

    public function deliveryService()
    {
        return $this->hasOne(DeliveryServiceDetail::class, 'order_id');
    }

    public function occasionService()
    {
        return $this->hasOne(OccasionServiceDetail::class, 'order_id');
    }

    /*public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'order_contents');
    }*/

    public function contents()
    {
        return $this->hasMany(OrderContent::class);
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
