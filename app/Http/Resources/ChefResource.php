<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{
    User,
    Dish
};
use Illuminate\Support\Facades\DB;

class ChefResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "longitude" => $this->longitude,
            "latitude" => $this->latitude,
            "photo" => $this->photo,
            "rating" => $this->profile['rating'],
            "is_certified" => $this->profile['is_certified'],
            "is_restaurant" => $this->profile['is_restaurant'],
            "dishes" => count($this->dishes),
            "service" => $this->services->pluck('service_type')
        ];
    }
}
