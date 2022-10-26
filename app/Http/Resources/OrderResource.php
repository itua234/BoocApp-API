<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            "order_no" => $this->order_no,
            "type" => $this->type,
            "chef_name" => User::find($this->chef_id)->fullname,
            "chef_photo" => User::find($this->photo),
            "status" => $this->order_status,
            "date" => $this->updated_at
        ];
    }
}
