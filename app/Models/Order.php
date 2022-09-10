<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    use BelongsToUser;

    protected $with = ['detail'];

    public function detail(){
        if($this->type == 'Home Service'):
            return $this->homeService()->first();
        elseif($this->type == 'Delivery Service'):
            return $this->deliveryService()->first();
        else:
            return null;
        endif;
    }

    public function homeService()
    {
        return $this->hasMany(HomeServiceOrderDetail::class);
    } 

    public function deliveryService()
    {
        return $this->hasMany(DeliveryServiceOrderDetail::class);
    }

}
