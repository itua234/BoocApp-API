<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class ReportResource extends JsonResource
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
            "client_name" => User::find($this->user_id)->fullname,
            "status" => $this->order_status,
            "date" => $this->updated_at,
            "total" => $this->total,
            "profits" => 600
        ];
    }
}
