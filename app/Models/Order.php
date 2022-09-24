<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chef_id',
        'total',
        'reference',
        'payment_status',
        'order_status',
        'payment_channel',
        'discount_code'
    ];

    protected $hidden = [
        'created_at',
        //'updated_at',
    ];

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->toFormattedDateString(),
            set: fn ($value) => $value,
        );
    }

    protected $with = ['detail', 'contents'];

    public function detail(){
        if($this->type == 'Home Service'):
            return $this->homeService()->first();
        elseif($this->type == 'Delivery Service'):
            return $this->deliveryService()->first();
        elseif($this->type == 'Occasion Service'):
            return $this->occasionService()->first();
        else:
            return null;
        endif;
    }

    public function homeService()
    {
        return $this->hasOne(HomeServiceDetail::class);
    } 

    public function deliveryService()
    {
        return $this->hasOne(DeliveryServiceDetail::class);
    }

    public function occasionService()
    {
        return $this->hasOne(OccasionServiceDetail::class);
    }

    public function contents()
    {
        return $this->hasMany(OrderContent::class);
    }

    public function chef(){
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}
